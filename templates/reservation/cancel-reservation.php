<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<p class="cancel-reservation">
	<a href="<?php echo esc_url( $reservation->get_booking_cancel_url() ); ?>" class="button button--cancel-reservation-button"><?php _e( 'Cancel reservation', 'wp-mb_hms' ); ?></a>
</p>
