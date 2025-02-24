<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! $variation->has_conditions() ) {
	return;
}

?>

<div class="rate__conditions rate__conditions--single">

	<span class="rate__conditions-title rate__conditions-title--single"><?php esc_html_e( 'Rate conditions:', 'wp-mb_hms' ) ?></span>

	<ul class="rate__conditions-list rate__conditions-list--single">

	<?php foreach ( $variation->get_room_conditions() as $condition ) : ?>

		<li class="rate__conditions-item rate__conditions-item--single"><?php echo esc_html( $condition ); ?></li>

	<?php endforeach; ?>

	</ul>

</div>
