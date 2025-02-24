<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $description = $variation->get_room_description() ) : ?>

	<div class="rate__description rate__description--single"><?php echo wp_kses( $variation->get_room_description(), array( 'p' => array() ) ); ?></div>

<?php endif; ?>
