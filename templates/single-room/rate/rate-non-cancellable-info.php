<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $variation->is_cancellable() ) {
	return;
}
?>

<span class="rate__non-cancellable-info rate__non-cancellable-info--single"><?php echo esc_html( apply_filters( 'mb_hms_single_room_non_cancellable_info_text', __( 'Non-refundable', 'wp-mb_hms' ) ) ); ?></span>
