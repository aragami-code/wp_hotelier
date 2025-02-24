<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

if ( ! $room->has_conditions() ) {
	return;
}

?>

<div class="room__conditions room__conditions--single">

	<h3 class="room__conditions-title room__conditions-title--single"><?php esc_html_e( 'Room conditions', 'wp-mb_hms' ); ?></h3>

	<ul class="room__conditions-list room__conditions-list--single">

	<?php foreach ( $room->get_room_conditions() as $condition ) : ?>

		<li class="room__conditions-item room__conditions-item--single"><?php echo esc_html( $condition ); ?></li>

	<?php endforeach; ?>

	</ul>

</div>
