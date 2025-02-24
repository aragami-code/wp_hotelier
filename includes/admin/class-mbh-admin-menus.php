<?php
/**
 *
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Admin_Menus' ) ) :

/**
 * _Admin_Menus Class
 *
 * Creates the admin menu pages.
 */
class MBH_Admin_Menus {
	/**
	 * Constructor.
	 */
	public function __construct() {
		// Add mb_hms pages to WP menu
		add_action( 'admin_menu', array( $this, 'admin_menu' ), 9 );
		add_action( 'admin_menu', array( $this, 'admin_calendar' ), 30 );
		add_action( 'admin_menu', array( $this, 'hum' ), 40);
		add_action( 'admin_menu', array( $this, 'hum2' ), 50);

	}

	/**
	 * Add settings page
	 */


		public function hum() {
		add_submenu_page( 
		'mb_hms-settings',
		'Manage Rooms',
		'Manage Rooms',
		'edit_posts',
		'edit.php?post_type=room'
		 
		 //'manage_mb_hms',
		   );
		add_submenu_page( 
		'mb_hms-settings',
		'Categories Rooms',
		'Categories Rooms',
		'edit_posts',
		'edit-tags.php?taxonomy=room_cat&post_type=room'
		 
		 //'manage_mb_hms',
		   );

		

		add_submenu_page( 
		'mb_hms-settings',
		'Facilities',
		'Facilities',
		'manage_options',
		'edit-tags.php?taxonomy=room_facilities&post_type=room',
		null
		 
		 //'manage_mb_hms',
		   );
	}
	public function hum2() {

		add_submenu_page( 
		'mb_hms-settings',
		'Rates',
		'Rates',
		'manage_categories',
		'edit-tags.php?taxonomy=room_rate&post_type=room',null
		 
		 //'manage_mb_hms',
		   );

	}

	public function admin_menu() {
		add_submenu_page( 'mb_hms-settings',
		 esc_html__( 'Add New Reservation', 'wp-mb_hms' ),
		 esc_html__( 'Add Reservation', 'wp-mb_hms' ) ,
		 'manage_mb_hms', 'mb_hms-add-reservation',
		  array( $this, 'add_new_reservation_page' ) );
	}

	/**
	 * Add calendar page
	 */
	public function admin_calendar() {
		add_submenu_page( 'mb_hms-settings', esc_html__( 'Calendar', 'wp-mb_hms' ),  esc_html__( 'View Calendar', 'wp-mb_hms' ) , 'manage_mb_hms', 'mb_hms-calendar', array( $this, 'calendar_page' ) );
	}

	/**
	 * Init the 'Add New Reservation' page
	 */
	public function add_new_reservation_page() {
		MBH_Admin_New_Reservation::output();
	}

	/**
	 * Init the 'Calendar' page
	 */
	public function calendar_page() {
		MBH_Admin_Calendar::output();
	}

}

endif;

return new MBH_Admin_Menus();
