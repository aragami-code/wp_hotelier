<?php
/**
 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

?>

<div class="room__description room__description--loop">
	<?php echo apply_filters( 'mb_hms_short_description', $post->post_excerpt ) ?>
</div>
