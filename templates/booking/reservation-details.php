<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="reservation-details" class="booking__section booking__section--reservation-details">

	<header class="section-header">
		<h3 class="section-header__title"><?php esc_html_e( 'Booking details', 'wp-mb_hms' ); ?></h3>
	</header>

	<?php do_action( 'mb_hms_booking_before_booking_details' ); ?>

	<table class="table table--reservation-table reservation-table reservation-table--reservation-details mb_hms-table">
		<tbody class="reservation-table__body">
			<tr class="reservation-table__row reservation-table__row--body">
				<th class="reservation-table__label"><?php esc_html_e( 'Check-in', 'wp-mb_hms' ); ?></th>
				<td class="reservation-table__data"><?php echo esc_html( $checkin ); ?></td>
			</tr>
			<tr class="reservation-table__row reservation-table__row--body">
				<th class="reservation-table__label"><?php esc_html_e( 'Check-out', 'wp-mb_hms' ); ?></th>
				<td class="reservation-table__data"><?php echo esc_html( $checkout ); ?></td>
			</tr>
			<?php if ( apply_filters( 'mb_hms_show_pets_message', true ) ) : ?>
				<tr class="reservation-table__row reservation-table__row--body">
					<th class="reservation-table__label"><?php esc_html_e( 'Pets', 'wp-mb_hms' ); ?></th>
					<td class="reservation-table__data"><?php echo esc_html( $pets_message ); ?></td>
				</tr>
			<?php endif; ?>

			<?php if ( $cards ) : ?>
			<tr class="reservation-table__row reservation-table__row--body">
				<th class="reservation-table__label"><?php esc_html_e( 'Accepted credit cards', 'wp-mb_hms' ); ?></th>
				<td class="reservation-table__data reservation-table__data--credit-cards">
					<ul class="credit-cards__list">
					<?php foreach ( $cards as $card ) : ?>
						<li class="credit-cards__icon credit-cards__icon--<?php echo esc_attr( $card ); ?>"><?php echo esc_html( ucfirst( $card ) ); ?></li>
					<?php endforeach; ?>
					</ul>
				</td>
			</tr>
			<?php endif; ?>

		</tbody>
	</table>

	<?php do_action( 'mb_hms_booking_after_booking_details' ); ?>

</div>
