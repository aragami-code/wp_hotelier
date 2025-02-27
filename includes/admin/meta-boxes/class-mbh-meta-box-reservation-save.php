<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Meta_Box_Reservation_Save' ) ) :

/**
 * MBH_Meta_Box_Reservation_Save Class
 */
class MBH_Meta_Box_Reservation_Save {

	/**
	 * Output the metabox
	 */
	public static function output( $post ) {
		global $post, $thereservation;

		if ( ! is_object( $thereservation ) ) {
			$thereservation = mbh_get_reservation( $post->ID );
		}

		$reservation = $thereservation;
		?>
		<div class="submitbox">

			<div id="reservation-actions">

				<p><?php esc_html_e( 'Resend reservation emails.', 'wp-mb_hms' ); ?></p>

				<select name="mb_hms_emails_action" id="emails-action">
					<option value=""><?php esc_html_e( 'Emails', 'wp-mb_hms' ); ?></option>

					<?php
					$mailer           = MBH()->mailer();
					$available_emails = apply_filters( 'mb_hms_resend_reservation_emails_available', array( 'guest_request_received', 'guest_confirmed_reservation', 'guest_cancelled_reservation', 'guest_invoice' ) );
					$mails            = $mailer->get_emails();

					if ( ! empty( $mails ) ) {
						foreach ( $mails as $mail ) {
							if ( in_array( $mail->id, $available_emails ) ) {
								echo '<option value="send_email_'. esc_attr( $mail->id ) .'">' . esc_html( $mail->title ) . '</option>';
							}
						}
					}
					?>
				</select>

				<button class="button" title="<?php esc_attr_e( 'Send email', 'wp-mb_hms' ); ?>"><?php esc_html_e( 'Send email', 'wp-mb_hms' ); ?></button>

			</div>

			<div id="delete-action"><?php
				if ( current_user_can( 'delete_post', $post->ID ) ) {

					if ( ! EMPTY_TRASH_DAYS ) {
						$delete_text = esc_html__( 'Delete Permanently', 'wp-mb_hms' );
					} else {
						$delete_text = esc_html__( 'Move to Trash', 'wp-mb_hms' );
					}
					?><a class="submitdelete deletion" href="<?php echo esc_url( get_delete_post_link( $post->ID ) ); ?>"><?php echo $delete_text; ?></a>
				<?php }
				?>
			</div>

			<input type="submit" class="button save-reservation button-primary" name="save" value="<?php esc_html_e( 'Save reservation', 'wp-mb_hms' ); ?>" />

		</div>

		<?php
	}

	/**
	 * Save meta box data
	 */
	public static function save( $post_id, $post ) {
		$reservation = mbh_get_reservation( $post_id );

		// Handle emails action
		if ( ! empty( $_POST[ 'mb_hms_emails_action' ] ) ) {
			$action = sanitize_text_field( $_POST[ 'mb_hms_emails_action' ] );

			if ( strstr( $action, 'send_email_' ) ) {

				do_action( 'mb_hms_before_resend_reservation_emails', $reservation );

				// Ensure gateways are loaded in case they need to insert data into the emails
				MBH()->payment_gateways();

				// Load mailer
				$mailer = MBH()->mailer();

				$email_to_send = str_replace( 'send_email_', '', $action );

				$mails = $mailer->get_emails();

				if ( ! empty( $mails ) ) {
					foreach ( $mails as $mail ) {
						if ( $mail->id == $email_to_send ) {
							$mail->trigger( $reservation->id );
							$reservation->add_reservation_note( sprintf( esc_html__( '%s email notification manually sent.', 'wp-mb_hms' ), $mail->title ), false, true );
						}
					}
				}

				do_action( 'mb_hms_after_resend_reservation_emails', $reservation );

				// Change the post saved message
				add_filter( 'redirect_post_location', array( __CLASS__, 'set_email_sent_message' ) );
			}
		}

		// Handle mark as paid/unpaid action
		if ( ! empty( $_POST[ 'mb_hms_mark_as_paid_action' ] ) ) {
			if ( 'paid' == $_POST[ 'mb_hms_mark_as_paid_action' ] ) {
				$reservation->mark_as_paid();
			} else if ( 'unpaid' == $_POST[ 'mb_hms_mark_as_paid_action' ] ) {
				$reservation->mark_as_unpaid();
			}
		}

		// Handle manual charge
		if ( ! empty( $_POST[ 'mb_hms_charge_remain_deposit' ] ) ) {
			// Do a final check here.
			// This ensures that:
			//      1. The reservation can be charged
			//      2. The payment method used in this reservation
			//         exists and supports manual charges
			if ( $reservation->can_be_charged() ) {
				$available_gateways = MBH()->payment_gateways->get_available_payment_gateways();
				$available_gateways[ $reservation->get_payment_method() ]->process_manual_charge( $reservation->id );
			}
		}
	}

	/**
	 * Set the correct message ID
	 *
	 * @static
     * @param $location
	 * @return string
	 */
	public static function set_email_sent_message( $location ) {
		return add_query_arg( 'message', 11, $location );
	}
}

endif;
