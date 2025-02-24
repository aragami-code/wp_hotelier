<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

$key             = mbh_generate_item_key( $room->id, 0 );
$checkin         = MBH()->session->get( 'checkin' );
$checkout        = MBH()->session->get( 'checkout' );
$available_rooms = absint( $room->get_available_rooms( $checkin, $checkout ) );

?>

<div class="add-to-cart-wrapper">

	<?php if ( $available_rooms > 0 && $is_available && apply_filters( 'mb_hms_show_add_to_cart_button', true, $room, $checkin, $checkout ) ) : ?>

		<?php do_action( 'mb_hms_before_add_to_cart_button' ); ?>

		<?php
			mb_hms_quantity_input( array(
				'id'          => 'add-to-cart-room[' . esc_attr( $key ) . ']',
				'min_value'   => apply_filters( 'mb_hms_quantity_input_min', 0, $room ),
				'max_value'   => apply_filters( 'mb_hms_quantity_input_max', $room->get_stock_rooms(), $room ),
				'input_value' => 0,
				'input_name'  => "quantity[{$key}]"
			) );
		?>

		<input type="hidden" name="add_to_cart_room[<?php echo esc_attr( $key ); ?>]" value="<?php echo esc_attr( apply_filters( 'mb_hms_add_to_cart_room_id', $room->id, $room ) ); ?>" />
		<input type="hidden" name="rate_id[<?php echo esc_attr( $key ); ?>]" value="0" />

		<a href="#reserve-button" data-selected-text-singular="<?php echo esc_attr_x( 'room selected', 'book now button text: singular', 'wp-mb_hms' ); ?>" data-selected-text-plural="<?php echo esc_attr_x( 'rooms selected', 'book now button text: plural', 'wp-mb_hms' ); ?>" class="button button--add-to-cart"><?php esc_html_e( 'Book now', 'wp-mb_hms' ); ?></a>

		<?php do_action( 'mb_hms_after_add_to_cart_button' ); ?>

	<?php endif; ?>

</div>
