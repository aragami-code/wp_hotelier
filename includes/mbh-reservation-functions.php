<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Main function for returning reservations.
 *
 * @param  mixed $the_reservation Post object or post ID of the reservation.
 * @return HTL_Reservation
 */
function mbh_get_reservation( $the_reservation = false ) {
	return new MBH_Reservation( $the_reservation );
}

/**
 * Create a new reservation programmatically
 *
 * Returns a new reservation object on success which can then be used to add additional data.
 *
 * @return HTL_Reservation on success, WP_Error on failure
 */
function mbh_create_reservation( $args = array() ) {
	$default_args = array(
		'status'           => '',
		'guest_name'       => '',
		'email'            => '',
		'special_requests' => null,
		'reservation_id'   => 0,
		'created_via'      => '',
		'parent'           => 0
	);

	$args             = wp_parse_args( $args, $default_args );
	$reservation_data = array();

	if ( $args[ 'reservation_id' ] > 0 ) {
		$updating                 = true;
		$reservation_data[ 'ID' ] = $args[ 'reservation_id' ];
	} else {
		$updating                            = false;
		$reservation_data[ 'post_type' ]     = 'room_reservation';
		$reservation_data[ 'post_author' ]   = 1;
		$reservation_data[ 'post_status' ]   = 'mbh-pending';
		$reservation_data[ 'ping_status' ]   = 'closed';
		$reservation_data[ 'post_title' ]    = wp_strip_all_tags( $args[ 'guest_name' ] );
		$reservation_data[ 'post_parent' ]   = absint( $args[ 'parent' ] );
	}

	if ( $args[ 'status' ] ) {
		if ( ! in_array( 'mbh-' . $args[ 'status' ], array_keys( mbh_get_reservation_statuses() ) ) ) {
			return new WP_Error( 'mb_hms_invalid_reservation_status', esc_html__( 'Invalid reservation status', 'wp-mb_hms' ) );
		}

		$reservation_data[ 'post_status' ]  = 'mbh-' . $args[ 'status' ];
	}

	if ( ! is_null( $args[ 'special_requests' ] ) ) {
		$reservation_data[ 'post_excerpt' ] = $args[ 'special_requests' ];
	}

	if ( $updating ) {
		$reservation_id = wp_update_post( $reservation_data );
	} else {
		$reservation_id = wp_insert_post( apply_filters( 'mb_hms_new_reservation_data', $reservation_data ), true );

		// insert the reservation ID before the title ( #123 - John Doe )
		$reservation_title = '#' . $reservation_id . ' - ' . get_the_title( $reservation_id );
		$reservation_id = wp_update_post( array( 'ID' => $reservation_id, 'post_title' => apply_filters( 'mb_hms_new_reservation_title', sanitize_text_field( $reservation_title ) ) ) );
	}

	if ( is_wp_error( $reservation_id ) ) {
		return $reservation_id;
	}

	if ( ! $updating ) {
		update_post_meta( $reservation_id, '_reservation_key', 'mbh_' . apply_filters( 'mb_hms_generate_reservation_key', uniqid( 'reservation_' ) ) );
		update_post_meta( $reservation_id, '_reservation_guest_ip_address', mbh_get_ip() );
		update_post_meta( $reservation_id, '_reservation_currency', mbh_get_currency() );
		update_post_meta( $reservation_id, '_created_via', sanitize_text_field( $args[ 'created_via' ] ) );
	}

	return mbh_get_reservation( $reservation_id );
}

/**
 * Update a reservation. Uses mbh_create_reservation.
 *
 * @param  array $args
 * @return string | mbh_Reservation
 */
function mbh_update_reservation( $args ) {
	if ( ! $args[ 'reservation_id' ] ) {
		return new WP_Error( esc_html__( 'Invalid reservation ID', 'wp-mb_hms' ) );
	}

	return mbh_create_reservation( $args );
}

/**
 * Get all reservation statuses
 *
 * @return array
 */
function mbh_get_reservation_statuses() {
	$reservation_statuses = array(
		'mbh-completed'  => esc_html_x( 'Completed', 'Reservation status', 'wp-mb_hms' ),
		'mbh-confirmed'  => esc_html_x( 'Confirmed', 'Reservation status', 'wp-mb_hms' ),
		'mbh-pending'    => esc_html_x( 'Pending', 'Reservation status', 'wp-mb_hms' ),
		'mbh-on-hold'    => esc_html_x( 'On Hold', 'Reservation status', 'wp-mb_hms' ),
		'mbh-cancelled'  => esc_html_x( 'Cancelled', 'Reservation status', 'wp-mb_hms' ),
		'mbh-refunded'   => esc_html_x( 'Refunded', 'Reservation status', 'wp-mb_hms' ),
		'mbh-failed'     => esc_html_x( 'Failed', 'Reservation status', 'wp-mb_hms' )
	);

	return apply_filters( 'mb_hms_reservation_statuses', $reservation_statuses );
}

