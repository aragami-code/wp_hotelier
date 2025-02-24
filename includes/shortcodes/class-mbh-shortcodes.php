<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Shortcodes' ) ) :

/**
 * MBH_Shortcodes Class
 */
class MBH_Shortcodes {

	/**
	 * Init shortcodes
	 */
	public static function init() {
		self::includes();

		$shortcodes = array(
			'mb_hms_recent_rooms' => __CLASS__ . '::recent_rooms',
			'mb_hms_rooms'        => __CLASS__ . '::rooms',
			'mb_hms_room_type'    => __CLASS__ . '::room_type',
			'mb_hms_booking'      => __CLASS__ . '::booking',
			'mb_hms_listing'      => __CLASS__ . '::room_list',
			'mb_hms_datepicker'   => __CLASS__ . '::datepicker',
		);

		foreach ( $shortcodes as $shortcode => $function ) {
			add_shortcode( apply_filters( "{$shortcode}_shortcode_tag", $shortcode ), $function );
		}
	}

	/**
	 * Include required files
	 */
	public static function includes() {
		include_once( 'class-mbh-shortcode-booking.php' );
		include_once( 'class-mbh-shortcode-room-list.php' );
		include_once( 'class-mbh-shortcode-datepicker.php' );
	}

	/**
	 * Shortcode Wrapper
	 *
	 * @param string[] $function
	 * @param array $atts (default: array())
	 * @return string
	 */
	public static function shortcode_wrapper(
		$function,
		$atts    = array(),
		$wrapper = array(
			'class'  => 'mb_hms',
			'before' => null,
			'after'  => null
		)
	) {
		ob_start();

		echo empty( $wrapper[ 'before' ] ) ? '<div class="' . esc_attr( $wrapper[ 'class' ] ) . '">' : $wrapper[ 'before' ];
		call_user_func( $function, $atts );
		echo empty( $wrapper[ 'after' ] ) ? '</div>' : $wrapper[ 'after' ];

		return ob_get_clean();
	}

	/**
	 * Loop over found rooms.
	 * @param  array $query_args
	 * @param  array $atts
	 * @param  string $loop_name
	 * @return string
	 */
	private static function room_loop( $query_args, $atts, $loop_name ) {
		global $mb_hms_loop;

		$rooms                      = new WP_Query( apply_filters( 'mb_hms_shortcode_rooms_query', $query_args, $atts ) );
		$columns                    = absint( $atts[ 'columns' ] );
		$mb_hms_loop[ 'columns' ] = $columns;

		ob_start();

		if ( $rooms->have_posts() ) : ?>

			<?php do_action( "mb_hms_shortcode_before_{$loop_name}_loop" ); ?>

			<?php mb_hms_room_loop_start(); ?>

				<?php while ( $rooms->have_posts() ) : $rooms->the_post(); ?>

					<?php mbh_get_template_part( 'archive/content', 'room' ); ?>

				<?php endwhile; // end of the loop. ?>

			<?php mb_hms_room_loop_end(); ?>

			<?php do_action( "mb_hms_shortcode_after_{$loop_name}_loop" ); ?>

		<?php endif;

		mb_hms_reset_loop();
		wp_reset_postdata();

		return '<div class="mb_hms room-loop room-loop--shortcode-rooms room-loop--columns-' . $columns . '">' . ob_get_clean() . '</div>';
	}

	/**
	 * Booking page shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function booking( $atts ) {
		return self::shortcode_wrapper( array( 'MBH_Shortcode_Booking', 'output' ), $atts );
	}

	/**
	 * Room list shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function room_list( $atts ) {
		$atts = shortcode_atts( array(
			'per_page' => '10',
			'orderby'  => 'title',
			'order'    => 'asc'
		), $atts );

		return self::shortcode_wrapper( array( 'MBH_Shortcode_Room_List', 'output' ), $atts );
	}

	/**
	 * Booking page shortcode.
	 *
	 * @param mixed $atts
	 * @return string
	 */
	public static function datepicker( $atts ) {
		return self::shortcode_wrapper( array( 'MBH_Shortcode_Datepicker', 'output' ), $atts );
	}

	/**
	 * Recent rooms shortcode.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function recent_rooms( $atts ) {
		$atts = shortcode_atts( array(
			'per_page' => '9',
			'columns'  => '3',
			'orderby'  => 'date',
			'order'    => 'desc',
		), $atts );

		$query_args = array(
			'post_type'           => 'room',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $atts[ 'per_page' ],
			'orderby'             => $atts[ 'orderby' ],
			'order'               => $atts[ 'order' ],
			'meta_query'          => array(
				array(
					'key'     => '_stock_rooms',
					'value'   => 0,
					'type'    => 'numeric',
					'compare' => '>',
				),
			),
		);

		return self::room_loop( $query_args, $atts, 'recent_rooms' );
	}

	/**
	 * List rooms in a category shortcode.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function room_type( $atts ) {
		$atts = shortcode_atts( array(
			'per_page' => '9',
			'columns'  => '3',
			'orderby'  => 'title',
			'order'    => 'desc',
			'category' => '',  // Slugs
			'operator' => 'IN' // Possible values are 'IN', 'NOT IN', 'AND'.
		), $atts );

		if ( ! $atts[ 'category' ] ) {
			return '';
		}

		$query_args = array(
			'post_type'           => 'room',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => $atts[ 'per_page' ],
			'orderby'             => $atts[ 'orderby' ],
			'order'               => $atts[ 'order' ],
			'meta_query'          => array(
				array(
					'key'     => '_stock_rooms',
					'value'   => 0,
					'type'    => 'numeric',
					'compare' => '>',
				),
			),
		);

		$query_args[ 'tax_query' ] = array(
			array(
				'taxonomy' => 'room_cat',
				'terms'    => array_map( 'sanitize_title', explode( ',', $atts[ 'category' ] ) ),
				'field'    => 'slug',
				'operator' => $atts[ 'operator' ]
			)
		);

		return self::room_loop( $query_args, $atts, 'room_type' );
	}

	/**
	 * Multiple rooms shortcode.
	 *
	 * @param array $atts
	 * @return string
	 */
	public static function rooms( $atts ) {
		$atts = shortcode_atts( array(
			'columns' => '3',
			'orderby' => 'title',
			'order'   => 'asc',
			'ids'     => '',
		), $atts );

		$query_args = array(
			'post_type'           => 'room',
			'post_status'         => 'publish',
			'ignore_sticky_posts' => 1,
			'posts_per_page'      => -1,
			'orderby'             => $atts[ 'orderby' ],
			'order'               => $atts[ 'order' ],
			'meta_query'          => array(
				array(
					'key'     => '_stock_rooms',
					'value'   => 0,
					'type'    => 'numeric',
					'compare' => '>',
				),
			),
		);

		if ( ! empty( $atts[ 'ids' ] ) ) {
			$ids = array_map( 'trim', explode( ',', $atts[ 'ids' ] ) );
			$ids = array_map( 'absint', $ids );
			$query_args[ 'post__in' ] = $ids;
			$query_args[ 'orderby' ]  = 'post__in';
		}

		return self::room_loop( $query_args, $atts, 'rooms' );
	}
}

endif;
