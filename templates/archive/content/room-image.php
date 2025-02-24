<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<a href="<?php the_permalink() ?>" class="room__thumbnail room__thumbnail--loop">
	<?php if ( has_post_thumbnail() ) :
		the_post_thumbnail( 'room_catalog' );
	else :
		echo htl_placeholder_img( 'room_catalog' );
	endif; ?>
</a>
