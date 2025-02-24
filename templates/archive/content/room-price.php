<?php
/**
 * 
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

?>

<span class="room__price room__price--loop"><?php echo $room->get_min_price_html(); ?></span>
