<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<tr>
	<td style="text-align:left;font-size:16px;line-height:40px;color:#444444;font-weight:bold;font-family:Helvetica,Arial;"><?php esc_html_e( 'Guest address', 'wp-mb_hms' ); ?></td>
</tr>
<tr>
	<td style="text-align:left;font-size:13px;line-height:17px;color:#999999;font-family:Helvetica,Arial;"><?php echo $reservation->get_formatted_guest_address(); ?></td>
</tr>
