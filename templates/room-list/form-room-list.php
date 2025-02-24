<?php
/**
 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

mbh_print_notices();

if ( $rooms && $rooms->have_posts() || ( $room_id && $room_id_available ) ) : ?>

	<?php
		/**
		 * mb_hms_room_list_datepicker hook
		 *
		 * @hooked mb_hms_template_datepicker - 10
		 */
		do_action( 'mb_hms_room_list_datepicker' );
	?>

	<?php
		/**
		 * mb_hms_room_list_selected_nights hook
		 *
		 * @hooked mb_hms_template_selected_nights - 10
		 */
		do_action( 'mb_hms_room_list_selected_nights' );
	?>

	<form name="room_list" method="post" id="form--listing" class="form--listing listing" enctype="multipart/form-data">

		<?php do_action( 'mb_hms_before_room_list_loop' ); ?>

		<?php if ( $room_id ) : ?>
			<?php if ( ! $room_id_available ) : ?>

				<?php mbh_get_template( 'room-list/single-room-not-available-info.php', array( 'rooms' => $rooms ) ); ?>

			<?php else : ?>

				<?php mbh_get_template( 'room-list/single-room-available-info.php', array( 'rooms' => $rooms ) ); ?>

			<?php endif; ?>
		<?php endif; ?>

		<?php mb_hms_room_list_start(); ?>

			<?php if ( $room_id && $room_id_available ) :
				global $post;

				$post = get_post( $room_id );
				setup_postdata( $post );
				?>

				<?php
					/**
					 * mb_hms_room_list_item_content hook
					 *
					 * @hooked mb_hms_template_loop_room_content - 10
					 */
					do_action( 'mb_hms_room_list_item_content', true );
				?>

				<?php wp_reset_postdata(); ?>
			<?php endif; ?>

			<?php if ( $rooms && $rooms->have_posts() ) : ?>

				<?php while ( $rooms->have_posts() ) : $rooms->the_post();

					global $room;

					// Ensure visibility
					if ( ! $room || ! $room->is_visible() ) {
						return;
					}

					?>

					<?php
						/**
						 * mb_hms_room_list_item_content hook
						 *
						 * @hooked mb_hms_template_loop_room_content - 10
						 */
						do_action( 'mb_hms_room_list_item_content' );
					?>

				<?php endwhile; // end of the loop. ?>

			<?php endif; ?>

		<?php mb_hms_room_list_end(); ?>

		<?php wp_reset_postdata(); ?>

		<?php
			/**
			 * mb_hms_reserve_button hook
			 *
			 * @hooked mb_hms_template_loop_room_reserve_button - 10
			 */
			do_action( 'mb_hms_reserve_button' );
		?>

		<?php do_action( 'mb_hms_after_room_list_loop' ); ?>

	</form>

<?php else: ?>

	<?php mbh_get_template( 'room-list/no-rooms-available.php' ); ?>

<?php endif;  ?>

<?php do_action( 'mb_hms_after_room_list_form' ); ?>
