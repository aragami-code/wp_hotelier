<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Post_Types' ) ) :

/**
 * HTL_Post_Types Class
 */
class MBH_Post_Types {

	/**
	 * Constructor.
	 */
	public function __construct() {
		add_action( 'init', array( $this, 'register_taxonomies' ), 5 );
		add_action( 'init', array( $this, 'register_post_types' ), 5 );
		add_action( 'init', array( $this, 'register_post_status' ), 9 );
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_filter( 'gutenberg_can_edit_post_type', array( $this, 'block_editor_can_edit_post_type' ), 10, 2 );
		add_filter( 'use_block_editor_for_post_type', array( $this, 'block_editor_can_edit_post_type' ), 10, 2 );
	}

	/**
	 * Register core post types.
	 */
	public function register_post_types() {
		if ( post_type_exists( 'room' ) ) {
			return;
		}

		$archives = defined( 'MBH_DISABLE_ARCHIVE' ) && MBH_DISABLE_ARCHIVE ? false : true;
		$slug     = defined( 'MBH_SLUG' ) ? MBH_SLUG : 'rooms';
		$rewrite  = defined( 'MBH_DISABLE_REWRITE' ) && MBH_DISABLE_REWRITE ? false : array( 'slug' => $slug, 'with_front' => false );

		do_action( 'mb_hms_register_post_type' );

		// Room Post Type
		$room_labels =  apply_filters( 'mb_hms_room_labels', array(
			'name'               => esc_html_x( 'Rooms', 'room post type name', 'wp-mb_hms' ),
			'singular_name'      => esc_html_x( 'Room', 'singular room post type name', 'wp-mb_hms' ),
			'add_new'            => esc_html__( 'Add New', 'wp-mb_hms' ),
			'add_new_item'       => esc_html__( 'Add New Room', 'wp-mb_hms' ),
			'edit_item'          => esc_html__( 'Edit Room', 'wp-mb_hms' ),
			'new_item'           => esc_html__( 'New Room', 'wp-mb_hms' ),
			'all_items'          => esc_html__( 'All Rooms', 'wp-mb_hms' ),
			'view_item'          => esc_html__( 'View Room', 'wp-mb_hms' ),
			'search_items'       => esc_html__( 'Search Rooms', 'wp-mb_hms' ),
			'not_found'          => esc_html__( 'No Rooms found', 'wp-mb_hms' ),
			'not_found_in_trash' => esc_html__( 'No Rooms found in Trash', 'wp-mb_hms' ),
			'parent_item_colon'  => '',
			'menu_name'          => esc_html_x( 'Rooms', 'room post type menu name', 'wp-mb_hms' )
		) );

		$room_args = array(
			'labels'             => $room_labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => false,
			'menu_position'      => 46,
			'query_var'          => true,
			'rewrite'            => $rewrite,
			'capability_type'    => 'room',
			'map_meta_cap'       => true,
			'has_archive'        => $archives,
			'hierarchical'       => false,
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt', 'page-attributes', 'publicize' ),
		);
		register_post_type( 'room', apply_filters( 'mb_hms_room_post_type_args', $room_args ) );

		// Reservation Post Type
		$reservation_labels = apply_filters( 'mb_hms_reservation_labels', array(
			'name'               => esc_html_x( 'Reservations', 'post type general name', 'wp-mb_hms' ),
			'singular_name'      => esc_html_x( 'Reservation', 'post type singular name', 'wp-mb_hms' ),
			'add_new'            => esc_html__( 'Add Reservation', 'wp-mb_hms' ),
			'add_new_item'       => esc_html__( 'Add New Reservation', 'wp-mb_hms' ),
			'edit'               => esc_html__( 'Edit', 'wp-mb_hms' ),
			'edit_item'          => esc_html__( 'Edit Reservation', 'wp-mb_hms' ),
			'new_item'           => esc_html__( 'New Reservation', 'wp-mb_hms' ),
			'view'               => esc_html__( 'View Reservation', 'wp-mb_hms' ),
			'view_item'          => esc_html__( 'View Reservation', 'wp-mb_hms' ),
			'search_items'       => esc_html__( 'Search Reservations', 'wp-mb_hms' ),
			'not_found'          => esc_html__( 'No Reservations found', 'wp-mb_hms' ),
			'not_found_in_trash' => esc_html__( 'No Reservations found in Trash', 'wp-mb_hms' ),
			'parent'             => esc_html__( 'Parent Reservation', 'wp-mb_hms' ),
			'menu_name'          => esc_html_x( 'Reservations', 'admin menu name', 'wp-mb_hms' )
		) );

		$reservation_args = array(
			'labels'              => apply_filters( 'mb_hms_reservation_labels', $reservation_labels ),
			'description'         => esc_html__( 'This is where hotel reservations are stored.', 'wp-mb_hms' ),
			'public'              => false,
			'show_ui'             => true,
			'query_var'           => false,
			'rewrite'             => false,
			'publicly_queryable'  => false,
			'exclude_from_search' => true,
			'show_in_menu'        => current_user_can( 'manage_mb_hms' ) ? 'mb_hms-settings' : true,
			'capability_type'     => 'room_reservation',
			'map_meta_cap'        => true,
			'hierarchical'        => false,
			'show_in_nav_menus'   => false,
			'rewrite'             => false,
			'query_var'           => false,
			'has_archive'         => false,
			'supports'            => array( 'title', 'comments' ),
			'capabilities'        => array(
				'create_posts' => 'do_not_allow',
			)
		);
		register_post_type( 'room_reservation', apply_filters( 'mb_hms_reservation_post_type_args', $reservation_args ) );
	}

