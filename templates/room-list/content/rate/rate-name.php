<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<h4 class="rate__room rate__room--listing"><?php the_title(); ?></h4>
<span class="rate__name rate__name--listing"><?php echo esc_html( $variation->get_formatted_room_rate() ); ?></span>