/**
 * Adds reservation data to the bookings table.
 *
 * @access public
 * @param int $reservation_id
 * @param string $checkin
 * @param string $checkout
 * @param string $status
 * @param bool $force
 * @return mixed
 */
function mbh_add_booking( $reservation_id, $checkin, $checkout, $status, $force = false ) {
	global $wpdb;

	$reservation_id = absint( $reservation_id );

	if ( ! $reservation_id || ! $checkin || ! $checkout ) {
		return false;
	}

	// Check dates
	if ( ! MBH_Formatting_Helper::is_valid_checkin_checkout( $checkin, $checkout, $force ) ) {
		return false;
	}

	// Check status
	if ( ! in_array( 'mbh-' . $status, array_keys( mbh_get_reservation_statuses() ) ) ) {
		$status = 'pending';
	}

	$wpdb->insert(
		$wpdb->prefix . "mb_hms_bookings",
		array(
			'reservation_id' => $reservation_id,
			'checkin'        => $checkin,
			'checkout'       => $checkout,
			'status'         => $status,
		),
		array(
			'%s', '%s', '%s', '%s'
		)
	);

	$row_id = absint( $wpdb->insert_id );

	do_action( 'mb_hms_new_booking_row', $row_id, $reservation_id, $checkin, $checkout );

	return $row_id;
}

/**
 * Populate rooms_bookings table.
 *
 * @access public
 * @param int $reservation_id
 * @param int $room_id
 * @return mixed
 */
function mbh_populate_rooms_bookings( $reservation_id, $room_id ) {
	global $wpdb;

	$reservation_id = absint( $reservation_id );
	$room_id        = absint( $room_id );

	if ( ! $reservation_id || ! $room_id ) {
		return false;
	}

	$wpdb->insert(
		$wpdb->prefix . "mb_hms_rooms_bookings",
		array(
			'reservation_id' => $reservation_id,
			'room_id'        => $room_id
		),
		array(
			'%d', '%d'
		)
	);

	$row_id = absint( $wpdb->insert_id );

	do_action( 'mb_hms_new_rooms_bookings_row', $row_id, $reservation_id, $room_id );

	return $row_id;
}

/**
 * Add a room to a reservation.
 *
 * @access public
 * @param int $reservation_id
 * @return mixed
 */
function mbh_add_reservation_item( $reservation_id, $item ) {
	global $wpdb;

	$reservation_id = absint( $reservation_id );

	if ( ! $reservation_id ) {
		return false;
	}

	$defaults = array(
		'reservation_item_name' => ''
	);

	$item = wp_parse_args( $item, $defaults );

	$wpdb->insert(
		$wpdb->prefix . "mb_hms_reservation_items",
		array(
			'reservation_item_name' => $item[ 'reservation_item_name' ],
			'reservation_id'        => $reservation_id
		),
		array(
			'%s', '%d'
		)
	);

	$item_id = absint( $wpdb->insert_id );

	do_action( 'mb_hms_new_reservation_item', $item_id, $item, $reservation_id );

	return $item_id;
}

/**
 * Add reservation item meta
 *
 * @access public
 * @param mixed $item_id
 * @param mixed $meta_key
 * @param mixed $meta_value
 * @param bool $unique (default: false)
 * @return bool
 */
function mbh_add_reservation_item_meta( $item_id, $meta_key, $meta_value, $unique = false ) {
	return add_metadata( 'reservation_item', $item_id, $meta_key, $meta_value, $unique );
}

/**
 * Set table name
 */
function mbh_taxonomy_metadata_wpdbfix() {
	global $wpdb;
	$itemmeta_name = 'mb_hms_reservation_itemmeta';

	$wpdb->reservation_itemmeta = $wpdb->prefix . $itemmeta_name;

	$wpdb->tables[] = 'mb_hms_reservation_itemmeta';
}
add_action( 'init', 'mbh_taxonomy_metadata_wpdbfix', 0 );
add_action( 'switch_blog', 'mbh_taxonomy_metadata_wpdbfix', 0 );

/**
 * Get the nice name for a reservation status
 *
 * @param  string $status
 * @return string
 */
function mbh_get_reservation_status_name( $status ) {
	$statuses = mbh_get_reservation_statuses();
	$status   = 'mbh-' === substr( $status, 0, 4 ) ? substr( $status, 4 ) : $status;
	$status   = isset( $statuses[ 'mbh-' . $status ] ) ? $statuses[ 'mbh-' . $status ] : $status;

	return $status;
}

/**
 * Get the nice name of a rate name
 *
 * @param  string $rate
 * @return string
 */
function mbh_get_formatted_room_rate( $rate ) {
	$term = term_exists( $rate, 'room_rate' );

	if ( $term !== 0 && $term !== null ) {
		$rate = get_term_by( 'id', $term[ 'term_id' ], 'room_rate' );
		$rate = $rate->name;
	} else {
		$rate = ucfirst( str_replace('-', ' ', $rate ) );
	}

	return $rate;
}

