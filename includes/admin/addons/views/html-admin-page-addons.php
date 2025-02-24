<?php
/**
 * Admin View: extensions & Themes
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="wrap mb_hms">
	<h1><?php echo get_admin_page_title(); ?></h1>

	<?php if ( $addons ) : ?>

		<?php if ( isset( $addons->extensions ) ) : ?>
			<div class="mb_hms-addons">
				<h2 class="mb_hms-addons__title"><?php esc_html_e( 'Supercharge your hotel website with extensions built specifically for Easy WP mb_hms.', 'wp-mb_hms' ); ?></h2>
				<p class="mb_hms-addons__description"><?php esc_html_e( 'Premium extensions are special WordPress plugins that enhance, or extend, the core Easy WP mb_hms functionalities.', 'wp-mb_hms' ); ?></p>

				<ul class="mb_hms-addons__list">
					<?php foreach ( $addons->extensions as $extension ) :
						$title       = sanitize_text_field( $extension->title );
						$icon        = sanitize_text_field( $extension->icon );
						$description = sanitize_text_field( $extension->description );
						$link        = sanitize_text_field( $extension->link );
						?>
						<li class="mb_hms-addons__extension">
							<a class="mb_hms-addons__extension-thumbnail" href="<?php echo esc_url( $link ); ?>" style="background-image: url(<?php echo esc_url( $icon ); ?>);"></a>

							<div class="mb_hms-addons__extension-content">
								<h4 class="mb_hms-addons__extension-title"><a href="<?php echo esc_url( $link ); ?>"><?php echo esc_html( $title ); ?></a></h4>

								<div class="mb_hms-addons__extension-description">
									<p><?php echo esc_html( $description ); ?></p>
								</div>

								<a class="mb_hms-addons__extension-button" href="<?php echo esc_url( $link ); ?>" title="<?php esc_attr_e( 'View extension', 'wp-mb_hms' ); ?>"><?php esc_html_e( 'Get this extension', 'wp-mb_hms' ); ?></a>
							</div>
						</li>
					<?php endforeach; ?>
					<li></li>
				</ul>
			</div>
		<?php endif; ?>

	<?php else : ?>
		<p><?php printf( __( 'Our catalog of Easy WP mb_hms Extensions can be found on Easy WP mb_hms here: <a href="%s">Easy WP mb_hms Extensions</a>', 'wp-mb_hms' ), 'https://wpmb_hms.com/extensions/' ); ?></p>
	<?php endif; ?>
