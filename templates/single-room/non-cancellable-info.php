<?php
/**
 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

if ( $room->is_variable_room() || $room->is_cancellable() ) {
	return;
}
?>

<div class="room__non-cancellable-info room__non-cancellable-info--single">
	<p><?php echo ( apply_filters( 'mb_hms_room_list_non_cancellable_info_text', esc_html__( 'Non-refundable', 'wp-mb_hms' ) ) ); ?></p>
</div>
