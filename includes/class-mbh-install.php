<?php
/**
 
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Install' ) ) :

/**
 * HTL_Install Class
 */
class MBH_Install {

	/**
	 * Init functions.
	 * @access public
	 * @return void
	 */
	public static function init() {
		add_action( 'admin_init', array( __CLASS__, 'check_version' ), 5 );
		add_filter( 'plugin_action_links_' . MBH_PLUGIN_BASENAME, array( __CLASS__, 'plugin_action_links' ) );
		add_filter( 'plugin_row_meta', array( __CLASS__, 'plugin_row_meta' ), 10, 2 );
	}

	/**
	 * Check HTL version.
	 *
	 * @access public
	 * @return void
	 */
	public static function check_version() {
		if ( ! defined( 'IFRAME_REQUEST' ) && ( get_option( 'mb_hms_version' ) != MBH_VERSION ) ) {
			self::install();
			do_action( 'mb_hms_updated' );
		}
	}

	/**
	 * Install HTL
	 */
	public static function install() {
		// Ensure needed classes are loaded
		include_once MBH_PLUGIN_DIR . 'includes/class-mbh-roles.php';

		// Create tables
		self::create_tables();

		// Create log files
		self::create_files();

		// Cron jobs
		self::create_cron_jobs();

		// Create HTL hotel roles
		$roles = new MBH_Roles;
		$roles->add_roles();
		$roles->add_caps();

		// Update version
		delete_option( 'mb_hms_version' );
		add_option( 'mb_hms_version', MBH_VERSION );

		// Register endpoints
		MBH()->query->init_query_vars();
		MBH()->query->add_endpoints();

		// Clear the permalinks
		flush_rewrite_rules( false );

		// Bail if activating from network, or bulk
		if ( is_network_admin() || isset( $_GET[ 'activate-multi' ] ) ) {
			return;
		}

		// Trigger action
		do_action( 'mb_hms_installed' );
	}

	/**
	 * Create files/directories
	 */
	private static function create_files() {
		// Install files and folders for uploading files and prevent hotlinking
		$upload_dir = wp_upload_dir();

		$files = array(
			array(
				'base' 		=> MBH_LOG_DIR,
				'file' 		=> '.htaccess',
				'content' 	=> 'deny from all'
			),
			array(
				'base' 		=> MBH_LOG_DIR,
				'file' 		=> 'index.html',
				'content' 	=> ''
			)
		);

		foreach ( $files as $file ) {
			if ( wp_mkdir_p( $file[ 'base' ] ) && ! file_exists( trailingslashit( $file[ 'base' ] ) . $file[ 'file' ] ) ) {
				if ( $file_handle = @fopen( trailingslashit( $file[ 'base' ] ) . $file[ 'file' ], 'w' ) ) {
					fwrite( $file_handle, $file[ 'content' ] );
					fclose( $file_handle );
				}
			}
		}
	}

	/**
	 * Set up the database tables which mb_hms needs to function.
	 */
	private static function create_tables() {
		global $wpdb;

		$wpdb->hide_errors();

		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

		dbDelta( self::get_schema() );
	}

	/**
	 * Get Table schema
	 * @return string
	 */
	private static function get_schema() {
		global $wpdb;

		$charset_collate = $wpdb->get_charset_collate();

		$sql = "CREATE TABLE {$wpdb->prefix}mb_hms_reservation_items (
			reservation_item_id bigint(20) NOT NULL auto_increment,
			reservation_item_name longtext NOT NULL,
			reservation_id bigint(20) NOT NULL,
			PRIMARY KEY  (reservation_item_id),
			KEY reservation_id (reservation_id)
		) $charset_collate;
		CREATE TABLE {$wpdb->prefix}mb_hms_reservation_itemmeta (
			meta_id bigint(20) NOT NULL auto_increment,
			reservation_item_id bigint(20) NOT NULL,
			meta_key varchar(255) NULL,
			meta_value longtext NULL,
			PRIMARY KEY  (meta_id),
			KEY reservation_item_id (reservation_item_id),
			KEY meta_key (meta_key)
		) $charset_collate;
		CREATE TABLE {$wpdb->prefix}mb_hms_bookings (
			id bigint(20) NOT NULL auto_increment,
			reservation_id bigint(20) NOT NULL,
			checkin date NOT NULL,
			checkout date NOT NULL,
			status varchar(255) NOT NULL,
			PRIMARY KEY  (id),
			KEY reservation_id (reservation_id)
		) $charset_collate;
		CREATE TABLE {$wpdb->prefix}mb_hms_rooms_bookings (
			id bigint(20) NOT NULL auto_increment,
			reservation_id bigint(20) NOT NULL,
			room_id bigint(20) NOT NULL,
			PRIMARY KEY  (id),
			KEY reservation_id (reservation_id),
			KEY room_id (room_id)
		) $charset_collate;
		CREATE TABLE {$wpdb->prefix}mb_hms_sessions (
			session_id bigint(20) NOT NULL AUTO_INCREMENT,
			session_key char(32) NOT NULL,
			session_value longtext NOT NULL,
			session_expiry bigint(20) NOT NULL,
			UNIQUE KEY session_id (session_id),
			PRIMARY KEY  (session_key)
		) $charset_collate;
		";

