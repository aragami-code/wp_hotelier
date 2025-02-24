<?php
/**
 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $variation->is_cancellable() ) {
	return;
}
?>

<div class="rate__non-cancellable-info rate__non-cancellable-info--listing">
	<p><?php echo ( apply_filters( 'mb_hms_room_list_non_cancellable_info_text', esc_html__( 'Non-refundable', 'wp-mb_hms' ) ) ); ?></p>
</div>
