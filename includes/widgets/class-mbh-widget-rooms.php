<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MBH_Widget_Rooms extends MBH_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'widget--mb_hms widget-rooms';
		$this->widget_description = __( 'Display a list of rooms on your site.', 'wp-mb_hms' );
		$this->widget_id          = 'mb_hms-widget-rooms';
		$this->widget_name        = __( 'mb_hms Rooms', 'wp-mb_hms' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => __( 'Rooms', 'wp-mb_hms' ),
				'label' => __( 'Title', 'wp-mb_hms' )
			),
			'number' => array(
				'type'  => 'number',
				'step'  => 1,
				'min'   => 1,
				'max'   => '',
				'std'   => 5,
				'label' => __( 'Number of rooms to show', 'wp-mb_hms' )
			),
			'order' => array(
				'type'  => 'select',
				'std'   => 'date',
				'label' => __( 'Order by?', 'wp-mb_hms' ),
				'options' => array(
					'date' => __( 'Date', 'wp-mb_hms' ),
					'cat'  => __( 'Category', 'wp-mb_hms' ),
					'ids'  => __( 'IDs', 'wp-mb_hms' ),
				)
			),
			'cats'  => array(
				'type'        => 'text',
				'label'       => __( 'Category IDs', 'wp-mb_hms' ),
				'std'         => '',
				'description' => __( 'List of category IDs separated by commas (eg. 1,5,8). Works only when the order type is set to "Category"', 'wp-mb_hms' )
			),
			'ids'  => array(
				'type'        => 'text',
				'label'       => __( 'Room IDs', 'wp-mb_hms' ),
				'std'         => '',
				'description' => __( 'List of room IDs separated by commas (eg. 1,5,8). Works only when the order type is set to "IDs"', 'wp-mb_hms' )
			),
		);

		parent::__construct();
	}

	/**
	 * Query the rooms and return them.
	 * @param  array $args
	 * @param  array $instance
	 * @return WP_Query
	 */
	public function get_rooms( $args, $instance ) {
		$number  = $instance[ 'number' ] ? absint( $instance[ 'number' ] ) : $this->settings[ 'number' ][ 'std' ];
		$order    = $instance[ 'order' ] ? sanitize_title( $instance[ 'order' ] ) : $this->settings[ 'order' ][ 'std' ];
		$cats = $instance[ 'cats' ] ? MBH_Formatting_Helper::sanitize_ids( $instance[ 'cats' ] ) : array();
		$ids = $instance[ 'ids' ] ? MBH_Formatting_Helper::sanitize_ids( $instance[ 'ids' ] ) : array();

		$query_args = array(
			'post_status'    => 'publish',
			'post_type'      => 'room',
			'no_found_rows'  => 1,
			'meta_query'     => array()
		);

		switch ( $order ) {
			case 'cat' :
				$query_args[ 'posts_per_page' ] = $number;
				$query_args[ 'tax_query' ]      = array(
					array(
						'taxonomy' => 'room_cat',
						'field'    => 'term_id',
						'terms'    => $cats,
					) );
				break;

			case 'ids' :
				$ids = explode( ',', $ids );
				$query_args[ 'post__in' ] = $ids;
				$query_args[ 'orderby' ]  = 'post__in';
				break;

			default :
				$query_args[ 'posts_per_page' ] = $number;
				break;
		}

		return new WP_Query( apply_filters( 'mb_hms_rooms_widget_query_args', $query_args ) );
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		if ( $this->get_cached_widget( $args ) ) {
			return;
		}

		ob_start();

		if ( ( $rooms = $this->get_rooms( $args, $instance ) ) && $rooms->have_posts() ) {
			$this->widget_start( $args, $instance );

			echo apply_filters( 'mb_hms_before_widget_room_list', '<ul class="widget-rooms__list">' );

			while ( $rooms->have_posts() ) {
				$rooms->the_post();
				mbh_get_template( 'widgets/content-widget-room.php' );
			}

			echo apply_filters( 'mb_hms_after_widget_room_list', '</ul>' );

			$this->widget_end( $args );
		}

		wp_reset_postdata();

		echo $this->cache_widget( $args, ob_get_clean() );
	}
}
