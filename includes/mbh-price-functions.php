<?php
/**
 *
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Return the thousand separator for prices
 * @return string
 */
function mbh_get_price_thousand_separator() {
	$separator = stripslashes( mbh_get_option( 'thousands_separator', ',' ) );
	return $separator;
}

/**
 * Return the decimal separator for prices
 * @return string
 */
function mbh_get_price_decimal_separator() {
	$separator = stripslashes( mbh_get_option( 'decimal_separator', '.' ) );
	return $separator ? $separator : '.';
}

/**
 * Return the number of decimals after the decimal point.
 * @return int
 */
function mbh_get_price_decimals() {
	return absint( mbh_get_option( 'price_num_decimals', 2 ) );
}

/**
 * Get full list of currency codes.
 */
function mbh_get_currencies() {
	$currencies = array(
		'USD'  => esc_html__( 'US Dollars (&#36;)', 'wp-mb_hms' ),
		'XAF'  => esc_html__( 'Francs CFA ', 'wp-mb_hms' ),
		'XOF'  => esc_html__( 'Francs CFA ', 'wp-mb_hms' ),
		'EUR'  => esc_html__( 'Euros (&euro;)', 'wp-mb_hms' ),
		'GBP'  => esc_html__( 'Pounds Sterling (&pound;)', 'wp-mb_hms' ),
		'AUD'  => esc_html__( 'Australian Dollars (&#36;)', 'wp-mb_hms' ),
		'BRL'  => esc_html__( 'Brazilian Real (R&#36;)', 'wp-mb_hms' ),
		'CAD'  => esc_html__( 'Canadian Dollars (&#36;)', 'wp-mb_hms' ),
		'CZK'  => esc_html__( 'Czech Koruna', 'wp-mb_hms' ),
		'DKK'  => esc_html__( 'Danish Krone', 'wp-mb_hms' ),
		'HKD'  => esc_html__( 'Hong Kong Dollar (&#36;)', 'wp-mb_hms' ),
		'HUF'  => esc_html__( 'Hungarian Forint', 'wp-mb_hms' ),
		'ILS'  => esc_html__( 'Israeli Shekel (&#8362;)', 'wp-mb_hms' ),
		'JPY'  => esc_html__( 'Japanese Yen (&yen;)', 'wp-mb_hms' ),
		'MYR'  => esc_html__( 'Malaysian Ringgits', 'wp-mb_hms' ),
		'MXN'  => esc_html__( 'Mexican Peso (&#36;)', 'wp-mb_hms' ),
		'NZD'  => esc_html__( 'New Zealand Dollar (&#36;)', 'wp-mb_hms' ),
		'NOK'  => esc_html__( 'Norwegian Krone', 'wp-mb_hms' ),
		'PHP'  => esc_html__( 'Philippine Pesos', 'wp-mb_hms' ),
		'PLN'  => esc_html__( 'Polish Zloty', 'wp-mb_hms' ),
		'SGD'  => esc_html__( 'Singapore Dollar (&#36;)', 'wp-mb_hms' ),
		'SEK'  => esc_html__( 'Swedish Krona', 'wp-mb_hms' ),
		'CHF'  => esc_html__( 'Swiss Franc', 'wp-mb_hms' ),
		'TWD'  => esc_html__( 'Taiwan New Dollars', 'wp-mb_hms' ),
		'THB'  => esc_html__( 'Thai Baht (&#3647;)', 'wp-mb_hms' ),
		'INR'  => esc_html__( 'Indian Rupee (&#8377;)', 'wp-mb_hms' ),
		'TRY'  => esc_html__( 'Turkish Lira (&#8378;)', 'wp-mb_hms' ),
		'RUB'  => esc_html__( 'Russian Rubles', 'wp-mb_hms' )
	);

	return apply_filters( 'mb_hms_currencies', $currencies );
}

/**
 * Get Base Currency Code.
 * @return string
 */
function mbh_get_currency() {
	return apply_filters( 'mb_hms_currency', mbh_get_option( 'currency', 'USD' ) );
}

