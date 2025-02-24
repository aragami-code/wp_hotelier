<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

?>

<?php if ( $facilities = $room->get_facilities() ) : ?>

<div class="room__meta room__meta--listing">

	<p class="room__facilities room__facilities--listing"><strong><?php esc_html_e( 'Room facilities:', 'wp-mb_hms' ); ?></strong> <?php echo $facilities; ?></p>

</div>

<?php endif; ?>
