<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

if ( $is_available ) {
	return;
}
?>

<div class="room__not-available-info">
	<p><?php echo ( apply_filters( 'mb_hms_room_list_not_available_info_text', esc_html__( 'The room is not available for this date', 'wp-mb_hms' ) ) ); ?></p>
</div>
