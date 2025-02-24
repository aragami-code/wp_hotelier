<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="reservation-table" class="booking__section booking__section--reservation-table">

	<header class="section-header">
		<h3 class="section-header__title"><?php esc_html_e( 'Your reservation', 'wp-mb_hms' ); ?></h3>
	</header>

	<?php do_action( 'mb_hms_booking_before_booking_table' ); ?>

	<table class="table table--reservation-table reservation-table mb_hms-table">
		<thead class="reservation-table__heading">
			<tr class="reservation-table__row reservation-table__row--heading">
				<th class="reservation-table__room-name reservation-table__room-name--heading"><?php esc_html_e( 'Room', 'wp-mb_hms' ); ?></th>
				<th class="reservation-table__room-qty reservation-table__room-qty--heading"><?php esc_html_e( 'Qty', 'wp-mb_hms' ); ?></th>
				<th class="reservation-table__room-cost reservation-table__room-cost--heading"><?php esc_html_e( 'Cost', 'wp-mb_hms' ); ?></th>
			</tr>
		</thead>
		<tbody class="reservation-table__body">
			<?php
				foreach ( MBH()->cart->get_cart() as $cart_item_key => $cart_item ) :
					$_room    = $cart_item[ 'data' ];
					$_room_id = $cart_item[ 'room_id' ];

					if ( $_room && $_room->exists() && $cart_item[ 'quantity' ] > 0 ) : ?>

						<?php
						$item_key = mbh_generate_item_key( $cart_item[ 'room_id' ], $cart_item[ 'rate_id' ] );
						?>

						<tr class="reservation-table__row reservation-table__row--body">
							<td class="reservation-table__room-name reservation-table__room-name--body">
								<a class="reservation-table__room-link" href="<?php echo esc_url( get_permalink( $_room_id ) ); ?>"><?php echo esc_html( $_room->get_title() ); ?></a>

								<?php if ( $cart_item[ 'rate_name' ] ) : ?>
									<small class="reservation-table__room-rate"><?php printf( esc_html__( 'Rate: %s', 'wp-mb_hms' ), mbh_get_formatted_room_rate( $cart_item[ 'rate_name' ] ) ); ?></small>
								<?php endif; ?>

								<?php if ( ! $cart_item[ 'is_cancellable' ] ) : ?>
									<span class="reservation-table__room-non-cancellable"><?php echo esc_html_e( 'Non-refundable', 'wp-mb_hms' ); ?></span>
								<?php endif; ?>

								<?php
									echo apply_filters( 'mb_hms_cart_item_remove_link', sprintf(
										'<a href="%s" class="reservation-table__room-remove remove button">%s</a>',
										esc_url( mbh_get_cart_remove_url( $cart_item_key ) ),
										esc_html__( 'Remove', 'wp-mb_hms' )
									), $cart_item_key );
								?>

								<?php do_action( 'mb_hms_reservation_table_guests', $_room, $item_key, $cart_item[ 'quantity' ] ); ?>
							</td>

							<td class="reservation-table__room-qty reservation-table__room-qty--body"><?php echo absint( $cart_item[ 'quantity' ] ); ?></td>

							<td class="reservation-table__room-cost reservation-table__room-cost--body">
								<?php echo MBH()->cart->get_room_price( $cart_item[ 'total' ] ); ?>

								<?php if ( $nights > 1 && apply_filters( 'mb_hms_show_price_breakdown', true, MBH()->session->get( 'checkin' ), MBH()->session->get( 'checkout' ), $cart_item[ 'room_id' ], $cart_item[ 'rate_id' ], $cart_item[ 'quantity' ] ) ) : ?>
								<a class="view-price-breakdown" href="#<?php echo esc_attr( $item_key ); ?>" data-closed="<?php esc_html_e( 'View price breakdown', 'wp-mb_hms' ); ?>" data-open="<?php esc_html_e( 'Hide price breakdown', 'wp-mb_hms' ); ?>"><?php esc_html_e( 'View price breakdown', 'wp-mb_hms' ); ?></a>
								<?php endif; ?>
							</td>
						</tr>

						<?php if ( $nights > 1 && apply_filters( 'mb_hms_show_price_breakdown', true, MBH()->session->get( 'checkin' ), MBH()->session->get( 'checkout' ), $cart_item[ 'room_id' ], $cart_item[ 'rate_id' ], $cart_item[ 'quantity' ] ) ) : ?>
						<tr class="reservation-table__row reservation-table__row--body reservation-table__row--price-breakdown">
							<td colspan="3" class="price-breakdown-wrapper">
								<?php echo mbh_cart_price_breakdown( MBH()->session->get( 'checkin' ), MBH()->session->get( 'checkout' ), $cart_item[ 'room_id' ], $cart_item[ 'rate_id' ], $cart_item[ 'quantity' ] ); ?>
							</td>
						</tr>
						<?php endif;

					endif;
				endforeach;
			?>

		</tbody>
		<tfoot class="reservation-table__footer">
			<?php
				if ( mbh_is_tax_enabled() && mbh_get_tax_rate() > 0 ) : ?>

					<tr class="reservation-table__row reservation-table__row--footer">
						<th colspan="2" class="reservation-table__label reservation-table__label--subtotal"><?php esc_html_e( 'Subtotal:', 'wp-mb_hms' ); ?></th>
						<td class="reservation-table__data reservation-table__data--subtotal"><strong><?php echo mbh_cart_formatted_subtotal(); ?></strong></td>
					</tr>

					<tr class="reservation-table__row reservation-table__row--footer">
						<th colspan="2" class="reservation-table__label reservation-table__label--tax-total"><?php esc_html_e( 'Tax total:', 'wp-mb_hms' ); ?></th>
						<td class="reservation-table__data reservation-table__data--tax-total"><strong><?php echo mbh_cart_formatted_tax_total(); ?></strong></td>
					</tr>

				<?php endif;

				if ( MBH()->cart->needs_payment() ) : ?>

					<tr class="reservation-table__row reservation-table__row--footer">
						<th colspan="2" class="reservation-table__label reservation-table__label--total"><?php esc_html_e( 'Total:', 'wp-mb_hms' ); ?></th>
						<td class="reservation-table__data reservation-table__data--total"><strong><?php echo mbh_cart_formatted_total(); ?></strong></td>
					</tr>

					<?php if ( mbh_get_option( 'booking_mode' ) == 'instant-booking' ) : ?>

						<tr class="reservation-table__row reservation-table__row--footer">
							<th colspan="2" class="reservation-table__label reservation-table__label--total reservation-table__label--deposit"><?php esc_html_e( 'Deposit due now:', 'wp-mb_hms' ); ?></th>
							<td class="reservation-table__data reservation-table__data--total reservation-table__data--deposit"><strong><?php echo mbh_cart_formatted_required_deposit(); ?></strong></td>
						</tr>

					<?php else : ?>

						<tr class="reservation-table__row reservation-table__row--footer">
							<th colspan="2" class="reservation-table__label reservation-table__label--total reservation-table__label--deposit"><?php esc_html_e( 'Deposit due after confirm:', 'wp-mb_hms' ); ?></th>
							<td class="reservation-table__data reservation-table__data--total reservation-table__data--deposit"><strong><?php echo mbh_cart_formatted_required_deposit(); ?></strong></td>
						</tr>

					<?php endif; ?>

				<?php else : ?>

					<tr class="reservation-table__row reservation-table__row--footer">
						<th colspan="2" class="reservation-table__label reservation-table__label--total"><?php esc_html_e( 'Total:', 'wp-mb_hms' ); ?></th>
						<td class="reservation-table__data reservation-table__data--total"><strong><?php echo mbh_cart_formatted_total(); ?></strong></td>
					</tr>

				<?php endif;
			?>
		</tfoot>
	</table>

	<?php if ( ! MBH()->cart->is_cancellable() ) : ?>
		<div class="reservation-non-cancellable-disclaimer">
			<p class="reservation-non-cancellable-disclaimer__text">
				<?php esc_html_e( 'This reservation includes a non-cancellable and non-refundable room. You will be charged the total price if you cancel your booking.', 'wp-mb_hms' ); ?>
			</p>
		</div>
	<?php endif; ?>

	<?php do_action( 'mb_hms_booking_after_booking_table' ); ?>

</div>
