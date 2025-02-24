<?php
/**
 * Plugin Name:       Hotel Management System
 * Description:       Manage your hotel.
 * Version:           1.0
 * Author:            ngouma timothee fredy
 * Text Domain:       wp-mb_hms
 * Domain Path:       languages
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'Mb_hms' ) ) :

/**
 * Main mb_hms Class
 */
final class Mb_hms {

	/**
	 * @var string
	 */
	public $version = '1.8.4';

	/**
	 * @var mb_hms The single instance of the class
	 */
	private static $_instance = null;

	/**
	 * MBH Session Object
	 *
	 * @var object
	 */
	public $session = null;

	/**
	 * MBH Query Object
	 *
	 * @var object
	 */
	public $query = null;

	/**
	 * MBH Roles Object
	 *
	 * @var object
	 */
	public $roles = null;

	/**
	 * MBH Cart Object
	 *
	 * @var object
	 */
	public $cart = null;

	/**
	 * Main mb_hms Instance
	 *
	 * Insures that only one instance of mb_hms exists in memory at any one time.
	 *
	 * @static
	 * @see MBH()
	 * @return mb_hms - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}

		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'wp-mb_hms' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'wp-mb_hms' ), '1.0.0' );
	}

	/**
	 * Auto-load in-accessible properties on demand.
	 * @param mixed $key
	 * @return mixed
	 */
	public function __get( $key ) {
		if ( in_array( $key, array( 'payment_gateways', 'mailer', 'booking' ) ) ) {
			return $this->$key();
		}
	}

	/**
	 * mb_hms Constructor.
	 */
	public function __construct() {
		$this->setup_constants();
		$this->includes();
		$this->init_hooks();

		do_action( 'mb_hms_loaded' );
	}

	/**
	 * Hook into actions and filters
	 */
	private function init_hooks() {
		register_activation_hook( __FILE__, array( 'MBH_Install', 'install' ) );
		add_action( 'after_setup_theme', array( $this, 'setup_thumbnails' ) );
		add_action( 'after_setup_theme', array( $this, 'template_functions' ), 11 );
		add_action( 'init', array( $this, 'init' ), 0 );
		add_action( 'init', array( 'MBH_Emails', 'init_transactional_emails' ) );

		if ( $this->is_request( 'frontend' ) ) {
			add_action( 'init', array( 'MBH_Shortcodes', 'init' ) );
		}
	}

