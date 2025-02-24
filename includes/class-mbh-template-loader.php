<?php
/**
 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Template_Loader' ) ) :

	class MBH_Template_Loader {

		/**
		 * Hook in methods.
		 */
		public static function init() {
			add_filter( 'template_include', array( __CLASS__, 'template_loader' ) );
		}

		/**
		 * Load a template.
		 *
		 * @param mixed $template
		 * @return string
		 */
		public static function template_loader( $template ) {
			$find = array();
			$file = '';

			if ( is_single() && get_post_type() == 'room' ) {

				$file 	= 'single-room/single-room.php';
				$find[] = $file;
				$find[] = MBH()->template_path() . $file;

			} elseif ( is_room_category() ) {

				$term   = get_queried_object();

				if ( is_tax( 'room_cat' ) ) {
					$file = 'archive/taxonomy-' . $term->taxonomy . '.php';
				} else {
					$file = 'archive/archive-room.php';
				}

				$find[] = $file;
				$find[] = MBH()->template_path() . $file;

			} elseif ( is_post_type_archive( 'room' ) ) {

				$file 	= 'archive/archive-room.php';
				$find[] = $file;
				$find[] = MBH()->template_path() . $file;

			}

			if ( $file ) {
				$template       = locate_template( array_unique( $find ) );
				if ( ! $template || MBH_TEMPLATE_DEBUG_MODE ) {
					$template = MBH()->plugin_path() . '/templates/' . $file;
				}
			}

			return $template;
		}

	}

endif;

MBH_Template_Loader::init();
