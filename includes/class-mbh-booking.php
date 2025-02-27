<?php
/**
 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Booking' ) ) :

/**
 * MBH_Booking Class
 */
class MBH_Booking {

	/**
	 * Array of posted form data.
	 *
	 * @var array
	 */
	public $form_data;

	/**
	 * Array of fields to display on the booking form.
	 *
	 * @var array
	 */
	public $booking_fields;

	/**
	 * The booking method being used.
	 *
	 * @var string
	 */
	public $booking_method;

	/**
	 * The payment gateway being used.
	 *
	 * @var bool
	 */
	public $payment_method;

	/**
	 * The arrival date of the guest.
	 *
	 * @var string
	 */
	public $checkin;

	/**
	 * The departure date of the guest.
	 *
	 * @var string
	 */
	public $checkout;

	/**
	 * @var MBH_Booking The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Main MBH_Booking Instance
	 *
	 * Insures that only one instance of MBH_Booking exists in memory at any one time.
	 *
	 * @static
	 * @return MBH_Booking Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	}

	/**
	 * Cloning is forbidden.
	 */
	public function __clone() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'wp-mb_hms' ), '1.0.0' );
	}

	/**
	 * Unserializing instances of this class is forbidden.
	 */
	public function __wakeup() {
		_doing_it_wrong( __FUNCTION__, esc_html__( 'Cheatin&#8217; huh?', 'wp-mb_hms' ), '1.0.0' );
	}

	/**
	 * Constructor for the MBH_Booking class.
	 *
	 * @access public
	 */
	public function __construct () {
		// actions
		add_action( 'mb_hms_booking_guest_details', array( $this, 'booking_form_guest_details' ) );
		add_action( 'mb_hms_booking_additional_information', array( $this, 'booking_form_additional_information' ) );
		add_action( 'mb_hms_booking_details', array( $this, 'booking_details' ) );
		add_action( 'mb_hms_booking_table', array( $this, 'booking_table' ) );
		add_action( 'mb_hms_booking_payment', array( $this, 'payment_section' ) );
		add_action( 'mb_hms_book_button', array( $this, 'book_button' ) );

		$this->booking_method = mbh_get_option( 'booking_mode', 'manual-booking' );
		$this->booking_fields = $this->get_booking_fields();
		$this->checkin        = MBH()->session->get( 'checkin' );
		$this->checkout       = MBH()->session->get( 'checkout' );

		do_action( 'mb_hms_booking_init', $this );
	}

	/**
	 * Create a reservation.
	 *
	 * Error codes:
	 * 		400 - Cannot insert reservation into the database (reservations_items table)
	 * 		401 - Cannot insert booking into the database (bookings table)
	 * 		402 - Cannot populate room_bookings
	 * 		403 - Cannot add item to reservation
	 * 		404 - Cannot update existing reservation
	 *
	 * @access public
	 * @throws Exception
	 * @return int|WP_ERROR
	 */
	public function create_reservation() {
		global $wpdb;

		try {
			// Start transaction if available
			$wpdb->query( 'START TRANSACTION' );

			$reservation_data = array(
				'status'           => 'pending',
				'guest_name'       => $this->get_formatted_guest_full_name(),
				'email'            => $this->get_form_data_field( 'email' ),
				'special_requests' => $this->get_form_data_field( 'special_requests' ),
				'created_via'      => 'booking'
			);

			// Insert or update the post data
			$reservation_id = absint( MBH()->session->get( 'reservation_awaiting_payment' ) );

			// Resume the unpaid reservation if its pending
			if ( $reservation_id > 0 && ( $reservation = mbh_get_reservation( $reservation_id ) ) && $reservation->has_status( array( 'pending', 'failed' ) ) ) {

				$reservation_data[ 'reservation_id' ] = $reservation_id;
				$reservation                          = mbh_update_reservation( $reservation_data );

				if ( is_wp_error( $reservation ) ) {
					throw new Exception( sprintf( esc_html__( 'Error %d: Unable to create reservation. Please try again.', 'wp-mb_hms' ), 404 ) );
				} else {
					$reservation->remove_reservation_items();
					do_action( 'mb_hms_resume_reservation', $reservation_id );
				}

			} else {

				$reservation = mbh_create_reservation( $reservation_data );

				if ( is_wp_error( $reservation ) ) {
					throw new Exception( sprintf( esc_html__( 'Error %d: Unable to create reservation. Please try again.', 'wp-mb_hms' ), 400 ) );
				} else {
					$reservation_id = $reservation->id;
					$booking_id = mbh_add_booking( $reservation_id, $this->checkin, $this->checkout, 'pending' );

					if ( ! $booking_id ) {
						throw new Exception( sprintf( esc_html__( 'Error %d: Unable to create reservation. Please try again.', 'wp-mb_hms' ), 401 ) );
					}

					do_action( 'mb_hms_new_reservation', $reservation_id );
				}
			}

			// Guest address
			$guest_address = array();

			if ( $this->booking_fields[ 'address_fields' ] ) {
				foreach ( array_keys( $this->booking_fields[ 'address_fields' ] ) as $field ) {
					$guest_address[ $field ] = $this->get_form_data_field( $field );
				}
			}

			foreach ( MBH()->cart->get_cart() as $cart_item_key => $values ) {
				for ( $i = 0; $i < $values[ 'quantity' ]; $i++ ) {
					$rooms_bookings_id = mbh_populate_rooms_bookings( $reservation_id, $values[ 'room_id' ] );

					if ( ! $rooms_bookings_id ) {
						throw new Exception( sprintf( esc_html__( 'Error %d: Unable to create reservation. Please try again.', 'wp-mb_hms' ), 402 ) );
					}
				}

				// Since the version 1.2.0, the calculated deposit is saved into
				// the reservation meta (as well as the percent deposit)
				$deposit = round( ( $values[ 'price' ] * $values[ 'deposit' ] ) / 100 );
				$deposit = array(
					'deposit'         => $deposit,
					'percent_deposit' => $values[ 'deposit' ],
				);
				$deposit = apply_filters( 'mb_hms_get_item_deposit_for_reservation', $deposit, $values );

				// Default adults/children for this room
				$adults   = $values[ 'max_guests' ];
				$children = 0;

				if ( mbh_get_option( 'booking_number_of_guests_selection', true ) ) {
					// Adults
					$cart_adults = isset( $this->form_data[ 'adults' ] ) ? $this->form_data[ 'adults' ] : false;

					if ( $cart_adults && is_array( $cart_adults ) && isset( $cart_adults[ $cart_item_key ] ) ) {
						$adults = $cart_adults[ $cart_item_key ];

						// Sanitize values
						$adults = array_map( 'absint', $adults );
					}

					// Children
					$cart_children = isset( $this->form_data[ 'children' ] ) ? $this->form_data[ 'children' ] : false;

					if ( $cart_children && is_array( $cart_children ) && isset( $cart_children[ $cart_item_key ] ) ) {
						$children = $cart_children[ $cart_item_key ];

						// Sanitize values
						$children = array_map( 'absint', $children );
					}
				}

				$item_id = $reservation->add_item(
					$values[ 'data' ],
					$values[ 'quantity' ],
					array(
						'rate_name'       => $values[ 'rate_name' ],
						'max_guests'      => $values[ 'max_guests' ],
						'price'           => $values[ 'price' ],
						'total'           => $values[ 'total' ],
						'percent_deposit' => $deposit[ 'percent_deposit' ],
						'deposit'         => $deposit[ 'deposit' ],
						'is_cancellable'  => $values[ 'is_cancellable' ],
						'adults'          => $adults,
						'children'        => $children,
					)
				);

				if ( ! $item_id ) {
					throw new Exception( sprintf( esc_html__( 'Error %d: Unable to create reservation. Please try again.', 'wp-mb_hms' ), 403 ) );
				}

				// Allow plugins to add reservation item meta
				do_action( 'mb_hms_add_reservation_item_meta', $item_id, $values, $cart_item_key );
			}

			$reservation->set_checkin( $this->checkin );
			$reservation->set_checkout( $this->checkout );
			$reservation->set_address( $guest_address );
			$reservation->set_arrival_time( $this->get_form_data_field( 'arrival_time' ) );
			$reservation->set_booking_method( $this->booking_method );
			$reservation->set_subtotal( MBH()->cart->get_subtotal() );
			$reservation->set_tax_total( MBH()->cart->get_tax_total() );
			$reservation->set_total( MBH()->cart->get_total() );
			$reservation->set_deposit( MBH()->cart->get_required_deposit() );
			$reservation->set_payment_method( $this->payment_method );

			// Let extensions add their own meta
			do_action( 'mb_hms_booking_update_reservation_meta', $reservation_id, $this->form_data );

			// If we got here, the reservation was created without problems!
			$wpdb->query( 'COMMIT' );

		} catch ( Exception $e ) {

			// There was an error adding reservation data
			$wpdb->query( 'ROLLBACK' );
			return new WP_Error( 'booking-error', $e->getMessage() );
		}

		return $reservation_id;
	}

	/**
	 * Get booking form fields.
	 *
	 * @access public
	 * @return array
	 */
	public function get_booking_fields() {
		/**
		 * Filters are provided for each form section to allow
		 * extensions and other plugins to add their own fields
		 */
		$fields = array(
			'address_fields' => apply_filters( 'mb_hms_booking_default_address_fields',
				array(
					'first_name' => array(
						'label'       => esc_html__( 'First name', 'wp-mb_hms' ),
						'placeholder' => esc_html_x( 'First name', 'placeholder', 'wp-mb_hms' ),
						'required'    => true,
						'class'       => array( 'form-row--first' ),
					),
					'last_name' => array(
						'label'       => esc_html__( 'Last name', 'wp-mb_hms' ),
						'placeholder' => esc_html_x( 'Last name', 'placeholder', 'wp-mb_hms' ),
						'required'    => true,
						'class'       => array( 'form-row--last' ),
						'clear'       => true
					),
					'email' => array(
						'label'       => esc_html__( 'Email address', 'wp-mb_hms' ),
						'placeholder' => esc_html_x( 'Email address', 'placeholder', 'wp-mb_hms' ),
						'type'        => 'email',
						'required'    => true,
						'class'       => array( 'form-row--wide' ),
						'validate'    => array( 'email' )
					),
					'telephone' => array(
						'label'       => esc_html__( 'Telephone', 'wp-mb_hms' ),
						'placeholder' => esc_html_x( 'Telephone', 'placeholder', 'wp-mb_hms' ),
						'type'        => 'tel',
						'required'    => true,
						'class'       => array( 'form-row--wide' ),
						'validate'    => array( 'phone' )
					),
					'country' => array(
						'label'       => esc_html__( 'Country', 'wp-mb_hms' ),
						'placeholder' => esc_html_x( 'Country', 'placeholder', 'wp-mb_hms' ),
						'required'    => true,
						'class'       => array( 'form-row--wide' ),
					),
					'address1' => array(
						'label'       => esc_html__( 'Address', 'wp-mb_hms' ),
						'placeholder' => esc_html_x( 'Street address', 'placeholder', 'wp-mb_hms' ),
						'required'    => false,
						'class'       => array( 'form-row--wide' )
					),
					'address2' => array(
						'placeholder' => esc_html_x( 'Apartment, suite, unit etc. (optional)', 'placeholder', 'wp-mb_hms' ),
						'required'    => false,
						'class'       => array( 'form-row--wide' )
					),
					'city' => array(
						'label'       => esc_html__( 'Town / City', 'wp-mb_hms' ),
						'placeholder' => esc_html_x( 'Town / City', 'placeholder', 'wp-mb_hms' ),
						'required'    => false,
						'class'       => array( 'form-row--wide' )
					),
					'state' => array(
						'label'       => esc_html__( 'State / County', 'wp-mb_hms' ),
						'placeholder' => esc_html_x( 'State / County', 'placeholder', 'wp-mb_hms' ),
						'required'    => false,
						'class'       => array( 'form-row--first' )
					),
					'postcode' => array(
						'label'       => esc_html__( 'Postcode / Zip', 'wp-mb_hms' ),
						'placeholder' => esc_html_x( 'Postcode / Zip', 'placeholder', 'wp-mb_hms' ),
						'required'    => false,
						'class'       => array( 'form-row--last' ),
						'clear'       => true
					)
				)
			)
		);

		if ( mbh_get_option( 'booking_additional_information' ) ) {
			$fields[ 'additional_information_fields' ] = apply_filters( 'mb_hms_booking_additional_information_fields',
				array(
					'arrival_time' => array(
						'label'    => esc_html__( 'Your estimated time of arrival', 'wp-mb_hms' ),
						'desc'     => mbh_get_option( 'hotel_locality' ) ? sprintf( esc_html__( 'Time is for %s time zone', 'wp-mb_hms' ), mbh_get_option( 'hotel_locality' ) ) : '',
						'required' => false,
						'class'    => array( 'form-row--wide form-row--arrival-time' ),
						'type'     => 'select',
						'options'  => array(
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
					),
					'special_requests' => array(
						'label'    => esc_html__( 'Special requests', 'wp-mb_hms' ),
						'required' => false,
						'type'     => 'textarea',
						'desc'     => mbh_get_option( 'hotel_special_requests_message', esc_html__( 'Special requests cannot be guaranteed but we will do our best to meet your needs.', 'wp-mb_hms' ) ),
						'class'    => array( 'form-row--wide' )
					)
				)
			);
		}

		return apply_filters( 'mb_hms_booking_fields', $fields );
	}

	/**
	 * Checkout process
	 */
	public function check_rooms_availability() {
		// When we process the booking, lets ensure room items are rechecked to prevent
		// unavailable rooms are booked
		do_action( 'mb_hms_booking_check_rooms_availability' );
	}

	/**
	 * Output the guest details form
	 */
	public function booking_form_guest_details() {
		mbh_get_template( 'booking/form-guest-details.php', array( 'booking' => $this ) );
	}

	/**
	 * Output the additional information form
	 */
	public function booking_form_additional_information() {
		mbh_get_template( 'booking/form-additional-information.php', array( 'booking' => $this ) );
	}

	/**
	 * Output the book button
	 */
	public function book_button() {
		if ( $this->booking_method == 'instant-booking' ) {
			if ( MBH()->cart->needs_payment() ) {

				$button_text = apply_filters( 'mb_hms_instant_booking_with_deposit_button_text', esc_html__( 'Book now & pay deposit', 'wp-mb_hms' ) );

			} else {

				$button_text = apply_filters( 'mb_hms_instant_booking_button_text', esc_html__( 'Book now', 'wp-mb_hms' ) );

			}

		} elseif ( $this->booking_method == 'manual-booking' ) {

			$button_text = apply_filters( 'mb_hms_manual_booking_button_text', esc_html__( 'Send request', 'wp-mb_hms' ) );

		}

		mbh_get_template( 'booking/book-button.php', array(
			'booking'     => $this,
			'button_text' => $button_text
		) );
	}

	/**
	 * Output the booking details
	 */
	public function booking_details() {
		$checkin      = date_i18n( get_option( 'date_format' ), strtotime( $this->checkin ) ) . ' (' . MBH_Info::get_hotel_checkin() . ')';
		$checkout     = date_i18n( get_option( 'date_format' ), strtotime( $this->checkout ) ) . ' (' . MBH_Info::get_hotel_checkout() . ')';
		$pets_message = MBH_Info::get_hotel_pets_message();
		$cards        = MBH_Info::get_hotel_accepted_credit_cards();

		mbh_get_template( 'booking/reservation-details.php', array(
			'booking'      => $this,
			'checkin'      => $checkin,
			'checkout'     => $checkout,
			'pets_message' => $pets_message,
			'cards'        => $cards,
		) );
	}

	/**
	 * Output the booking table
	 */
	public function booking_table() {
		$checkin  = new DateTime( $this->checkin );
		$checkout = new DateTime( $this->checkout );
		$nights   = $checkin->diff( $checkout )->days;

		mbh_get_template( 'booking/reservation-table.php', array(
			'booking' => $this,
			'nights'  => $nights,

		) );
	}

	/**
	 * Output the payment section
	 */
	public function payment_section() {
		if ( $this->booking_method != 'instant-booking' ) {
			return;
		}

		$available_gateways = MBH()->payment_gateways()->get_available_payment_gateways();
		MBH()->payment_gateways()->set_selected_gateway( $available_gateways );

		if ( $this->booking_method == 'instant-booking' && MBH()->cart->needs_payment() ) {

			mbh_get_template( 'booking/payment.php', array(
				'booking'            => $this,
				'available_gateways' => $available_gateways
			) );

		}
	}

	/**
	 * Process the form after the book button is pressed
	 */
	public function process_booking() {
		try {
			if ( empty( $_POST[ '_wpnonce' ] ) || ! wp_verify_nonce( $_POST[ '_wpnonce' ], 'mb_hms_process_booking' ) ) {
				throw new Exception( esc_html__( 'We were unable to process your reservation, please try again.', 'wp-mb_hms' ) );
			}

			if ( ! defined( 'mb_hms_BOOKING' ) ) {
				define( 'mb_hms_BOOKING', true );
			}

			// Prevent timeout
			@set_time_limit( 0 );

			do_action( 'mb_hms_before_booking_process' );

			if ( MBH()->cart->is_empty() ) {
				throw new Exception( sprintf( wp_kses( __( 'Sorry, your session has expired. <a href="%s" class="mbh-backward">Return to homepage</a>', 'wp-mb_hms' ), array( 'a' => array( 'href' => array() ) ) ), home_url() ) );
			}

			do_action( 'mb_hms_booking_process' );

			// Booking fields (not defined in booking_fields)
			$this->form_data[ 'booking_terms' ]  = isset( $_POST[ 'booking_terms' ] ) ? 1 : 0;
			$this->form_data[ 'payment_method' ] = isset( $_POST[ 'payment_method' ] ) ? sanitize_text_field( $_POST[ 'payment_method' ] ) : '';

			// Add adults/children dropdowns if enabled
			if ( mbh_get_option( 'booking_number_of_guests_selection', true ) ) {
				$this->form_data[ 'adults' ]   = isset( $_POST[ 'adults' ] ) ? $_POST[ 'adults' ] : 0;
				$this->form_data[ 'children' ] = isset( $_POST[ 'children' ] ) ? $_POST[ 'children' ] : 0;
			}

			MBH()->session->set( 'chosen_payment_method', $this->form_data[ 'payment_method' ] );

			// Get posted booking_fields and sanitize
			foreach ( $this->booking_fields as $fieldset_key => $fieldset ) {

				foreach ( $fieldset as $key => $field ) {

					if ( ! isset( $field[ 'type' ] ) ) {
						$field[ 'type' ] = 'text';
					}

					switch ( $field[ 'type' ] ) {

						case 'checkbox' :
							$this->form_data[ $key ] = isset( $_POST[ $key ] ) ? 1 : 0;

							break;

						case 'textarea' :
							$this->form_data[ $key ] = isset( $_POST[ $key ] ) ? wp_strip_all_tags( wp_check_invalid_utf8( stripslashes( $_POST[ $key ] ) ) ) : '';

							break;

						default :
							$this->form_data[ $key ] = isset( $_POST[ $key ] ) ? ( is_array( $_POST[ $key ] ) ? array_map( 'sanitize_text_field', $_POST[ $key ] ) : sanitize_text_field( $_POST[ $key ] ) ) : '';

							break;
					}

					// Hook
					$this->form_data[ $key ] = apply_filters( 'mb_hms_process_booking_field_' . $key, $this->form_data[ $key ] );

					// Validation: Required fields
					if ( isset( $field[ 'required' ] ) && $field[ 'required' ] && empty( $this->form_data[ $key ] ) ) {
						mbh_add_notice( '<strong>' . $field[ 'label' ] . '</strong> ' . esc_html__( 'is a required field.', 'wp-mb_hms' ), 'error' );
					}

					if ( ! empty( $this->form_data[ $key ] ) ) {
						if ( ! empty( $field[ 'validate' ] ) && is_array( $field[ 'validate' ] ) ) {

							foreach ( $field[ 'validate' ] as $data_type ) {
								switch ( $data_type ) {
									case 'email' :
										if ( ! is_email( $this->form_data[ $key ] ) ) {
											mbh_add_notice( '<strong>' . $field[ 'label' ] . '</strong> ' . esc_html__( 'is not a valid email address.', 'wp-mb_hms' ), 'error' );
										}

										break;

									case 'number' :
										if ( ! is_numeric( $this->form_data[ $key ] ) ) {
											mbh_add_notice( '<strong>' . $field[ 'label' ] . '</strong> ' . esc_html__( 'is not a valid number.', 'wp-mb_hms' ), 'error' );
										}

										break;

									case 'phone' :
										$this->form_data[ $key ] = MBH_Formatting_Helper::validate_phone( $this->form_data[ $key ] );

										if ( ! MBH_Formatting_Helper::is_phone( $this->form_data[ $key ] ) ) {
											mbh_add_notice( '<strong>' . $field[ 'label' ] . '</strong> ' . esc_html__( 'is not a valid phone number.', 'wp-mb_hms' ), 'error' );
										}

										break;
								}
							}
						}
					}
				}
			}

			// Terms and conditions
			if ( ! empty( $_POST[ 'has_terms_field' ] ) && empty( $this->form_data[ 'booking_terms' ] ) ) {
				mbh_add_notice( esc_html__( 'You must accept our Terms &amp; Conditions.', 'wp-mb_hms' ), 'error' );
			}

			MBH()->cart->calculate_totals();

			if ( $this->booking_method == 'instant-booking' && MBH()->cart->needs_payment() ) {
				// Payment Method
				$available_gateways = MBH()->payment_gateways()->get_available_payment_gateways();

				if ( ! isset( $available_gateways[ $this->form_data[ 'payment_method' ] ] ) ) {
					$this->payment_method = '';
					mbh_add_notice( esc_html__( 'Invalid payment method.', 'wp-mb_hms' ), 'error' );
				} else {
					$this->payment_method = $available_gateways[ $this->form_data[ 'payment_method' ] ];
					$this->payment_method->validate_fields();
				}
			} else {
				$available_gateways = array();
			}

			// Action after validation
			do_action( 'mb_hms_after_booking_validation', $this->form_data );

			if ( mbh_notice_count( 'error' ) == 0 ) {

				// Do a final check at this point
				$this->check_rooms_availability();

				// Abort if errors are present
				if ( mbh_notice_count( 'error' ) > 0 ) {
					throw new Exception( sprintf( wp_kses( __( 'List of available rooms <a href="%s">here</a>', 'wp-mb_hms' ), array( 'a' => array( 'href' => array() ) ) ), MBH()->cart->get_room_list_form_url() ) );
				}

				$reservation_id = $this->create_reservation();

				if ( is_wp_error( $reservation_id ) ) {
					throw new Exception( $reservation_id->get_error_message() );
				}

				do_action( 'mb_hms_booking_reservation_processed', $reservation_id, $this->form_data );

				// Process payment
				if ( $this->booking_method == 'instant-booking' && MBH()->cart->needs_payment() ) {

					// Store Reservation ID in session so it can be re-used after payment failure
					MBH()->session->set( 'reservation_awaiting_payment', $reservation_id );

					// Process Payment
					$result = $available_gateways[ $this->form_data[ 'payment_method' ] ]->process_payment( $reservation_id );

					// Redirect to success/confirmation/payment page
					if ( $result[ 'result' ] == 'success' ) {

						$result = apply_filters( 'mb_hms_payment_successful_result', $result, $reservation_id );

						wp_redirect( $result[ 'redirect' ] );
						exit;
					}

				}  else {

					if ( empty( $reservation ) ) {
						$reservation = mbh_get_reservation( $reservation_id );
					}

					if ( $this->booking_method == 'instant-booking' ) {

						// No payment was required for reservation
						$reservation->payment_complete();

					} else {

						// Manual booking - Send request
						$reservation->send_request();
					}

					// Empty the Cart
					MBH()->cart->empty_cart();

					// Get redirect
					$return_url = $reservation->get_booking_received_url();

					// Redirect to success/confirmation/payment page
					wp_safe_redirect(
						apply_filters( 'mb_hms_booking_no_payment_needed_redirect', $return_url, $reservation )
					);
					exit;
				}
			}

		} catch ( Exception $e ) {
			if ( ! empty( $e ) ) {
				mbh_add_notice( $e->getMessage(), 'error' );
			}
		}
	}

	/**
	 * Get a form field value after sanitization and validation.
	 * @param string $field
	 * @return string
	 */
	public function get_form_data_field( $field ) {
		return isset( $this->form_data[ $field ] ) ? $this->form_data[ $field ] : '';
	}

	/**
	 * Get a formatted guest full name.
	 *
	 * @return string
	 */
	public function get_formatted_guest_full_name() {
		return sprintf( '%1$s %2$s', $this->get_form_data_field( 'first_name' ), $this->get_form_data_field( 'last_name' ) );
	}

	/**
	 * Gets the value from the posted data
	 *
	 * @access public
	 * @param string $input
	 * @return string|null
	 */
	public function get_value( $input ) {
		if ( ! empty( $_POST[ $input ] ) ) {

			$value = ( is_array( $_POST[ $input ] ) ) ? array_map( 'sanitize_text_field', $_POST[ $input ] ) : sanitize_text_field( $_POST[ $input ] );

			return $value;

		} else {

			$value = apply_filters( 'mb_hms_booking_get_value', null, $input );

			if ( $value !== null ) {
				return $value;
			}
		}
	}
}

endif;
