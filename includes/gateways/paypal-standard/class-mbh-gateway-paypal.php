<?php
/**
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

class MBH_Gateway_Paypal extends MBH_Payment_Gateway {

	/** @var boolean Whether or not logging is enabled */
	public static $log_enabled = false;

	/** @var HTL_Log Logger instance */
	public static $log = false;

	/**
	 * Get things going.
	 */
	public function __construct() {
		$this->id          = 'paypal';
		$this->title       = esc_html__( 'PayPal', 'wp-mb_hms' );
		$this->description = mbh_get_option( 'paypal_message' );
		$this->icon        = MBH()->plugin_url() . '/includes/gateways/paypal-standard/assets/images/paypal.svg';
		$this->testmode    = mbh_get_option( 'paypal_sandbox' ) ? true : false;
		$this->debug       = mbh_get_option( 'paypal_log' ) ? true : false;
		$this->email       = $this->get_email();

		self::$log_enabled = $this->debug;

		include_once( 'includes/class-mbh-gateway-paypal-response.php' );
		new MBH_Gateway_Paypal_Response( $this->testmode, $this->email );

		add_filter( 'mb_hms_settings_payment', array( $this, 'settings_fields' ), 0 );
	}

	/**
	 * Add settings fields in admin.
	 */
	public function settings_fields( $fields ) {
		$gateway_fields = array(
			'paypal_settings' => array(
				'id'   => 'paypal_settings',
				'name' => '<strong>' . esc_html__( 'PayPal settings', 'wp-mb_hms' ) . '</strong>',
				'type' => 'header'
			),
			'paypal_description' => array(
				'id'   => 'paypal_description',
				'desc' => sprintf( wp_kses( __( 'PayPal standard sends customers to PayPal to enter their payment information. PayPal IPN requires <a href="%s">fsockopen/cURL</a> support to update bookings after payment.', 'wp-mb_hms' ), array( 'a' => array( 'href' => array() ) ) ), esc_url( '?page=mb_hms-settings&tab=tools' ) ),
				'type' => 'description'
			),
			'paypal_message' => array(
				'id'   => 'paypal_message',
				'name' => esc_html__( 'PayPal description', 'wp-mb_hms' ),
				'desc' => esc_html__( 'The description the user sees during the booking.', 'wp-mb_hms' ),
				'std'  => esc_html__( 'Pay with PayPal - The safer, easier way to pay online!', 'wp-mb_hms' ),
				'type' => 'textarea'
			),
			'paypal_sandbox' => array(
				'id'   => 'paypal_sandbox',
				'name' => esc_html__( 'PayPal sandbox', 'wp-mb_hms' ),
				'desc' => esc_html__( 'Enable test mode.', 'wp-mb_hms' ),
				'subdesc' => esc_html__( 'While in test mode no live transactions are processed. To fully use test mode, you must have a PayPal sandbox (test) account.', 'wp-mb_hms' ),
				'type' => 'checkbox'
			),
			'paypal_log' => array(
				'id'   => 'paypal_log',
				'name' => esc_html__( 'Debug log', 'wp-mb_hms' ),
				'desc' => esc_html__( 'Enable logging.', 'wp-mb_hms' ),
				'subdesc' => sprintf( __( 'Log PayPal events, such as IPN requests, inside <code>%s</code>. Please note: this may log personal information. We recommend using this for debugging purposes only and deleting the logs when finished.', 'wp-mb_hms' ), mbh_get_log_file_path( 'paypal' ) ),
				'type' => 'checkbox'
			),
			'paypal_email' => array(
				'id'   => 'paypal_email',
				'name' => esc_html__( 'PayPal email', 'wp-mb_hms' ),
				'desc' => esc_html__( 'Enter your PayPal account\'s email.', 'wp-mb_hms' ),
				'type' => 'text',
				'std'  => ''
			),
			'paypal_page_style' => array(
				'id'   => 'paypal_page_style',
				'name' => esc_html__( 'PayPal page style', 'wp-mb_hms' ),
				'desc' => esc_html__( 'Enter the name of the page style to use, or leave blank for default. These are defined within your PayPal account.', 'wp-mb_hms' ),
				'type' => 'text',
				'std'  => ''
			),
		);

		return array_merge( $fields, $gateway_fields );
	}

	/**
	 * Logging method
	 * @param  string $message
	 */
	public static function log( $message ) {
		if ( self::$log_enabled ) {
			if ( empty( self::$log ) ) {
				self::$log = new MBH_Log();
			}

			self::$log->add( 'paypal', $message );
		}
	}

	/**
	 * Get valid PayPal email.
	 *
	 * @return string
	 */
	public function get_email() {
		$email = mbh_get_option( 'paypal_email' );

		if ( is_email( $email ) ) {
			return $email;
		} else {
			return '';
		}
	}

	/**
	 * Get the transaction URL.
	 *
	 * @param  Reservation $reservation
	 *
	 * @return string
	 */
	public function get_transaction_url( $reservation ) {
		if ( $this->testmode ) {
			$this->view_transaction_url = 'https://www.sandbox.paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=%s';
		} else {
			$this->view_transaction_url = 'https://www.paypal.com/cgi-bin/webscr?cmd=_view-a-trans&id=%s';
		}

		return parent::get_transaction_url( $reservation );
	}

	/**
	 * Process the payment and return the result
	 *
	 * @param int $reservation_id
	 * @return array
	 */
	public function process_payment( $reservation_id ) {
		include_once( 'includes/class-mbh-gateway-paypal-request.php' );

		$reservation    = mbh_get_reservation( $reservation_id );
		$paypal_request = new MBH_Gateway_Paypal_Request( $this );

		// Remove cart
		MBH()->cart->empty_cart();

		return array(
			'result'   => 'success',
			'redirect' => $paypal_request->get_request_url( $reservation, $this->testmode )
		);
	}
}
