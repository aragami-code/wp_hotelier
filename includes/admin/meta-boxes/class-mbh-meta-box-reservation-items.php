<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Meta_Box_Reservation_Items' ) ) :

/**
 * HTL_Meta_Box_Reservation_Items Class
 */
class MBH_Meta_Box_Reservation_Items {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		global $post, $thereservation;

		if ( ! is_object( $thereservation ) ) {
			$thereservation = mbh_get_reservation( $post->ID );
		}

		$reservation = $thereservation;

		include( 'views/html-meta-box-reservation-items.php' );
	}
}

endif;
