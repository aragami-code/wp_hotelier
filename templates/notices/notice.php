<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! $messages ){
	return;
}

?>

<?php foreach ( $messages as $message ) : ?>
	<div class="mb_hms-notice mb_hms-notice--info"><?php echo wp_kses_post( $message ); ?></div>
<?php endforeach; ?>
