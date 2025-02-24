<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

if ( ! $room->has_conditions() ) {
	return;
}

?>

<div class="room__conditions room__conditions--listing">

	<strong class="room__conditions-title room__conditions-title--listing"><?php esc_html_e( 'Room conditions:', 'wp-mb_hms' ) ?></strong>

	<ul class="room__conditions-list room__conditions-list--listing">

	<?php foreach ( $room->get_room_conditions() as $condition ) : ?>

		<li class="room__conditions-item room__conditions-item--listing"><?php echo esc_html( $condition ); ?></li>

	<?php endforeach; ?>

	</ul>

</div>
