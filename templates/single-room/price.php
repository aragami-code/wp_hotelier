<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

?>

<div class="room__price-wrapper room__price-wrapper--single">
	<span class="room__price room__price--single"><?php echo $room->get_min_price_html(); ?></span>

	<?php if ( $room->is_variable_room() ) : ?>

		<p class="room-available-rates"><a class="room-available-rates__link" href="#room-rates-<?php echo absint( get_the_ID() ); ?>"><?php esc_html_e( 'See available rates', 'wp-mb_hms' ); ?></a></p>

	<?php endif; ?>
</div>
