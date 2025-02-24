<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $room;

if ( ! $room->is_variable_room() && $room->needs_deposit() ) : ?>

<div class="room__deposit room__deposit--listing">
	<span class="room__deposit-label room__deposit-label--listing"><?php esc_html_e( 'Deposit required', 'wp-mb_hms' ); ?></span>
	<span class="room__deposit-amount room__deposit-amount--listing"><?php echo wp_kses( $room->get_formatted_deposit(), array( 'span' => array( 'class' => array() ) ) ); ?></span>
</div>

<?php endif; ?>
