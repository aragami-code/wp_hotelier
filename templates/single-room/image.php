<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// The .room__thumbnail div is closed in single-room/gallery.php!

?>

<div class="room__thumbnail room__thumbnail--single">

	<?php
		if ( has_post_thumbnail() ) {
			the_post_thumbnail( 'room_single' );

		} else {

			echo mbh_placeholder_img( 'room_single' );
		}
	?>
