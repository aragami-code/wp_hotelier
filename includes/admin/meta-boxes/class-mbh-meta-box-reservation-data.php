<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Meta_Box_Reservation_Data' ) ) :

/**
 * _Meta_Box_Reservation_Data Class
 */
class MBH_Meta_Box_Reservation_Data {

	/**
	 * Guest details
	 *
	 * @var array
	 */
	protected static $guest_details = array();

	/**
	 * Guest info
	 *
	 * @var array
	 */
	protected static $guest_info = array();

	/**
	 * Init guest details fields
	 */
	public static function init_guest_details() {

		self::$guest_details = apply_filters( 'mb_hms_admin_guest_details_fields', array(
			'first_name' => array(
				'label'    => esc_html__( 'First name', 'wp-mb_hms' ),
				'required' => true,
			),
			'last_name' => array(
				'label'         => esc_html__( 'Last name', 'wp-mb_hms' ),
				'wrapper_class' => 'form-field-last',
				'required'      => true,
			),
			'email' => array(
				'label'    => esc_html__( 'Email address', 'wp-mb_hms' ),
				'type'     => 'email',
				'required' => true,
			),
			'telephone' => array(
				'label'         => esc_html__( 'Telephone', 'wp-mb_hms' ),
				'wrapper_class' => 'form-field-last'
			),
			'address1' => array(
				'label'         => esc_html__( 'Address 1', 'wp-mb_hms' ),
				'wrapper_class' => 'form-field-wide'
			),
			'address2' => array(
				'label'         => esc_html__( 'Address 2', 'wp-mb_hms' ),
				'wrapper_class' => 'form-field-wide'
			),
			'city' => array(
				'label' => esc_html__( 'Town / City', 'wp-mb_hms' ),
			),
			'postcode' => array(
				'label'         => esc_html__( 'Postcode / Zip', 'wp-mb_hms' ),
				'wrapper_class' => 'form-field-last'
			),
			'state' => array(
				'label' => esc_html__( 'State / County', 'wp-mb_hms' ),
			),
			'country' => array(
				'label'         => esc_html__( 'Country', 'wp-mb_hms' ),
				'wrapper_class' => 'form-field-last'
			)
		) );
	}

	/**
	 * Init guest info fields
	 */
	public static function init_guest_info() {

		self::$guest_info = apply_filters( 'mb_hms_admin_guest_info_fields', array(
			'guest_arrival_time' => array(
				'id'      => 'guest_arrival_time',
				'label'   => esc_html__( 'Estimated arrival time', 'wp-mb_hms' ),
				'type'    => 'select',
				'options' => array(
					'-1' => esc_html__( 'I don\'t know', 'wp-mb_hms' ),
					'0'  => '00:00 - 01:00',
					'1'  => '01:00 - 02:00',
					'2'  => '02:00 - 03:00',
					'3'  => '03:00 - 04:00',
					'4'  => '04:00 - 05:00',
					'5'  => '05:00 - 06:00',
					'6'  => '06:00 - 07:00',
					'7'  => '07:00 - 08:00',
					'8'  => '08:00 - 09:00',
					'9'  => '09:00 - 10:00',
					'10' => '10:00 - 11:00',
					'11' => '11:00 - 12:00',
					'12' => '12:00 - 13:00',
					'13' => '13:00 - 14:00',
					'14' => '14:00 - 15:00',
					'15' => '15:00 - 16:00',
					'16' => '16:00 - 17:00',
					'17' => '17:00 - 18:00',
					'18' => '18:00 - 19:00',
					'19' => '19:00 - 20:00',
					'20' => '20:00 - 21:00',
					'21' => '21:00 - 22:00',
					'22' => '22:00 - 23:00',
					'23' => '23:00 - 00:00'
				)
			)
		) );
	}

	/**
	 * Get guest details fields
	 */
	public static function get_guest_details_fields() {
		self::init_guest_details();

		return self::$guest_details;
	}

	/**
	 * Get guest info fields
	 */
	public static function get_guest_info_fields() {
		self::init_guest_info();

		return self::$guest_info;
	}

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		global $thereservation;

		if ( ! is_object( $thereservation ) ) {
			$thereservation = mbh_get_reservation( $post->ID );
		}

		$reservation = $thereservation;
		if ( MBH()->payment_gateways() ) {
			$payment_gateways = MBH()->payment_gateways->get_available_payment_gateways();
		} else {
			$payment_gateways = array();
		}

		$payment_method = $reservation->get_payment_method() ? $reservation->get_payment_method() : '';

		$booking_method = $reservation->booking_method;

		self::init_guest_details();
		self::init_guest_info();

