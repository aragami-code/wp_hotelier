<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! mbh_get_option( 'booking_number_of_guests_selection', true ) ) {
	return;
}
?>

<?php for ( $q = 0; $q < $quantity; $q++ ) : ?>

	<div class="reservation-table__room-guests reservation-table__room-guests--booking">

		<?php if ( $quantity > 1 ) : ?>
			<span class="reservation-table__room-guests-label"><?php echo sprintf( esc_html__( 'Number of guests (Room %d):', 'wp-mb_hms' ), $q + 1 ); ?></span>
		<?php else : ?>
			<span class="reservation-table__room-guests-label"><?php esc_html_e( 'Number of guests:', 'wp-mb_hms' ); ?></span>
		<?php endif; ?>

		<?php
		$adults_options = array();

		for ( $i = 1; $i <= $adults; $i++ ) {
			$adults_options[ $i ] = $i;
		}

		$adults_args = array(
			'type'    => 'select',
			'label'   => esc_html__( 'Adults', 'wp-mb_hms' ),
			'class'   => array(),
			'default' => $adults,
			'options' => $adults_options
		);

		mbh_form_field( 'adults[' . $item_key . '][' . $q . ']', $adults_args );

		if ( $children > 0 ) {
			$children_options = array();

			for ( $i = 0; $i <= $children; $i++ ) {
				$children_options[ $i ] = $i;
			}

			$children_args = array(
				'type'    => 'select',
				'label'   => esc_html__( 'Children', 'wp-mb_hms' ),
				'class'   => array(),
				'options' => $children_options
			);

			mbh_form_field( 'children[' . $item_key . '][' . $q . ']', $children_args );
		} ?>
	</div>

<?php endfor; ?>
