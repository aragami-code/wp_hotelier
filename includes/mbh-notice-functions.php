<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Add and store a notice.
 */
function mbh_add_notice( $message, $notice_type = 'notice' ) {
	$notices = MBH()->session->get( 'mbh_notices', array() );
	$notices[ $notice_type ][] = apply_filters( 'mb_hms_add_' . $notice_type, $message );

	MBH()->session->set( 'mbh_notices', $notices );
}

/**
 * Print notices.
 */
function mbh_print_notices() {
	$all_notices  = MBH()->session->get( 'mbh_notices', array() );
	$notice_types = apply_filters( 'mb_hms_notice_types', array( 'error', 'notice' ) );

	foreach ( $notice_types as $notice_type ) {
		if ( mbh_notice_count( $notice_type ) > 0 ) {
			mbh_get_template( "notices/{$notice_type}.php", array(
				'messages' => $all_notices[ $notice_type ]
			) );
		}
	}

	mbh_clear_notices();
}

/**
 * Get the count of notices added.
 */
function mbh_notice_count( $notice_type = '' ) {
	$notice_count = 0;
	$notices  = MBH()->session->get( 'mbh_notices', array() );

	if ( isset( $notices[ $notice_type ] ) ) {

		$notice_count = absint( sizeof( $notices[ $notice_type ] ) );

	} elseif ( empty( $notice_type ) ) {

		foreach ( $notices as $notice ) {
			$notice_count += absint( sizeof( $notices ) );
		}

	}

	return $notice_count;
}

/**
 * Unset all notices.
 */
function mbh_clear_notices() {
	MBH()->session->set( 'mbh_notices', null );
}
