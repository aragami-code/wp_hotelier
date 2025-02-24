<?php
/**
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php
	/**
	 * mb_hms_before_single_room hook.
	 *
	 * @hooked htl_print_notices - 10
	 */
	do_action( 'mb_hms_before_single_room' );

	if ( post_password_required() ) {
		echo get_the_password_form();
		return;
	}
?>

<div id="room-<?php echo absint( get_the_ID() ); ?>" <?php post_class(); ?>>

	<?php
		/**
		 * mb_hms_single_room_title hook.
		 *
		 * @hooked mb_hms_template_single_room_title - 10
		 */
		do_action( 'mb_hms_single_room_title' );
	?>

	<?php
		/**
		 * mb_hms_single_room_images hook.
		 *
		 * @hooked mb_hms_template_single_room_image - 10
		 * @hooked mb_hms_template_single_room_gallery - 20
		 */
		do_action( 'mb_hms_single_room_images' );
	?>

	<div class="entry-content room__content room__content--single">

		<div class="room__details room__details--single">

			<?php
				/**
				 * mb_hms_single_room_details hook.
				 *
				 * @hooked mb_hms_template_single_room_datepicker - 5
				 * @hooked mb_hms_template_single_room_price - 10
				 * @hooked mb_hms_template_single_room_non_cancellable_info - 15
				 * @hooked mb_hms_template_single_room_deposit - 20
				 * @hooked mb_hms_template_single_room_min_max_info - 25
				 * @hooked mb_hms_template_single_room_meta - 30
				 * @hooked mb_hms_template_single_room_facilities - 40
				 * @hooked mb_hms_template_single_room_conditions - 50
				 * @hooked mb_hms_template_single_room_sharing - 60
				 */
				do_action( 'mb_hms_single_room_details' );
			?>

		</div>

		<div class="room__description room__description--single">

			<?php
				/**
				 * mb_hms_single_room_description hook.
				 *
				 * @hooked mb_hms_template_single_room_description - 10
				 */
				do_action( 'mb_hms_single_room_description' );
			?>

		</div>

		<?php
			/**
			 * mb_hms_single_room_rates hook.
			 *
			 * @hooked mb_hms_template_single_room_rates - 10
			 */
			do_action( 'mb_hms_single_room_rates' );
		?>

		<?php
			/**
			 * mb_hms_output_related_rooms hook.
			 *
			 * @hooked mb_hms_template_related_rooms - 10
			 */
			do_action( 'mb_hms_output_related_rooms' );
		?>

	</div>
</div>
