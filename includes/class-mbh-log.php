<?php
/**
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Log' ) ) :

/**
 * HTL_Log Class
 */
class MBH_Log {

	/**
	 * @var array Stores open file _handles.
	 * @access private
	 */
	private $_handles;

	/**
	 * Constructor for the logger.
	 */
	public function __construct() {
		$this->_handles = array();
	}

	/**
	 * Destructor.
	 */
	public function __destruct() {
		foreach ( $this->_handles as $handle ) {
			@fclose( $handle );
		}
	}

	/**
	 * Open log file for writing.
	 *
	 * @access private
	 * @param mixed $handle
	 * @return bool success
	 */
	private function open( $handle ) {
		if ( isset( $this->_handles[ $handle ] ) ) {
			return true;
		}

		if ( $this->_handles[ $handle ] = @fopen( mbh_get_log_file_path( $handle ), 'a' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * Add a log entry to chosen file.
	 *
	 * @param string $handle
	 * @param string $message
	 */
	public function add( $handle, $message ) {
		if ( $this->open( $handle ) && is_resource( $this->_handles[ $handle ] ) ) {
			$time = date_i18n( 'm-d-Y @ H:i:s -' ); // Grab Time
			@fwrite( $this->_handles[ $handle ], $time . " " . $message . "\n" );
		}

		do_action( 'mb_hms_log_add', $handle, $message );
	}

	/**
	 * Clear entries from chosen file.
	 *
	 * @param mixed $handle
	 */
	public function clear( $handle ) {
		if ( $this->open( $handle ) && is_resource( $this->_handles[ $handle ] ) ) {
			@ftruncate( $this->_handles[ $handle ], 0 );
		}

		do_action( 'mb_hms_log_clear', $handle );
	}
}

endif;
