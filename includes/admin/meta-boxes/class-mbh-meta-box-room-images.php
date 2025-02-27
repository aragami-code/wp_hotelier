<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Meta_Box_Room_Images' ) ) :

/**
 * MBH_Meta_Box_Room_Images Class
 */
class MBH_Meta_Box_Room_Images {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		?>
		<div id="room-images-container">
			<ul class="room-images">
				<?php
				$room_image_gallery = get_post_meta( $post->ID, '_room_image_gallery', true );
				$attachments = array_filter( explode( ',', $room_image_gallery ) );

				if ( ! empty( $attachments ) ) {
					foreach ( $attachments as $attachment_id ) {
						echo '<li class="image" data-attachment_id="' . esc_attr( $attachment_id ) . '">
							' . wp_get_attachment_image( $attachment_id, 'thumbnail' ) . '
							<a href="#" class="delete" title="' . esc_attr__( 'Delete image', 'wp-mb_hms' ) . '">' . esc_html__( 'Delete', 'wp-mb_hms' ) . '</a>
						</li>';
					}
				}
				?>
			</ul>

			<input type="hidden" id="room-image-gallery" name="_room_image_gallery" value="<?php echo esc_attr( $room_image_gallery ); ?>" />

		</div>

		<p class="add-room-images-wrap hide-if-no-js">
			<a href="#" id="add-room-images" data-choose="<?php esc_attr_e( 'Add images to room gallery', 'wp-mb_hms' ); ?>" data-update="<?php esc_attr_e( 'Add to gallery', 'wp-mb_hms' ); ?>" data-delete="<?php esc_attr_e( 'Delete image', 'wp-mb_hms' ); ?>" data-text="<?php esc_attr_e( 'Delete', 'wp-mb_hms' ); ?>"><?php esc_html_e( 'Add room gallery images', 'wp-mb_hms' ); ?></a>
		</p>
		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		$attachment_ids = isset( $_POST[ 'room_image_gallery' ] ) ? array_filter( explode( ',', sanitize_text_field( $_POST[ 'room_image_gallery' ] ) ) ) : array();

		update_post_meta( $post_id, '_room_image_gallery', implode( ',', $attachment_ids ) );
	}
}

endif;
