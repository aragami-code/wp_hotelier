<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<header class="page-header page__header">
	<?php do_action( 'mb_hms_before_page_title' ); ?>

	<h1 class="page-title page__title"><?php echo wp_kses_post( $page_title ); ?></h1>

	<?php do_action( 'mb_hms_after_page_title' ); ?>
</header>
