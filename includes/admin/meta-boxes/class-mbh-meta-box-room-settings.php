<?php
/**
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Meta_Box_Room_Settings' ) ) :

/**
 * MBH_Meta_Box_Room_Settings Class
 */
class MBH_Meta_Box_Room_Settings {

	/**
	 * Get max guests option.
	 */
	public static function get_guests() {
		$max_guests = apply_filters( 'mb_hms_max_guests', 10 );
		$guests = array_combine( range( 1, $max_guests ), range( 1, $max_guests ) );

		return $guests;
	}

	/**
	 * Get max children option.
	 */
	public static function get_children() {
		$max_children = apply_filters( 'mb_hms_max_children', 5 );
		$children = range( 0, $max_children );

		return $children;
	}

	/**
	 * Get room stock option.
	 */
	public static function get_stock_rooms() {
		$stock_rooms = apply_filters( 'mb_hms_stock_rooms', 15 );
		$quantity = range( 0, $stock_rooms );

		return $quantity;
	}

	/**
	 * Get price placeholder.
	 */
	public static function get_price_placeholder() {
		$thousands_sep = mbh_get_price_thousand_separator();
		$decimal_sep   = mbh_get_price_decimal_separator();
		$decimals      = mbh_get_price_decimals();

		$placeholder = number_format( '0', $decimals, $decimal_sep, $thousands_sep );

		return $placeholder;
	}

	/**
	 * Get deposit options.
	 */
	public static function get_deposit_options() {
		$options =  array(
			'100' => '100%',
			'90'  => '90%',
			'80'  => '80%',
			'70'  => '70%',
			'60'  => '60%',
			'50'  => '50%',
			'40'  => '40%',
			'30'  => '30%',
			'20'  => '20%',
			'10'  => '10%',
		);

		// extensions can hook into here to add their own options
		return apply_filters( 'mb_hms_deposit_options', $options );
	}

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		$thepostid = empty( $thepostid ) ? $post->ID : $thepostid;
		wp_nonce_field( 'mb_hms_save_data', 'mb_hms_meta_nonce' );
		?>

		<div class="panel-wrap room-settings">

			<div class="room-general-settings">

				<?php

				// room type
				MBH_Meta_Boxes_Helper::select_input(
					array(
						'id'      => '_room_type',
						'show_id' => true,
						'label'   => esc_html__( 'Room type:', 'wp-mb_hms' ),
						'options' => array(
							'standard_room' => esc_html__( 'Standard room', 'wp-mb_hms' ),
							'variable_room' => esc_html__( 'Variable room', 'wp-mb_hms' )
						)
					)
				);

				// guests
				MBH_Meta_Boxes_Helper::select_input(
					array(
						'id'      => '_max_guests',
						'show_id' => true,
						'label'   => esc_html__( 'Guests:', 'wp-mb_hms' ),
						'options' => self::get_guests()
					)
				);

				// children
				MBH_Meta_Boxes_Helper::select_input(
					array(
						'id'      => '_max_children',
						'show_id' => true,
						'label'   => esc_html__( 'Children:', 'wp-mb_hms' ),
						'options' => self::get_children()
					)
				);

				// bed size(s)
				MBH_Meta_Boxes_Helper::text_input(
					array(
						'id'          => '_bed_size',
						'show_id'     => true,
						'label'       => esc_html__( 'Bed size(s):', 'wp-mb_hms' ),
						'placeholder' => esc_html__( '1 king', 'wp-mb_hms' )
					)
				);

				// room size
				$mb_hms_dimension_unit = mbh_get_option( 'room_size_unit', 'm²' );
				MBH_Meta_Boxes_Helper::text_input(
					array(
						'id'          => '_room_size',
						'show_id'     => true,
						'label'       => esc_html__( 'Room size', 'wp-mb_hms' ) . ' (' . $mb_hms_dimension_unit . '):' ,
						'placeholder' => '10'
					)
				);

				// available rooms
				MBH_Meta_Boxes_Helper::select_input(
					array(
						'id'          => '_stock_rooms',
						'show_id'     => true,
						'label'       => esc_html__( 'Stock rooms?', 'wp-mb_hms' ),
						'options'     => self::get_stock_rooms(),
						'description' => esc_html__( 'This is the total number of rooms available in the structure.', 'wp-mb_hms' )
					)
				);

				// additional settings button
				?>
				<p class="form-field"><a id="view-room-additional-settings" href="#room-additional-settings" class="button button-primary"><?php esc_html_e( 'Additional settings', 'wp-mb_hms' ) ?></a></p>

				<?php
				/**
				 * A filter is provided to allow extensions to add their own room general settings
				 */
				do_action( 'mb_hms_room_general_settings' ); ?>

