<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$room_id = is_room() ? get_the_ID() : false;

// extensions can hook into here to add their own pages
$datepicker_form_url = apply_filters( 'mb_hms_datepicker_form_url', MBH()->cart->get_room_list_form_url( $room_id ) ); ?>

<form name="mb_hms_datepicker" method="post" id="mb_hms-datepicker" class="datepicker-form" action="<?php echo esc_url( $datepicker_form_url ); ?>" enctype="multipart/form-data">

	<span class="datepicker-input-select-wrapper"><input class="datepicker-input-select" type="text" id="mb_hms-datepicker-select" value=""></span>
	<input type="text" id="mb_hms-datepicker-checkin" class="datepicker-input datepicker-input--checkin" name="checkin" value="<?php echo esc_attr( $checkin ); ?>">
	<input type="text" id="mb_hms-datepicker-checkout" class="datepicker-input datepicker-input--checkout" name="checkout" value="<?php echo esc_attr( $checkout ); ?>">

	<?php echo apply_filters( 'mb_hms_datepicker_button_html', '<input type="submit" class="button button--datepicker" name="mb_hms_datepicker_button" id="datepicker-button" value="' . esc_attr__( 'Check availability', 'wp-mb_hms' ) . '" />' ); ?>
</form>
