<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( mbh_get_page_id( 'terms' ) > 0 ) : ?>
	<?php do_action( 'mb_hms_before_terms_and_conditions' ); ?>

	<p class="form-row form-row--booking-terms">
		<input type="checkbox" class="input-checkbox input--booking-terms" name="booking_terms" <?php checked( isset( $_POST[ 'booking_terms' ] ), true ); ?> id="booking-terms" />
		<label for="booking-terms" class="checkbox label--booking-terms"><?php printf( wp_kses( __( 'I&rsquo;ve read and accept the <a href="%s" target="_blank">terms &amp; conditions</a>', 'wp-mb_hms' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( mbh_get_page_permalink( 'terms' ) ) ); ?> <abbr class="required" title="' . esc_attr__( 'required', 'wp-mb_hms'  ) . '">*</abbr></label>

		<input type="hidden" name="has_terms_field" value="1" />
	</p>

	<?php do_action( 'mb_hms_after_terms_and_conditions' ); ?>
<?php endif; ?>
