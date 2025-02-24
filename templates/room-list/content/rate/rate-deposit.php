<?php
/**
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( $variation->needs_deposit() ) : ?>

<div class="rate__deposit rate__deposit--listing">
	<span class="rate__deposit-label rate__deposit-label--listing"><?php esc_html_e( 'Deposit required', 'wp-mb_hms' ); ?></span>
	<span class="rate__deposit-amount rate__deposit-amount--listing"><?php echo wp_kses( $variation->get_formatted_deposit(), array( 'span' => array( 'class' => array() ) ) ); ?></span>
</div>

<?php endif; ?>
