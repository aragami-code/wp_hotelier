<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Shortcode_Booking' ) ) :

/**
 * _Shortcode_Booking Class
 */
class MBH_Shortcode_Booking {

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
		global $wp;

		// Check cart class is loaded or abort
		if ( is_null( MBH()->cart ) ) {
			return;
		}

		// Handle booking actions
		if ( ! empty( $wp->query_vars[ 'pay-reservation' ] ) ) {

			self::pay_reservation( $wp->query_vars[ 'pay-reservation' ] );

		} elseif ( isset( $wp->query_vars[ 'reservation-received' ] ) ) {

			self::reservation_received( $wp->query_vars[ 'reservation-received' ] );

		} else {

			self::booking();

		}
	}

	/**
	 * Show the pay page.
	 *
	 * @param int $reservation_id
	 */
	private static function pay_reservation( $reservation_id ) {

		do_action( 'before_mb_hms_pay' );

		mbh_print_notices();

		$reservation_id = absint( $reservation_id );

		// Handle payment
		if ( isset( $_GET[ 'pay_for_reservation' ] ) && isset( $_GET[ 'key' ] ) && $reservation_id ) {

			// Pay for existing reservation
			$reservation_key = $_GET[ 'key' ];
			$reservation     = mbh_get_reservation( $reservation_id );

			if ( $reservation->id == $reservation_id && $reservation->reservation_key == $reservation_key ) {

				if ( $reservation->needs_payment() ) {

					$available_gateways = MBH()->payment_gateways->get_available_payment_gateways();

					if ( sizeof( $available_gateways ) ) {
						current( $available_gateways )->set_selected();
					}

					$checkin      = $reservation->get_formatted_checkin();
					$checkout     = $reservation->get_formatted_checkout();
					$pets_message = MBH_Info::get_hotel_pets_message();
					$cards        = MBH_Info::get_hotel_accepted_credit_cards();

					mbh_get_template( 'booking/reservation-details.php', array(
						'reservation'  => $reservation,
						'checkin'      => $checkin,
						'checkout'     => $checkout,
						'pets_message' => $pets_message,
						'cards'        => $cards,
					) );

					mbh_get_template( 'booking/form-pay.php', array(
						'reservation'             => $reservation,
						'available_gateways'      => $available_gateways,
						'reservation_button_text' => apply_filters( 'mb_hms_pay_reservation_button_text', esc_html__( 'Pay deposit', 'wp-mb_hms' ) )
					) );

				} else {
					mbh_add_notice( esc_html__( 'Hi there! Seems that this reservation does not require a deposit. Please contact us if you need assistance.', 'wp-mb_hms' ), 'error' );
				}

			} else {
				mbh_add_notice( esc_html__( 'Sorry, this reservation is invalid and cannot be paid for.', 'wp-mb_hms' ), 'error' );
			}

		} else {
			mbh_add_notice( esc_html__( 'Invalid reservation.', 'wp-mb_hms' ), 'error' );
		}

		mbh_print_notices();

		do_action( 'after_mb_hms_pay' );
	}

	/**
	 * Show the reservation received page.
	 *
	 * @param int $reservation_id
	 */
	private static function reservation_received( $reservation_id = 0 ) {

		mbh_print_notices();

		$reservation = false;

		// Get the reservation
		$reservation_id  = apply_filters( 'mb_hms_received_reservation_id', absint( $reservation_id ) );
		$reservation_key = apply_filters( 'mb_hms_received_reservation_key', empty( $_GET[ 'key' ] ) ? '' : sanitize_text_field( $_GET[ 'key' ] ) );

		if ( $reservation_id > 0 ) {
			$reservation = mbh_get_reservation( $reservation_id );
			if ( $reservation->reservation_key != $reservation_key )
				$reservation = false;
		}

		// Empty awaiting payment session
		MBH()->session->set( 'reservation_awaiting_payment', null );

		mbh_get_template( 'booking/received.php', array( 'reservation' => $reservation ) );
	}

	/**
	 * Show the booking form
	 */
	private static function booking() {
		// Hide booking page when booking_mode is set to 'no-booking'
		if ( mbh_get_option( 'booking_mode' ) != 'no-booking' ) {

			// Show non-cart errors
			mbh_print_notices();

			// Check cart has contents
			if ( MBH()->cart->is_empty() ) {
				return;
			}

			// Check cart contents for errors
			do_action( 'mb_hms_booking_check_rooms_availability' );

			// Calc totals
			MBH()->cart->calculate_totals();

			// Get booking object
			$booking = MBH()->booking();

			if ( empty( $_POST ) && mbh_notice_count( 'error' ) > 0 ) {

				mbh_get_template( 'booking/cart-errors.php', array( 'booking' => $booking ) );

			} else {

				mbh_get_template( 'booking/form-booking.php', array( 'booking' => $booking ) );

			}
		}
	}
}

endif;
