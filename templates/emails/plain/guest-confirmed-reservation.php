<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo "= " . esc_html( $email_heading ) . " =\n\n";

echo sprintf( esc_html__( 'Hello %s, your reservation has been confirmed. Your reservation details are shown below for your reference.', 'wp-mb_hms' ), $reservation->get_formatted_guest_full_name() ) . "\n\n";

echo "=====================================================================\n\n";

do_action( 'mb_hms_email_hotel_info', $plain_text );

echo "==========\n\n";

echo sprintf( esc_html__( 'Check-in: %s', 'wp-mb_hms' ), $reservation->get_formatted_checkin() ) . ' (' . MBH_Info::get_hotel_checkin() . ')' . "\n";
echo sprintf( esc_html__( 'Check-out: %s', 'wp-mb_hms' ), $reservation->get_formatted_checkout() ) . ' (' . MBH_Info::get_hotel_checkout() . ')' . "\n";
echo sprintf( esc_html__( 'Nights: %s', 'wp-mb_hms' ), $reservation->get_nights() ) . "\n\n";

echo "=====================================================================\n\n";

echo strtoupper( sprintf( esc_html__( 'Reservation number: %s', 'wp-mb_hms' ), $reservation->get_reservation_number() ) ) . "\n";
echo date_i18n( get_option( 'date_format' ), strtotime( $reservation->reservation_date ) ) . "\n";

echo "\n" . $reservation->email_reservation_items_table( true );

echo "==========\n\n";

if ( $totals = $reservation->get_reservation_totals() ) {
	foreach ( $totals as $total ) {
		echo esc_html( $total[ 'label' ] ) . " " . wp_kses_post( $total[ 'value' ] ) . "\n";
	}
}

if ( ! $reservation->can_be_cancelled() ) {
	echo "\n=====================================================================\n\n";

	esc_html_e( 'This reservation includes a non-cancellable and non-refundable room. You will be charged the total price if you cancel your booking.', 'wp-mb_hms' ) . "\n\n";
}

echo "\n=====================================================================\n\n";

echo sprintf( esc_html__( 'View reservation: %s', 'wp-mb_hms' ), esc_url( $reservation->get_booking_received_url() ) ) . "\n";

echo "\n=====================================================================\n\n";

do_action( 'mb_hms_email_reservation_instructions', $reservation, $sent_to_admin, $plain_text );

do_action( 'mb_hms_email_guest_details', $reservation, $sent_to_admin, $plain_text );

do_action( 'mb_hms_email_reservation_meta', $reservation, $sent_to_admin, $plain_text );

echo "\n=====================================================================\n\n";

echo apply_filters( 'mb_hms_email_footer_text', mbh_get_option( 'emails_footer_text', 'Powered by MAGE BAZAR' ) );
