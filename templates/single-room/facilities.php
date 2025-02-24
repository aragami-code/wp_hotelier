<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

?>

<?php if ( $facilities = $room->get_facilities() ) : ?>

<div class="room__facilities room__facilities--single">

	<h3 class="room__facilities-title room__facilities-title--single"><?php esc_html_e( 'Facilities', 'wp-mb_hms' ); ?></h3>

	<p class="room__facilities-content room__facilities-content--single"><?php echo $facilities; ?></p>

</div>

<?php endif; ?>