	/**
	 * Register post statuses, used for reservation status.
	 */
	public function register_post_status() {
		register_post_status( 'mbh-pending', array(
			'label'                     => esc_html_x( 'Pending Payment', 'Reservation status', 'wp-mb_hms' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Pending Payment <span class="count">(%s)</span>', 'Pending Payment <span class="count">(%s)</span>', 'wp-mb_hms' )
		) );
		register_post_status( 'mbh-on-hold', array(
			'label'                     => esc_html_x( 'On Hold', 'Reservation status', 'wp-mb_hms' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'On Hold <span class="count">(%s)</span>', 'On Hold <span class="count">(%s)</span>', 'wp-mb_hms' )
		) );
		register_post_status( 'mbh-confirmed', array(
			'label'                     => esc_html_x( 'Confirmed', 'Reservation status', 'wp-mb_hms' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Confirmed <span class="count">(%s)</span>', 'Confirmed <span class="count">(%s)</span>', 'wp-mb_hms' )
		) );
		register_post_status( 'mbh-completed', array(
			'label'                     => esc_html_x( 'Completed', 'Reservation status', 'wp-mb_hms' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Completed <span class="count">(%s)</span>', 'Completed <span class="count">(%s)</span>', 'wp-mb_hms' )
		) );
		register_post_status( 'mbh-cancelled', array(
			'label'                     => esc_html_x( 'Cancelled', 'Reservation status', 'wp-mb_hms' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Cancelled <span class="count">(%s)</span>', 'Cancelled <span class="count">(%s)</span>', 'wp-mb_hms' )
		) );
		register_post_status( 'mbh-failed', array(
			'label'                     => esc_html_x( 'Failed', 'Reservation status', 'wp-mb_hms' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Failed <span class="count">(%s)</span>', 'Failed <span class="count">(%s)</span>', 'wp-mb_hms' )
		) );
		register_post_status( 'mbh-refunded', array(
			'label'                     => esc_html_x( 'Refunded', 'Reservation status', 'wp-mb_hms' ),
			'public'                    => true,
			'exclude_from_search'       => false,
			'show_in_admin_all_list'    => true,
			'show_in_admin_status_list' => true,
			'label_count'               => _n_noop( 'Refunded <span class="count">(%s)</span>', 'Refunded <span class="count">(%s)</span>', 'wp-mb_hms' )
		) );

	}

	/**
	 * Register core taxonomies.
	 */
	public function register_taxonomies() {
		if ( taxonomy_exists( 'room_cat' ) ) {
			return;
		}

		$slug     = defined( 'MBH_ROOM_CAT_SLUG' ) ? MBH_ROOM_CAT_SLUG : 'room-type';
		$rewrite  = defined( 'MBH_DISABLE_ROOM_CAT_REWRITE' ) && MBH_DISABLE_ROOM_CAT_REWRITE ? false : array( 'slug' => $slug, 'with_front' => false );

		do_action( 'mb_hms_register_taxonomy' );

		register_taxonomy( 'room_cat',
			apply_filters( 'mb_hms_taxonomy_objects_room_cat', array( 'room' ) ),
			apply_filters( 'mb_hms_taxonomy_args_room_cat', array(
				'hierarchical'          => true,
				'labels' => array(
						'name'              => esc_html__( 'Room Categories', 'wp-mb_hms' ),
						'singular_name'     => esc_html__( 'Room Category', 'wp-mb_hms' ),
						'menu_name'         => esc_html__( 'Categories', 'wp-mb_hms' ),
						'search_items'      => esc_html__( 'Search Room Categories', 'wp-mb_hms' ),
						'all_items'         => esc_html__( 'All Room Categories', 'wp-mb_hms' ),
						'edit_item'         => esc_html__( 'Edit Room Category', 'wp-mb_hms' ),
						'update_item'       => esc_html__( 'Update Room Category', 'wp-mb_hms' ),
						'add_new_item'      => esc_html__( 'Add New Room Category', 'wp-mb_hms' ),
						'new_item_name'     => esc_html__( 'New Room Category Name', 'wp-mb_hms' ),
					),
				'show_ui'               => true,
				'show_admin_column'     => true,
				'query_var'             => true,
				'capabilities'          => array(
						'manage_terms' => 'manage_room_terms',
						'edit_terms'   => 'edit_room_terms',
						'delete_terms' => 'delete_room_terms',
						'assign_terms' => 'assign_room_terms',
				),
				'rewrite' 				=> $rewrite,
			) )
		);

		register_taxonomy( 'room_rate',
			apply_filters( 'mb_hms_taxonomy_objects_room_rate', array( 'room' ) ),
			apply_filters( 'mb_hms_taxonomy_args_room_rate', array(
				'hierarchical'       => true,
				'labels' => array(
						'name'              => esc_html_x( 'Rates', 'taxonomy general name', 'wp-mb_hms' ),
						'singular_name'     => esc_html_x( 'Rate', 'taxonomy singular name', 'wp-mb_hms' ),
						'menu_name'         => esc_html_x( 'Rates', 'admin menu name', 'wp-mb_hms' ),
						'search_items'      => esc_html__( 'Search Rates', 'wp-mb_hms' ),
						'all_items'         => esc_html__( 'All Rates', 'wp-mb_hms' ),
						'edit_item'         => esc_html__( 'Edit Rate', 'wp-mb_hms' ),
						'update_item'       => esc_html__( 'Update Rate', 'wp-mb_hms' ),
						'add_new_item'      => esc_html__( 'Add New Rate', 'wp-mb_hms' ),
						'new_item_name'     => esc_html__( 'New Rate Name', 'wp-mb_hms' )
					),
				'public'             => false,
				'show_ui'            => true,
				'show_in_nav_menus'  => false,
				'show_in_quick_edit' => false,
				'query_var'          => false,
				'capabilities'       => array(
						'manage_terms' => 'manage_room_terms',
						'edit_terms'   => 'edit_room_terms',
						'delete_terms' => 'delete_room_terms',
						'assign_terms' => 'assign_room_terms',
				)
			) )
		);

		register_taxonomy( 'room_facilities',
			apply_filters( 'mb_hms_taxonomy_objects_room_facilities', array( 'room' ) ),
			apply_filters( 'mb_hms_taxonomy_args_room_facilities', array(
				'hierarchical'          => false,
				'labels' => array(
						'name'              => esc_html_x( 'Facilities', 'taxonomy general name', 'wp-mb_hms' ),
						'singular_name'     => esc_html_x( 'Facility', 'taxonomy singular name', 'wp-mb_hms' ),
						'menu_name'         => esc_html_x( 'Facilities', 'admin menu name', 'wp-mb_hms' ),
						'search_items'      => esc_html__( 'Search Facilities', 'wp-mb_hms' ),
						'all_items'         => esc_html__( 'All Facilities', 'wp-mb_hms' ),
						'edit_item'         => esc_html__( 'Edit Facility', 'wp-mb_hms' ),
						'update_item'       => esc_html__( 'Update Facility', 'wp-mb_hms' ),
						'add_new_item'      => esc_html__( 'Add New Facility', 'wp-mb_hms' ),
						'new_item_name'     => esc_html__( 'New Facility Name', 'wp-mb_hms' )
					),
				'public'                => false,
				'show_ui'               => true,
				'show_in_nav_menus'  	=> false,
				'query_var'             => false,
				'capabilities'          => array(
						'manage_terms' => 'manage_room_terms',
						'edit_terms'   => 'edit_room_terms',
						'delete_terms' => 'delete_room_terms',
						'assign_terms' => 'assign_room_terms',
				)
			) )
		);

	}

	/**
	 * Change messages when a post type is updated.
	 * @param  array $messages
	 * @return array
	 */
	public function post_updated_messages( $messages ) {
		global $post, $post_ID;

		$messages[ 'room' ] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __( 'Room updated. <a href="%s">View Room</a>', 'wp-mb_hms' ), esc_url( get_permalink( $post_ID ) ) ),
			2 => esc_html__( 'Custom field updated.', 'wp-mb_hms' ),
			3 => esc_html__( 'Custom field deleted.', 'wp-mb_hms' ),
			4 => esc_html__( 'Room updated.', 'wp-mb_hms' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Room restored to revision from %s', 'wp-mb_hms' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __( 'Room published. <a href="%s">View Room</a>', 'wp-mb_hms' ), esc_url( get_permalink( $post_ID ) ) ),
			7 => __( 'Room saved.', 'wp-mb_hms' ),
			8 => sprintf( __( 'Room submitted. <a target="_blank" href="%s">Preview Room</a>', 'wp-mb_hms' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
			9 => sprintf( __( 'Room scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Room</a>', 'wp-mb_hms' ),
			  date_i18n( __( 'M j, Y @ G:i', 'wp-mb_hms' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
			10 => sprintf( __( 'Room draft updated. <a target="_blank" href="%s">Preview Room</a>', 'wp-mb_hms' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
		);

		$messages[ 'room_reservation' ] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => esc_html__( 'Reservation updated.', 'wp-mb_hms' ),
			2 => esc_html__( 'Custom field updated.', 'wp-mb_hms' ),
			3 => esc_html__( 'Custom field deleted.', 'wp-mb_hms' ),
			4 => esc_html__( 'Reservation updated.', 'wp-mb_hms' ),
			5 => isset( $_GET['revision'] ) ? sprintf( __( 'Reservation restored to revision from %s', 'wp-mb_hms' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => esc_html__( 'Reservation updated.', 'wp-mb_hms' ),
			7 => esc_html__( 'Reservation saved.', 'wp-mb_hms' ),
			8 => esc_html__( 'Reservation submitted.', 'wp-mb_hms' ),
			9 => sprintf( __( 'Reservation scheduled for: <strong>%1$s</strong>.', 'wp-mb_hms' ),
			date_i18n( __( 'M j, Y @ G:i', 'wp-mb_hms' ), strtotime( $post->post_date ) ) ),
			10 => esc_html__( 'Reservation draft updated.', 'wp-mb_hms' ),
			11 => esc_html__( 'Reservation updated and email sent.', 'wp-mb_hms' ),
		);

		return $messages;
	}

	/**
	 * Disable block editor for rooms.
	 *
	 * @param bool   $can_edit Whether the post type can be edited or not.
	 * @param string $post_type The post type being checked.
	 * @return bool
	 */
	public static function block_editor_can_edit_post_type( $can_edit, $post_type ) {
		if ( 'room' === $post_type ) {
			return false;
		}

		return $can_edit;
	}
}

endif;

return new MBH_Post_Types();
