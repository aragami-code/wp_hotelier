<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Admin_Scripts' ) ) :

/**
 * MBH_Admin_Scripts Class
 */
class MBH_Admin_Scripts {
	/**
	 * Construct.
	 */
	public function __construct() {
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_styles' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_scripts' ) );
	}

	/**
	 * Enqueue styles
	 *
	 * @access public
	 * @return void
	 */
	public function admin_styles() {
		$screen = get_current_screen();
		$prefix = MBH_Admin_Functions::get_prefix_screen_id();

		// Menu icon
		wp_enqueue_style( 'mb_hms_menu_styles', MBH_PLUGIN_URL . 'assets/css/menu.css', array(), MBH_VERSION );

		// jquery-ui
		wp_register_style( 'jquery-ui-css', MBH_PLUGIN_URL . 'assets/css/jquery-ui.css', array(), MBH_VERSION );

		// Tipsy
		wp_register_style( 'tipsy-css', MBH_PLUGIN_URL . 'assets/css/tipsy.css', array(), MBH_VERSION );

		if ( in_array( $screen->id, MBH_Admin_Functions::get_screen_ids() ) ) {

			if ( $screen->id != $prefix . '_mb_hms-calendar' ) {
				// Admin styles for mb_hms pages only
				wp_enqueue_style( 'mb_hms_admin_styles', MBH_PLUGIN_URL . 'assets/css/admin.css', array(), MBH_VERSION );
				wp_enqueue_style( 'tipsy-css' );
			}


		}

		// Settings, new reservation and calendar pages only
		if ( $screen->id == 'toplevel_page_mb_hms-settings' || $screen->id == $prefix . '_mb_hms-calendar' || $screen->id == $prefix . '_mb_hms-add-reservation' ) {
			wp_enqueue_style( 'jquery-ui-css' );
		}

		// Booking calendar style
		if ( $screen->id == $prefix . '_mb_hms-calendar' ) {
			wp_enqueue_style( 'mb_hms_calendar_styles', MBH_PLUGIN_URL . 'assets/css/calendar.css', array(), MBH_VERSION );
			wp_enqueue_style( 'tipsy-css' );
		}

		// Addons page style
		//if ( $screen->id == $prefix . '_mb_hms-addons' ) {
		//	wp_enqueue_style( 'mb_hms_addons_styles', MBH_PLUGIN_URL .// 'assets/css/addons.css', array(), MBH_VERSION );
		//}
	}

	/**
	 * Enqueue scripts
	 *
	 * @access public
	 * @return void
	 */
	public function admin_scripts() {
		$screen = get_current_screen();
		$prefix = MBH_Admin_Functions::get_prefix_screen_id();

		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

		// Register scripts

		wp_register_script( 'mbh-admin-settings', MBH_PLUGIN_URL . 'assets/js/admin/settings' . $suffix . '.js', array( 'jquery' ), MBH_VERSION );
		wp_register_script( 'mbh-admin-meta-boxes', MBH_PLUGIN_URL . 'assets/js/admin/meta-boxes' . $suffix . '.js', array( 'jquery', 'jquery-ui-sortable', 'jquery-tipsy' ), MBH_VERSION );
		wp_register_script( 'jquery-tipsy', MBH_PLUGIN_URL . 'assets/js/lib/jquery-tipsy/jquery-tipsy' . $suffix . '.js', array( 'jquery' ), MBH_VERSION );

		// Admin settings
		if ( $screen->id == 'toplevel_page_mb_hms-settings' ) {
			wp_enqueue_media();
			wp_enqueue_script( 'mbh-admin-settings' );
		}

		// Admin, new reservation and calendar
		if ( $screen->id == 'toplevel_page_mb_hms-settings' || $screen->id == $prefix . '_mb_hms-calendar' || $screen->id == $prefix . '_mb_hms-add-reservation' ) {
			wp_enqueue_script( 'jquery-ui-datepicker' );
		}

		// Room meta boxes
		if ( in_array( $screen->id, array( 'room', 'edit-room' ) ) ) {
			wp_register_script( 'accounting', MBH_PLUGIN_URL . 'assets/js/lib/accounting/accounting' . $suffix . '.js', array( 'jquery' ), '0.4.2' );

			$room_params = array(
				'decimal_error'                => sprintf( esc_html__( 'Please enter in decimal (%s) format without thousand separators.', 'wp-mb_hms' ), mbh_get_price_decimal_separator() ),
				'sale_less_than_regular_error'  => esc_html__( 'Please enter in a value less than the regular price.', 'wp-mb_hms' ),
				'decimal_point'                     => mbh_get_price_decimal_separator()
			);

			wp_register_script( 'mbh-admin-room-meta-boxes', MBH_PLUGIN_URL . 'assets/js/admin/meta-boxes-room' . $suffix . '.js', array( 'mbh-admin-meta-boxes', 'accounting' ), MBH_VERSION );

			wp_localize_script( 'mbh-admin-room-meta-boxes', 'room_params', $room_params );

			wp_enqueue_script( 'mbh-admin-room-meta-boxes' );
		}

		// Reservation meta boxes
		if ( in_array( $screen->id, array( 'room_reservation', 'edit-room_reservation' ) ) ) {
			$reservation_params = array(
				'i18n_do_remain_deposit_charge' => esc_html__( 'Are you sure you wish to proceed with this charge? This action cannot be undone.', 'wp-mb_hms' )
			);

			wp_enqueue_script( 'mbh-admin-reservation-meta-boxes', MBH_PLUGIN_URL . 'assets/js/admin/meta-boxes-reservation' . $suffix . '.js', array( 'mbh-admin-meta-boxes' ), MBH_VERSION );

			wp_localize_script( 'mbh-admin-reservation-meta-boxes', 'reservation_meta_params', $reservation_params );
		}

		// New reservation
		if ( $screen->id == $prefix . '_mb_hms-add-reservation' ) {
			wp_enqueue_script( 'mbh-admin-add-reservation', MBH_PLUGIN_URL . 'assets/js/admin/new-reservation' . $suffix . '.js', array( 'jquery' ), MBH_VERSION );
		}

		// Calendar script
		if ( $screen->id == $prefix . '_mb_hms-calendar' ) {
			wp_enqueue_script( 'mbh-admin-calendar', MBH_PLUGIN_URL . 'assets/js/admin/calendar' . $suffix . '.js', array( 'jquery', 'jquery-tipsy' ), MBH_VERSION );
		}
	}

}

endif;

return new MBH_Admin_Scripts();
