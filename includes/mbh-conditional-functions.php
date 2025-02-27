<?php
/**
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! function_exists( 'is_mb_hms' ) ) {
	/**
	 * Returns true if on a page which uses mb_hms templates (booking and listing are standard pages with shortcodes and thus are not included).
	 * @return bool
	 */
	function is_mb_hms() {
		return apply_filters( 'is_mb_hms', ( is_room_category() || is_room_archive() || is_room() ) ? true : false );
	}
}

if ( ! function_exists( 'is_ajax' ) ) {

	/**
	 * Returns true when the page is loaded via ajax.
	 * @return bool
	 */
	function is_ajax() {
		return defined( 'DOING_AJAX' );
	}
}

if ( ! function_exists( 'is_booking' ) ) {

	/**
	 * Returns true when viewing the booking page.
	 * @return bool
	 */
	function is_booking() {
		return is_page( mbh_get_page_id( 'booking' ) ) || apply_filters( 'mb_hms_is_booking', false ) ? true : false;
	}
}

if ( ! function_exists( 'is_listing' ) ) {

	/**
	 * Returns true when viewing the listing page (room_list form).
	 * @return bool
	 */
	function is_listing() {
		return is_page( mbh_get_page_id( 'listing' ) ) || apply_filters( 'mb_hms_is_listing', false ) ? true : false;
	}
}

if ( ! function_exists( 'is_reservation_received_page' ) ) {

	/**
	* Returns true when viewing the reservation received page.
	* @return bool
	*/
	function is_reservation_received_page() {
		global $wp;

		return ( is_page( mbh_get_page_id( 'booking' ) || apply_filters( 'mb_hms_is_booking', false ) ) && isset( $wp->query_vars[ 'reservation-received' ] ) ) ? true : false;
	}
}

if ( ! function_exists( 'is_pay_reservation_page' ) ) {

	/**
	* Returns true when viewing the pay reservation page.
	* @return bool
	*/
	function is_pay_reservation_page() {
		global $wp;

		return ( is_page( mbh_get_page_id( 'booking' ) || apply_filters( 'mb_hms_is_booking', false ) ) && isset( $wp->query_vars[ 'pay-reservation' ] ) ) ? true : false;
	}
}

if ( ! function_exists( 'is_booking_page' ) ) {

	/**
	* Returns true when viewing a booking page (listing and booking).
	* The pay reservation and received page are not included.
	* @return bool
	*/
	function is_booking_page() {
		return ( ( is_booking() || is_listing() ) && ! is_reservation_received_page() && ! is_pay_reservation_page() ) ? true : false;
	}
}

if ( ! function_exists( 'is_room' ) ) {

	/**
	 * Returns true when viewing a single room.
	 * @return bool
	 */
	function is_room() {
		return is_singular( array( 'room' ) );
	}
}

if ( ! function_exists( 'is_room_archive' ) ) {

	/**
	 * Returns true when viewing the room archive.
	 * @return bool
	 */
	function is_room_archive() {
		return is_post_type_archive( 'room' );
	}
}

if ( ! function_exists( 'is_room_category' ) ) {

	/**
	 * Returns true when viewing a room category.
	 * @param  string $term (default: '') The term slug your checking for. Leave blank to return true on any.
	 * @return bool
	 */
	function is_room_category( $term = '' ) {
		return is_tax( 'room_cat', $term );
	}
}

if ( ! function_exists( 'is_mbh_endpoint_url' ) ) {

	/**
	 * Check if an endpoint is showing
	 * @param  string $endpoint
	 * @return bool
	 */
	function is_mbh_endpoint_url( $endpoint = false ) {
		global $wp;

		$mbh_endpoints = MBH()->query->get_query_vars();

		if ( $endpoint !== false ) {
			if ( ! isset( $mbh_endpoints[ $endpoint ] ) ) {
				return false;
			} else {
				$endpoint_var = $mbh_endpoints[ $endpoint ];
			}

			return isset( $wp->query_vars[ $endpoint_var ] );
		} else {
			foreach ( $mbh_endpoints as $key => $value ) {
				if ( isset( $wp->query_vars[ $key ] ) ) {
					return true;
				}
			}

			return false;
		}
	}
}
