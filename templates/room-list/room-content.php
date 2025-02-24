<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

$checkin      = MBH()->session->get( 'checkin' );
$checkout     = MBH()->session->get( 'checkout' );
$is_available = $room->is_available( $checkin, $checkout );

$listing_room_classes = array(
	'listing__room'
);

if ( $is_single ) {
	$listing_room_classes[] = 'listing__room--queried';
}
?>

<li <?php post_class( $listing_room_classes ); ?>>

	<div class="room__content-wrapper">

		<div class="room__content room__content--listing">

			<?php
				/**
				 * mb_hms_room_list_item_title hook
				 *
				 * @hooked mb_hms_template_rooms_left - 10
				 * @hooked mb_hms_template_loop_room_title - 20
				 */
				do_action( 'mb_hms_room_list_item_title', $is_available, $checkin, $checkout );
			?>

			<div class="room__gallery room__gallery--listing">

				<?php
					/**
					 * mb_hms_room_list_item_thumb hook
					 *
					 * @hooked mb_hms_template_loop_room_image - 10
					 * @hooked mb_hms_template_loop_room_thumbnails - 20
					 */
					do_action( 'mb_hms_room_list_item_images' );
				?>

			</div>

			<?php
				/**
				 * mb_hms_room_list_item_description hook
				 *
				 * @hooked mb_hms_template_loop_room_short_description - 10
				 */
				do_action( 'mb_hms_room_list_item_description' );
			?>

			<div id="room-details-<?php echo esc_attr( $room->id ); ?>" class="room__details room__details--listing">

				<?php
					/**
					 * mb_hms_room_list_item_meta hook
					 *
					 * @hooked mb_hms_template_loop_room_facilities - 10
					 * @hooked mb_hms_template_loop_room_meta - 15
					 * @hooked mb_hms_template_loop_room_conditions - 20
					 */
					do_action( 'mb_hms_room_list_item_meta' );
				?>

			</div>

			<?php
				/**
				 * mb_hms_room_list_item_deposit hook
				 *
				 * @hooked mb_hms_template_loop_room_deposit - 10
				 */
				do_action( 'mb_hms_room_list_item_deposit' );
			?>

			<?php
				/**
				 * mb_hms_room_list_item_guests hook
				 *
				 * @hooked mb_hms_template_loop_room_guests - 10
				 */
				do_action( 'mb_hms_room_list_item_guests' );
			?>

			<?php
				/**
				 * mb_hms_room_list_not_available_info hook
				 *
				 * @hooked mb_hms_template_loop_room_not_available_info - 10
				 */
				do_action( 'mb_hms_room_list_not_available_info', $is_available );
			?>

			<?php
				/**
				 * mb_hms_room_list_min_max_info hook
				 *
				 * @hooked mb_hms_template_loop_room_min_max_info - 10
				 */
				do_action( 'mb_hms_room_list_min_max_info' );
			?>

			<?php do_action( 'mb_hms_room_list_after_content' ); ?>

		</div><!-- .room__content -->

		<div class="room__actions">

		<?php
			/**
			 * mb_hms_room_list_item_price hook
			 *
			 * @hooked mb_hms_template_loop_room_price - 10
			 */
			do_action( 'mb_hms_room_list_item_price', $checkin, $checkout );
		?>

		<?php if ( $room->is_variable_room() ) : ?>

				<a href="#room-variations-<?php echo absint( $room->id ); ?>" data-closed="<?php esc_html_e( 'Show rates', 'wp-mb_hms' ); ?>" data-open="<?php esc_html_e( 'Hide rates', 'wp-mb_hms' ); ?>" class="button button--toggle-rates"><?php esc_html_e( 'Hide rates', 'wp-mb_hms' ); ?></a>

				</div><!-- .room__actions -->

			</div><!-- .room__content-wrapper -->

			<div class="clear"></div>

			<div id="room-variations-<?php echo absint( $room->id ); ?>" class="room__rates room__rates--listing">

				<?php
				$varitations = $room->get_room_variations();

				// Print room rates
				foreach ( $varitations as $variation ) :
					$variation = new MBH_Room_Variation( $variation, $room->id ); ?>

					<?php
						/**
						 * mb_hms_room_list_item_rate hook
						 *
						 * @hooked mb_hms_template_loop_room_rate - 10
						 */
						do_action( 'mb_hms_room_list_item_rate', $variation, $is_available, $checkin, $checkout );
					?>

				<?php endforeach; ?>

			</div><!-- .room__rates -->

		<?php else : ?>

				<?php
					/**
					 * mb_hms_room_list_item_before_add_to_cart hook
					 *
					 * @hooked mb_hms_template_loop_room_non_cancellable_info - 10
					 */
					do_action( 'mb_hms_room_list_item_before_add_to_cart' );
				?>

				<?php
					/**
					 * mb_hms_room_list_item_add_to_cart hook
					 *
					 * @hooked mb_hms_template_loop_room_add_to_cart - 10
					 */
					do_action( 'mb_hms_room_list_item_add_to_cart', $is_available );
				?>

				</div><!-- .room__actions -->

			</div><!-- .room__content-wrapper -->

		<?php endif; ?>

</li>
