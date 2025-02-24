<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Shortcode_Datepicker' ) ) :

/**
 * _Shortcode_Datepicker Class
 */
class MBH_Shortcode_Datepicker {

	/**
	 * Get the shortcode content.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function get( $atts ) {
		return MBH_Shortcodes::shortcode_wrapper( array( __CLASS__, 'output' ), $atts );
	}

	/**
	 * Output the shortcode.
	 *
	 * @param array $atts
	 */
	public static function output( $atts ) {
		self::datepicker( $atts );
	}

	/**
	 * Show the datepicker form
	 */
	private static function datepicker( $atts ) {

		$checkin  = MBH()->session->get( 'checkin' ) ? MBH()->session->get( 'checkin' ) :  null;
		$checkout = MBH()->session->get( 'checkout' ) ? MBH()->session->get( 'checkout' ) : null;

		// Enqueue the datepicker scripts
		wp_enqueue_script( 'mb_hms-init-datepicker' );

		mbh_get_template( 'global/datepicker.php', array( 'checkin' => $checkin, 'checkout' => $checkout ) );
	}
}

endif;
