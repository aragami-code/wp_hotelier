<?php
/**
 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="guest-details" class="booking__section booking__section--guest-details">

	<header class="section-header">
		<h3 class="section-header__title"><?php esc_html_e( 'Guest details', 'wp-mb_hms' ); ?></h3>
	</header>

	<?php do_action( 'mb_hms_booking_before_guest_details' ); ?>

	<div class="guest-details-fields">

		<?php foreach ( $booking->booking_fields[ 'address_fields' ] as $key => $field ) : ?>

			<?php mbh_form_field( $key, $field, $booking->get_value( $key ) ); ?>

		<?php endforeach; ?>

	</div>

	<?php do_action( 'mb_hms_booking_after_guest_details' ); ?>

</div>
