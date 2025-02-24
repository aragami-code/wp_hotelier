<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo "\n" . strtoupper( esc_html__( 'Guest address', 'wp-mb_hms' ) ) . "\n\n";
echo preg_replace( '#<br\s*/?>#i', "\n", $reservation->get_formatted_guest_address() ) . "\n\n";
