<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="request-booking" class="booking__section booking__section--request-booking">

	<div class="form-row">
		<?php do_action( 'mb_hms_booking_before_submit' ); ?>

		<?php echo apply_filters( 'mb_hms_book_button_html', '<input type="submit" class="button button--book-button" name="mb_hms_booking_book_button" id="book-button" value="' . esc_attr( $button_text ) . '" />' ); ?>

		<input type="hidden" name="mb_hms_booking_action" value="1" />

		<?php do_action( 'mb_hms_booking_after_submit' ); ?>

		<?php wp_nonce_field( 'mb_hms_process_booking' ); ?>
	</div>

</div>
