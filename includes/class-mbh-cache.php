<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Cache' ) ) :

/**
 * MBH_Cache Class
 */
class MBH_Cache {

	/**
	 * Hook in methods.
	 */
	public static function init() {
		add_action( 'before_mb_hms_init', array( __CLASS__, 'prevent_caching' ) );
	}

	/**
	 * Get the page name/id for a MBH page.
	 * @param  string $MBH_page
	 * @return array
	 */
	private static function get_page_uris( $mbh_page ) {
		$mbh_page_uris = array();

		if ( ( $page_id = mbh_get_page_id( $mbh_page ) ) && $page_id > 0 && ( $page = get_post( $page_id ) ) ) {
			$mbh_page_uris[] = 'p=' . $page_id;
			$mbh_page_uris[] = '/' . $page->post_name . '/';
		}

		return $mbh_page_uris;
	}

	/**
	 * Prevent caching on dynamic pages.
	 * @access public
	 */
	public static function prevent_caching() {
		if ( false === ( $mbh_page_uris = get_transient( 'hotelier_cache_excluded_uris' ) ) ) {
			$mbh_page_uris   = array_filter( array_merge( self::get_page_uris( 'listing' ), self::get_page_uris( 'booking' ) ) );
	    	set_transient( 'hotelier_cache_excluded_uris', $mbh_page_uris );
		}

		if ( is_array( $mbh_page_uris ) ) {
			foreach( $mbh_page_uris as $uri ) {
				if ( stristr( $_SERVER[ 'REQUEST_URI' ], $uri ) ) {
					self::nocache();
					break;
				}
			}
		}
	}

	/**
	 * Set nocache constants and headers.
	 * @access private
	 */
	private static function nocache() {
		if ( ! defined( 'DONOTCACHEPAGE' ) ) {
			define( "DONOTCACHEPAGE", "true" );
		}

		if ( ! defined( 'DONOTCACHEOBJECT' ) ) {
			define( "DONOTCACHEOBJECT", "true" );
		}

		if ( ! defined( 'DONOTCACHEDB' ) ) {
			define( "DONOTCACHEDB", "true" );
		}

		nocache_headers();
	}
}

endif;

MBH_Cache::init();
