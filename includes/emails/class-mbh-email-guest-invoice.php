<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Email_Guest_Invoice' ) ) :

/**
 * MBH_Email_Guest_Invoice Class
 */
class MBH_Email_Guest_Invoice extends MBH_Email {

	/**
	 * Constructor
	 */
	function __construct() {

		$this->id               = 'guest_invoice';
		$this->title            = esc_html__( 'Guest invoice', 'wp-mb_hms' );

		$this->heading          = mbh_get_option( 'emails_guest_invoice_heading', __( 'Invoice for reservation #{reservation_number}', 'wp-mb_hms' ) );
		$this->subject          = mbh_get_option( 'emails_guest_invoice_subject', __( '{site_title} - Cancelled reservation #{reservation_number}', 'wp-mb_hms' ) );

		$this->template_html    = 'emails/guest-invoice.php';
		$this->template_plain   = 'emails/plain/guest-invoice.php';
		$this->enabled          = mbh_get_option( 'emails_guest_invoice_enabled', true );

		// Call parent constructor
		parent::__construct();
	}

	/**
	 * Trigger.
	 */
	function trigger( $reservation_id ) {

		if ( $reservation_id ) {
			$this->object                          = mbh_get_reservation( $reservation_id );
			$this->find[ 'reservation-number' ]    = '{reservation_number}';
			$this->replace[ 'reservation-number' ] = $this->object->get_reservation_number();
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

return new MBH_Email_Guest_Invoice();
