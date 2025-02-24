<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

if ( ! $room->is_variable_room() ) {
	return;
}

$variations = $room->get_room_variations();

?>

<div class="room__rates room__rates--single">

	<h3 class="room__rates-title"><?php esc_html_e( 'Available rates', 'wp-mb_hms' ); ?></h3>

	<ul id="room-rates-<?php echo absint( get_the_ID() ); ?>" class="room__rates-list">

		<?php
		// Print room rates
		foreach ( $variations as $variation ) :
			$variation = new MBH_Room_Variation( $variation, $room->id ); ?>

			<?php
				/**
				 * mb_hms_single_room_single_rate hook.
				 *
				 * @hooked mb_hms_template_single_room_single_rate - 10
				 */
				do_action( 'mb_hms_single_room_single_rate', $variation );
			?>

		<?php endforeach; ?>
	</ul>

</div>
