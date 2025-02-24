<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<p class="mb_hms-notice mb_hms-notice--info mb_hms-notice--single-room-available-info"><?php echo $rooms ? esc_html__( 'Hooray this room is available! For your convenience, below you can find other rooms that are available on the same dates.', 'wp-mb_hms' ) : esc_html__( 'Hooray this room is available!', 'wp-mb_hms' ); ?></p>
