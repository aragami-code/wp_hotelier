<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

?>

<div class="room__meta room__meta--listing">

	<?php if ( $room->get_room_size() ) : ?>
		<p class="room__size room__size--listing"><strong><?php esc_html_e( 'Room size:', 'wp-mb_hms' ) ?></strong> <?php echo esc_html( $room->get_formatted_room_size() ); ?></p>
	<?php endif; ?>

	<?php if ( $room->get_bed_size() ) : ?>
		<p class="room__bed-size room__bed-size--listing"><strong><?php esc_html_e( 'Bed size(s):', 'wp-mb_hms' ) ?></strong> <?php echo esc_html( $room->get_bed_size() ); ?></p>
	<?php endif; ?>

</div>
