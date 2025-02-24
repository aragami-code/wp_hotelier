<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

foreach ( $items as $item_id => $item ) :

	// Title
	echo esc_html( $item[ 'name' ] );

	if ( isset( $item[ 'rate_name' ] ) ) {
		// Rate
		echo "\n" . sprintf( esc_html__( 'Rate: %s', 'wp-mb_hms' ), mbh_get_formatted_room_rate( $item[ 'rate_name' ] ) );
	}

	if ( ! $item[ 'is_cancellable' ] ) {
		// Non cancellable info
		echo "\n" .  esc_html__( 'Non-refundable', 'wp-mb_hms' );
	}

	// Quantity
	echo "\n" . sprintf( esc_html__( 'Quantity: %s', 'wp-mb_hms' ), $item[ 'qty' ] );

	// Cost
	echo "\n" . sprintf( esc_html__( 'Cost: %s', 'wp-mb_hms' ), $reservation->get_formatted_line_total( $item ) );

	if ( mbh_get_option( 'booking_number_of_guests_selection', true ) ) {
		// Adults/children info

		$adults   = isset( $item[ 'adults' ] ) ? maybe_unserialize( $item[ 'adults' ] ) : false;
		$children = isset( $item[ 'children' ] ) ? maybe_unserialize( $item[ 'children' ] ) : false;

		for ( $q = 0; $q < $item[ 'qty' ]; $q++ ) {
			$line_adults   = isset( $adults[ $q ] ) && ( $adults[ $q ] > 0 ) ? $adults[ $q ] : false;
			$line_children = isset( $children[ $q ] ) && ( $children[ $q ] > 0 ) ? $children[ $q ] : false;

			if ( $line_adults || $line_children ) {
				if ( $item[ 'qty' ] > 1 ) {
					echo "\n" . sprintf( esc_html__( 'Number of guests (Room %d):', 'wp-mb_hms' ), $q + 1 );
				} else {
					echo "\n" . esc_html__( 'Number of guests:', 'wp-mb_hms' );
				}

				if ( $line_adults ) {
					echo " " . sprintf( _n( '%s Adult', '%s Adults', $line_adults, 'wp-mb_hms' ), $line_adults );
				}

				if ( $line_children ) {
					echo " " . sprintf( esc_html__( '%d Children', 'wp-mb_hms' ), $line_children );
				}
			}
		}
	}

	// Allow other plugins to add additional room information here
	do_action( 'mb_hms_reservation_item_meta', $item_id, $item, $reservation );

	echo "\n\n";

endforeach;
