<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

?>

<div class="room__rate room__rate--listing">
	<div class="rate__content rate__content--listing">
		<?php
			/**
			 * mb_hms_room_list_item_rate_content hook
			 *
			 * @hooked mb_hms_template_loop_room_rate_name - 10
			 * @hooked mb_hms_template_loop_room_rate_description - 15
			 * @hooked mb_hms_template_loop_room_rate_conditions - 20
			 * @hooked mb_hms_template_loop_room_rate_deposit - 25
			 * @hooked mb_hms_template_loop_room_rate_min_max_info - 30
			 */
			do_action( 'mb_hms_room_list_item_rate_content', $variation );
		?>
	</div><!-- .rate__content -->

	<div class="rate__actions rate__actions--listing">
		<?php
			/**
			 * mb_hms_room_list_item_rate_actions hook
			 *
			 * @hooked mb_hms_template_loop_room_rate_price - 10
			 * @hooked mb_hms_template_loop_room_rate_non_cancellable_info - 12
			 * @hooked mb_hms_template_loop_room_rate_add_to_cart - 15
			 */
			do_action( 'mb_hms_room_list_item_rate_actions', $variation, $is_available, $checkin, $checkout );
		?>
	</div><!-- .rate__actions -->
</div>
