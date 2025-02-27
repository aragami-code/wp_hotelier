<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Shortcode_Room_List' ) ) :

/**
 * 
 _Shortcode_Room_List Class
 */
class MBH_Shortcode_Room_List {

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
		self::room_list( $atts );
	}

	/**
	 * Show the room list form
	 */
	private static function room_list( $atts ) {
		$checkin  = MBH()->session->get( 'checkin' ) ? MBH()->session->get( 'checkin' ) :  null;
		$checkout = MBH()->session->get( 'checkout' ) ? MBH()->session->get( 'checkout' ) : null;

		// Check if we have valid dates before to run the query
		if ( ! MBH_Formatting_Helper::is_valid_checkin_checkout( $checkin, $checkout ) ) {
			mbh_get_template( 'room-list/no-rooms-available.php' );
		} else {
			$room_id           = isset( $_GET[ 'room-id' ] ) ? absint( $_GET[ 'room-id' ] ) : false;
			$room_id_available = false;

			// A room ID was passed to the query so check if it is available
			if ( $room_id ) {
				$_room = mbh_get_room( $room_id );

				if ( $_room->exists() && $_room->is_available( $checkin, $checkout ) ) {
					$room_id_available = true;
				}
			}

			// Get available rooms
			$rooms = mbh_get_listing_rooms_query( $checkin, $checkout, $room_id );

			// Pass args to the template
			$room_list_args = array(
				'rooms'             => $rooms,
				'room_id'           => $room_id,
				'room_id_available' => $room_id_available
			);

			mbh_get_template( 'room-list/form-room-list.php', $room_list_args );
		}
	}
}

endif;
