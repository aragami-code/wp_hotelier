<?php
/**
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Prevent password protected rooms being added to the cart
 *
 * @param  bool $passed
 * @param  int $room_id
 * @return bool
 */
function mbh_protected_room_add_to_cart( $passed, $room_id ) {
	if ( post_password_required( $room_id ) ) {
		$passed = false;
		mbh_add_notice( esc_html__( 'This room is protected and cannot be reserved.', 'wp-mb_hms' ), 'error' );
	}
	return $passed;
}
add_filter( 'mb_hms_add_to_cart_validation', 'mbh_protected_room_add_to_cart', 10, 2 );

/**
 * Clear cart after payment.
 *
 * @access public
 */
function mbh_clear_cart_after_payment() {
	global $wp;

	if ( ! empty( $wp->query_vars[ 'reservation-received' ] ) ) {

		$reservation_id  = absint( $wp->query_vars[ 'reservation-received' ] );
		$reservation_key = isset( $_GET[ 'key' ] ) ? sanitize_text_field( $_GET[ 'key' ] ) : '';

		if ( $reservation_id > 0 ) {
			$reservation = mbh_get_reservation( $reservation_id );

			if ( $reservation->reservation_key === $reservation_key ) {
				MBH()->cart->empty_cart();
			}
		}
	}

	if ( MBH()->session->get( 'reservation_awaiting_payment' ) > 0 ) {
		$reservation = mbh_get_reservation( MBH()->session->get( 'reservation_awaiting_payment' ) );

		if ( $reservation && $reservation->id > 0 ) {
			// If the reservation has not failed, or is not pending, the reservation must have gone through
			if ( ! $reservation->has_status( array( 'failed', 'pending', 'cancelled', 'refunded' ) ) ) {
				MBH()->cart->empty_cart();
			}
		}
	}
}
add_action( 'get_header', 'mbh_clear_cart_after_payment' );

/**
 * Clears the cart session when called.
 */
function mbh_empty_cart() {
	if ( ! isset( MBH()->cart ) || MBH()->cart == '' ) {
		MBH()->cart = new MBH_Cart();
	}
	MBH()->cart->empty_cart();
}

/**
 * Get the formatted total.
 *
 * @access public
 * @return string
 */
function mbh_cart_formatted_total() {
	$total = mbh_price( mbh_convert_to_cents( MBH()->cart->get_total() ) );

	echo $total;
}

/**
 * Get the formatted subtotal.
 *
 * @access public
 * @return string
 */
function mbh_cart_formatted_subtotal() {
	$subtotal = mbh_price( mbh_convert_to_cents( MBH()->cart->get_subtotal() ) );

	echo $subtotal;
}

/**
 * Get the formatted subtotal.
 *
 * @access public
 * @return string
 */
function mbh_cart_formatted_tax_total() {
	$tax_total = mbh_price( mbh_convert_to_cents( MBH()->cart->get_tax_total() ) );

	echo $tax_total;
}

/**
 * Get the formatted required deposit.
 *
 * @access public
 * @return string
 */
function mbh_cart_formatted_required_deposit() {
	$required_deposit = mbh_price( mbh_convert_to_cents( MBH()->cart->get_required_deposit() ) );

	echo $required_deposit;
}

/**
 * Output the price breakdown table.
 *
 * @access public
 * @param string $checkin
 * @param string $checkout
 * @param int $room_id
 * @param int $rate_id
 * @param int $qty
 * @return string
 */
function mbh_cart_price_breakdown( $checkin, $checkout, $room_id, $rate_id, $qty ) {

	$breakdown = mbh_get_room_price_breakdown( $checkin, $checkout, $room_id, $rate_id, $qty );

	$html = '<table class="table table--price-breakdown price-breakdown" id="' . esc_attr( mbh_generate_item_key( $room_id, $rate_id ) ) . '">';
	$html .= '<thead><tr class="price-breakdown__row price-breakdown__row--heading"><th colspan="2" class="price-breakdown__day price-breakdown__day--heading">' . esc_html__( 'Day', 'wp-mb_hms' ) . '</th><th class="price-breakdown__cost price-breakdown__cost--heading">' . esc_html__( 'Cost', 'wp-mb_hms' ) . '</th></tr><tbody>';

	foreach ( $breakdown as $day => $price ) {
		$html .= '<tr class="price-breakdown__row price-breakdown__row--body">';
		$html .= '<td colspan="2" class="price-breakdown__day price-breakdown__day--body">' . esc_html( date_i18n( get_option( 'date_format' ), strtotime( $day ) ) ) . '</td>';
		$html .= '<td class="price-breakdown__cost price-breakdown__cost--body">' . mbh_price( mbh_convert_to_cents( $price ) ) . '</td>';
		$html .= '</tr>';
	}

	$html .= '</tbody></table>';

	echo apply_filters( 'mb_hms_room_price_breakdown_table', $html, $checkin, $checkout, $room_id, $rate_id );
}