		return $sql;

	}

	/**
	 * Create mb_hms pages, storing page id's in variables.
	 */
	public static function create_pages() {
		$pages = apply_filters( 'mb_hms_create_pages', array(
			'listing' => array(
				'name'    => esc_html_x( 'available-rooms', 'Page slug', 'wp-mb_hms' ),
				'title'   => esc_html_x( 'Available rooms', 'Page title', 'wp-mb_hms' ),
				'content' => '[' . apply_filters( 'mb_hms_listing_shortcode_tag', 'mb_hms_listing' ) . ']'
			),
			'booking' => array(
				'name'    => esc_html_x( 'booking', 'Page slug', 'wp-mb_hms' ),
				'title'   => esc_html_x( 'Booking', 'Page title', 'wp-mb_hms' ),
				'content' => '[' . apply_filters( 'mb_hms_booking_shortcode_tag', 'mb_hms_booking' ) . ']'
			)
		) );

		foreach ( $pages as $key => $page ) {
			MBH_Admin_Functions::create_page( $key, esc_sql( $page['name'] ), 'mb_hms_' . $key . '_page_id', $page[ 'title' ], $page[ 'content' ], ! empty( $page[ 'parent' ] ) ? mbh_get_page_id( $page[ 'parent' ] ) : '' );
		}

		delete_transient( 'mb_hms_cache_excluded_uris' );
	}

	/**
	 * Create cron jobs (clear them first).
	 */
	private static function create_cron_jobs() {
		wp_clear_scheduled_hook( 'mb_hms_cancel_pending_reservations' );
		wp_clear_scheduled_hook( 'mb_hms_process_completed_reservations' );
		wp_clear_scheduled_hook( 'mb_hms_cleanup_sessions' );
		wp_clear_scheduled_hook( 'mb_hms_check_license_cron' );

		$hold_minutes = mbh_get_option( 'booking_hold_minutes', '60' );

		if ( $hold_minutes != 0 ) {
			wp_schedule_single_event( time() + ( absint( $hold_minutes ) * 60 ), 'mb_hms_cancel_pending_reservations' );
		}

		wp_schedule_event( time(), 'daily', 'mb_hms_process_completed_reservations' );
		wp_schedule_event( time(), 'twicedaily', 'mb_hms_cleanup_sessions' );
		wp_schedule_event( time(), 'weekly', 'mb_hms_check_license_cron' );
	}

	/**
	 * Show action links on the plugin screen.
	 *
	 * @param	mixed $links Plugin Action links
	 * @return	array
	 */
	public static function plugin_action_links( $links ) {
		$action_links = array(
			'settings' => '<a href="' . admin_url( 'admin.php?page=mb_hms-settings' ) . '" title="' . esc_attr__( 'View mb_hms settings', 'wp-mb_hms' ) . '">' . esc_html__( 'Settings', 'wp-mb_hms' ) . '</a>',
		);

		return array_merge( $action_links, $links );
	}

	/**
	 * Show row meta on the plugin screen.
	 *
	 * @param	mixed $links Plugin Row Meta
	 * @param	mixed $file  Plugin Base file
	 * @return	array
	 */
	public static function plugin_row_meta( $links, $file ) {
		if ( $file == MBH_PLUGIN_BASENAME ) {
			$row_meta = array(
			);

			return array_merge( $links, $row_meta );
		}

		return (array) $links;
	}
}

endif;

MBH_Install::init();
