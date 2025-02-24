<?php
/**
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Check default checkin and checkout when the page loads
 * and set the default values
 */
function mbh_check_default_dates() {
	if ( ! is_admin() ) {
		if ( ! headers_sent() && did_action( 'wp_loaded' ) ) {

			// Check if we have checkin/checkout dates in session. If not, load default dates.
			if ( is_null( MBH()->session->get( 'checkin' ) ) || is_null( MBH()->session->get( 'checkout' ) ) ) {
				do_action( 'mb_hms_set_cookies', true );

				$dates = mbh_get_default_dates();

				MBH()->session->set( 'checkin', $dates[ 'checkin' ] );
				MBH()->session->set( 'checkout', $dates[ 'checkout' ] );

			// Check if the checkin date is greater than today.
			// If not, load default dates.
			} else if ( ! is_null( MBH()->session->get( 'checkin' ) ) ) {
				$today    = new DateTime( current_time( 'Y-m-d' ) );
				$checkin  = new DateTime( MBH()->session->get( 'checkin' ) );

				if ( $checkin < $today ) {
					$dates = mbh_get_default_dates();

					MBH()->session->set( 'checkin', $dates[ 'checkin' ] );
					MBH()->session->set( 'checkout', $dates[ 'checkout' ] );
				}
			}
		}
	}
}
add_action( 'wp_loaded', 'mbh_check_default_dates' );

/**
 * Get valid default checkin/checkout dates
 */
function mbh_get_default_dates() {
	$dates = array();

	// Arrival date must be "XX" days from current date (default 0).
	$from = mbh_get_option( 'booking_arrival_date', 0 );

	// Get minimum number of nights a guest can book
	$minimum_nights = apply_filters( 'mb_hms_booking_minimum_nights', mbh_get_option( 'booking_minimum_nights', 1 ) );

	// Set default checkout
	$to = $from + $minimum_nights;

	$dates[ 'checkin' ]  = date( 'Y-m-d', strtotime( "+$from days" ) );
	$dates[ 'checkout' ] = date( 'Y-m-d', strtotime( "+$to days" ) );

	return apply_filters( 'mb_hms_get_default_dates', $dates );
}
