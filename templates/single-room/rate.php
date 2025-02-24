<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<li class="room__rate room__rate--single">

	<div class="rate__description-wrapper">

		<?php
			/**
			 * mb_hms_single_room_rate_content hook
			 *
			 * @hooked mb_hms_template_single_room_rate_name - 10
			 * @hooked mb_hms_template_single_room_rate_description - 15
			 * @hooked mb_hms_template_single_room_rate_conditions - 20
			 * @hooked mb_hms_template_single_room_rate_min_max_info - 25
			 */
			do_action( 'mb_hms_single_room_rate_content', $variation );
		?>

	</div>

	<div class="rate__actions rate__actions--single">

		<?php
			/**
			 * mb_hms_single_room_rate_actions hook
			 *
			 * @hooked mb_hms_template_single_room_rate_price - 10
			 * @hooked mb_hms_template_single_room_rate_non_cancellable_info - 15
			 * @hooked mb_hms_template_single_room_rate_check_availability - 20
			 * @hooked mb_hms_template_single_room_rate_deposit - 25
			 */
			do_action( 'mb_hms_single_room_rate_actions', $variation );
		?>

	</div>

</li>
