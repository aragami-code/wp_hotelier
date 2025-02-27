<?php
/**
 *-t
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Meta_Box_Room_Excerpt' ) ) :

/**
 * MBH_Meta_Box_Room_Excerpt Class
 */
class MBH_Meta_Box_Room_Excerpt {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {

		$settings = array(
			'textarea_name' => 'excerpt',
			'media_buttons' => false,
			'quicktags'     => array( 'buttons' => 'em,strong,link,del,ins' ),
			'tinymce'       => false,
			'editor_css'    => '<style>#wp-excerpt-editor-container .wp-editor-area{height:175px; width:100%;}</style>'
		);

		wp_editor( htmlspecialchars_decode( $post->post_excerpt ), 'excerpt', apply_filters( 'mb_hms_room_short_description_editor_settings', $settings ) );
	}
}

endif;
