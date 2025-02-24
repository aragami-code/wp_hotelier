<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $post;

if ( ! $post->post_excerpt ) {
	return;
}

?>

<div class="room__description room__description--listing">
	<?php echo apply_filters( 'mb_hms_short_description', $post->post_excerpt ) ?>
</div>

<p class="room__more"><a class="room__more-link" href="#room-details-<?php echo esc_attr( $post->ID ); ?>" data-closed="<?php esc_html_e( 'More about this room', 'wp-mb_hms' ); ?>" data-open="<?php esc_html_e( 'Hide room details', 'wp-mb_hms' ); ?>"><?php esc_html_e( 'More about this room', 'wp-mb_hms' ); ?></a></p>
