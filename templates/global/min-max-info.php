<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="room__min-max-stay room__min-max-stay--<?php echo esc_attr( $location ); ?>">
	<?php echo wp_kses_post( $info ); ?>
</div>