			</div><!-- .room-general-settings -->

			<div class="room-advanced-settings">

				<div class="standard-room-panel">

					<h4><?php esc_html_e( 'Standard room', 'wp-mb_hms' ); ?></h4>

					<?php

					// room price

					include( 'views/html-meta-box-room-price.php' );

					?>

					<?php do_action( 'mb_hms_room_standard_settings_after_price' ); ?>

					<div class="room-deposit">

						<?php
						MBH_Meta_Boxes_Helper::checkbox_input(
							array(
								'id'          => '_require_deposit',
								'show_id'     => true,
								'class'       => 'require-deposit',
								'label'       => esc_html__( 'Require deposit?', 'wp-mb_hms' ),
								'description' => esc_html__( 'When selected, a deposit is required at the time of booking.', 'wp-mb_hms' )
							)
						); ?>

						<div class="room-deposit-amount">

							<?php
							MBH_Meta_Boxes_Helper::select_input(
								array(
									'id'      => '_deposit_amount',
									'class'   => 'deposit-amount-select',
									'show_id' => true,
									'label'   => esc_html__( 'Deposit amount:', 'wp-mb_hms' ),
									'options' => self::get_deposit_options()
								)
							);
							?>

							<?php
							/**
							 * A filter is provided to allow extensions to add their own deposit options
							 */
							do_action( 'mb_hms_room_standard_deposit_options' ); ?>

						</div><!-- .room-deposit-amount -->

					</div><!-- .room-deposit -->

					<div class="room-cancellation">
						<?php
						MBH_Meta_Boxes_Helper::checkbox_input(
							array(
								'id'          => '_non_cancellable',
								'show_id'     => true,
								'label'       => esc_html__( 'Non cancellable?', 'wp-mb_hms' ),
								'description' => esc_html__( 'When checked, reservations that include this room will be non cancellable and non refundable.', 'wp-mb_hms' )
							)
						); ?>
					</div>

					<?php

					// room conditions

					include( 'views/html-meta-box-room-conditions.php' );

					?>

					<?php
					/**
					 * A filter is provided to allow extensions to add their own standard room settings
					 */
					do_action( 'mb_hms_room_standard_settings' ); ?>

				</div><!-- .standard-room-panel -->

				<div class="variation-room-panel">

					<h4><?php esc_html_e( 'Variable room', 'wp-mb_hms' ); ?></h4>

					<div class="toolbar">
						<a href="#" class="expand-all"><?php esc_html_e( 'Expand all', 'wp-mb_hms' ); ?></a>
						<a href="#" class="close-all"><?php esc_html_e( 'Close all', 'wp-mb_hms' ); ?></a>
						<button type="button" class="add-variation button button-primary"><?php esc_html_e( 'Add room rate', 'wp-mb_hms' ); ?></button>
					</div>

					<?php
					$get_room_rates = get_terms( 'room_rate', 'hide_empty=0' );

					if ( ! empty( $get_room_rates ) && ! is_wp_error( $get_room_rates ) ) : ?>

						<div class="room-variations">

							<?php
							$variations = maybe_unserialize( get_post_meta( $thepostid, '_room_variations', true ) );

							$loop_lenght = $variations ? count( $variations ) : 1;

							$variation_state_class = $variations ? 'closed' : '';

							for ( $loop = 1; $loop <= $loop_lenght; $loop++ ) : ?>

								<div class="room-variation <?php echo esc_attr( $variation_state_class ); ?>" data-key="<?php echo absint( $loop ); ?>">

									<div class="room-variation-header">

										<?php

										// room rate

										echo '<label><strong>' . esc_html__( 'Room rate:', 'wp-mb_hms' ) . '</strong><select class="room-rates" name="_room_variations[' . absint( $loop ) . '][room_rate]">';

										$room_rate_selected = is_array( $variations ) && isset( $variations[ absint( $loop ) ][ 'room_rate' ] ) ? $variations[ absint( $loop ) ][ 'room_rate' ] : false;

										foreach ( $get_room_rates as $room_rate ) {
											echo '<option value="' . esc_attr( $room_rate->slug ) . '" ' . selected( esc_attr( $room_rate_selected ), esc_attr( $room_rate->slug ), false ) . ' >' . esc_html( $room_rate->name ) . '</option>';
										}
										echo '</select></label>';

										?>

										<button type="button" class="remove-variation button"><?php esc_html_e( 'Remove', 'wp-mb_hms' ); ?></button>

										<input type="hidden" class="variation-index" name="_room_variations[<?php echo absint( $loop ); ?>][index]" value="<?php echo absint( $loop ); ?>">

									</div><!-- .room-variation-header -->

