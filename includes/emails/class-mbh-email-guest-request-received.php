<?php
/**
 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Email_Request_Received' ) ) :

/**
 * MBH_Email_Request_Received Class
 */
class MBH_Email_Request_Received extends MBH_Email {

	/**
	 * Constructor
	 */
	function __construct() {

		$this->id               = 'guest_request_received';
		$this->title            = esc_html__( 'Request received', 'wp-mb_hms' );

		$this->heading          = mbh_get_option( 'emails_request_received_heading', __( 'Request received', 'wp-mb_hms' ) );
		$this->subject          = mbh_get_option( 'emails_request_received_subject', __( 'Your reservation for {site_title}', 'wp-mb_hms' ) );

		$this->template_html    = 'emails/guest-request-received.php';
		$this->template_plain   = 'emails/plain/guest-request-received.php';
		$this->enabled          = mbh_get_option( 'emails_request_received_enabled', true );

		// Triggers for this email
		add_action( 'mb_hms_new_booking_request_notification', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();
	}

	/**
	 * Trigger.
	 */
	function trigger( $reservation_id ) {

		if ( $reservation_id ) {
			$this->object                          = mbh_get_reservation( $reservation_id );
			$this->recipient                       = $this->object->guest_email;
		}

		if ( ! $this->is_enabled() || ! $this->get_recipient() ) {
			return;
		}

		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html() {
		ob_start();
		mbh_get_template( $this->template_html, array(
			'reservation'   => $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => true,
			'plain_text'    => false
		) );
		return ob_get_clean();
	}

	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_plain() {
		ob_start();
		mbh_get_template( $this->template_plain, array(
			'reservation'   => $this->object,
			'email_heading' => $this->get_heading(),
			'sent_to_admin' => true,
			'plain_text'    => true
		) );
		return ob_get_clean();
	}
}

endif;

return new MBH_Email_Request_Received();
