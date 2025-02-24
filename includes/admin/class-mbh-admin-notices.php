<?php
/**
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Admin_Notices' ) ) :

/**
 * MBH_Admin_Notices Class
 */
class MBH_Admin_Notices {

	/**
	 * Get things going.
	 */
	public function __construct() {
		add_action( 'admin_notices', array( $this, 'show_notices' ) );
	}

	/**
	 * Show notices.
	 */
	public function show_notices() {
		$notices = array(
			'updated'	=> array(),
			'error'		=> array()
		);

		settings_errors( 'mb_hms-notices' );
	}
}

endif;

return new MBH_Admin_Notices();