	/**
	 * Setup plugin constants
	 *
	 * @access private
	 * @return void
	 */
	private function setup_constants() {
		$upload_dir = wp_upload_dir();

		// Plugin version
		if ( ! defined( 'MBH_VERSION' ) ) {
			define( 'MBH_VERSION', $this->version );
		}

		// Plugin Folder Path
		if ( ! defined( 'MBH_PLUGIN_DIR' ) ) {
			define( 'MBH_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		// Plugin Folder URL
		if ( ! defined( 'MBH_PLUGIN_URL' ) ) {
			define( 'MBH_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}

		// Plugin Root File
		if ( ! defined( 'MBH_PLUGIN_FILE' ) ) {
			define( 'MBH_PLUGIN_FILE', __FILE__ );
		}

		// Plugin Basename
		if ( ! defined( 'MBH_PLUGIN_BASENAME' ) ) {
			define( 'MBH_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		}

		// Make sure CAL_GREGORIAN is defined
		if ( ! defined( 'CAL_GREGORIAN' ) ) {
			define( 'CAL_GREGORIAN', 1 );
		}

		// Log File Folder
		if ( ! defined( 'MBH_LOG_DIR' ) ) {
			define( 'MBH_LOG_DIR', $upload_dir[ 'basedir' ] . '/mbh-logs/' );
		}

		// Log File Folder
		if ( ! defined( 'MBH_SESSION_CACHE_GROUP' ) ) {
			define( 'MBH_SESSION_CACHE_GROUP', 'mbh_session_id' );
		}
	}

	/**
	 * What type of request is this?
	 * string $type ajax, frontend or admin
	 * @return bool
	 */
	private function is_request( $type ) {
		switch ( $type ) {
			case 'admin' :
				return is_admin();
			case 'ajax' :
				return defined( 'DOING_AJAX' );
			case 'cron' :
				return defined( 'DOING_CRON' );
			case 'frontend' :
				return ( ! is_admin() || defined( 'DOING_AJAX' ) ) && ! defined( 'DOING_CRON' );
		}
	}

	/**
	 * Include required files used in admin and on the frontend.
	 *
	 * @access private
	 * @return void
	 */
	private function includes() {
		include_once MBH_PLUGIN_DIR . 'includes/class-mbh-install.php';
		include_once MBH_PLUGIN_DIR . 'includes/class-mbh-formatting-helper.php';
		include_once MBH_PLUGIN_DIR . 'includes/class-mbh-room.php';
		include_once MBH_PLUGIN_DIR . 'includes/class-mbh-room-variation.php';
		include_once MBH_PLUGIN_DIR . 'includes/class-mbh-reservation.php';
		include_once MBH_PLUGIN_DIR . 'includes/class-mbh-comments.php';
		include_once MBH_PLUGIN_DIR . 'includes/class-mbh-booking.php';
		include_once MBH_PLUGIN_DIR . 'includes/class-mbh-log.php';
		include_once MBH_PLUGIN_DIR . 'includes/gateways/abstract-mbh-payment-gateway.php';
		include_once MBH_PLUGIN_DIR . 'includes/class-mbh-payment-gateways.php';
		include_once MBH_PLUGIN_DIR . 'includes/class-mbh-emails.php';
		include_once MBH_PLUGIN_DIR . 'includes/class-mbh-ajax.php';
		include_once MBH_PLUGIN_DIR . 'includes/mbh-core-functions.php';
		include_once MBH_PLUGIN_DIR . 'includes/mbh-tax-functions.php';
		include_once MBH_PLUGIN_DIR . 'includes/mbh-widget-functions.php';
		include_once MBH_PLUGIN_DIR . 'includes/mbh-booking-functions.php';
		include_once MBH_PLUGIN_DIR . 'includes/privacy/class-mbh-privacy.php';

		if ( is_admin() ) {
			include_once MBH_PLUGIN_DIR . 'includes/admin/class-mbh-admin.php';
			//include_once MBH_PLUGIN_DIR . 'includes/admin/license-manager/class-MBH-admin-license-manager.php';
		}

		if ( $this->is_request( 'frontend' ) ) {
			$this->frontend_includes();
		}

		if ( $this->is_request( 'frontend' ) || $this->is_request( 'cron' ) ) {
			include_once MBH_PLUGIN_DIR . 'includes/class-mbh-session.php';
		}

		$this->api   = include( 'includes/class-mbh-api.php' );
		$this->query = include( 'includes/class-mbh-query.php' );

		include_once MBH_PLUGIN_DIR . 'includes/class-mbh-post-types.php';
		include_once MBH_PLUGIN_DIR . 'includes/mbh-misc-functions.php';
		include_once MBH_PLUGIN_DIR . 'includes/mbh-page-functions.php';
		include_once MBH_PLUGIN_DIR . 'includes/class-mbh-info.php';
		include_once MBH_PLUGIN_DIR . 'includes/class-mbh-cache.php';
	}

	/**
	 * Include required frontend files.
	 */
	public function frontend_includes() {
		include_once MBH_PLUGIN_DIR . 'includes/mbh-session-functions.php';
		include_once MBH_PLUGIN_DIR . 'includes/mbh-cart-functions.php';
		include_once MBH_PLUGIN_DIR . 'includes/mbh-notice-functions.php';
		include_once MBH_PLUGIN_DIR . 'includes/mbh-template-hooks.php';
		include_once MBH_PLUGIN_DIR . 'includes/class-mbh-template-loader.php';
		include_once MBH_PLUGIN_DIR . 'includes/class-mbh-frontend-scripts.php';
		include_once MBH_PLUGIN_DIR . 'includes/class-mbh-form-functions.php';
		include_once MBH_PLUGIN_DIR . 'includes/class-mbh-cart.php';
		include_once MBH_PLUGIN_DIR . 'includes/shortcodes/class-mbh-shortcodes.php';
		include_once MBH_PLUGIN_DIR . 'includes/class-mbh-https.php';
		include_once MBH_PLUGIN_DIR . 'includes/theme-support/mbh-theme-support-functions.php';
	}

	/**
	 * Include Template Functions.
	 */
	public function template_functions() {
		include_once MBH_PLUGIN_DIR . 'includes/mbh-template-functions.php';
	}

	/**
	 * Init mb_hms when WordPress initialises.
	 *
	 * @access public
	 * @return void
	 */
	public function init() {
		// Before init action
		do_action( 'before_mb_hms_init' );

		// Set up localisation
		$this->load_textdomain();

		// Classes/actions loaded for the frontend and for ajax requests
		if ( $this->is_request( 'frontend' ) ) {
			$this->cart = new MBH_Cart();
		}

		// Session class, handles session data for users
		if ( $this->is_request( 'frontend' ) || $this->is_request( 'cron' ) ) {
			$this->session  = new MBH_Session();
		}

		// Init action
		do_action( 'mb_hms_init' );
	}

	/**
	 * Loads the plugin language files
	 *
	 * @access public
	 * @return void
	 */
	public function load_textdomain() {
		// Set filter for plugin's languages directory
		$mb_hms_lang_dir = dirname( MBH_PLUGIN_BASENAME ) . '/languages/';
		$mb_hms_lang_dir = apply_filters( 'mb_hms_languages_directory', $mb_hms_lang_dir );

		// Traditional WordPress plugin locale filter
		$locale = apply_filters( 'plugin_locale', get_locale(), 'wp-mb_hms' );
		$mofile = sprintf( '%1$s-%2$s.mo', 'wp-mb_hms', $locale );

		// Setup paths to current locale file
		$mofile_local  = $mb_hms_lang_dir . $mofile;
		$mofile_global = WP_LANG_DIR . '/wp-mb_hms/' . $mofile;

		if ( file_exists( $mofile_global ) ) {
			// Look in global /wp-content/languages/wp-mb_hms folder
			load_textdomain( 'wp-mb_hms', $mofile_global );
		} elseif ( file_exists( $mofile_local ) ) {
			// Look in local /wp-content/plugins/wp-mb_hms/languages/ folder
			load_textdomain( 'wp-mb_hms', $mofile_local );
		} else {
			// Load the default language files
			load_plugin_textdomain( 'wp-mb_hms', false, $mb_hms_lang_dir );
		}
	}

	/**
	 * Setup image sizes.
	 */
	public function setup_thumbnails() {
		$this->add_thumbnail_support();
		$this->add_image_sizes();
	}

	/**
	 * Ensure post thumbnail support is turned on
	 */
	private function add_thumbnail_support() {
		if ( ! current_theme_supports( 'post-thumbnails' ) ) {
			add_theme_support( 'post-thumbnails' );
		}
		add_post_type_support( 'room', 'thumbnail' );
	}

	/**
	 * Add MBH Image sizes to WP
	 */
	private function add_image_sizes() {
		$room_thumbnail = MBH_get_image_size( 'room_thumbnail' );
		$room_catalog   = MBH_get_image_size( 'room_catalog' );
		$room_single    = MBH_get_image_size( 'room_single' );

		add_image_size( 'room_thumbnail', $room_thumbnail[ 'width' ], $room_thumbnail[ 'height' ], $room_thumbnail[ 'crop' ] );
		add_image_size( 'room_catalog', $room_catalog[ 'width' ], $room_catalog[ 'height' ], $room_catalog[ 'crop' ] );
		add_image_size( 'room_single', $room_single[ 'width' ], $room_single[ 'height' ], $room_single[ 'crop' ] );
	}

	/**
	 * Get the plugin url.
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugins_url( '/', __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}

	/**
	 * Get the template path.
	 * @return string
	 */
	public function template_path() {
		return apply_filters( 'mb_hms_template_path', 'mb_hms/' );
	}

	/**
	 * Get Ajax URL.
	 * @return string
	 */
	public function ajax_url() {
		return admin_url( 'admin-ajax.php', 'relative' );
	}

	/**
	 * Return the MBH API URL for a given request (used by gateways)
	 *
	 * @param string $request
	 * @param mixed $ssl (default: null)
	 * @return string
	 */
	public function api_request_url( $request, $ssl = null ) {
		if ( is_null( $ssl ) ) {
			$scheme = parse_url( home_url(), PHP_URL_SCHEME );
		} elseif ( $ssl ) {
			$scheme = 'https';
		} else {
			$scheme = 'http';
		}

		$api_request_url = add_query_arg( 'mbh-api', $request, trailingslashit( home_url( '', $scheme ) ) );

		return esc_url_raw( $api_request_url );
	}

	/**
	 * Get Booking Class.
	 * @return MBH_Booking
	 */
	public function booking() {
		return MBH_Booking::instance();
	}

	/**
	 * Get gateways class
	 * @return MBH_Payment_Gateways
	 */
	public function payment_gateways() {
		return MBH_Payment_Gateways::instance();
	}

	/**
	 * Email Class.
	 * @return MBH_Emails
	 */
	public function mailer() {
		return MBH_Emails::instance();
	}
}

endif;

if ( ! function_exists( 'MBH' ) ) :
	/**
	 * Returns the main instance of MBH to prevent the need to use globals.
	 *
	 * @return mb_hms
	 */
	function MBH() {
		return mb_hms::instance();
	}
endif;

// Get MBH Running
MBH();
