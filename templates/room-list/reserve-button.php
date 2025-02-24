<?php
/**
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

?>

<div id="reserve-rooms-button">

	<?php wp_nonce_field( 'mb_hms_reserve_rooms' ); ?>

	<?php do_action( 'mb_hms_room_list_before_submit' ); ?>

	<?php echo apply_filters( 'mb_hms_reserve_button_html', '<input type="submit" class="button button--reserve" name="mb_hms_reserve_rooms_button" id="reserve-button" value="' . esc_html__( 'Reserve', 'wp-mb_hms' ) . '" />' ); ?>

	<?php do_action( 'mb_hms_room_list_after_submit' ); ?>

</div>