		wp_nonce_field( 'mb_hms_save_data', 'mb_hms_meta_nonce' );
		?>

		<style type="text/css">
			#post-body-content, #titlediv, .misc-pub-section.misc-pub-post-status, #visibility, #minor-publishing-actions { display:none }
		</style>

		<div class="panel-wrap mb_hms">
			<input name="post_title" type="hidden" value="<?php echo empty( $post->post_title ) ? esc_attr__( 'Reservation', 'wp-mb_hms' ) : esc_attr( $post->post_title ); ?>" />
			<input name="post_status" type="hidden" value="<?php echo esc_attr( $post->post_status ); ?>" />

			<div id="reservation-data" class="panel">
				<h2><?php echo sprintf( esc_html__( 'Reservation #%d details', 'wp-mb_hms' ), $reservation->id ); ?></h2>

				<p class="booking-details">
					<span><?php echo esc_html__( 'Booking mode', 'wp-mb_hms' ) . ': ' . esc_html( ucfirst( str_replace( '-', ' ', $booking_method ) ) ); ?></span>

					<?php if ( ( get_post_meta( $post->ID, '_created_via', true ) == 'admin' ) ) : ?>
						<span> <?php esc_html_e( '(created by admin)', 'wp-mb_hms' ); ?></span>
					<?php endif; ?>

					<?php if ( $payment_method ) : ?>
						<span> &ndash; <?php echo sprintf( esc_html__( 'Payment via %s', 'wp-mb_hms' ), $reservation->get_payment_method_title() ); ?></span>

						<?php if ( $transaction_id = $reservation->get_transaction_id() ) {
							if ( isset( $payment_gateways[ $payment_method ] ) && ( $url = $payment_gateways[ $payment_method ]->get_transaction_url( $reservation ) ) ) {
								?>
								<div><small><?php _e( 'Transaction ID:', 'wp-mb_hms' ); ?> <a class="transaction-id" href="<?php echo esc_url( $url ); ?>" target="_blank"><?php echo esc_html( $transaction_id ); ?></a></small></div>
								<?php
							}
						} ?>
					<?php endif; ?>

					<?php do_action( 'mb_hms_reservation_after_booking_details' ); ?>
				</p>

				<div class="reservation-data-column-wrap">
					<div class="reservation-data-column">
						<h4><?php esc_html_e( 'General details', 'wp-mb_hms' ); ?></h4>

						<p class="form-field form-field-wide"><label for="reservation-status"><?php _e( 'Reservation status:', 'wp-mb_hms' ) ?></label>

						<select id="reservation-status" name="reservation_status">
							<?php
								$statuses = mbh_get_reservation_statuses();

								foreach ( $statuses as $status => $status_name ) {
									echo '<option value="' . esc_attr( $status ) . '" ' . selected( $status, 'mbh-' . $reservation->get_status(), false ) . '>' . esc_html( $status_name ) . '</option>';
								}
							?>
						</select></p>

						<div class="reservation-details">
							<p>
								<strong><?php esc_html_e( 'Check-in:', 'wp-mb_hms' ) ?></strong>
								<?php echo esc_html( $reservation->get_formatted_checkin() ); ?>
							</p>

							<p>
								<strong><?php esc_html_e( 'Check-out:', 'wp-mb_hms' ) ?></strong>
								<?php echo esc_html( $reservation->get_formatted_checkout() ); ?>
							</p>

							<p class="night-stay"><strong><?php printf( esc_html__( '%d-night stay', 'wp-mb_hms' ), $reservation->get_nights() ); ?></strong></p>
						</div>
					</div>

					<div class="reservation-data-column">
						<h4>
							<?php esc_html_e( 'Guest details', 'wp-mb_hms' ); ?>
							<a href="#" class="edit-address"><i class="dashicons dashicons-edit"></i></a>
						</h4>

						<div class="guest-details guest-data">
							<?php do_action( 'mb_hms_reservation_guest_data' ); ?>

							<?php do_action( 'mb_hms_reservation_before_guest_details' ); ?>

							<?php if ( $reservation->get_formatted_guest_address() ) : ?>
								<p>
									<strong><?php esc_html_e( 'Address', 'wp-mb_hms' ); ?>:</strong>
									<?php echo wp_kses( $reservation->get_formatted_guest_address(), array( 'br' => array() ) ); ?>
								</p>
							<?php endif; ?>

							<?php if ( $reservation->guest_email ) : ?>
								<p>
									<strong><?php esc_html_e( 'Email', 'wp-mb_hms' ); ?>:</strong>
									<a href="mailto:<?php echo esc_attr( esc_html( $reservation->guest_email ) ); ?>"><?php echo esc_html( $reservation->guest_email ); ?></a>
								</p>
							<?php endif; ?>