/**
 * Get Currency symbol.
 * @param string $currency (default: '')
 * @return string
 */
function mbh_get_currency_symbol( $currency = '' ) {
	if ( ! $currency ) {
		$currency = mbh_get_currency();
	}

	switch ( $currency ) {
		case 'AUD' :
		case 'XAF' :
		case 'XOF' :
		case 'CAD' :
		case 'HKD' :
		case 'MXN' :
		case 'NZD' :
		case 'SGD' :
		case 'USD' :
			$currency_symbol = '&#36;';
			break;
		case 'BRL' :
			$currency_symbol = '&#82;&#36;';
			break;
		case 'CHF' :
			$currency_symbol = '&#67;&#72;&#70;';
			break;
		case 'JPY' :
			$currency_symbol = '&yen;';
			break;
		case 'CZK' :
			$currency_symbol = '&#75;&#269;';
			break;
		case 'DKK' :
			$currency_symbol = 'DKK';
			break;
		case 'EUR' :
			$currency_symbol = '&euro;';
			break;
		case 'GBP' :
			$currency_symbol = '&pound;';
			break;
		case 'HUF' :
			$currency_symbol = '&#70;&#116;';
			break;
		case 'ILS' :
			$currency_symbol = '&#8362;';
			break;
		case 'INR' :
			$currency_symbol = 'Rs.';
			break;
		case 'MYR' :
			$currency_symbol = '&#82;&#77;';
			break;
		case 'NOK' :
			$currency_symbol = '&#107;&#114;';
			break;
		case 'PHP' :
			$currency_symbol = '&#8369;';
			break;
		case 'PLN' :
			$currency_symbol = '&#122;&#322;';
			break;
		case 'RUB' :
			$currency_symbol = '&#1088;&#1091;&#1073;.';
			break;
		case 'SEK' :
			$currency_symbol = '&#107;&#114;';
			break;
		case 'THB' :
			$currency_symbol = '&#3647;';
			break;
		case 'TRY' :
			$currency_symbol = '&#8378;';
			break;
		case 'TWD' :
			$currency_symbol = '&#78;&#84;&#36;';
			break;
		default :
			$currency_symbol = '';
			break;
	}

	return apply_filters( 'mb_hms_currency_symbol', $currency_symbol, $currency );
}


/**
 * Format the price with a currency symbol.
 *
 * @param float $price
 * @param string $currency
 * @return string
 */
function mbh_price( $price, $currency = '' ) {
	$thousands_sep = mbh_get_price_thousand_separator();
	$decimal_sep   = mbh_get_price_decimal_separator();
	$decimals      = mbh_get_price_decimals();
	$position      = mbh_get_option( 'currency_position', 'before' );
	$price         = number_format( (double) $price, $decimals, $decimal_sep, $thousands_sep );
	$price         = ( $position == 'before' ) ? mbh_get_currency_symbol( $currency ) . $price : $price . mbh_get_currency_symbol( $currency );
	$return        = '<span class="amount">' . $price . '</span>';

	return apply_filters( 'mb_hms_price', $return, $price );
}

/**
 * Count cents in prices (prices are stored as integers).
 *
 * @param int $amount
 * @return float
 */
function mbh_convert_to_cents( $amount ) {
	$amount = $amount / 100;

	return apply_filters( 'mb_hms_convert_to_cents', $amount );
}

/**
 * Calculates the deposit with a currency symbol.
 *
 * @param int $price
 * @param int $deposit
 * @param string $currency
 * @return string
 */
function mbh_calculate_deposit( $price, $deposit, $currency = '' ) {
	$price = $price / 100;
	$price = ( $price * $deposit ) / 100;

	$price = mbh_price( $price, $currency );

	return apply_filters( 'mb_hms_calculate_deposit', $price );
}

/**
 * Gets seasonal prices schema.
 *
 * @return array
 */
function mbh_get_seasonal_prices_schema() {
	$schema = mbh_get_option( 'seasonal_prices_schema', array() );

	return $schema;
}
