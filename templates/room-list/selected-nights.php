<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$nights = absint( $nights );

?>

<?php if ( $nights > 0 ) : ?>

	<p class="selected-nights"><?php printf( _nx( '%s-night stay', '%s-nights stay', $nights, 'selected_nights', 'wp-mb_hms' ), $nights ); ?></p>

<?php endif; ?>
