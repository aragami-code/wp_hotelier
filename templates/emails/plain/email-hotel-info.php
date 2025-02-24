<?php
/**
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

echo esc_html( MBH_Info::get_hotel_name() ) . "\n";
echo esc_html( MBH_Info::get_hotel_address() . ", " . MBH_Info::get_hotel_postcode() . ", " . MBH_Info::get_hotel_locality() ) . "\n";
echo sprintf( esc_html__( 'Telephone: %s.', 'wp-mb_hms' ), MBH_Info::get_hotel_telephone() ) . "\n";
echo sprintf( esc_html__( 'Fax: %s.', 'wp-mb_hms' ), MBH_Info::get_hotel_fax() ) . "\n";
echo sprintf( esc_html__( 'Email: %s.', 'wp-mb_hms' ), MBH_Info::get_hotel_email() ) . "\n\n";