							<?php if ( $reservation->guest_telephone ) : ?>
								<p>
									<strong><?php esc_html_e( 'Telephone', 'wp-mb_hms' ); ?>:</strong>
									<?php echo esc_html( $reservation->guest_telephone ); ?>
								</p>
							<?php endif; ?>

							<?php do_action( 'mb_hms_reservation_after_guest_details' ); ?>
						</div>

						<div class="edit-guest-details edit-fields">
							<?php
							foreach ( self::$guest_details as $key => $field ) {
								if ( ! isset( $field[ 'id' ] ) ){
									$field[ 'id' ] = '_guest_' . $key;
								}
								MBH_Meta_Boxes_Helper::text_input( $field );
							}
							?>
						</div>
					</div>

					<div class="reservation-data-column">
						<h4>
							<?php esc_html_e( 'Guest notes', 'wp-mb_hms' ); ?>
							<a href="#" class="edit-address"><i class="dashicons dashicons-edit"></i></a>
						</h4>

						<div class="guest-data">

							<?php do_action( 'mb_hms_reservation_before_guest_arrival_time' ); ?>

							<?php if ( $reservation->get_arrival_time() ) : ?>
								<p>
									<strong><?php esc_html_e( 'Estimated arrival time', 'wp-mb_hms' ); ?>:</strong>
									<?php echo esc_html( $reservation->get_formatted_arrival_time() ); ?>
								</p>
							<?php endif; ?>

							<?php do_action( 'mb_hms_reservation_after_guest_arrival_time' ); ?>

							<?php do_action( 'mb_hms_reservation_before_guest_special_requets' ); ?>

							<p class="guest-special-requests">
								<strong><?php esc_html_e( 'Special requests', 'wp-mb_hms' ); ?>:</strong>
								<?php echo esc_html( $reservation->get_guest_special_requests() ? $reservation->get_guest_special_requests() : esc_html__( 'None', 'wp-mb_hms' ) ); ?>
							</p>

							<?php do_action( 'mb_hms_reservation_after_guest_special_requets' ); ?>

						</div>

						<div class="edit-guest-info edit-fields">
							<p class="form-field form-field-wide"><label for="reservation-status"><?php esc_html_e( 'Estimated arrival time', 'wp-mb_hms' ) ?></label>
							<select id="guest-arrival-time" name="guest_arrival_time">
								<?php
									$hours = array(
										'-1' => esc_html__( 'I don\'t know', 'wp-mb_hms' ),
										'0'  => '00:00 - 01:00',
										'1'  => '01:00 - 02:00',
										'2'  => '02:00 - 03:00',
										'3'  => '03:00 - 04:00',
										'4'  => '04:00 - 05:00',
										'5'  => '05:00 - 06:00',
										'6'  => '06:00 - 07:00',
										'7'  => '07:00 - 08:00',
										'8'  => '08:00 - 09:00',
										'9'  => '09:00 - 10:00',
										'10' => '10:00 - 11:00',
										'11' => '11:00 - 12:00',
										'12' => '12:00 - 13:00',
										'13' => '13:00 - 14:00',
										'14' => '14:00 - 15:00',
										'15' => '15:00 - 16:00',
										'16' => '16:00 - 17:00',
										'17' => '17:00 - 18:00',
										'18' => '18:00 - 19:00',
										'19' => '19:00 - 20:00',
										'20' => '20:00 - 21:00',
										'21' => '21:00 - 22:00',
										'22' => '22:00 - 23:00',
										'23' => '23:00 - 00:00'
									);

									foreach ( $hours as $hour => $display ) {
										echo '<option value="' . esc_attr( $hour ) . '" ' . selected( $hour, $reservation->get_arrival_time(), false ) . '>' . esc_html( $display ) . '</option>';
									}
								?>
							</select></p>

							<p class="form-field"><label><?php esc_html_e( 'Special requests', 'wp-mb_hms' ); ?></label><textarea name="guest_special_requests" class="input-text" rows="7" cols="5"><?php echo esc_html( $reservation->get_guest_special_requests() ); ?></textarea></p>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Save reservation data
	 */
	public static function save( $post_id, $post ) {
		$reservation = mbh_get_reservation( $post_id );

		// Reservation status
		$reservation->update_status( sanitize_text_field( $_POST[ 'reservation_status' ] ), '', true );

		// Guest special requests
		$reservation->update_guest_special_requests( sanitize_text_field( $_POST[ 'guest_special_requests' ] ), '', true );

		// Guest estimated arrival time
		$reservation->set_arrival_time( sanitize_text_field( $_POST[ 'guest_arrival_time' ] ) );
	}
}

endif;
