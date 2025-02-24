<?php
/**
 * 
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Admin' ) ) :

/**
 * MBH_Admin Class
 */
class MBH_Admin {
	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'includes' ) );
		add_filter( 'admin_footer_text', array( $this, 'rate_us_text' ), 1 );
	}

	/**
	 * Include any classes we need within admin.
	 */
	public function includes() {
		include_once( 'settings/class-mbh-admin-settings.php' );
		include_once( 'class-mbh-admin-functions.php' );
		include_once( 'meta-boxes/class-mbh-admin-meta-boxes-helper.php' );
		include_once( 'class-mbh-admin-post-types.php' );
		include_once( 'class-mbh-admin-menus.php' );
		include_once( 'class-mbh-admin-scripts.php' );
		include_once( 'class-mbh-admin-notices.php' );
		include_once( 'new-reservation/class-mbh-admin-new-reservation.php' );
		include_once( 'calendar/class-mbh-admin-calendar.php' );
		//include_once( 'settings/class-mbh-admin-logs.php' );
		//include_once( 'addons/class-mbh-admin-addons.php' );
	}

	/**
	 * Add rating text to the admin dashboard.
	 */
	public function rate_us_text( $footer_text ) {
		if ( ! current_user_can( 'manage_mb_hms' ) ) {
			return $footer_text;
		}

		$screen = get_current_screen();

		if ( class_exists( 'MBH_Admin_Functions' ) && in_array( $screen->id, MBH_Admin_Functions::get_screen_ids() ) ) {
			
		}

		return $footer_text;
	}
}

endif;

return new MBH_Admin();
