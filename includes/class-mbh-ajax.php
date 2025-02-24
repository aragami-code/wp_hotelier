<?php
/**
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Ajax' ) ) :

/**
 * MBH_Ajax Class
 */
class MBH_Ajax {

	/**
	 * Hook in ajax handlers.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'define_ajax' ), 0 );
		add_action( 'template_redirect', array( __CLASS__, 'do_ajax' ), 0 );
		self::add_ajax_events();
	}

	/**
	 * Set MBH AJAX constant and headers.
	 */
	public static function define_ajax() {
		if ( ! empty( $_GET[ 'mbh-ajax' ] ) ) {
			if ( ! defined( 'DOING_AJAX' ) ) {
				define( 'DOING_AJAX', true );
			}

			// Turn off display_errors during AJAX events to prevent malformed JSON
			if ( ! WP_DEBUG || ( WP_DEBUG && ! WP_DEBUG_DISPLAY ) ) {
				@ini_set( 'display_errors', 0 );
			}
			$GLOBALS[ 'wpdb' ]->hide_errors();
		}
	}

	/**
	 * Check for Ajax request and fire action.
	 */
	public static function do_ajax() {
		global $wp_query;

		if ( ! empty( $_GET[ 'mbh-ajax' ] ) ) {
			$wp_query->set( 'mbh-ajax', sanitize_text_field( $_GET[ 'mbh-ajax' ] ) );
		}

		if ( $action = $wp_query->get( 'mbh-ajax' ) ) {
			self::ajax_headers();
			do_action( 'mbh_ajax_' . sanitize_text_field( $action ) );
			die();
		}
	}

	/**
	 * Send headers for Ajax Requests
	 * @since 2.5.0
	 */
	private static function ajax_headers() {
		send_origin_headers();
		@header( 'Content-Type: text/html; charset=' . get_option( 'blog_charset' ) );
		@header( 'X-Robots-Tag: noindex' );
		send_nosniff_header();
		nocache_headers();
		status_header( 200 );
	}

	/**
	 * Get HTL Ajax Endpoint.
	 * @param  string $request Optional
	 * @return string
	 */
	public static function get_endpoint( $request = '' ) {
		return esc_url_raw( apply_filters( 'mb_hms_ajax_get_endpoint', add_query_arg( 'mbh-ajax', $request ), $request ) );
	}

	/**
	 * Hook in methods - uses WordPress ajax handlers (admin-ajax).
	 */
	public static function add_ajax_events() {
		$ajax_events = array(
			'get_checkin_dates' => true,
		);

		foreach ( $ajax_events as $ajax_event => $nopriv ) {
			add_action( 'wp_ajax_mb_hms_' . $ajax_event, array( __CLASS__, $ajax_event ) );

			if ( $nopriv ) {
				add_action( 'wp_ajax_nopriv_mb_hms_' . $ajax_event, array( __CLASS__, $ajax_event ) );

				// mbh AJAX can be used for frontend ajax requests
				add_action( 'mbh_ajax_' . $ajax_event, array( __CLASS__, $ajax_event ) );
			}
		}
	}

	/**
	 * Get checkin and checkout dates.
	 */
	public static function get_checkin_dates() {
		$data = array(
			'checkin'  => MBH()->session->get( 'checkin' ),
			'checkout' => MBH()->session->get( 'checkout' )
		);

		wp_send_json( $data );
		die();
	}
}

endif;

MBH_Ajax::init();
