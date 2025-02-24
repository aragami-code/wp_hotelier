<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<p class="mb_hms-notice mb_hms-notice--info mb_hms-notice--single-room-not-available"><?php echo $rooms ? esc_html__( 'We are sorry, this room is not available on your requested dates. Please try again with some different dates or have a look at the available rooms below.', 'wp-mb_hms' ) : esc_html__( 'We are sorry, this room is not available on your requested dates. Please try again with some different dates.', 'wp-mb_hms' ); ?></p>
