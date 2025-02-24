<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<p class="mb_hms-notice mb_hms-notice--info mb_hms-notice--no-rooms-available"><?php esc_html_e( 'We are sorry, there are no rooms available on your requested dates. Please try again with some different dates.', 'wp-mb_hms' ); ?></p>

<?php
	/**
	 * mb_hms_room_list_datepicker hook
	 *
	 * @hooked mb_hms_template_datepicker - 10
	 */
	do_action( 'mb_hms_room_list_datepicker' );
?>
