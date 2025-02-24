<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="guest-additional-information" class="booking__section booking__section--guest-additional-information">

	<header class="section-header">
		<h3 class="section-header__title"><?php esc_html_e( 'Additional information', 'wp-mb_hms' ); ?></h3>
	</header>

	<?php do_action( 'mb_hms_booking_before_additional_information' ); ?>

	<div class="guest-additional-information-fields">

		<?php foreach ( $booking->booking_fields[ 'additional_information_fields' ] as $key => $field ) : ?>

			<?php mbh_form_field( $key, $field, $booking->get_value( $key ) ); ?>

		<?php endforeach; ?>

	</div>

	<?php do_action( 'mb_hms_booking_after_additional_information' ); ?>

</div>
