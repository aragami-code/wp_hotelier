<?php
/**
 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Frontend_Scripts' ) ) :

/**
 * HTL_Frontend_Scripts Class
 */
class MBH_Frontend_Scripts {
	/**
	 * Construct.
	 */
	public function __construct() {
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_styles' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'frontend_scripts' ) );
	}

	/**
	 * Enqueue styles
	 *
	 * @access public
	 * @return void
	 */
	public function frontend_styles() {
		$default_style    = apply_filters( 'mb_hms_enqueue_styles', true );
		$lightbox_enabled = mbh_get_option( 'room_lightbox', true );

		if ( $lightbox_enabled && ( is_listing() || is_room() ) ) {
			wp_register_style( 'photoswipe', MBH_PLUGIN_URL . 'assets/css/photoswipe/photoswipe.css', array(), '4.1.1' );
			wp_enqueue_style( 'photoswipe-default-skin', MBH_PLUGIN_URL . 'assets/css/photoswipe/default-skin/default-skin.css', array( 'photoswipe' ), '4.1.1' );
		}

		if ( $default_style ) {
			wp_enqueue_style( 'mb_hms-css', MBH_PLUGIN_URL . 'assets/css/mb_hms.css', array(), MBH_VERSION );
		}
	}

	/**
	 * Enqueue scripts
	 *
	 * @access public
	 * @return void
	 */
	public function frontend_scripts() {
		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix           = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';
		$lightbox_enabled = mbh_get_option( 'room_lightbox', true );

		// Enqueue the main mb_hms script
		wp_enqueue_script( 'mb_hms-js', MBH_PLUGIN_URL . 'assets/js/frontend/mb_hms' . $suffix . '.js', array( 'jquery' ), MBH_VERSION, true );

		// Datepicker params
		$mb_hms_params = array(
			'book_now_redirect_to_booking_page' => mbh_get_option( 'book_now_redirect_to_booking_page', 0 ),
			'book_now_allow_quantity_selection' => mbh_get_option( 'book_now_allow_quantity_selection', 0 ),
		);

		wp_localize_script( 'mb_hms-js', 'mb_hms_params', $mb_hms_params );

		// Get first day of week (can be sunday or monday)
		$start_of_week = 'monday';
		if ( get_option( 'start_of_week' ) && ( get_option( 'start_of_week' ) === 1 ) ) {
			$start_of_week = 'sunday';
		}

		// Start date
		$arrival_date = mbh_get_option( 'booking_arrival_date', 0 );
		$start_date   = date( 'Y-m-d', strtotime( "+$arrival_date days" ) );

		// End date
		$end_date = false;

		// Check if months_advance is set.
		$months_advance = mbh_get_option( 'booking_months_advance', 0 );

		if ( $months_advance ) {
			$end_date = date( 'Y-m-d', strtotime( "+$months_advance months" ) );
		}

		// Create array of weekday names
		$timestamp       = strtotime('next Sunday');
		$day_names       = array();
		$day_names_short = array();

		for ( $i = 0; $i < 7; $i++ ) {
			$day_names[] = date_i18n( 'l', $timestamp );
			$day_names_short[] = date_i18n( 'D', $timestamp );
			$timestamp = strtotime('+1 day', $timestamp);
		}

		// Create array of month names (full textual and short)
		$month_names = array();
		$month_names_short = array();
		$timestamp = strtotime('2016-01-01');

		for ( $i = 0; $i < 12; $i++ ) {
			$month_names[] = date_i18n( 'F', $timestamp );
			$month_names_short[] = date_i18n( 'M', $timestamp );
			$timestamp = strtotime('+1 month', $timestamp);
		}

		// Datepicker params
		$datepicker_params = array(
			'ajax_url'           => MBH()->ajax_url(),
			'mbh_ajax_url'       => MBH_AJAX::get_endpoint( 'get_checkin_dates' ),
			'start_of_week'      => $start_of_week,
			'start_date'         => $start_date,
			'end_date'           => $end_date,
			'min_nights'         => apply_filters( 'mb_hms_datepicker_min_nights', mbh_get_option( 'booking_minimum_nights', 1 ) ),
			'max_nights'         => apply_filters( 'mb_hms_datepicker_max_nights', mbh_get_option( 'booking_maximum_nights', 0 ) ),
			'datepicker_format'  => apply_filters( 'mb_hms_datepicker_format', 'D MMM YYYY' ),
			'disabled_dates'     => apply_filters( 'mb_hms_datepicker_disabled_dates', array() ),
			'enable_checkout'    => apply_filters( 'mb_hms_datepicker_enable_checkout', true ),
			'disabled_days_of_week' => apply_filters( 'mb_hms_datepicker_disabled_days_of_week', array() ),
			'i18n'               => array(
				'selected'          => esc_html_x( 'Your stay:', 'datepicker_selected', 'wp-mb_hms' ),
				'night'             => esc_html_x( 'Night', 'datepicker_night', 'wp-mb_hms' ),
				'nights'            => esc_html_x( 'Nights', 'datepicker_nights', 'wp-mb_hms' ),
				'button'            => esc_html_x( 'Close', 'datepicker_apply', 'wp-mb_hms' ),
				'day-names'         => $day_names,
				'day-names-short'   => $day_names_short,
				'month-names'       => $month_names,
				'month-names-short' => $month_names_short,
				'error-more'        => esc_html_x( 'Date range should not be more than 1 night', 'datepicker_error_more', 'wp-mb_hms' ),
				'error-more-plural' => esc_html_x( 'Date range should not be more than %d nights', 'datepicker_error_more_plural', 'wp-mb_hms' ),
				'error-less'        => esc_html_x( 'Date range should not be less than 1 night', 'datepicker_error_less', 'wp-mb_hms' ),
				'error-less-plural' => esc_html_x( 'Date range should not be less than %d nights', 'datepicker_error_less_plural', 'wp-mb_hms' ),
				'info-more'         => esc_html_x( 'Please select a date range longer than 1 night', 'datepicker_info_more', 'wp-mb_hms' ),
				'info-more-plural'  => esc_html_x( 'Please select a date range longer than %d nights', 'datepicker_info_more_plural', 'wp-mb_hms' ),
				'info-range'        => esc_html_x( 'Please select a date range between %d and %d nights', 'datepicker_info_range', 'wp-mb_hms' ),
				'info-default'      => esc_html_x( 'Please select a date range', 'datepicker_info_default', 'wp-mb_hms' )
			)
		);

		// Localize and enqueue the datepicker scripts
		wp_register_script( 'fecha', MBH_PLUGIN_URL . 'assets/js/lib/fecha/fecha' . $suffix . '.js', array(), '2.3.0', true );
		wp_register_script( 'hotel-datepicker', MBH_PLUGIN_URL . 'assets/js/lib/hotel-datepicker/hotel-datepicker' . $suffix . '.js', array( 'fecha' ), '3.4.0', true );

		wp_register_script( 'mb_hms-init-datepicker', MBH_PLUGIN_URL . 'assets/js/frontend/mb_hms-init-datepicker' . $suffix . '.js', array( 'jquery', 'hotel-datepicker' ), MBH_VERSION, true );

		wp_localize_script( 'mb_hms-init-datepicker', 'datepicker_params', $datepicker_params );

		if ( is_listing() || is_room() ) {
			wp_enqueue_script( 'mb_hms-init-datepicker' );
		}

		// Lightbox scripts
		if ( $lightbox_enabled && ( is_listing() || is_room() ) ) {

			// PhotoSwipe
			wp_enqueue_script( 'photoswipe', MBH_PLUGIN_URL . 'assets/js/lib/photoswipe/photoswipe' . $suffix . '.js', array(), '4.1.1', true );
			wp_enqueue_script( 'photoswipe-ui', MBH_PLUGIN_URL . 'assets/js/lib/photoswipe/photoswipe-ui-default' . $suffix . '.js', array( 'photoswipe' ), '4.1.1', true );
			wp_enqueue_script( 'photoswipe-init', MBH_PLUGIN_URL . 'assets/js/frontend/photoswipe.init' . $suffix . '.js', array( 'jquery', 'photoswipe-ui' ), MBH_VERSION, true );
		}
	}

}

endif;

return new MBH_Frontend_Scripts();
