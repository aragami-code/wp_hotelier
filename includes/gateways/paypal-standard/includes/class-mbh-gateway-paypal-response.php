<?php
/**
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Gateway_Paypal_Response' ) ) :

/**
 * ponse Class
 */
class MBH_Gateway_Paypal_Response {

	/** @var bool Sandbox mode */
	protected $sandbox = false;

	/** @var string Receiver email address to validate */
	protected $email;

	/**
	 * Constructor
	 */
	public function __construct( $sandbox = false, $email = '' ) {
		add_action( 'mb_hms_api_mbh_gateway_paypal', array( $this, 'check_response' ) );
		add_action( 'valid-paypal-standard-ipn-request', array( $this, 'valid_response' ) );

		$this->email   = $email;
		$this->sandbox = $sandbox;
	}

	/**
	 * Check for PayPal IPN Response
	 */
	public function check_response() {
		if ( ! empty( $_POST ) && $this->validate_ipn() ) {
			$posted = wp_unslash( $_POST );

			do_action( "valid-paypal-standard-ipn-request", $posted );
			exit;
		}

		wp_die( "PayPal IPN Request Failure", "PayPal IPN", array( 'response' => 500 ) );
	}

	/**
	 * There was a valid response
	 * @param  array $posted Post data after wp_unslash
	 */
	public function valid_response( $posted ) {
		if ( ! empty( $posted[ 'custom' ] ) && ( $reservation = $this->get_paypal_reservation( $posted[ 'custom' ] ) ) ) {

			// Lowercase returned variables
			$posted[ 'payment_status' ] = strtolower( $posted[ 'payment_status' ] );

			// Sandbox fix
			if ( isset( $posted[ 'test_ipn' ] ) && 1 == $posted[ 'test_ipn' ] && 'pending' == $posted[ 'payment_status' ] ) {
				$posted[ 'payment_status' ] = 'confirmed';
			}

			MBH_Gateway_Paypal::log( 'Found reservation #' . $reservation->id );
			MBH_Gateway_Paypal::log( 'Payment status: ' . $posted[ 'payment_status' ] );

			if ( method_exists( $this, 'payment_status_' . $posted[ 'payment_status' ] ) ) {
				call_user_func( array( $this, 'payment_status_' . $posted[ 'payment_status' ] ), $reservation, $posted );
			}
		}
	}

