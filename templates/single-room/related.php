<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $related_rooms && $related_rooms->have_posts() ) : ?>

	<div class="related-rooms">

		<h3 class="related-rooms-title"><?php _e( 'Related rooms', 'wp-mb_hms' ); ?></h3>

		<div class="mb_hms room-loop room-loop--related-rooms room-loop--columns-<?php echo absint( $columns ); ?>">

			<?php mb_hms_room_loop_start(); ?>

				<?php while ( $related_rooms->have_posts() ) : $related_rooms->the_post(); ?>

					<?php mbh_get_template_part( 'archive/content', 'room' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php mb_hms_room_loop_end(); ?>

		</div>

	</div>

<?php endif;

wp_reset_postdata();
