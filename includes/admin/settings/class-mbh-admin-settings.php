<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Admin_Settings' ) ) :

/**
 * MBH_Admin_Settings Class
 *
 * Creates the Settings page.
 */
class MBH_Admin_Settings {

	/**
    * Holds the mb_hms_options array
    */
    private $options = array();

    /**
    * Holds the tabs array
    */
	private $settings_tabs = array();

	/**
    * Holds the registered settings array
    */
	private $registered_settings = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Add menus
		add_action( 'admin_menu', array( $this, 'add_settings_menu_page' ), 9 );
		add_action( 'admin_init', array( $this, 'add_settings' ) );
		add_action( 'admin_init', array( $this, 'add_separator' ) );
		add_action( 'init', array( $this, 'registered_settings' ) );

		$this->includes();
		$this->options = (array) get_option( 'mb_hms_settings' );
	}

	/**
	 * Include required files.
	 */
	public function includes() {
		include_once 'class-mbh-admin-settings-default.php';
		include_once 'class-mbh-admin-settings-fields.php';
	}

	/**
	 * Add menu items
	 */
	public function add_settings_menu_page() {
		add_menu_page( '', esc_html__( 'MB HMS', 'wp-mb_hms' ), 'manage_mb_hms', 'mb_hms-settings', array( $this, 'create_settings_page' ), '', '45.5' );

		add_submenu_page( 'mb_hms-settings', esc_html__( 'Hotel Management System Settings', 'wp-mb_hms' ), esc_html__( 'Settings', 'wp-mb_hms' ), 'manage_mb_hms', 'mb_hms-settings', array( $this, 'create_settings_page' ) );
	}

	/**
	 * Register menu separator
	 */
	public function add_separator( $position ) {
		global $menu;

		$menu[ $position ] = array(
			0	=>	'',
			1	=>	'read',
			2	=>	'separator' . $position,
			3	=>	'',
			4	=>	'wp-menu-separator mb_hms'
		);
	}

	/**
	 * Get settings tabs.
	 */
	public function get_settings_tabs() {
		if ( empty( $this->settings_tabs ) ) {
			$settings_tabs = array();

			$settings_tabs[ 'general' ]                = esc_html__( 'General options', 'wp-mb_hms' );
			//$settings_tabs[ 'rooms-and-reservations' ] = esc_html__( 'Rooms & reservations', 'wp-mb_hms' );
			//$settings_tabs[ 'seasonal-prices' ]        = esc_html__( 'Seasonal prices', 'wp-mb_hms' );
			$settings_tabs[ 'payment' ]                = esc_html__( 'Payment gateways', 'wp-mb_hms' );
			//$settings_tabs[ 'tax' ]                    = esc_html__( 'Tax', 'wp-mb_hms' );
			$settings_tabs[ 'emails' ]                 = esc_html__( 'Others options', 'wp-mb_hms' );
			//$settings_tabs[ 'tools' ]                  = esc_html__( 'Tools', 'wp-mb_hms' );

			$this->settings_tabs = apply_filters( 'mb_hms_get_settings_tabs', $settings_tabs );
		}

		return $this->settings_tabs;
	}

	/**
	 * Settings page.
	 *
	 * Renders the settings page contents.
	 */
	public function create_settings_page() {

		// Include settings tabs
		$tabs = $this->get_settings_tabs();

		$active_tab = isset( $_GET[ 'tab' ] ) && array_key_exists( $_GET[ 'tab' ], $tabs ) ? $_GET[ 'tab' ] : 'general';

		ob_start();
		?>
		<div class="wrap mb_hms-settings mb_hms-settings-<?php echo esc_attr( $active_tab ); ?>">
			<h2 class="nav-tab-wrapper">
				<?php
				foreach( $tabs as $tab_id => $tab_name ) {

					$tab_url = add_query_arg( array(
						'settings-updated' => false,
						'tab' => $tab_id
					) );

					$active = $active_tab == $tab_id ? ' nav-tab-active' : '';

					echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab' . $active . '">';
						echo esc_html( $tab_name );
					echo '</a>';
				}

				//echo '<a href="' . esc_url( admin_url( 'admin.php?page=mb_hms-logs' ) ) . '" title="' . esc_attr__( 'Logs', 'wp-mb_hms' ) . '" class="nav-tab">';
					//	echo esc_html__( 'Logs', 'wp-mb_hms' );
				//echo '</a>';
				?>
			</h2>
			<div id="tab_container">
				<form method="post" action="options.php">
					<table class="form-table">
					<?php
					settings_fields( 'mb_hms_settings' );
					do_action( 'mb_hms_settings_tab_top_' . $active_tab );
					do_settings_fields( 'mb_hms_settings_' . $active_tab, 'mb_hms_settings_' . $active_tab );
					?>
					</table>
					<?php submit_button(); ?>
				</form>
			</div><!-- #tab_container-->
		</div><!-- .wrap -->
		<?php
		echo ob_get_clean();
	}

	/**
	 * Retrieve the array of plugin settings.
	 */
	public function registered_settings() {
		return MBH_Admin_Settings_Default::settings();
	}

	/**
	 * Add all settings sections and fields.
	 */
	public function add_settings() {
		if ( false == get_option( 'mb_hms_settings' ) ) {
			add_option( 'mb_hms_settings' );
		}

		foreach( $this->registered_settings() as $tab => $settings ) {
			add_settings_section(
				'mb_hms_settings_' . $tab,
				__return_null(),
				'__return_false',
				'mb_hms_settings_' . $tab
			);

			foreach ( $settings as $option ) {
				$name = isset( $option[ 'name' ] ) ? $option[ 'name' ] : '';

				add_settings_field(
					'mb_hms_settings[ ' . $option[ 'id' ] . ' ]',
					$name,
					array( $this, 'default_callback' ),
					'mb_hms_settings_' . $tab,
					'mb_hms_settings_' . $tab,
					array(
						'section'      => $tab,
						'id'           => isset( $option[ 'id' ] ) ? $option[ 'id' ] : null,
						'type'         => isset( $option[ 'type' ] ) ? $option[ 'type' ] : 'text',
						'desc'         => ! empty( $option[ 'desc' ] ) ? $option[ 'desc' ] : '',
						'subdesc'      => ! empty( $option[ 'subdesc' ] ) ? $option[ 'subdesc' ] : '',
						'name'         => isset( $option[ 'name' ] ) ? $option[ 'name' ] : null,
						'size'         => isset( $option[ 'size' ] ) ? $option[ 'size' ] : null,
						'options'      => isset( $option[ 'options' ] ) ? $option[ 'options' ] : '',
						'std'          => isset( $option[ 'std' ] ) ? $option[ 'std' ] : '',
						'multiple'     => isset( $option[ 'multiple' ] ) ? $option[ 'multiple' ] : null,
						'placeholder'  => isset( $option[ 'placeholder' ] ) ? $option[ 'placeholder' ] : null
					)
				);
			}
		}

		// Creates our settings in the options table
		register_setting( 'mb_hms_settings', 'mb_hms_settings', array( $this, 'sanitize_settings' ) );
	}

	/**
	 * Settings Sanitization
	 *
	 *
	 * @param array $input The value inputted in the field
	 *
	 * @return string $input Sanitizied value
	 */
	function sanitize_settings( $input = array() ) {
		if ( empty( $_POST[ '_wp_http_referer' ] ) ) {
			return $input;
		}

		parse_str( $_POST[ '_wp_http_referer' ], $referrer );

		$settings = $this->registered_settings();
		$tab      = isset( $referrer[ 'tab' ] ) ? $referrer[ 'tab' ] : 'general';

		// Save unchecked checkboxes
		foreach ( $settings[ $tab ] as $index => $args ) {
			if ( isset( $args[ 'type' ] ) && ( $args[ 'type' ] == 'checkbox' ) ) {
				if ( ! isset( $input[ $args[ 'id' ] ] ) ) {
					$input[ $args[ 'id' ] ] = 0;
				}
			}
		}

		$input = $input ? $input : array();
		$input = apply_filters( 'mb_hms_settings_' . $tab . '_sanitize', $input );

		// Loop through each setting being saved and pass it through a sanitization filter
		foreach ( $input as $key => $value ) {

			// Get the setting type (checkbox, select, etc)
			$type = isset( $settings[ $tab ][ $key ][ 'type' ] ) ? $settings[ $tab ][ $key ][ 'type' ] : false;

			if ( $type ) {
				// Field type specific filter
				$input[ $key ] = apply_filters( 'mb_hms_settings_sanitize_' . $type, $value, $key );
			}
		}

		// Loop through the settings and unset any that are empty for the tab being saved
		if ( ! empty( $settings[ $tab ] ) ) {
			foreach ( $settings[ $tab ] as $key => $value ) {

				if ( is_numeric( $key ) ) {
					$key = $value[ 'id' ];
				}

				if ( empty( $input[ $key ] ) ) {
					unset( $this->options[ $key ] );
				}
			}
		}

		// Merge new settings with the existing
		$output = array_merge( $this->options, $input );

		add_settings_error( 'mb_hms-notices', '', esc_html__( 'Your settings have been saved.', 'wp-mb_hms' ), 'updated' );

		return $output;
	}

	/**
	 * Default field callback
	 *
	 * Default output is empty (a simple warning). We will use a filter to create the field.
	 *
	 * @param array $args Arguments passed by the setting
	 */
	function default_callback( $args ) {
		$html = sprintf( __( 'The callback function used for the <strong>%s</strong> setting is missing.', 'wp-mb_hms' ), $args[ 'id' ] );
		echo apply_filters( 'mb_hms_settings_' . $args[ 'type' ] . '_callback', $html, $args );
	}
}

endif;

return new MBH_Admin_Settings();
