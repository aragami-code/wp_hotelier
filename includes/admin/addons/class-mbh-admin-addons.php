<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Admin_Addons' ) ) :

/**
 * MBH_Admin_Addons Class
 *
 * Creates the Extensions & Themes page.
 */
class MBH_Admin_Addons {
	/**
	 * Get mb_hms addons (extensions & themes)
	 *
	 * @return array of objects
	 */
	public static function get_addons() {
		if ( false === ( $sections = get_transient( 'mb_hms_addons' ) ) ) {
			$raw_addons = wp_safe_remote_get( 'https://assets.wpmb_hms.com/api/addons.json', array( 'user-agent' => 'mb_hms Addons Page' ) );
			if ( ! is_wp_error( $raw_addons ) ) {
				$sections = json_decode( wp_remote_retrieve_body( $raw_addons ) );

				if ( $sections ) {
					set_transient( 'mb_hms_addons', $sections, WEEK_IN_SECONDS );
				}
			}
		}

		$addons = array();

		if ( $sections ) {
			$addons = $sections;
		}

		return $addons;
	}

	/**
	 * Handles the display of the Reservations page in admin.
	 */
	public static function output() {
		$addons = self::get_addons();

		include_once 'views/html-admin-page-addons.php';
	}

}

endif;
