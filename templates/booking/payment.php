<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div id="payment" class="booking__section booking__section--payment">
	<header class="section-header">
		<h3 class="section-header__title"><?php esc_html_e( 'Payment method', 'wp-mb_hms' ); ?></h3>
	</header>

	<ul class="payment-methods">
		<?php
			if ( ! empty( $available_gateways ) ) {
				$single = ( count( $available_gateways ) == 1 ) ? true : false;

				foreach ( $available_gateways as $gateway ) {
					htl_get_template( 'booking/payment-method.php', array( 'gateway' => $gateway, 'single' => $single ) );
				}
			} else {
				echo '<li class="payment-method payment-method--error">' . esc_html__( 'Sorry, it seems that there are no available payment methods. Please contact us if you require assistance.', 'wp-mb_hms' ) . '</li>';
			}
		?>
	</ul>
</div>
