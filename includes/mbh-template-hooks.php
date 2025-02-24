<?php
/**
 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

add_filter( 'body_class', 'mbh_body_class' );

/**
 * Output mb_hms generator tag
 */
add_action( 'get_the_generator_html', 'mbh_generator_tag', 10, 2 );
add_action( 'get_the_generator_xhtml', 'mbh_generator_tag', 10, 2 );

/**
 * Global
 */
add_action( 'mb_hms_before_main_content', 'mb_hms_output_content_wrapper', 10 );
add_action( 'mb_hms_after_main_content', 'mb_hms_output_content_wrapper_end', 10 );
add_action( 'mb_hms_sidebar', 'mb_hms_get_sidebar', 10 );
add_action( 'mb_hms_pagination', 'mb_hms_pagination', 10 );

/**
 * Single Room
 */
add_action( 'mb_hms_single_room_images', 'mb_hms_template_single_room_image', 10 );
add_action( 'mb_hms_single_room_images', 'mb_hms_template_single_room_gallery', 20 );
add_action( 'mb_hms_single_room_title', 'mb_hms_template_single_room_title', 10 );
add_action( 'mb_hms_single_room_details', 'mb_hms_template_single_room_datepicker', 5 );
add_action( 'mb_hms_single_room_details', 'mb_hms_template_single_room_price', 10 );
add_action( 'mb_hms_single_room_details', 'mb_hms_template_single_room_non_cancellable_info', 15 );
add_action( 'mb_hms_single_room_details', 'mb_hms_template_single_room_deposit', 20 );
add_action( 'mb_hms_single_room_details', 'mb_hms_template_single_room_min_max_info', 25 );
add_action( 'mb_hms_single_room_details', 'mb_hms_template_single_room_meta', 30 );
add_action( 'mb_hms_single_room_details', 'mb_hms_template_single_room_facilities', 40 );
add_action( 'mb_hms_single_room_details', 'mb_hms_template_single_room_conditions', 50 );
add_action( 'mb_hms_single_room_details', 'mb_hms_template_single_room_sharing', 60 );
add_action( 'mb_hms_single_room_description', 'mb_hms_template_single_room_description', 10 );
add_action( 'mb_hms_single_room_rates', 'mb_hms_template_single_room_rates', 10 );
add_action( 'mb_hms_single_room_single_rate', 'mb_hms_template_single_room_single_rate', 10 );

// Rate content
add_action( 'mb_hms_single_room_rate_content', 'mb_hms_template_single_room_rate_name', 10 );
add_action( 'mb_hms_single_room_rate_content', 'mb_hms_template_single_room_rate_description', 15 );
add_action( 'mb_hms_single_room_rate_content', 'mb_hms_template_single_room_rate_conditions', 20 );
add_action( 'mb_hms_single_room_rate_content', 'mb_hms_template_single_room_rate_min_max_info', 25 );
add_action( 'mb_hms_single_room_rate_actions', 'mb_hms_template_single_room_rate_price', 10 );
add_action( 'mb_hms_single_room_rate_actions', 'mb_hms_template_single_room_rate_non_cancellable_info', 15 );
add_action( 'mb_hms_single_room_rate_actions', 'mb_hms_template_single_room_rate_check_availability', 20 );
add_action( 'mb_hms_single_room_rate_actions', 'mb_hms_template_single_room_rate_deposit', 25 );
add_action( 'mb_hms_output_related_rooms', 'mb_hms_template_related_rooms', 10 );

/**
 * Archive Loop Items
 */
add_action( 'mb_hms_archive_description', 'mb_hms_taxonomy_archive_description', 10 );
add_action( 'mb_hms_before_archive_room_loop', 'mb_hms_before_archive_room_loop', 10 );
add_action( 'mb_hms_after_archive_room_loop', 'mb_hms_output_loop_wrapper_end', 10 );
add_action( 'mb_hms_archive_item_room', 'mb_hms_template_archive_room_image', 5 );
add_action( 'mb_hms_archive_item_room', 'mb_hms_template_archive_room_title', 10 );
add_action( 'mb_hms_archive_item_room', 'mb_hms_template_archive_room_description', 20 );
add_action( 'mb_hms_archive_item_room', 'mb_hms_template_archive_room_price', 30 );
add_action( 'mb_hms_archive_item_room', 'mb_hms_template_archive_room_more', 40 );

/**
 * Room Loop Items
 */