									<div class="room-variation-content">

										<?php

										// room price

										include( 'views/html-meta-box-room-price-variation.php' );

										?>

										<?php do_action( 'mb_hms_room_variation_settings_after_price', absint( $loop ) ); ?>

										<div class="room-deposit">

											<?php
											MBH_Meta_Boxes_Helper::checkbox_input(
												array(
													'id'          => '_room_variations',
													'name'        => '_room_variations[' . absint( $loop ) . '][require_deposit]',
													'depth'       => array( absint( $loop ), 'require_deposit' ),
													'class'       => 'require-deposit',
													'label'       => esc_html__( 'Require deposit?', 'wp-mb_hms' ),
													'description' => esc_html__( 'When selected, a deposit is required at the time of booking.', 'wp-mb_hms' )
												)
											); ?>

											<div class="room-deposit-amount">

												<?php
												MBH_Meta_Boxes_Helper::select_input(
													array(
														'id'      => '_room_variations',
														'class'   => 'deposit-amount-select',
														'name'    => '_room_variations[' . absint( $loop ) . '][deposit_amount]',
														'depth'   => array( absint( $loop ), 'deposit_amount' ),
														'label'   => esc_html__( 'Deposit amount:', 'wp-mb_hms' ),
														'options' => self::get_deposit_options()
													)
												);
												?>

												<?php
												/**
												 * A filter is provided to allow extensions to add their own deposit options
												 */
												do_action( 'mb_hms_room_variation_deposit_options', absint( $loop ) ); ?>

											</div><!-- .room-deposit-amount -->

										</div><!-- .room-deposit -->

										<div class="room-cancellation">
											<?php
											MBH_Meta_Boxes_Helper::checkbox_input(
												array(
													'id'          => '_room_variations',
													'name'        => '_room_variations[' . absint( $loop ) . '][non_cancellable]',
													'depth'       => array( absint( $loop ), 'non_cancellable' ),
													'label'       => esc_html__( 'Non cancellable?', 'wp-mb_hms' ),
													'description' => esc_html__( 'When checked, reservations that include this room will be non cancellable and non refundable.', 'wp-mb_hms' )
												)
											); ?>
										</div>

										<?php
										// room conditions

										include( 'views/html-meta-box-room-conditions-variation.php' );

										?>

										<?php
										/**
										 * A filter is provided to allow extensions to add their own room variation settings
										 */
										do_action( 'mb_hms_room_variation_settings' ); ?>

									</div><!-- .room-variation-content -->

								</div><!-- .room-variation -->

							<?php endfor; ?>

						</div><!-- .room-variations -->

					<?php else : ?>

						<p class="message empty-variations"><?php printf( wp_kses( __( 'Before adding variations, add and save some <a href="%1$s">room rates</a>.', 'wp-mb_hms' ), array( 'a' => array( 'href' => array() ) ) ), 'edit-tags.php?taxonomy=room_rate&post_type=room' ); ?></p>

					<?php endif; ?>

					<div class="toolbar">
						<a href="#" class="expand-all"><?php esc_html_e( 'Expand all', 'wp-mb_hms' ); ?></a>
						<a href="#" class="close-all"><?php esc_html_e( 'Close all', 'wp-mb_hms' ); ?></a>
						<button type="button" class="add-variation button button-primary"><?php esc_html_e( 'Add room rate', 'wp-mb_hms' ); ?></button>
					</div>

				</div><!-- .variation-room-panel -->

			</div><!-- .room-advanced-settings -->

			<div id="room-additional-settings" class="room-additional-settings">
				<h4><?php esc_html_e( 'Additional room settings', 'wp-mb_hms' ); ?></h4>

				<p class="form-field room-additional-settings-close-button">
					<a href="#mb_hms-room-settings" id="close-room-additional-settings" class="button"><?php esc_html_e( 'Back to the room settings', 'wp-mb_hms' ); ?></a>
				</p>

				<?php
				/**
				 * A filter is provided to allow extensions to add their own room additional settings
				 */
				do_action( 'mb_hms_room_before_additional_settings' );

				// additional details
				MBH_Meta_Boxes_Helper::textarea_input(
					array(
						'id'          => '_room_additional_details',
						'show_id'     => true,
						'label'       => esc_html__( 'Additional details', 'wp-mb_hms' ),
						'description' => esc_html__( 'These details are not prominent by default; however, some themes may show them.', 'wp-mb_hms' )
					)
				);

				/**
				 * A filter is provided to allow extensions to add their own room additional settings
				 */
				do_action( 'mb_hms_room_after_additional_settings' );
				?>
			</div>

		</div><!-- .room-settings -->

		<?php
	}
}

endif;
