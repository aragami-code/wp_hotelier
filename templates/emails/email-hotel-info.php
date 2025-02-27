<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<tr>
	<td style="text-align:left;font-size:16px;line-height:20px;color:#444444;font-weight:bold;font-family:Helvetica,Arial;"><?php echo esc_html( MBH_Info::get_hotel_name() ); ?></td>
</tr>
<tr>
	<td style="text-align:left;font-size:13px;line-height:30px;color:#999999;font-family:Helvetica,Arial;"><?php echo esc_html( MBH_Info::get_hotel_address() . ", " . MBH_Info::get_hotel_postcode() . ", " . MBH_Info::get_hotel_locality() ); ?></td>
</tr>
<tr>
	<td style="text-align:left;font-size:13px;line-height:17px;color:#999999;font-family:Helvetica,Arial;"><strong style="color:#444444;"><?php esc_html_e( 'Telephone:', 'wp-mb_hms' ) ?></strong> <a href="tel:<?php echo esc_attr( MBH_Info::get_hotel_telephone() ); ?>" value="<?php echo esc_attr( MBH_Info::get_hotel_telephone() ); ?>" target="_blank" style="color:#5CC8FF"><?php echo esc_html( MBH_Info::get_hotel_telephone() ); ?></a></td>
</tr>
<tr>
	<td style="text-align:left;font-size:13px;line-height:17px;color:#999999;font-family:Helvetica,Arial;"><strong style="color:#444444;"><?php esc_html_e( 'Fax:', 'wp-mb_hms' ) ?></strong> <?php echo esc_html( MBH_Info::get_hotel_fax() ); ?></td>
</tr>
<tr>
	<td style="text-align:left;font-size:13px;line-height:17px;color:#999999;font-family:Helvetica,Arial;"><strong style="color:#444444;"><?php esc_html_e( 'Email:', 'wp-mb_hms' ) ?></strong> <a href="mailto:<?php echo esc_attr( MBH_Info::get_hotel_email() ); ?>" value="<?php echo esc_attr( MBH_Info::get_hotel_email() ); ?>" target="_blank" style="color:#5CC8FF"><?php echo esc_html( MBH_Info::get_hotel_email() ); ?></a></td>
</tr>
<tr>
	<td style="border-bottom: solid 1px #e9e9e9; background: #ffffff" bgcolor="ffffff" width="100%">&nbsp;</td>
</tr>
<tr>
	<td>&nbsp;</td>
</tr>
