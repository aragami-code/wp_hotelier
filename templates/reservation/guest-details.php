<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="reservation-received__section">

	<header class="section-header">
		<h3 class="section-header__title"><?php esc_html_e( 'Guest details', 'wp-mb_hms' ); ?></h3>
	</header>

	<table class="table table--guest-details mb_hms-table">
		<?php if ( $reservation->get_formatted_guest_full_name() ) : ?>
			<tr class="reservation-table__row reservation-table__row--body">
				<th class="reservation-table__label"><?php esc_html_e( 'Name:', 'wp-mb_hms' ); ?></th>
				<td class="reservation-table__data"><?php echo esc_html( $reservation->get_formatted_guest_full_name() ); ?></td>
			</tr>
		<?php endif; ?>

		<?php if ( $reservation->guest_email ) : ?>
			<tr class="reservation-table__row reservation-table__row--body">
				<th class="reservation-table__label"><?php esc_html_e( 'Email:', 'wp-mb_hms' ); ?></th>
				<td class="reservation-table__data"><?php echo esc_html( $reservation->guest_email ); ?></td>
			</tr>
		<?php endif; ?>

		<?php if ( $reservation->guest_telephone ) : ?>
			<tr class="reservation-table__row reservation-table__row--body">
				<th class="reservation-table__label"><?php esc_html_e( 'Telephone:', 'wp-mb_hms' ); ?></th>
				<td class="reservation-table__data"><?php echo esc_html( $reservation->guest_telephone ); ?></td>
			</tr>
		<?php endif; ?>

		<?php do_action( 'mb_hms_reservation_after_guest_details', $reservation ); ?>
	</table>

</div>

<div class="reservation-received__section">
	<header class="section-header">
		<h3 class="section-header__title"><?php esc_html_e( 'Guest address', 'wp-mb_hms' ); ?></h3>
	</header>

	<address class="address address--guest-address">
		<?php echo $reservation->get_formatted_guest_address(); ?>
	</address>
</div>
