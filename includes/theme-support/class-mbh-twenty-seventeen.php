<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Twenty_Seventeen' ) ) :

/**
 * MBH_Twenty_Seventeen Class
 */
class MBH_Twenty_Seventeen {

	/**
	 * Constructor.
	 */
	public function __construct() {
		// Remove default wrappers
		remove_action( 'mb_hms_before_main_content', 'mb_hms_output_content_wrapper', 10 );
		remove_action( 'mb_hms_after_main_content', 'mb_hms_output_content_wrapper_end', 10 );

		// Add custom wrappers
		add_action( 'mb_hms_before_main_content', array( $this, 'open_content_wrapper' ), 10 );
		add_action( 'mb_hms_after_main_content', array( $this, 'close_content_wrapper_end' ), 10 );
		add_action( 'mb_hms_sidebar', array( $this, 'close_page_wrapper' ), 50 );

		// Enqueue custom style
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles' ) );
	}

	/**
	 * Open the Twenty Seventeen content wrapper.
	 */
	public function open_content_wrapper() {
		?>
		<div class="wrap">
			<div id="primary" class="content-area twentyseventeen">
				<main id="main" class="site-main" role="main">
		<?php
	}

	/**
	 * Close the Twenty Seventeen content wrapper.
	 */
	public function close_content_wrapper_end() {
		?>
			</main><!-- .site-main -->
		</div><!-- .content-area -->
		<?php
	}

	/**
	 * Close the Twenty Seventeen page element wrapper after the sidebar.
	 */
	public function close_page_wrapper() {
		?>
		</div><!-- .wrap -->
		<?php
	}

	/**
	 * Enqueue styles
	 *
	 * @access public
	 * @return void
	 */
	public function enqueue_styles() {
		wp_enqueue_style( 'mb_hms-twenty-seventeen', MBH_PLUGIN_URL . 'assets/css/twenty-seventeen.css', array(), MBH_VERSION );
	}
}

endif;

new MBH_Twenty_Seventeen();
