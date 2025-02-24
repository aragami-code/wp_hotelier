<?php
/**
 * 
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

		<?php while ( have_posts() ) : the_post(); ?>

			<?php mbh_get_template_part( 'single-room/content', 'single-room' ); ?>

		<?php endwhile; // end of the loop. ?>

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
