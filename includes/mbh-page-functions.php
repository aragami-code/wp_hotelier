<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get page ids / returns -1 if page is not found
 *
 * @param string $page
 * @return int
 */
function mbh_get_page_id( $page ) {
	$page = apply_filters( 'mb_hms_get_' . $page . '_page_id', get_option( 'mb_hms_' . $page . '_page_id' ) );

	return $page ? absint( $page ) : -1;
}

/**
 * Get page permalink
 *
 * @param string $page
 * @return string
 */
function mbh_get_page_permalink( $page ) {
	$page_id   = mbh_get_page_id( $page );
	$permalink = $page_id ? get_permalink( $page_id ) : '';
	return apply_filters( 'mb_hms_get_' . $page . '_page_permalink', $permalink );
}

/**
 * Replace a page title with the endpoint title
 * @param  string $title
 * @return string
 */
function mbh_page_endpoint_title( $title ) {
	global $wp_query;

	if ( ! is_null( $wp_query ) && ! is_admin() && is_main_query() && in_the_loop() && is_page() && is_mbh_endpoint_url() ) {
		$endpoint = MBH()->query->get_current_endpoint();

		if ( $endpoint_title = MBH()->query->get_endpoint_title( $endpoint ) ) {
			$title = $endpoint_title;
		}

		remove_filter( 'the_title', 'mbh_page_endpoint_title' );
	}

	return $title;
}
add_filter( 'the_title', 'mbh_page_endpoint_title' );

/**
 * Get endpoint URL
 *
 * Gets the URL for an endpoint, which varies depending on permalink settings.
 *
 * @return string
 */
function mbh_get_endpoint_url( $endpoint, $value = '', $permalink = '' ) {
	if ( ! $permalink )
		$permalink = get_permalink();

	// Map endpoint to options
	$endpoint = isset( MBH()->query->query_vars[ $endpoint ] ) ? MBH()->query->query_vars[ $endpoint ] : $endpoint;

	if ( get_option( 'permalink_structure' ) ) {
		if ( strstr( $permalink, '?' ) ) {
			$query_string = '?' . parse_url( $permalink, PHP_URL_QUERY );
			$permalink    = current( explode( '?', $permalink ) );
		} else {
			$query_string = '';
		}
		$url = trailingslashit( $permalink ) . $endpoint . '/' . $value . $query_string;
	} else {
		$url = add_query_arg( $endpoint, $value, $permalink );
	}

	return apply_filters( 'mb_hms_get_endpoint_url', $url, $endpoint, $value, $permalink );
}