/**
 * Finds a Reservation ID based on an reservation key.
 *
 * @access public
 * @param string $reservation_key An reservation key has generated by
 * @return int The ID of an reservation, or 0 if the reservation could not be found
 */
function mbh_get_reservation_id_by_reservation_key( $reservation_key ) {
	global $wpdb;

	// Faster than get_posts()
	$reservation_id = $wpdb->get_var( $wpdb->prepare( "SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_reservation_key' AND meta_value = %s", $reservation_key ) );

	return $reservation_id;
}

/**
 * Cancel all pending reservations after hold minutes.
 * Please note: for "pending" we mean "unpaid reservations" (pending and failed) in this case.
 *
 * @access public
 */
function mbh_cancel_pending_reservations() {
	global $wpdb;

	$hold_minutes = mbh_get_option( 'booking_hold_minutes', '60' );

	if ( $hold_minutes < 1 ) {
		return;
	}

	$date = date( "Y-m-d H:i:s", strtotime( '-' . absint( $hold_minutes ) . ' MINUTES', current_time( 'timestamp' ) ) );

	$pending_reservations = $wpdb->get_col( $wpdb->prepare( "
		SELECT posts.ID
		FROM {$wpdb->posts} AS posts
		WHERE 	posts.post_type   IN ('room_reservation')
		AND 	posts.post_status = 'mbh-pending'
		OR 		posts.post_status = 'mbh-failed'
		AND 	posts.post_modified < %s
	", $date ) );

	if ( $pending_reservations ) {
		foreach ( $pending_reservations as $pending_reservation ) {
			$reservation = mbh_get_reservation( $pending_reservation );

			if ( apply_filters( 'mb_hms_cancel_pending_reservation', 'booking' === get_post_meta( $pending_reservation, '_created_via', true ), $reservation ) ) {
				$reservation->update_status( 'cancelled', esc_html__( 'Time limit reached.', 'wp-mb_hms' ) );
			}
		}
	}

	wp_clear_scheduled_hook( 'mb_hms_cancel_pending_reservations' );
	wp_schedule_single_event( time() + ( absint( $hold_minutes ) * 60 ), 'mb_hms_cancel_pending_reservations' );
}
add_action( 'mb_hms_cancel_pending_reservations', 'mbh_cancel_pending_reservations' );

/**
 * Process reservations after checkout date.
 *
 * @access public
 */
function mbh_process_completed_reservations() {
	global $wpdb;

	$date = date( 'Y-m-d' );

	$completed_reservations = $wpdb->get_col( $wpdb->prepare( "
		SELECT reservations.reservation_id
		FROM {$wpdb->prefix}mb_hms_bookings AS reservations
		WHERE 	reservations.checkout < %s
	", $date ) );

	if ( $completed_reservations ) {
		foreach ( $completed_reservations as $completed_reservation ) {
			$reservation = mbh_get_reservation( $completed_reservation );

			// Skip already completed or cancelled/refunded reservations
			if ( $reservation->get_status() == 'completed' || $reservation->get_status() == 'cancelled' || $reservation->get_status() == 'refunded' ) {
				return;
			}

			if ( $reservation->get_status() == 'confirmed' ) {
				$reservation->update_status( 'completed', esc_html__( 'Check-out date reached.', 'wp-mb_hms' ) );
			} else {
				$reservation->update_status( 'cancelled', esc_html__( 'Check-out date reached.', 'wp-mb_hms' ) );
			}
		}
	}

}
add_action( 'mb_hms_process_completed_reservations', 'mbh_process_completed_reservations' );

/**
 * Function that returns an array containing all reservations made on a specific date range.
 *
 * @access public
 * @param string $checkin
 * @param string $checkout
 * @return array (reservation_id, checkin, checkout and status)
 */
function mbh_get_all_reservations( $checkin, $checkout ) {
	$reservations = array();
	$all_rooms = apply_filters( 'mb_hms_get_room_ids_for_reservations', mbh_get_room_ids() );

	foreach ( $all_rooms as $room_id ) {
		$room_reservations        = mbh_get_room_reservations( $room_id, $checkin, $checkout );
		$reservations[ $room_id ] = $room_reservations;
	}

	return $reservations;
}

/**
 * Get reservations by guest email.
 *
 * @since 1.6.0
 * @param array $email
 * @return Array of reservation objects
 */
function mbh_get_reservations_by_email( $email ) {
	$reservations_objects = array();

	if ( ! $email ) {
		return $reservations_objects;
	}

	$query_args = array(
		'post_type'           => 'room_reservation',
		'ignore_sticky_posts' => 1,
		'posts_per_page'      => -1,
		'meta_query'          => array(
			array(
				'key'   => '_guest_email',
				'value' => $email,
			),
		),
	);

	$reservations = new WP_Query( $query_args );

	if ( $reservations->have_posts() ) {

		while ( $reservations->have_posts() ) {
			$reservations->the_post();
			global $post;

			$reservations_objects[] = mbh_get_reservation( $post->ID );
		}

		wp_reset_postdata();
	}

	return $reservations_objects;
}
