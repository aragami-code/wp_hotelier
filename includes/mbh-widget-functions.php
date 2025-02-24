<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

// Include widget classes.
include_once( 'widgets/abstract-mbh-widget.php' );
include_once( 'widgets/class-mbh-widget-booking.php' );
include_once( 'widgets/class-mbh-widget-rooms-filter.php' );
include_once( 'widgets/class-mbh-widget-room-search.php' );
include_once( 'widgets/class-mbh-widget-rooms.php' );

/**
 * Register Widgets.
 */
function mbh_register_widgets() {
	register_widget( 'MBH_Widget_Booking' );
	register_widget( 'MBH_Widget_Rooms_Filter' );
	register_widget( 'MBH_Widget_Room_Search' );
	register_widget( 'MBH_Widget_Rooms' );
}
add_action( 'widgets_init', 'mbh_register_widgets' );