add_action( 'mb_hms_room_list_datepicker', 'mb_hms_template_datepicker', 10 );
add_action( 'mb_hms_room_list_selected_nights', 'mb_hms_template_selected_nights', 10 );
add_action( 'mb_hms_room_list_item_content', 'mb_hms_template_room_list_content', 10, 2 );
add_action( 'mb_hms_room_list_item_title', 'mb_hms_template_rooms_left', 10, 4 );
add_action( 'mb_hms_room_list_item_title', 'mb_hms_template_room_list_title', 20 );
add_action( 'mb_hms_room_list_item_images', 'mb_hms_template_loop_room_image', 10 );
add_action( 'mb_hms_room_list_item_images', 'mb_hms_template_loop_room_thumbnails', 20 );
add_action( 'mb_hms_room_list_item_description', 'mb_hms_template_loop_room_short_description', 10 );
add_action( 'mb_hms_room_list_item_meta', 'mb_hms_template_loop_room_facilities', 10 );
add_action( 'mb_hms_room_list_item_meta', 'mb_hms_template_loop_room_meta', 15 );
add_action( 'mb_hms_room_list_item_meta', 'mb_hms_template_loop_room_conditions', 20 );
add_action( 'mb_hms_room_list_item_deposit', 'mb_hms_template_loop_room_deposit', 10 );
add_action( 'mb_hms_room_list_item_guests', 'mb_hms_template_loop_room_guests', 10 );
add_action( 'mb_hms_room_list_item_price', 'mb_hms_template_loop_room_price', 10, 3 );
add_action( 'mb_hms_room_list_not_available_info', 'mb_hms_template_loop_room_not_available_info', 10, 2 );
add_action( 'mb_hms_room_list_min_max_info', 'mb_hms_template_loop_room_min_max_info', 10 );
add_action( 'mb_hms_room_list_item_before_add_to_cart', 'mb_hms_template_loop_room_non_cancellable_info', 10 );

// Hide book button when booking_mode is set to 'no-booking'
if ( mbh_get_option( 'booking_mode' ) != 'no-booking' ) {
	add_action( 'mb_hms_room_list_item_add_to_cart', 'mb_hms_template_loop_room_add_to_cart', 10, 2 );
	add_action( 'mb_hms_reserve_button', 'mb_hms_template_loop_room_reserve_button', 10 );
}

add_action( 'mb_hms_room_list_item_rate', 'mb_hms_template_loop_room_rate', 10, 5 );
add_action( 'mb_hms_room_list_item_rate_content', 'mb_hms_template_loop_room_rate_name', 10, 2 );
add_action( 'mb_hms_room_list_item_rate_content', 'mb_hms_template_loop_room_rate_description', 15, 2 );
add_action( 'mb_hms_room_list_item_rate_content', 'mb_hms_template_loop_room_rate_conditions', 20, 2 );
add_action( 'mb_hms_room_list_item_rate_content', 'mb_hms_template_loop_room_rate_deposit', 25, 2 );
add_action( 'mb_hms_room_list_item_rate_content', 'mb_hms_template_loop_room_rate_min_max_info', 30, 2 );
add_action( 'mb_hms_room_list_item_rate_actions', 'mb_hms_template_loop_room_rate_price', 10, 4 );
add_action( 'mb_hms_room_list_item_rate_actions', 'mb_hms_template_loop_room_rate_non_cancellable_info', 12, 4 );

// Hide book button when booking_mode is set to 'no-booking'
if ( mbh_get_option( 'booking_mode' ) != 'no-booking' ) {
	add_action( 'mb_hms_room_list_item_rate_actions', 'mb_hms_template_loop_room_rate_add_to_cart', 15, 4 );
}


/**
 * Booking and form pay page
 */
add_action( 'mb_hms_booking_before_submit', 'mb_hms_privacy_policy_text', 5 );
add_action( 'mb_hms_booking_before_submit', 'mb_hms_template_terms_checkbox', 10 );
add_action( 'mb_hms_form_pay_before_submit', 'mb_hms_privacy_policy_text', 5 );
add_action( 'mb_hms_form_pay_before_submit', 'mb_hms_template_terms_checkbox', 10 );
add_action( 'mb_hms_reservation_table_guests', 'mb_hms_reservation_table_guests', 10, 3 );

/**
 * Reservation details
 */
add_action( 'mb_hms_received', 'mb_hms_template_reservation_table', 10 );
add_action( 'mb_hms_reservation_details', 'mb_hms_template_reservation_details', 10 );
add_action( 'mb_hms_after_reservation_table', 'mb_hms_template_guest_details', 10 );
add_action( 'mb_hms_after_reservation_table', 'mb_hms_template_cancel_reservation', 10 );
