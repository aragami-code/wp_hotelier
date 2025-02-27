<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$reservation = mbh_get_reservation( $reservation_id );

?>

<div class="reservation-received__section">

	<header class="section-header">
		<h3 class="section-header__title"><?php esc_html_e( 'Reservation details', 'wp-mb_hms' ); ?></h3>
	</header>

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
				foreach( $reservation->get_items() as $item_id => $item ) {
					$room = $reservation->get_room_from_item( $item );

					mbh_get_template( 'reservation/item.php', array(
						'reservation' => $reservation,
						'item_id'     => $item_id,
						'item'        => $item,
						'room'        => $room,
					) );
				}
			?>
		</tbody>
		<tfoot class="reservation-table__footer">
			<?php
			if ( $totals = $reservation->get_reservation_totals() ) :
				foreach ( $totals as $total ) : ?>
					<tr class="reservation-table__row reservation-table__row--footer">
						<th class="reservation-table__label reservation-table__label--total" colspan="2"><?php echo esc_html( $total[ 'label' ] ); ?></th>
						<td class="reservation-table__data reservation-table__data--total"><strong><?php echo wp_kses_post( $total[ 'value' ] ); ?></strong></td>
					</tr>
				<?php endforeach;
			endif; ?>
		</tfoot>
	</table>

	<?php if ( ! $reservation->can_be_cancelled() ) : ?>
		<div class="reservation-non-cancellable-disclaimer">
			<p class="reservation-non-cancellable-disclaimer__text">
				<?php esc_html_e( 'This reservation includes a non-cancellable and non-refundable room. You will be charged the total price if you cancel your booking.', 'wp-mb_hms' ); ?>
			</p>
		</div>
	<?php endif; ?>

</div>

<?php do_action( 'mb_hms_after_reservation_table', $reservation ); ?>
