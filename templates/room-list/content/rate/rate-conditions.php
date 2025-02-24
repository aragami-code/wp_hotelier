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

<div class="rate__conditions rate__conditions--listing">

	<strong class="rate__conditions-title rate__conditions-title--listing"><?php esc_html_e( 'Room conditions:', 'wp-mb_hms' ) ?></strong>

	<ul class="rate__conditions-list rate__conditions-list--listing">

	<?php foreach ( $variation->get_room_conditions() as $condition ) : ?>

		<li class="rate__conditions-item rate__conditions-item--listing"><?php echo esc_html( $condition ); ?></li>

	<?php endforeach; ?>

	</ul>

</div>
