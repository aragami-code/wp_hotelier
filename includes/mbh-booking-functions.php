<?php
/**
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Generate unique key
 *
 * Returns the md5 hash of a string
 *
 * @param string $rate_name Room rate
 * @param string $rate_id Room rate ID
 * @return string
 */
function mbh_generate_item_key( $room_id, $rate_id ) {
	return md5( $room_id . '_' . $rate_id );
}