	/**
	 * Check PayPal IPN validity
	 */
	public function validate_ipn() {
		MBH_Gateway_Paypal::log( 'Checking IPN response is valid' );

		// Get received values from post data
		$validate_ipn = array( 'cmd' => '_notify-validate' );
		$validate_ipn += wp_unslash( $_POST );

		// Send back post vars to paypal
		$params = array(
			'body'        => $validate_ipn,
			'timeout'     => 60,
			'httpversion' => '1.1',
			'compress'    => false,
			'decompress'  => false,
			'user-agent'  => 'Mb_hms/' . MBH_VERSION
		);

		// Post back to get a response
		$response = wp_safe_remote_post( $this->sandbox ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr', $params );

		MBH_Gateway_Paypal::log( 'IPN Request: ' . print_r( $params, true ) );
		MBH_Gateway_Paypal::log( 'IPN Response: ' . print_r( $response, true ) );

		// check to see if the request was valid
		if ( ! is_wp_error( $response ) && $response[ 'response' ][ 'code' ] >= 200 && $response[ 'response' ][ 'code' ] < 300 && strstr( $response[ 'body' ], 'VERIFIED' ) ) {
			MBH_Gateway_Paypal::log( 'Received valid response from PayPal' );
			return true;
		}

		MBH_Gateway_Paypal::log( 'Received invalid response from PayPal' );

		if ( is_wp_error( $response ) ) {
			MBH_Gateway_Paypal::log( 'Error response: ' . $response->get_error_message() );
		}

		return false;
	}

	/**
	 * Check for a valid transaction type
	 * @param  string $txn_type
	 */
	protected function validate_transaction_type( $txn_type ) {
		$accepted_types = array( 'cart', 'instant', 'express_checkout', 'web_accept' );

		if ( ! in_array( strtolower( $txn_type ), $accepted_types ) ) {
			MBH_Gateway_Paypal::log( 'Aborting, Invalid type:' . $txn_type );
			exit;
		}
	}

	/**
	 * Check currency from IPN matches the reservation
	 * @param  _Reservation $reservation
	 * @param  string $currency
	 */
	protected function validate_currency( $reservation, $currency ) {
		// Validate currency
		if ( $reservation->get_reservation_currency() != $currency ) {
			MBH_Gateway_Paypal::log( 'Payment error: Currencies do not match (sent "' . $reservation->get_reservation_currency() . '" | returned "' . $currency . '")' );

			// Put this reservation on-hold for manual checking
			$reservation->update_status( 'on-hold', sprintf( esc_html__( 'Validation error: PayPal currencies do not match (code %s).', 'wp-mb_hms' ), $currency ) );
			exit;
		}
	}

	/**
	 * Check payment amount from IPN matches the reservation
	 * @param  _Reservation $reservation
	 */
	protected function validate_amount( $reservation, $amount ) {
		if ( number_format( mbh_convert_to_cents( $reservation->get_deposit() ), 2, '.', '' ) != number_format( $amount, 2, '.', '' ) ) {
			MBH_Gateway_Paypal::log( 'Payment error: Amounts do not match (gross ' . $amount . ')' );

			// Put this reservation on-hold for manual checking
			$reservation->update_status( 'on-hold', sprintf( esc_html__( 'Validation error: PayPal amounts do not match (gross %s).', 'wp-mb_hms' ), $amount ) );

			exit;
		}
	}

	/**
	 * Check payment amount from IPN matches the reservation
	 * @param  _Reservation $reservation
	 */
	protected function validate_receiver_email( $reservation, $email ) {
		if ( strcasecmp( trim( $email ), trim( $this->email ) ) != 0 ) {
			MBH_Gateway_Paypal::log( "IPN Response is for another account: {$receiver_email}. Your email is {$this->receiver_email}" );

			// Put this reservation on-hold for manual checking
			$reservation->update_status( 'on-hold', sprintf( esc_html__( 'Validation error: PayPal IPN response from a different email address (%s).', 'wp-mb_hms' ), $email ) );

			exit;
		}
	}

	/**
	 * Handle a completed payment
	 * @param  _Reservation $reservation
	 */
	protected function payment_status_completed( $reservation, $posted ) {
		if ( $reservation->has_status( 'confirmed' ) ) {
			MBH_Gateway_Paypal::log( 'Aborting, Reservation #' . $reservation->id . ' is already confirmed.' );
			exit;
		} elseif ( $reservation->has_status( 'completed' ) ) {
			MBH_Gateway_Paypal::log( 'Aborting, Reservation #' . $reservation->id . ' was completed on ' . date_i18n( get_option( 'date_format' ), strtotime( $reservation->get_checkout() ) ) . ' .' );
			exit;
		}

		$this->validate_transaction_type( $posted[ 'txn_type' ] );
		$this->validate_currency( $reservation, $posted[ 'mc_currency' ] );
		$this->validate_amount( $reservation, $posted[ 'mc_gross' ] );
		$this->validate_receiver_email( $reservation, $posted[ 'receiver_email' ] );
		$this->save_paypal_meta_data( $reservation, $posted );

		if ( 'completed' === $posted[ 'payment_status' ] ) {
			$this->payment_complete( $reservation, ( ! empty( $posted[ 'txn_id' ] ) ? sanitize_text_field( $posted[ 'txn_id' ] ) : '' ), esc_html__( 'IPN payment completed', 'wp-mb_hms' ) );

			if ( ! empty( $posted[ 'mc_fee' ] ) ) {
				// log paypal transaction fee
				update_post_meta( $reservation->id, 'PayPal Transaction Fee', sanitize_text_field( $posted[ 'mc_fee' ] ) );
			}

		} else {
			$this->payment_on_hold( $reservation, sprintf( esc_html__( 'Payment pending: %s', 'wp-mb_hms' ), $posted[ 'pending_reason' ] ) );
		}
	}

	/**
	 * Handle a pending payment
	 * @param  _Reservation $reservation
	 */
	protected function payment_status_pending( $reservation, $posted ) {
		$this->payment_status_completed( $reservation, $posted );
	}

	/**
	 * Handle a failed payment
	 * @param  _Reservation $reservation
	 */
	protected function payment_status_failed( $reservation, $posted ) {
		$reservation->update_status( 'failed', sprintf( esc_html__( 'Payment %s via IPN.', 'wp-mb_hms' ), sanitize_text_field( $posted[ 'payment_status' ] ) ) );
	}

	/**
	 * Handle a denied payment
	 * @param  L_Reservation $reservation
	 */
	protected function payment_status_denied( $reservation, $posted ) {
		$this->payment_status_failed( $reservation, $posted );
	}

	/**
	 * Handle an expired payment
	 * @param  _Reservation $reservation
	 */
	protected function payment_status_expired( $reservation, $posted ) {
		$this->payment_status_failed( $reservation, $posted );
	}

	/**
	 * Handle a voided payment
	 * @param  _Reservation $reservation
	 */
	protected function payment_status_voided( $reservation, $posted ) {
		$this->payment_status_failed( $reservation, $posted );
	}

	/**
	 * Handle a refunded payment
	 * @param  _Reservation $reservation
	 * @param  array $posted
	 */
	protected function payment_status_refunded( $reservation, $posted ) {
		// Log refund
		MBH_Gateway_Paypal::log( 'Payment refunded via IPN' );

		// Mark reservation as refunded.
		$reservation->update_status( 'refunded', sprintf( esc_html__( 'Payment %s via IPN (gross %s).', 'wp-mb_hms' ), strtolower( $posted[ 'payment_status' ] ), $posted[ 'mc_gross' ] ) );
	}

	/**
	 * Save important data from the IPN to the reservation
	 * @param L_Reservation $reservation
	 */
	protected function save_paypal_meta_data( $reservation, $posted ) {
		if ( ! empty( $posted[ 'payment_type' ] ) ) {
			update_post_meta( $reservation->id, 'Payment type', sanitize_text_field( $posted[ 'payment_type' ] ) );
		}
	}

	/**
	 * Get the reservation from the PayPal 'Custom' variable
	 *
	 * @param  string $raw_custom JSON Data passed back by PayPal
	 * @return boolL_Reservation object
	 */
	protected function get_paypal_reservation( $raw_custom ) {

		// We have the data in the correct format, so get the reservation
		if ( ( $custom = json_decode( $raw_custom ) ) && is_object( $custom ) ) {
			$reservation_id  = $custom->reservation_id;
			$reservation_key = $custom->reservation_key;

		// Nothing was found
		} else {
			MBH_Gateway_Paypal::log( 'Error: Reservation ID and key were not found in "custom".' );
			return false;
		}

		if ( ! $reservation = mbh_get_reservation( $reservation_id ) ) {
			// We have an invalid $reservation_id
			$reservation_id = mbh_get_reservation_id_by_reservation_key( $reservation_key );
			$reservation    = mbh_get_reservation( $reservation_id );
		}

		if ( ! $reservation || $reservation->reservation_key !== $reservation_key ) {
			MBH_Gateway_Paypal::log( 'Error: Reservation Keys do not match.' );
			return false;
		}

		return $reservation;
	}

	/**
	 * Complete reservation and add transaction ID
	 * @param  HTL_Reservation $reservation
	 * @param  string $txn_id
	 * @param  string $note
	 */
	protected function payment_complete( $reservation, $txn_id = '', $note = '' ) {
		$reservation->add_reservation_note( $note );
		$reservation->payment_complete( $txn_id );
	}

	/**
	 * Hold reservation and add note
	 * @param  _Reservation $reservation
	 * @param  string $reason
	 */
	protected function payment_on_hold( $reservation, $reason = '' ) {
		$reservation->update_status( 'on-hold', $reason );
		MBH()->cart->empty_cart();
	}
}

endif;
