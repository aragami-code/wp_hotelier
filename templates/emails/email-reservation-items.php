<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

foreach ( $items as $item_id => $item ) : ?>

	<tr>
		<td style="text-align:left;font-size:14px;line-height:20px;padding-top:10px;padding-bottom:10px;padding-left:0;padding-right:0;font-family:Helvetica,Arial;">
			<?php

			echo esc_html( $item[ 'name' ] );

			if ( isset( $item[ 'rate_name' ] ) ) {
				// Rate
				echo '<br><small style="text-align:left;font-size:12px;line-height:16px;color:#999999;font-family:Helvetica,Arial;">' . sprintf( esc_html__( 'Rate: %s', 'wp-mb_hms' ), mbh_get_formatted_room_rate( $item[ 'rate_name' ] ) ) . '</small>';
			}

			if ( ! $item[ 'is_cancellable' ] ) {
				// Non cancellable info
				echo '<br><small style="text-align:left;font-size:12px;line-height:16px;color:red;font-family:Helvetica,Arial;">' . esc_html__( 'Non-refundable', 'wp-mb_hms' ) . '</small>';
			}

			if ( mbh_get_option( 'booking_number_of_guests_selection', true ) ) {
				// Adults/children info

				$adults   = isset( $item[ 'adults' ] ) ? maybe_unserialize( $item[ 'adults' ] ) : false;
				$children = isset( $item[ 'children' ] ) ? maybe_unserialize( $item[ 'children' ] ) : false;

				for ( $q = 0; $q < $item[ 'qty' ]; $q++ ) {
					$line_adults   = isset( $adults[ $q ] ) && ( $adults[ $q ] > 0 ) ? $adults[ $q ] : false;
					$line_children = isset( $children[ $q ] ) && ( $children[ $q ] > 0 ) ? $children[ $q ] : false;

					if ( $line_adults || $line_children ) {
						if ( $item[ 'qty' ] > 1 ) {
							echo '<br><small style="text-align:left;font-size:12px;line-height:16px;font-family:Helvetica,Arial;">' . sprintf( esc_html__( 'Number of guests (Room %d):', 'wp-mb_hms' ), $q + 1 ) . '</small>';
						} else {
							echo '<br><small style="text-align:left;font-size:12px;line-height:16px;font-family:Helvetica,Arial;">' . esc_html__( 'Number of guests:', 'wp-mb_hms' ) . '</small>';
						}

						if ( $line_adults ) {
							echo '<small style="text-align:left;font-size:12px;line-height:16px;font-family:Helvetica,Arial;"> ' . sprintf( _n( '%s Adult', '%s Adults', $line_adults, 'wp-mb_hms' ), $line_adults ) . '</small>';
						}

						if ( $line_children ) {
							echo '<small style="text-align:left;font-size:12px;line-height:16px;font-family:Helvetica,Arial;"> ' . sprintf( esc_html__( '%d Children', 'wp-mb_hms' ), $line_children ) . '</small>';
						}
					}
				}
			}

			// Allow other plugins to add additional room information here
			do_action( 'mb_hms_reservation_item_meta', $item_id, $item, $reservation );
			?>
		</td>
		<td style="text-align:left;font-size:14px;line-height:20px;color:#999999;padding-top:10px;padding-bottom:10px;padding-left:0;padding-right:0;font-family:Helvetica,Arial;"><?php echo esc_html( $item[ 'qty' ] ); ?></td>
		<td style="text-align:left;font-size:14px;line-height:20px;color:#999999;padding-top:10px;padding-bottom:10px;padding-left:0;padding-right:0;font-family:Helvetica,Arial;"><?php echo $reservation->get_formatted_line_total( $item ); ?></td>
	</tr>

<?php endforeach; ?>
