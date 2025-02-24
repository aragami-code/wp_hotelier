<?php
/**
 * Shows the room price (variation)
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="room-price-panel">

	<?php

	// room price

	$price_type = array(
		'global'         => esc_html__( 'Global', 'wp-mb_hms' ),
		'per_day'        => esc_html__( 'Price per day', 'wp-mb_hms' ),
		'seasonal_price' => esc_html__( 'Seasonal price', 'wp-mb_hms' )
	);

	MBH_Meta_Boxes_Helper::select_input(
		array(
			'id'    => '_room_variations',
			'name'  => '_room_variations[' . absint( $loop ) . '][price_type]',
			'depth' => array( absint( $loop ), 'price_type' ),
			'label' => esc_html__( 'Price:', 'wp-mb_hms' ),
			'class' => 'room-price', 'options' => apply_filters( 'mb_hms_room_price_type', $price_type )
		)
	);
	?>

	<div class="price-panel price-panel-global">

		<?php

		MBH_Meta_Boxes_Helper::text_input(
			array(
				'id'            => '_room_variations',
				'name'          => '_room_variations[' . absint( $loop ) . '][regular_price]',
				'depth'         => array( absint( $loop ), 'regular_price' ),
				'label'         => esc_html__( 'Regular price:', 'wp-mb_hms' ),
				'wrapper_class' => 'price',
				'data_type'     => 'price',
				'placeholder'   => self::get_price_placeholder(),
				'desc_tip'      => 'true',
				'description'   => esc_html__( 'Same price for all days of the week.', 'wp-mb_hms' )
			)
		);

		MBH_Meta_Boxes_Helper::text_input(
			array(
				'id'            => '_room_variations',
				'name'          => '_room_variations[' . absint( $loop ) . '][sale_price]',
				'depth'         => array( absint( $loop ), 'sale_price' ),
				'label'         => esc_html__( 'Sale price:', 'wp-mb_hms' ),
				'wrapper_class' => 'price',
				'data_type'     => 'price',
				'placeholder'   => self::get_price_placeholder(),
				'desc_tip'      => 'true',
				'description'   => esc_html__( 'Same price for all days of the week.', 'wp-mb_hms' )
			)
		);

		?>

	</div><!-- .global-price -->

	<div class="price-panel price-panel-per_day">

		<?php

		MBH_Meta_Boxes_Helper::price_per_day(
			array(
				'id'          => '_room_variations',
				'name'        => '_room_variations[' . absint( $loop ) . '][price_day]',
				'depth'       => array( absint( $loop ), 'price_day' ),
				'label'       => esc_html__( 'Regular price:', 'wp-mb_hms' ),
				'desc_tip'    => 'true',
				'description' => esc_html__( 'The regular price of the room per day.', 'wp-mb_hms' )
			)
		);

		MBH_Meta_Boxes_Helper::price_per_day(
			array(
				'id'          => '_room_variations',
				'name'        => '_room_variations[' . absint( $loop ) . '][sale_price_day]',
				'depth'       => array( absint( $loop ), 'sale_price_day' ),
				'label'       => esc_html__( 'Sale price:', 'wp-mb_hms' ),
				'desc_tip'    => 'true',
				'description' => esc_html__( 'The sale price of the room per day.', 'wp-mb_hms' )
			)
		);

		?>

	</div><!-- .price-per-day -->

	<div class="price-panel price-panel-seasonal_price">

		<?php

		MBH_Meta_Boxes_Helper::text_input(
			array(
				'id'            => '_room_variations',
				'name'          => '_room_variations[' . absint( $loop ) . '][seasonal_base_price]',
				'depth'         => array( absint( $loop ), 'seasonal_base_price' ),
				'label'         => esc_html__( 'Default price:', 'wp-mb_hms' ),
				'wrapper_class' => 'price',
				'data_type'     => 'price',
				'placeholder'   => self::get_price_placeholder(),
				'desc_tip'      => 'true',
				'description'   => esc_html__( 'Default room price. Used when no rules are found.', 'wp-mb_hms' ) ) );

		if ( ( $seasonal_prices_schema = mbh_get_seasonal_prices_schema() ) && is_array( $seasonal_prices_schema ) ) {

			$seasonal_price_value = isset( $variations[ absint( $loop ) ][ 'seasonal_price' ] ) ? $variations[ absint( $loop ) ][ 'seasonal_price' ] : array();

			foreach ( $seasonal_prices_schema as $key => $rule ) {
				$seasonal_price_current_value = isset( $seasonal_price_value[ $key ] ) ? MBH_Formatting_Helper::localized_amount( $seasonal_price_value[ $key ] ) : '';
				$every_year = isset( $seasonal_prices_schema[ $key ][ 'every_year' ] ) ? 1 : 0;

				echo '<p class="form-field price"><label><span>' . wp_kses_post( sprintf( __( 'Price from %s to %s:', 'wp-mb_hms' ), '<em>' . esc_html( $rule[ 'from' ] ) . '</em>', '<em>' . esc_html( $rule[ 'to' ] ) . '</em>' ) ) . '</span><input type="text" class="mbh-input-price" name="_room_variations[' . absint( $loop ) . '][seasonal_price][' . esc_attr( $key ) . ']" value="' . $seasonal_price_current_value . '" placeholder="' . self::get_price_placeholder() . '" /></label>';

				if ( $every_year ) {
					echo '<span class="after-input">' . esc_html__( '(Every year)', 'wp-mb_hms' ) . '</span>';
				}

				echo '</p>';
			}

			echo '<p class="change-seasonal-prices-rules"><a href="admin.php?page=mb_hms-settings&tab=seasonal-prices">' . esc_html( 'Change seasonal prices schema', 'wp-mb_hms' ) . '</a></p>';

		} else {
			echo '<p class="message no-seasonal-prices-rules">' . sprintf( wp_kses( __( 'There are no seasonal prices defined. Add some date ranges <a href="%1$s">here</a>.', 'wp-mb_hms' ), array( 'a' => array( 'href' => array() ) ) ), 'admin.php?page=mb_hms-settings&tab=seasonal-prices' ) . '</p>';
		} ?>

	</div><!-- .seasonal-price -->

	<?php
	/**
	 * A filter is provided to allow extensions to add their own price settings
	 */
	do_action( 'mb_hms_room_price_settings_variation', self::get_price_placeholder(), $loop ); ?>

</div><!-- .room-price-panel -->
