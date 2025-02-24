<?php
/**
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Meta_Box_Reservation_Notes' ) ) :

/**
 * MBH_Meta_Box_Reservation_Notes Class
 */
class MBH_Meta_Box_Reservation_Notes {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		global $post;

		$args = array(
			'post_id'   => $post->ID,
			'orderby'   => 'comment_ID',
			'order'     => 'DESC',
			'approve'   => 'approve',
			'type'      => 'reservation_note'
		);

		remove_filter( 'comments_clauses', array( 'MBH_Comments', 'exclude_reservation_comments' ), 10, 1 );

		$notes = get_comments( $args );

		add_filter( 'comments_clauses', array( 'MBH_Comments', 'exclude_reservation_comments' ), 10, 1 );

		?>
		<ul class="reservation-notes">

			<?php
			if ( $notes ) {

			foreach( $notes as $note ) {
				?>
				<li>
					<p class="note-content"><?php echo esc_html( $note->comment_content ); ?><p>
					<abbr title="<?php echo esc_attr( $note->comment_date ); ?>"><?php printf( esc_html__( 'Added on %1$s at %2$s', 'wp-mb_hms' ), date_i18n( get_option( 'date_format' ), strtotime( $note->comment_date ) ), date_i18n( get_option( 'time_format' ), strtotime( $note->comment_date ) ) ); ?></abbr>
				</li>
				<?php
			}

		} else {
			echo '<li>' . esc_html__( 'There are no notes yet.', 'wp-mb_hms' ) . '</li>';
		}

		?>

		</ul>

		<?php
	}
}

endif;
