<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( htl_get_option( 'booking_mode' ) == 'no-booking' ) {
	return;
}

?>

<p><a href="#mb_hms-datepicker" class="button button--check-availability"><?php esc_html_e( 'Check availability', 'wp-mb_hms' ) ?></a></p>
