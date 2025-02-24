<?php
/**
 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header( 'mb_hms' ); ?>

	<?php
		/**
		 * mb_hms_before_main_content hook.
		 *
		 * @hooked mb_hms_output_content_wrapper - 10 (outputs opening divs for the content)
		 */
		do_action( 'mb_hms_before_main_content' );
	?>

	<?php if ( apply_filters( 'mb_hms_show_page_title', true ) ) : ?>

		<?php mb_hms_page_title(); ?>

	<?php endif; ?>

	<?php
		/**
		 * mb_hms_archive_description hook.
		 *
		 * @hooked mb_hms_taxonomy_archive_description - 10
		 */
		do_action( 'mb_hms_archive_description' );
	?>

	<?php if ( have_posts() ) : ?>

		<?php
			/**
			 * mb_hms_before_archive_room_loop hook.
			 *
			 * @hooked mb_hms_output_loop_wrapper - 10
			 */
			do_action( 'mb_hms_before_archive_room_loop' );
		?>

		<?php mb_hms_room_loop_start(); ?>

			<?php while ( have_posts() ) : the_post(); ?>

				<?php htl_get_template_part( 'archive/content', 'room' ); ?>

			<?php endwhile; // end of the loop. ?>

		<?php mb_hms_room_loop_end(); ?>

		<?php
			/**
			 * mb_hms_after_archive_room_loop hook.
			 *
			 * @hooked mb_hms_output_loop_wrapper_end - 10
			 */
			do_action( 'mb_hms_after_archive_room_loop' );
		?>

		<?php
			/**
			 * mb_hms_pagination hook.
			 *
			 * @hooked mb_hms_pagination - 10
			 */
			do_action( 'mb_hms_pagination' );
		?>

	<?php else : ?>

		<?php htl_get_template( 'loop/no-rooms-found.php' ); ?>

	<?php endif; ?>

	<?php
		/**
		 * mb_hms_after_main_content hook.
		 *
		 * @hooked mb_hms_output_content_wrapper_end - 10 (outputs closing divs for the content)
		 */
		do_action( 'mb_hms_after_main_content' );
	?>

	<?php
		/**
		 * mb_hms_sidebar hook.
		 *
		 * @hooked mb_hms_get_sidebar - 10
		 */
		do_action( 'mb_hms_sidebar' );
	?>

<?php get_footer( 'mb_hms' ); ?>
