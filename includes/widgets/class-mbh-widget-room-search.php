<?php
/**
 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MBH_Widget_Room_Search extends MBH_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'widget--mb_hms widget-room-search';
		$this->widget_description = __( 'A search box for rooms only.', 'wp-mb_hms' );
		$this->widget_id          = 'mb_hms-widget-room-search';
		$this->widget_name        = __( 'mb_hms Room Search', 'wp-mb_hms' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => '',
				'label' => __( 'Title', 'wp-mb_hms' )
			)
		);

		parent::__construct();
	}

	/**
	 * widget function.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {
		$this->widget_start( $args, $instance );

		do_action( 'mb_hms_before_widget_room_search' );
		?>

		<form role="search" method="get" class="form--room-search room-search" action="<?php echo esc_url( home_url( '/'  ) ); ?>">
			<label class="screen-reader-text" for="s"><?php esc_html_e( 'Search for:', 'wp-mb_hms' ); ?></label>
			<input type="search" class="room-search__input" placeholder="<?php echo esc_attr_x( 'Search rooms&hellip;', 'placeholder', 'wp-mb_hms' ); ?>" value="<?php echo get_search_query(); ?>" name="s" title="<?php echo esc_attr_x( 'Search for:', 'label', 'wp-mb_hms' ); ?>" />
			<input class="button button--room-search" type="submit" value="<?php echo esc_attr_x( 'Search', 'submit button', 'wp-mb_hms' ); ?>" />
			<input type="hidden" name="post_type" value="room" />

			<?php do_action( 'mb_hms_after_widget_room_search_fields' ); ?>
		</form>

		<?php
		do_action( 'mb_hms_after_widget_room_search' );

		$this->widget_end( $args );
	}
}
