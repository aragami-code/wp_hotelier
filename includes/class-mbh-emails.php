<?php
/**
 *.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Emails' ) ) :

/**
 * MBH_Emails Class
 */
class MBH_Emails {

	/** @var array Array of email notification classes */
	public $emails;

	/** @var MBH_Emails The single instance of the class */
	protected static $_instance = null;

	/**
	 * Main MBH_Emails Instance
	 *
	 * Ensures only one instance of MBH_Emails is loaded or can be loaded.
	 *
	 * @static
	 * @return MBH_Emails Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) ) {
			self::$_instance = new self();
		}
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
	 * Hook in all transactional emails
	 */
	public static function init_transactional_emails() {
		$email_actions = apply_filters( 'mb_hms_email_actions', array(
			'mb_hms_new_booking_request',
			'mb_hms_reservation_status_pending_to_confirmed',
			'mb_hms_reservation_status_pending_to_cancelled',
			'mb_hms_reservation_status_pending_to_on-hold',
			'mb_hms_reservation_status_failed_to_confirmed',
			'mb_hms_reservation_status_on-hold_to_confirmed',
			'mb_hms_reservation_status_on-hold_to_cancelled',
			'mb_hms_reservation_status_confirmed_to_completed',
			'mb_hms_reservation_status_confirmed_to_cancelled',
		) );

		foreach ( $email_actions as $action ) {
			add_action( $action, array( __CLASS__, 'send_transactional_email' ), 10, 10 );
		}
	}

	/**
	 * Init the mailer instance and call the notifications for the current filter.
	 */
	public static function send_transactional_email() {
		self::instance();
		$args = func_get_args();
		do_action_ref_array( current_filter() . '_notification', $args );
	}

	/**
	 * Constructor for the email class hooks in all emails that can be sent.
	 */
	public function __construct() {
		$this->init();

		// Email Header, Footer and content hooks
		add_action( 'mb_hms_email_header', array( $this, 'email_header' ), 10, 2 );
		add_action( 'mb_hms_email_footer', array( $this, 'email_footer' ) );
		add_action( 'mb_hms_email_hotel_info', array( $this, 'hotel_info' ), 10, 2 );
		add_action( 'mb_hms_email_guest_details', array( $this, 'guest_details' ), 10, 3 );
		add_action( 'mb_hms_email_guest_details', array( $this, 'guest_address' ), 20, 3 );
		add_action( 'mb_hms_email_reservation_meta', array( $this, 'guest_special_requests' ), 10, 3 );
		add_action( 'mb_hms_email_reservation_meta', array( $this, 'guest_arrival_time' ), 15, 3 );
	}

	/**
	 * Init email classes
	 */
	public function init() {
		// Include email classes
		include_once( 'emails/class-mbh-email.php' );

		$this->emails[ 'MBH_Email_New_Reservation' ]             = include( 'emails/class-mbh-email-new-reservation.php' );
		$this->emails[ 'MBH_Email_Request_Received' ]            = include( 'emails/class-mbh-email-guest-request-received.php' );
		$this->emails[ 'MBH_Email_Confirmed_Reservation' ]       = include( 'emails/class-mbh-email-guest-confirmed-reservation.php' );
		$this->emails[ 'MBH_Email_Cancelled_Reservation' ]       = include( 'emails/class-mbh-email-cancelled-reservation.php' );
		$this->emails[ 'MBH_Email_Guest_Cancelled_Reservation' ] = include( 'emails/class-mbh-email-guest-cancelled-reservation.php' );
		$this->emails[ 'MBH_Email_Guest_Invoice' ]               = include( 'emails/class-mbh-email-guest-invoice.php' );

		$this->emails                                            = apply_filters( 'mb_hms_email_classes', $this->emails );
	}

	/**
	 * Return the email classes - used in admin to load settings.
	 *
	 * @return array
	 */
	public function get_emails() {
		return $this->emails;
	}

	/**
	 * Get from name for email.
	 *
	 * @return string
	 */
	public function get_from_name() {
		return wp_specialchars_decode( mbh_get_option( 'mb_hms_email_from_name' ) );
	}

	/**
	 * Get from email address.
	 *
	 * @return string
	 */
	public function get_from_address() {
		return mbh_get_option( 'mb_hms_email_from_address' );
	}

	/**
	 * Get the email header.
	 *
	 * @param mixed $reservation
	 * @param mixed $email_heading heading for the email
	 */
	public function email_header( $reservation, $email_heading ) {
		mbh_get_template( 'emails/email-header.php', array( 'reservation' => $reservation, 'email_heading' => $email_heading ) );
	}

	/**
	 * Get the email footer.
	 */
	public function email_footer() {
		mbh_get_template( 'emails/email-footer.php' );
	}

	/**
	 * Wraps a message in the mb_hms mail template.
	 *
	 * @param mixed $email_heading
	 * @param string $message
	 * @return string
	 */
	public function wrap_message( $email_heading, $message, $plain_text = false ) {
		// Buffer
		ob_start();

		do_action( 'mb_hms_email_header', $reservation, $email_heading );

		echo wpautop( wptexturize( $message ) );

		do_action( 'mb_hms_email_footer' );

		// Get contents
		$message = ob_get_clean();

		return $message;
	}

