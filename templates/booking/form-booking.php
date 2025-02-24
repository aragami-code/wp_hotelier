<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

mbh_print_notices();

do_action( 'mb_hms_before_booking_form', $booking );

// extensions can hook into here to add their own pages
$booking_form_url = apply_filters( 'mb_hms_booking_form_url', MBH()->cart->get_booking_form_url() ); ?>

<form id="booking-form" name="booking" method="post" class="booking form--booking" action="<?php echo esc_url( $booking_form_url ); ?>" enctype="multipart/form-data">
	<?php if ( sizeof( $booking->booking_fields ) > 0 ) : ?>

		<?php do_action( 'mb_hms_booking_guest_details' ); ?>

		<?php
		// show additional information fields
		if ( htl_get_option( 'booking_additional_information' ) ) :	?>

			<?php do_action( 'mb_hms_booking_additional_information' ); ?>

		<?php endif; ?>

	<?php endif; ?>

	<?php do_action( 'mb_hms_booking_details' ); ?>

	<?php do_action( 'mb_hms_booking_table' ); ?>

	<?php do_action( 'mb_hms_booking_payment' ); ?>

	<?php do_action( 'mb_hms_book_button' ); ?>

</form>

<?php do_action( 'mb_hms_after_booking_form', $booking ); ?>
