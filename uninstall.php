<?php

if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

$mb_hms_options = get_option( 'mb_hms_settings' );

if ( ! empty( $mb_hms_options[ 'remove_data_uninstall' ] ) ) {

	// Roles + caps
	include_once( 'includes/class-mbh-roles.php' );
	$roles = new mbh_Roles;
	$roles->remove_roles();

	global $wpdb;

	// Pages.
	wp_trash_post( get_option( 'mb_hms_booking_page_id' ) );
	wp_trash_post( get_option( 'mb_hms_listing_page_id' ) );

	// Tables.
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mb_hms_bookings" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mb_hms_reservation_itemmeta" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mb_hms_reservation_items" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mb_hms_rooms_bookings" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mb_hms_sessions" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mb_hms_bookings" );
	$wpdb->query( "DROP TABLE IF EXISTS {$wpdb->prefix}mb_hms_bookings" );

	// Delete options
	$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE 'mb_hms\_%';");

	// Delete posts + meta.
	$wpdb->query( "DELETE FROM {$wpdb->posts} WHERE post_type IN ( 'room', 'room_reservation' );" );
	$wpdb->query( "DELETE meta FROM {$wpdb->postmeta} meta LEFT JOIN {$wpdb->posts} posts ON posts.ID = meta.post_id WHERE posts.ID IS NULL;" );

	// Delete cron jobs when uninstalling
	wp_clear_scheduled_hook( 'mb_hms_cancel_pending_reservations' );
	wp_clear_scheduled_hook( 'mb_hms_process_completed_reservations' );
	wp_clear_scheduled_hook( 'mb_hms_cleanup_sessions' );
	wp_clear_scheduled_hook( 'mb_hms_check_license_cron' );
}