	/**
	 * Send the email.
	 *
	 * @param mixed $to
	 * @param mixed $subject
	 * @param mixed $message
	 * @param string $headers (default: "Content-Type: text/html\r\n")
	 * @param string $attachments (default: "")
	 * @return bool
	 */
	public function send( $to, $subject, $message, $headers = "Content-Type: text/html\r\n", $attachments = "" ) {
		// Send
		$email = new MBH_Email();
		return $email->send( $to, $subject, $message, $headers, $attachments );
	}

	/**
	 * Prepare and send the customer invoice email on demand.
	 */
	public function customer_invoice( $reservation ) {
		$email = $this->emails[ 'MBH_Email_Guest_Invoice' ];
		$email->trigger( $reservation );
	}

	/**
	 * Show special requests in emails.
	 *
	 * @param mixed $reservation
	 * @param bool $sent_to_admin (default: false)
	 * @param bool $plain_text (default: false)
	 * @return string
	 */
	public function guest_special_requests( $reservation, $sent_to_admin = false, $plain_text = false ) {
		$label            = esc_html__( 'Special requests', 'wp-mb_hms' );
		$special_requests = $reservation->get_guest_special_requests() ? wptexturize( $reservation->get_guest_special_requests() ) : esc_html__( 'None', 'wp-mb_hms' );

		if ( $plain_text ) {
			echo strtoupper( $label ) . "\n\n";
			echo $special_requests . "\n";
		} else {
			mbh_get_template( 'emails/email-guest-requests.php', array( 'label' => $label, 'special_requests' => $special_requests ) );
		}
	}

	/**
	 * Show ETA (Estimated Arrival Time) in emails.
	 *
	 * @param mixed $reservation
	 * @param bool $sent_to_admin (default: false)
	 * @param bool $plain_text (default: false)
	 * @return string
	 */
	public function guest_arrival_time( $reservation, $sent_to_admin = false, $plain_text = false ) {
		$label              = esc_html__( 'Estimated arrival time', 'wp-mb_hms' );
		$guest_arrival_time = wptexturize( $reservation->get_formatted_arrival_time() );

		if ( $plain_text ) {
			echo "\n" . strtoupper( $label ) . "\n\n";
			echo $guest_arrival_time . "\n";
		} else {
			mbh_get_template( 'emails/email-guest-arrival-time.php', array( 'label' => $label, 'guest_arrival_time' => $guest_arrival_time ) );
		}
	}

	/**
	 * Add guest details to email templates.
	 *
	 * @param mixed $reservation
	 * @param bool $sent_to_admin (default: false)
	 * @param bool $plain_text (default: false)
	 * @return string
	 */
	public function guest_details( $reservation, $sent_to_admin = false, $plain_text = false ) {
		$fields = array();

		if ( $reservation->get_formatted_guest_full_name() ) {
			$fields[ 'guest_name' ] = array(
				'label' => esc_html__( 'Name', 'wp-mb_hms' ),
				'value' => wptexturize( $reservation->get_formatted_guest_full_name() )
			);
	    }

	    if ( $reservation->guest_email ) {
			$fields[ 'guest_email' ] = array(
				'label' => esc_html__( 'Email', 'wp-mb_hms' ),
				'value' => wptexturize( $reservation->guest_email )
			);
	    }

	    if ( $reservation->guest_telephone ) {
			$fields[ 'guest_telephone' ] = array(
				'label' => esc_html__( 'Telephone', 'wp-mb_hms' ),
				'value' => wptexturize( $reservation->guest_telephone )
			);
	    }

		$fields = apply_filters( 'mb_hms_email_guest_details_fields', $fields, $sent_to_admin, $reservation );

		if ( $fields ) {

			$heading = $sent_to_admin ? esc_html__( 'Guest details', 'wp-mb_hms' ) : esc_html__( 'Your details', 'wp-mb_hms' );

			$heading = apply_filters( 'mb_hms_email_custom_details_header', $heading, $sent_to_admin, $reservation );

			if ( $plain_text ) {

				echo strtoupper( $heading ) . "\n\n";

				foreach ( $fields as $field ) {
					if ( isset( $field[ 'label' ] ) && isset( $field[ 'value' ] ) && $field[ 'value' ] ) {
						echo esc_html( $field[ 'label' ] ) . ': ' . wptexturize( $field[ 'value' ] ) . "\n";
					}
				}

			} else {

				mbh_get_template( 'emails/email-guest-details.php', array( 'heading' => $heading, 'fields' => $fields ) );
			}

		}
	}

	/**
	 * Gets the guest address.
	 */
	public function guest_address( $reservation, $sent_to_admin = false, $plain_text = false ) {
		if ( $plain_text ) {
			mbh_get_template( 'emails/plain/email-guest-address.php', array( 'reservation' => $reservation ) );
		} else {
			mbh_get_template( 'emails/email-guest-address.php', array( 'reservation' => $reservation ) );
		}
	}

	/**
	 * Show the hotel info.
	 */
	public function hotel_info( $plain_text = false ) {
		if ( $plain_text ) {
			mbh_get_template( 'emails/plain/email-hotel-info.php' );
		} else {
			mbh_get_template( 'emails/email-hotel-info.php' );
		}
	}

	/**
	 * Get blog name formatted for emails
	 * @return string
	 */
	private function get_blogname() {
		return wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
	}
}

endif;
