<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_License_Manager' ) ) :

/**
 * MBH_License_Manager Class
 */
class MBH_License_Manager {
	private $file;
	private $license;
	private $item_name;
	private $item_id;
	private $item_shortname;
	private $version;
	private $api_url = 'https://.com';

	/**
	 * Class constructor
	 *
	 * @param string  $_file
	 * @param string  $_item_name
	 * @param string  $_version
	 */
	function __construct( $_file, $_item_name, $_version ) {
		$this->file      = $_file;
		$this->item_name = $_item_name;
		$this->item_shortname = 'mbh_' . preg_replace( '/[^a-zA-Z0-9_\s]/', '', str_replace( ' ', '_', strtolower( $this->item_name ) ) );
		$this->version        = $_version;
		$this->license        = trim( mbh_get_option( $this->item_shortname . '_license_key', '' ) );

		// Setup hooks
		$this->includes();
		$this->hooks();
	}

	/**
	 * Include the updater class
	 */
	private function includes() {
		if ( ! class_exists( 'EDD_SL_Plugin_Updater' ) )  {
			require_once 'vendor/EDD_SL_Plugin_Updater.php';
		}
	}

	/**
	 * Setup hooks
	 */
	private function hooks() {
		// Register settings
		add_filter( 'mb_hms_settings_licenses', array( $this, 'settings' ), 1 );

		// Add license tab
		add_filter( 'mb_hms_get_settings_tabs', array( $this, 'add_tab' ), 1 );

		// Display help text at the top of the Licenses tab
		add_action( 'mb_hms_settings_tab_top_licenses', array( $this, 'help_text' ) );

		// Activate license key on settings save
		add_action( 'admin_init', array( $this, 'activate_license' ) );

		// Deactivate license key on settings save
		add_action( 'admin_init', array( $this, 'deactivate_license' ) );

		// Check that license is valid once per week
		add_action( 'mb_hms_check_license_cron', array( $this, 'weekly_license_check' ) );

		// Updater
		add_action( 'admin_init', array( $this, 'auto_updater' ), 0 );

		// Display notices to admins
		add_action( 'admin_notices', array( $this, 'notices' ) );
		add_action( 'in_plugin_update_message-' . plugin_basename( $this->file ), array( $this, 'plugin_row_license_missing' ), 10, 2 );
	}

	/**
	 * Add license field to settings
	 */
	public function settings( $settings ) {
		$license_settings = array(
			array(
				'id'        => $this->item_shortname . '_license_key',
				'name'      => $this->item_name,
				'desc'      => '',
				'type'      => 'license_key',
			)
		);

		return array_merge( $settings, $license_settings );
	}

	/**
	 * Add license tab to settings
	 */
	public function add_tab( $settings_tabs ) {
		$settings_tabs[ 'licenses' ] = esc_html__( 'Licenses', 'wp-mb_hms' );

		return array_merge( $settings_tabs );
	}

	/**
	 * Display help text at the top of the Licenses tab
	 */
	public function help_text() {
		static $has_ran;

		if ( ! empty( $has_ran ) ) {
			return;
		}

		echo '<p class="tab-top-help-text">' . wp_kses_post( sprintf(
			__( 'Enter your extension license keys here to receive updates for purchased extensions. If your license key has expired, please <a href="%s" target="_blank">renew your license</a>.', 'wp-mb_hms' ),
			'http://docs.wpmb_hms.com/article/36-how-to-renew-a-license'
		) ) . '</p>';

		$has_ran = true;
	}

