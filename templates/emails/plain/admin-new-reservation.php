<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo "= " . esc_html( $email_heading ) . " =\n\n";

echo sprintf( esc_html__( 'You have received a reservation from %s.', 'wp-mb_hms' ), $reservation->get_formatted_guest_full_name() ) . "\n\n";

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

echo "\n" . sprintf( esc_html__( 'View reservation: %s', 'wp-mb_hms'), admin_url( 'post.php?post=' . $reservation->id . '&action=edit' ) ) . "\n";

echo "\n=====================================================================\n\n";

do_action( 'mb_hms_email_guest_details', $reservation, $sent_to_admin, $plain_text );

do_action( 'mb_hms_email_reservation_meta', $reservation, $sent_to_admin, $plain_text );

echo "\n=====================================================================\n\n";

echo apply_filters( 'mb_hms_email_footer_text', mbh_get_option( 'emails_footer_text', 'Powered by MAGE BAZAR' ) );
