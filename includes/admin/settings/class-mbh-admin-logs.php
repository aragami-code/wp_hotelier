<?php
/**
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Admin_Logs' ) ) :

/**
 * MBH_Admin_Logs Class
 */
class MBH_Admin_Logs {

	/**
	 * Scan the log files
	 * @return array
	 */
	public static function scan_log_files() {
		$files  = @scandir( MBH_LOG_DIR );
		$result = array();

		if ( $files ) {

			foreach ( $files as $key => $value ) {

				if ( ! in_array( $value, array( '.', '..' ) ) ) {
					if ( ! is_dir( $value ) && strstr( $value, '.log' ) ) {
						$result[ sanitize_title( $value ) ] = $value;
					}
				}
			}

		}

		return $result;
	}

	/**
	 * Show the logs page
	 */
	public static function output() {

		$logs = self::scan_log_files();

		if ( ! empty( $_REQUEST[ 'log_file' ] ) && isset( $logs[ sanitize_title( $_REQUEST[ 'log_file' ] ) ] ) ) {
			$viewed_log = $logs[ sanitize_title( $_REQUEST[ 'log_file' ] ) ];
		} elseif ( ! empty( $logs ) ) {
			$viewed_log = current( $logs );
		}

		include_once( 'views/html-admin-logs.php' );
	}
}

endif;