	/**
	 * Activate license when settings are saved
	 */
	public function activate_license() {
		if ( ! isset( $_POST[ 'mb_hms_settings' ] ) ) {
			return;
		}

		if ( ! isset( $_REQUEST[ $this->item_shortname . '_license_key-nonce'] ) || ! wp_verify_nonce( $_REQUEST[ $this->item_shortname . '_license_key-nonce'], $this->item_shortname . '_license_key-nonce' ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_mb_hms' ) ) {
			return;
		}

		if ( empty( $_POST[ 'mb_hms_settings' ][ $this->item_shortname . '_license_key' ] ) ) {
			delete_option( $this->item_shortname . '_license_key_active' );
			return;
		}

		foreach ( $_POST as $key => $value ) {
			if( false !== strpos( $key, 'license_key_deactivate' ) ) {
				// Don't activate a key when deactivating a different key
				return;
			}
		}

		$details = get_option( $this->item_shortname . '_license_key_active' );

		if ( is_object( $details ) && 'valid' === $details->license ) {
			return;
		}

		$license = sanitize_text_field( $_POST[ 'mb_hms_settings' ][ $this->item_shortname . '_license_key' ] );

		if ( empty( $license ) ) {
			return;
		}

		// Data to send to the API
		$api_params = array(
			'edd_action' => 'activate_license',
			'license'    => $license,
			'item_name'  => urlencode( $this->item_name ),
			'url'        => home_url()
		);

		// Call the API
		$response = wp_remote_post(
			$this->api_url,
			array(
				'user-agent' => 'Easy WP mb_hms License Manager',
				'timeout'    => 15,
				'sslverify'  => false,
				'body'       => $api_params
			)
		);

		// Make sure there are no errors
		if ( is_wp_error( $response ) ) {
			return;
		}

		// Tell WordPress to look for updates
		set_site_transient( 'update_plugins', null );

		// Decode license data
		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		update_option( $this->item_shortname . '_license_key_active', $license_data );
	}

	/**
	 * Deactivate the license key
	 */
	public function deactivate_license() {
		if ( ! isset( $_POST[ 'mb_hms_settings' ] ) ) {
			return;
		}

		if ( ! isset( $_POST[ 'mb_hms_settings' ][ $this->item_shortname . '_license_key' ] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_REQUEST[ $this->item_shortname . '_license_key-nonce' ], $this->item_shortname . '_license_key-nonce' ) ) {
			wp_die( __( 'Nonce verification failed', 'wp-mb_hms' ), __( 'Error', 'wp-mb_hms' ), array( 'response' => 403 ) );
		}

		if ( ! current_user_can( 'manage_mb_hms' ) ) {
			return;
		}

		// Run on deactivate button press
		if ( isset( $_POST[ $this->item_shortname . '_license_key_deactivate'] ) ) {

			// Data to send to the API
			$api_params = array(
				'edd_action' => 'deactivate_license',
				'license'    => $this->license,
				'item_name'  => urlencode( $this->item_name ),
				'url'        => home_url()
			);

			// Call the API
			$response = wp_remote_post(
				$this->api_url,
				array(
					'user-agent' => 'Easy WP mb_hms License Manager',
					'timeout'    => 15,
					'sslverify'  => false,
					'body'       => $api_params
				)
			);

			// Make sure there are no errors
			if ( is_wp_error( $response ) ) {
				return;
			}

			// Decode the license data
			$license_data = json_decode( wp_remote_retrieve_body( $response ) );

			delete_option( $this->item_shortname . '_license_key_active' );
		}
	}

	/**
	 * Check if license key is valid once per week
	 */
	public function weekly_license_check() {
		if( ! empty( $_POST[ 'mb_hms_settings' ] ) ) {
			return; // Don't fire when saving settings
		}

		if ( empty( $this->license ) ) {
			return;
		}

		// data to send in our API request
		$api_params = array(
			'edd_action' => 'check_license',
			'license'    => $this->license,
			'item_name'  => urlencode( $this->item_name ),
			'url'        => home_url()
		);

		// Call the API
		$response = wp_remote_post(
			$this->api_url,
			array(
				'user-agent' => 'Easy WP mb_hms License Manager',
				'timeout'    => 15,
				'sslverify'  => false,
				'body'       => $api_params
			)
		);

		if ( is_wp_error( $response ) ) {
			return false;
		}

		$license_data = json_decode( wp_remote_retrieve_body( $response ) );

		update_option( $this->item_shortname . '_license_key_active', $license_data );
	}

	/**
	 * Auto updater
	 */
	public function auto_updater() {
		$args = array(
			'version'   => $this->version,
			'license'   => $this->license,
			'author'    => 'mb_hms',
			'beta'      => false
		);

		if ( ! empty( $this->item_id ) ) {
			$args[ 'item_id' ]   = $this->item_id;
		} else {
			$args[ 'item_name' ] = $this->item_name;
		}

		// Setup the updater
		$edd_updater = new EDD_SL_Plugin_Updater(
			$this->api_url,
			$this->file,
			$args
		);
	}

	/**
	 * Admin notices for errors
	 */
	public function notices() {
		static $showed_invalid_message;

		if ( empty( $this->license ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_mb_hms' ) ) {
			return;
		}

		$messages = array();
		$license = get_option( $this->item_shortname . '_license_key_active' );

		if ( is_object( $license ) && 'valid' !== $license->license && empty( $showed_invalid_message ) ) {
			if ( empty( $_GET[ 'tab' ] ) || 'licenses' !== $_GET[ 'tab' ] ) {
				$messages[] = sprintf(
					__( 'You have invalid or expired license keys for Easy WP mb_hms. Please go to the <a href="%s">Licenses page</a> to correct this issue.', 'wp-mb_hms' ),
					admin_url( 'admin.php?page=mb_hms-settings&tab=licenses' )
				);

				$showed_invalid_message = true;
			}
		}

		if ( ! empty( $messages ) ) {
			foreach( $messages as $message ) {
				echo '<div class="error">';
					echo '<p>' . $message . '</p>';
				echo '</div>';
			}
		}
	}

	/**
	 * Displays message inline on plugin row that the license key is missing
	 */
	public function plugin_row_license_missing( $plugin_data, $version_info ) {
		static $showed_imissing_key_message;

		$license = get_option( $this->item_shortname . '_license_key_active' );

		if ( ( ! is_object( $license ) || 'valid' !== $license->license ) && empty( $showed_imissing_key_message[ $this->item_shortname ] ) ) {
			echo '&nbsp;<strong><a href="' . esc_url( admin_url( 'admin.php?page=mb_hms-settings&tab=licenses' ) ) . '">' . __( 'Enter valid license key for automatic updates.', 'wp-mb_hms' ) . '</a></strong>';
			$showed_imissing_key_message[ $this->item_shortname ] = true;
		}
	}
}

endif;
