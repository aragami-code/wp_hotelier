<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<?php mbh_print_notices(); ?>

<p class="cart-errors"><?php esc_html_e( 'There are some issues with the items in your cart (shown above). Please go back and resolve these issues before the booking.', 'wp-mb_hms' ) ?></p>

<?php do_action( 'mb_hms_cart_has_errors' ); ?>

<p><a class="button button--backward" href="<?php echo esc_url( MBH()->cart->get_room_list_form_url() ); ?>"><?php esc_html_e( 'List of available rooms', 'wp-mb_hms' ) ?></a></p>
