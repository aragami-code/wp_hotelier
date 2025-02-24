<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

if ( $price_html = $room->get_price_html( $checkin, $checkout ) ) : ?>

	<div class="room__price-wrapper room__price-wrapper--listing">
		<span class="room__price room__price--listing"><?php echo $price_html; ?></span>
		<span class="room__price-description"><?php esc_html_e( 'Prices are per room', 'wp-mb_hms' ); ?></span>
	</div>

<?php endif; ?>
