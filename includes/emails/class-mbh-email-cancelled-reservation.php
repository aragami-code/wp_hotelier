<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Email_Cancelled_Reservation' ) ) :

/**
 * MBH_Email_Cancelled_Reservation Class
 */
class MBH_Email_Cancelled_Reservation extends MBH_Email {

	/**
	 * Constructor
	 */
	function __construct() {

		$this->id               = 'cancelled_reservation';
		$this->title            = esc_html__( 'Cancelled reservation', 'wp-mb_hms' );

		$this->heading          = mbh_get_option( 'emails_cancelled_reservation_heading', __( 'Cancelled reservation', 'wp-mb_hms' ) );
		$this->subject          = mbh_get_option( 'emails_cancelled_reservation_subject', __( '{site_title} - Cancelled reservation #{reservation_number}', 'wp-mb_hms' ) );

		$this->template_html    = 'emails/admin-cancelled-reservation.php';
		$this->template_plain   = 'emails/plain/admin-cancelled-reservation.php';
		$this->enabled          = mbh_get_option( 'emails_cancelled_reservation_enabled', true );

		// Triggers for this email
		add_action( 'mb_hms_reservation_status_pending_to_cancelled_notification', array( $this, 'trigger' ) );
		add_action( 'mb_hms_reservation_status_on-hold_to_cancelled_notification', array( $this, 'trigger' ) );
		add_action( 'mb_hms_reservation_status_confirmed_to_cancelled_notification', array( $this, 'trigger' ) );

		// Call parent constructor
		parent::__construct();

		// Recipient
		$this->recipient = mbh_get_option( 'emails_admin_notice' );

		if ( ! $this->recipient ) {
			$this->recipient = get_option( 'admin_email' );
		}
	}

	/**
	 * Trigger.
	 */
	function trigger( $reservation_id ) {

		if ( $reservation_id ) {
			$this->object                          = mbh_get_reservation( $reservation_id );
			$this->find[ 'reservation-number' ]    = '{reservation_number}';
			$this->replace[ 'reservation-number' ] = $this->object->get_reservation_number();
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

return new MBH_Email_Cancelled_Reservation();
