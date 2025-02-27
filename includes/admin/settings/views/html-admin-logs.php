<?php
/**
 * Page - Status Logs
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

?>

<div class="wrap mb_hms-logs">

	<h2 class="nav-tab-wrapper">
		<?php
		$settings = new MBH_Admin_Settings();
		$tabs = $settings->get_settings_tabs();

		foreach( $tabs as $tab_id => $tab_name ) {

			$tab_url = add_query_arg( array(
				'settings-updated' => false,
				'tab' => $tab_id
				),
				admin_url( 'admin.php?page=mb_hms-settings' )
			);

			echo '<a href="' . esc_url( $tab_url ) . '" title="' . esc_attr( $tab_name ) . '" class="nav-tab">';
				echo esc_html( $tab_name );
			echo '</a>';
		}

		//echo '<a href="' . esc_url( admin_url( 'admin.php?page=mb_hms-logs' ) ) . '" title="' . esc_attr__( 'Logs', 'wp-mb_hms' ) . '" class="nav-tab nav-tab-active">';
		//		echo esc_html__( 'Logs', 'wp-mb_hms' );
		//echo '</a>';
		?>
	</h2>

	<div id="logs-container">

		<?php if ( $logs ) : ?>
			<h3><?php printf( esc_html__( 'Log file: %s (%s)', 'wp-mb_hms' ), esc_html( $viewed_log ), date_i18n( get_option( 'date_format') . ' ' . get_option( 'time_format'), filemtime( MBH_LOG_DIR . $viewed_log ) ) ); ?></h3>
			<form action="<?php echo esc_url( admin_url( 'admin.php?page=mb_hms-logs' ) ); ?>" method="post">
				<select name="log_file">
					<?php foreach ( $logs as $log_key => $log_file ) : ?>
						<option value="<?php echo esc_attr( $log_key ); ?>" <?php selected( sanitize_title( $viewed_log ), $log_key ); ?>><?php echo esc_html( $log_file ); ?> (<?php echo date_i18n( get_option( 'date_format') . ' ' . get_option( 'time_format'), filemtime( MBH_LOG_DIR . $log_file ) ); ?>)</option>
					<?php endforeach; ?>
				</select>
				<input type="submit" class="button" value="<?php esc_attr_e( 'View', 'wp-mb_hms' ); ?>" />
			</form>

			<div id="log-viewer">
				<textarea cols="70" rows="25"><?php echo esc_textarea( file_get_contents( MBH_LOG_DIR . $viewed_log ) ); ?></textarea>
			</div>
		<?php else : ?>
			<div class="updated below-h2"><p><?php esc_html_e( 'There are currently no logs to view.', 'wp-mb_hms' ); ?></p></div>
		<?php endif; ?>

	</div><!-- #logs-container -->

</div><!-- .wrap -->
