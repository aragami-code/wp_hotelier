<?php
/**
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Payment_Gateways' ) ) :

/**
 * HTL_Payment_Gateways Class
 */
class MBH_Payment_Gateways {
	/**
	 * Array of payment gateway classes.
	 *
	 * @var array
	 */
	public $payment_gateways;

	/**
	 * @var HTL_Payment_Gateways The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Main HTL_Payment_Gateways Instance
	 *
	 * Insures that only one instance of HTL_Payment_Gateways exists in memory at any one time.
	 *
	 * @static
	 * @return HTL_Payment_Gateways Main instance
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
	 * Initialize payment gateways.
	 */
	public function __construct() {
		$this->init();
	}

	/**
	 * Load gateways.
	 */
	public function init() {
		$this->includes();

		foreach ( $this->payment_gateways() as $gateway ) {
			$load_gateway = is_string( $gateway[ 'class' ] ) ? new $gateway[ 'class' ]() : $gateway[ 'class' ];
			$this->payment_gateways[ $load_gateway->id ] = $load_gateway;
		}
	}

	/**
	 * Include required files.
	 */
	public function includes() {
		include_once 'gateways/paypal-standard/class-mbh-gateway-paypal.php';
	}

	/**
	 * Get gateways.
	 *
	 * @access public
	 * @return array
	 */
	public function payment_gateways() {
		// Default, built-in gateways
		$gateways =  array(
			'paypal' => array(
				'id'          => 'paypal',
				'admin_label' => esc_html__( 'Paypal Standard', 'wp-mb_hms' ),
				'class'       => 'MBH_Gateway_Paypal',
			)
		);

		// extensions can hook into here to add their own gateways
		$gateways = apply_filters( 'mb_hms_payment_gateways', $gateways );

		return $gateways;
	}

	/**
	 * Returns a list of all enabled gateways.
	 *
	 * @access public
	 * @return array
	 */
	public function get_available_payment_gateways() {
		$payment_gateways = $this->payment_gateways;
		$_available_gateway_list = array();

		foreach ( $payment_gateways as $gateway ) {
			if ( $gateway->is_available() ) {
				$_available_gateway_list[ $gateway->id ] = $gateway;
			}
		}

		return apply_filters( 'mb_hms_available_payment_gateways', $_available_gateway_list );
	}

	/**
	 * Set the active gateway
	 */
	public function set_selected_gateway( $gateways ) {
		$default = mbh_get_option( 'default_gateway' );
		$current = MBH()->session->get( 'chosen_payment_method', $default );

		if ( isset( $gateways[ $current ] ) ) {
			$gateways[ $current ]->set_selected();
		} elseif ( isset( $gateways[ $default ] ) ) {
			$gateways[ $default ]->set_selected();
		}
	}
}

endif;
