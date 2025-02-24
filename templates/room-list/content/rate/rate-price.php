<?php
/**
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ( $price_html = $variation->get_price_html( $checkin, $checkout ) ) && apply_filters( 'mb_hms_show_rate_price', true, $checkin, $checkout, $variation ) ) : ?>

	<div class="rate__price rate__price--listing">
		<span class="rate__price rate__price--listing"><?php echo $price_html; ?></span>
		<span class="rate__price-description"><?php esc_html_e( 'Prices are per room', 'wp-mb_hms' ); ?></span>
	</div>

<?php endif; ?>
