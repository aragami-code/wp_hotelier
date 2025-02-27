<?php
/**
 * 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Admin_Settings_Default' ) ) :

/**
 * MBH_Admin_Settings_Default Class
 */
class MBH_Admin_Settings_Default {

	/**
	 * Get all published pages.
	 */
	public static function get_pages() {
		$all_pages = array( '' => '' ); // Blank option

		if( ( ! isset( $_GET[ 'page' ] ) || 'mb_hms-settings' != $_GET[ 'page' ] ) ) {
			return $all_pages;
		}

		$pages = get_pages();

		if ( $pages ) {
			foreach ( $pages as $page ) {
				$all_pages[ $page->ID ] = $page->post_title;
			}
		}

		return $all_pages;
	}

	/**
	 * Get room size options.
	 *
	 * A filter is provided to allow extensions to add their own room size
	 */
	public static function get_room_size_options() {
		$options = array(
			'm²'  => 'm²',
			'ft²' => 'ft²'
		);

		return apply_filters( 'mb_hms_room_size_options', $options );
	}

	/**
	 * Get booking mode options.
	 *
	 * A filter is provided to allow extensions to add their own booking mode options
	 */
	public static function get_booking_mode_options() {
		$options = array(
			'no-booking'      => esc_html__( 'No booking', 'wp-mb_hms' ),
			'manual-booking'  => esc_html__( 'Manual booking', 'wp-mb_hms' ),
			'instant-booking' => esc_html__( 'Instant booking ', 'wp-mb_hms' )
		);

		return apply_filters( 'mb_hms_booking_mode_options', $options );
	}

	/**
	 * Get emails type options.
	 */
	public static function get_emails_type_options() {
		$types = array(
			'plain'     => esc_html__( 'Plain text', 'wp-mb_hms' ),
			'html'      => esc_html__( 'web page HTML', 'wp-mb_hms' ),
			'multipart' => esc_html__( 'Multipart text', 'wp-mb_hms' )
		);

		return $types;
	}

	/**
	 * Get listing sorting options.
	 */
	public static function get_listing_sorting() {
		$options = array(
			'menu_order' => esc_html__( 'Menu order', 'wp-mb_hms' ),
			'date'       => esc_html__( 'Sort by most recent', 'wp-mb_hms' ),
			'title'      => esc_html__( 'Sort by title', 'wp-mb_hms' ),
		);

		return apply_filters( 'mb_hms_listing_sorting_options', $options );
	}

	/**
	 * Retrieve the array of plugin settings.
	 */
	public static function settings() {
		/**
		 * Filters are provided for each settings section to allow
		 * extensions and other plugins to add their own settings
		 */
		$settings = array(
			/* General Settings */
			'general' => apply_filters( 'mb_hms_settings_general',
				array(
					'mb_hms_info' => array(
						'id'   => 'mb_hms_info',
						'name' => '<strong>' . esc_html__( 'Hotel info', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'hotel_name' => array(
						'id'   => 'hotel_name',
						'name' => esc_html__( 'Hotel name', 'wp-mb_hms' ),
						'desc' => __( 'The name of your hotel.', 'wp-mb_hms' ),
						'type' => 'text',
						'std'  => ''
					),
					'hotel_address' => array(
						'id'   => 'hotel_address',
						'name' => esc_html__( 'Hotel address', 'wp-mb_hms' ),
						'desc' => __( 'The address of your hotel.', 'wp-mb_hms' ),
						'type' => 'text',
						'std'  => ''
					),
					'hotel_postcode' => array(
						'id'   => 'hotel_postcode',
						'name' => esc_html__( 'Hotel postcode', 'wp-mb_hms' ),
						'desc' => __( 'The postcode/zip of your hotel.', 'wp-mb_hms' ),
						'type' => 'text',
						'std'  => ''
					),
					'hotel_locality' => array(
						'id'   => 'hotel_locality',
						'name' => esc_html__( 'Hotel locality', 'wp-mb_hms' ),
						'desc' => __( 'The locality of your hotel.', 'wp-mb_hms' ),
						'type' => 'text',
						'std'  => ''
					),
					'hotel_telephone' => array(
						'id'   => 'hotel_telephone',
						'name' => esc_html__( 'Hotel telephone', 'wp-mb_hms' ),
						'desc' => __( 'The telephone number of your hotel.', 'wp-mb_hms' ),
						'type' => 'text',
						'std'  => ''
					),
					'hotel_fax' => array(
						'id'   => 'hotel_fax',
						'name' => esc_html__( 'Hotel fax', 'wp-mb_hms' ),
						'desc' => __( 'The fax number of your hotel.', 'wp-mb_hms' ),
						'type' => 'text',
						'std'  => ''
					),
					'hotel_email' => array(
						'id'   => 'hotel_email',
						'name' => esc_html__( 'Hotel email', 'wp-mb_hms' ),
						'desc' => __( 'The email address of your hotel.', 'wp-mb_hms' ),
						'type' => 'email',
						'std'  => get_option( 'admin_email' )
					),
					'hotel_checkin' => array(
						'id'   => 'hotel_checkin',
						'name' => esc_html__( 'Check-in', 'wp-mb_hms' ),
						'type' => 'from_to',
						'std'  => array(
							'from'  => '12',
							'to' => '21',
						),
					),
					'hotel_checkout' => array(
						'id'   => 'hotel_checkout',
						'name' => esc_html__( 'Check-out', 'wp-mb_hms' ),
						'type' => 'from_to',
						'std'  => array(
							'from'  => '8',
							'to' => '13',
						),
					),
					'hotel_pets' => array(
						'id'   => 'hotel_pets',
						'name' => esc_html__( 'Pets', 'wp-mb_hms' ),
						'desc' => __( 'Are pets allowed?', 'wp-mb_hms' ),
						'type' => 'checkbox',
						'std'  => ''
					),
					'hotel_pets_message' => array(
						'id'   => 'hotel_pets_message',
						'name' => esc_html__( 'Pets instructions', 'wp-mb_hms' ),
						'desc' => __( 'If you need to give some special instructions, use this field.', 'wp-mb_hms' ),
						'type' => 'text',
						'std'  => esc_html__( 'Pets are allowed', 'wp-mb_hms' ),
					),
					'hotel_special_requests_message' => array(
						'id'   => 'hotel_special_requests_message',
						'name' => esc_html__( 'Special requests message', 'wp-mb_hms' ),
						'desc' => __( 'This description will appear under the special requests textarea.', 'wp-mb_hms' ),
						'type' => 'textarea',
						'std'  => esc_html__( 'Special requests cannot be guaranteed but we will do our best to meet your needs.', 'wp-mb_hms' ),
					),
					'hotel_accepted_cards' => array(
						'id'   => 'hotel_accepted_cards',
						'name' => esc_html__( 'Accepted credit cards', 'wp-mb_hms' ),
						'desc' => sprintf( __( 'The accepted credit cards for payments made at the hotel. These are not used to pay the deposit (configure the <a href="%s">Payment Gateways</a> settings for that).', 'wp-mb_hms' ), '?page=mb_hms-settings&tab=payment' ),
						'type' => 'card_icons',
						'options'  => apply_filters( 'mb_hms_hotel_accepted_cards', array(
							'mastercard' => 'Mastercard',
							'visa'       => 'Visa',
							'amex'       => 'American Express',
							'discover'   => 'Discover',
							'maestro'    => 'Maestro',
							'visa_e'     => 'Visa Electron',
							'cirrus'     => 'Cirrus',
						) ),
					),
					'mb_hms_pages' => array(
						'id'   => 'mb_hms_pages',
						'name' => '<strong>' . esc_html__( 'mb_hms pages', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'listing_page' => array(
						'id'      => 'listing_page',
						'name'    => esc_html__( 'Listing page', 'wp-mb_hms' ),
						'desc'    => __( 'This is the page (the listing page) where guests will see the rooms that are available for the selected dates. The [mb_hms_listing] shortcode must be on this page.', 'wp-mb_hms' ),
						'type'    => 'select',
						'options' => self::get_pages()
					),
					'booking_page' => array(
						'id'      => 'booking_page',
						'name'    => esc_html__( 'Booking page', 'wp-mb_hms' ),
						'desc'    => __( 'This is the booking page where guests will complete their reservations. The [mb_hms_booking] shortcode must be on this page.', 'wp-mb_hms' ),
						'type'    => 'select',
						'options' => self::get_pages()
					),
					'terms_page' => array(
						'id'      => 'terms_page',
						'name'    => esc_html__( 'Terms page', 'wp-mb_hms' ),
						'desc'    => __( 'If set, guests will be asked to agree to the hotel terms before to request a new reservation.', 'wp-mb_hms' ),
						'type'    => 'select',
						'options' => self::get_pages()
					),
					'enforce_ssl_booking' => array(
						'id'   => 'enforce_ssl_booking',
						'name' => esc_html__( 'Enforce SSL booking', 'wp-mb_hms' ),
						'desc' => __( 'Enforce SSL (HTTPS) on the booking page (you must have an SSL certificate installed to use this option).', 'wp-mb_hms' ),
						'type' => 'checkbox'
					),
					'unforce_ssl_booking' => array(
						'id'   => 'unforce_ssl_booking',
						'name' => esc_html__( 'Force HTTP leaving booking', 'wp-mb_hms' ),
						'desc' => __( 'Force HTTP when leaving the booking page.', 'wp-mb_hms' ),
						'type' => 'checkbox'
					),
					'mb_hms_endpoints' => array(
						'id'   => 'mb_hms_endpoints',
						'name' => '<strong>' . esc_html__( 'mb_hms endpoints', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'reservation_received' => array(
						'id'      => 'reservation_received',
						'name'    => esc_html__( 'Reservation received', 'wp-mb_hms' ),
						'desc'    => __( 'This endpoint is appended to the booking page to display the page guests are sent to after completing their reservation.', 'wp-mb_hms' ),
						'type'    => 'text',
						'std'     => 'reservation-received'
					),
					'pay_endpoint' => array(
						'id'      => 'pay_endpoint',
						'name'    => esc_html__( 'Pay reservation', 'wp-mb_hms' ),
						'desc'    => __( 'This endpoint is appended to the booking page to display the payment form (for reservations generated by the admin).', 'wp-mb_hms' ),
						'type'    => 'text',
						'std'     => 'pay-reservation'
					),
					'currency_settings' => array(
						'id'   => 'currency_settings',
						'name' => '<strong>' . esc_html__( 'Currency settings', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'currency' => array(
						'id'      => 'currency',
						'name'    => esc_html__( 'Currency', 'wp-mb_hms' ),
						'desc'    => __( 'Choose your currency. Note that some payment gateways have currency restrictions.', 'wp-mb_hms' ),
						'type'    => 'select',
						'options' => mbh_get_currencies()
					),
					'currency_position' => array(
						'id'      => 'currency_position',
						'name'    => esc_html__( 'Currency position', 'wp-mb_hms' ),
						'desc'    => __( 'Choose the location of the currency sign.', 'wp-mb_hms' ),
						'type'    => 'select',
						'options' => array(
							'before' => esc_html__( 'Before - $10', 'wp-mb_hms' ),
							'after'  => esc_html__( 'After - 10$', 'wp-mb_hms' )
						)
					),
					'thousands_separator' => array(
						'id'   => 'thousands_separator',
						'name' => esc_html__( 'Thousands separator', 'wp-mb_hms' ),
						'desc' => __( 'This sets the thousand separator (usually , or .) of displayed prices.', 'wp-mb_hms' ),
						'type' => 'text',
						'size' => 'small',
						'std'  => ','
					),
					'decimal_separator' => array(
						'id'   => 'decimal_separator',
						'name' => esc_html__( 'Decimal separator', 'wp-mb_hms' ),
						'desc' => __( 'This sets the decimal separator (usually , or .) of displayed prices.', 'wp-mb_hms' ),
						'type' => 'text',
						'size' => 'small',
						'std'  => '.'
					),
					'price_num_decimals' => array(
						'id'   => 'price_num_decimals',
						'name' => esc_html__( 'Number of decimals', 'wp-mb_hms' ),
						'desc' => __( 'This sets the number of decimals points shown in displayed prices.', 'wp-mb_hms' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '2'
					),
					'privacy_settings' => array(
						'id'   => 'privacy_settings',
						'name' => '<strong>' . esc_html__( 'Privacy settings', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'privacy_remove_reservation_data_on_erasure_request' => array(
						'id'   => 'privacy_remove_reservation_data_on_erasure_request',
						'name' => esc_html__( 'Account erasure requests', 'wp-mb_hms' ),
						'desc' => __( 'When handling an account erasure request, should personal data within reservations be retained or removed?', 'wp-mb_hms' ),
						'type' => 'checkbox'
					),
					'privacy_settings_snippet' => array(
						'id'   => 'privacy_settings_snippet',
						'name' => esc_html__( 'Privacy snippet', 'wp-mb_hms' ),
						'desc' => sprintf( __( 'Optionally add some text about your website privacy policy to show on the booking form. %1$s will be replaced by a link to your <a href="%2$s">privacy policy page</a>.', 'wp-mb_hms' ), '<code>[privacy_policy]</code>', admin_url( 'privacy.php' ) ),
						'std'  => esc_html__( 'Your personal data will be used to support your experience throughout this website, to process your reservations, and for other purposes described in our [privacy_policy].', 'wp-mb_hms' ),
						'type' => 'textarea'
					),


					'room_settings' => array(
						'id'   => 'room_settings',
						'name' => '<strong>' . esc_html__( 'Room settings', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'room_size_unit' => array(
						'id'      => 'room_size_unit',
						'name'    => esc_html__( 'Room size unit', 'wp-mb_hms' ),
						'type'    => 'select',
						'options' => self::get_room_size_options()
					),
					'listing_settings' => array(
						'id'   => 'listing_settings',
						'name' => '<strong>' . esc_html__( 'Listing settings', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'listing_sorting' => array(
						'id'      => 'listing_sorting',
						'name'    => esc_html__( 'Default room sorting', 'wp-mb_hms' ),
						'type'    => 'select',
						'options' => self::get_listing_sorting()
					),
					'low_room_threshold' => array(
						'id'   => 'low_room_threshold',
						'name' => esc_html__( 'Low room availability threshold', 'wp-mb_hms' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '2'
					),
					'room_unavailable_visibility' => array(
						'id'   => 'room_unavailable_visibility',
						'name' => esc_html__( 'Show rooms unavailable', 'wp-mb_hms' ),
						'desc' => __( 'Show rooms that are unavailable for the selected dates.', 'wp-mb_hms' ),
						'type' => 'checkbox'
					),
					'book_now_redirect_to_booking_page' => array(
						'id'   => 'book_now_redirect_to_booking_page',
						'name' => esc_html__( 'Book now behaviour', 'wp-mb_hms' ),
						'desc' => __( 'Redirect to the booking page after successful addition (this will not allow multiple rooms on the same reservations).', 'wp-mb_hms' ),
						'type' => 'checkbox'
					),
					'book_now_allow_quantity_selection' => array(
						'id'   => 'book_now_allow_quantity_selection',
						'name' => esc_html__( 'Allow quantity selection', 'wp-mb_hms' ),
						'desc' => __( 'Allow quantity selection, then redirect to the booking page.', 'wp-mb_hms' ),
						'type' => 'checkbox'
					),
					'room_images' => array(
						'id'   => 'room_images',
						'name' => '<strong>' . esc_html__( 'Room images', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'room_images_description' => array(
						'id'   => 'room_images_description',
						'desc' => sprintf( __( 'These settings affect the display and dimensions of images in your website, but the display on the front-end will still be affected by the CSS of your theme. After changing these settings you may need to <a href="%s">regenerate your thumbnails</a>.', 'wp-mb_hms' ), esc_url( 'http://wordpress.org/extend/plugins/regenerate-thumbnails/' ) ),
						'type' => 'description'
					),
					'room_catalog_image_size' => array(
						'id'   => 'room_catalog_image_size',
						'name' => esc_html__( 'Catalog images', 'wp-mb_hms' ),
						'desc' => __( 'This size is usually used when you list the rooms.', 'wp-mb_hms' ),
						'type' => 'image_size',
						'std'  => array(
							'width'  => '300',
							'height' => '300',
							'crop'   => 1
						),
					),
					'room_single_image_size' => array(
						'id'   => 'room_single_image_size',
						'name' => esc_html__( 'Single room image', 'wp-mb_hms' ),
						'desc' => __( 'This size is the size used on the single room page.', 'wp-mb_hms' ),
						'type' => 'image_size',
						'std'  => array(
							'width'  => '600',
							'height' => '600',
							'crop'   => 1
						),
					),
					'room_thumbnail_image_size' => array(
						'id'   => 'room_thumbnail_image_size',
						'name' => esc_html__( 'Room thumbnails', 'wp-mb_hms' ),
						'desc' => __( 'This size is usually used for the gallery of images on the room page.', 'wp-mb_hms' ),
						'type' => 'image_size',
						'std'  => array(
							'width'  => '75',
							'height' => '75',
							'crop'   => 1
						),
					),
					'room_lightbox' => array(
						'id'   => 'room_lightbox',
						'name' => esc_html__( 'Enable lightbox for room images', 'wp-mb_hms' ),
						'desc' => __( 'Room gallery images will open in a lightbox.', 'wp-mb_hms' ),
						'type' => 'checkbox',
						'std'  => true
					),
					'reservation_settings' => array(
						'id'   => 'reservation_settings',
						'name' => '<strong>' . esc_html__( 'Reservation settings', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'booking_mode' => array(
						'id'      => 'booking_mode',
						'name'    => esc_html__( 'Booking mode', 'wp-mb_hms' ),
						'desc'    => __( '<ul><li><strong>No booking</strong>Show only the room details.</li><li><strong>Manual booking</strong>Guests will be able to request a reservation and the admin will approve or reject the booking manually.</li><li><strong>Instant booking</strong>Guests will be able to make a reservation without manual approval from the admin.</li></ul>', 'wp-mb_hms' ),
						'type'    => 'radio',
						'std'     => 'manual-booking',
						'options' => self::get_booking_mode_options()
					),
					'booking_additional_information' => array(
						'id'   => 'booking_additional_information',
						'name' => esc_html__( 'Show additional information', 'wp-mb_hms' ),
						'desc' => __( 'Show the "arrival estimated time" and the "special requests" field in the booking form.', 'wp-mb_hms' ),
						'type' => 'checkbox',
						'std'  => true,
					),
					'booking_number_of_guests_selection' => array(
						'id'   => 'booking_number_of_guests_selection',
						'name' => esc_html__( 'Show number of guests selection', 'wp-mb_hms' ),
						'desc' => __( 'Show a dropdown in the booking form where the guest specifies the number of adults and children for each room.', 'wp-mb_hms' ),
						'type' => 'checkbox',
						'std'  => true,
					),
					'booking_months_advance' => array(
						'id'   => 'booking_months_advance',
						'name' => esc_html__( 'Months in advance', 'wp-mb_hms' ),
						'desc' => __( 'Only allow reservations for "XX" months from current date (0 unlimited).', 'wp-mb_hms' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '0'
					),
					'booking_arrival_date' => array(
						'id'   => 'booking_arrival_date',
						'name' => esc_html__( 'Arrival date', 'wp-mb_hms' ),
						'desc' => __( 'Arrival date must be "XX" days from current date.', 'wp-mb_hms' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '0'
					),
					'booking_minimum_nights' => array(
						'id'   => 'booking_minimum_nights',
						'name' => esc_html__( 'Minimum nights', 'wp-mb_hms' ),
						'desc' => __( 'Minimum number of nights a guest can book.', 'wp-mb_hms' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '1'
					),
					'booking_maximum_nights' => array(
						'id'   => 'booking_maximum_nights',
						'name' => esc_html__( 'Maximum nights', 'wp-mb_hms' ),
						'desc' => __( 'Maximum number of nights a guest can book (0 unlimited).', 'wp-mb_hms' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '0'
					),
					'booking_hold_minutes' => array(
						'id'   => 'booking_hold_minutes',
						'name' => esc_html__( 'Hold reservation (minutes)', 'wp-mb_hms' ),
						'desc' => __( 'Hold reservation (for unpaid reservations that require a deposit) for "XX" minutes. When this limit is reached, the pending reservation will be cancelled. Type "0" to disable. Reservations created by admin will be not cancelled.', 'wp-mb_hms' ),
						'type' => 'booking_hold_minutes',
						'size' => 'small',
						'std'  => '60'
					),

					'seasonal_prices_info' => array(
						'id'   => 'seasonal_prices_info',
						'name' => '<strong>' . esc_html__( 'Seasonal prices schema', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'seasonal_prices_description' => array(
						'id'   => 'seasonal_prices_description',
						'desc' => __( 'Define here your global price schema, adding one rate for each date range. Rooms will have a default price (used when no rules are found) and a specific price for each season. To use this schema edit a room, select <em>Seasonal prices</em> in the <em>Price</em> dropdown, and enter the price amount of each season.', 'wp-mb_hms' ),
						'type' => 'description'
					),
					'seasonal_prices_schema' => array(
						'id'   => 'seasonal_prices_schema',
						'name' => esc_html__( 'Price schema', 'wp-mb_hms' ),
						'desc' => __( 'Each date range should have have a different price. The last rule defined overrides any previous rules. When you re-order the schema, remember to update the prices on already created rooms.', 'wp-mb_hms' ),
						'type' => 'seasonal_prices_table'
					),

					'tax_info' => array(
						'id'   => 'tax_info',
						'name' => '<strong>' . esc_html__( 'Tax settings', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'tax_enabled' => array(
						'id'   => 'tax_enabled',
						'name' => esc_html__( 'Enable tax', 'wp-mb_hms' ),
						'type' => 'checkbox',
						'std'  => false,
					),
					'tax_rate' => array(
						'id'          => 'tax_rate',
						'name'        => esc_html__( 'Tax rate %', 'wp-mb_hms' ),
						'desc'        => __( 'Enter a tax rate (percentage) to 4 decimal places. Use a point (.) for the decimal separator.', 'wp-mb_hms' ),
						'type'        => 'percentage',
						'placeholder' => '5.0000',
					),
					'tax_in_deposit' => array(
						'id'   => 'tax_in_deposit',
						'name' => esc_html__( 'Enable tax on deposits', 'wp-mb_hms' ),
						'type' => 'checkbox',
						'std'  => false,
					),


					//'install_pages' => array(
					//	'id'   => 'install_pages',
					//	'name' => esc_html__( 'Install mb_hms pages', 'wp-mb_hms' ),
					//	'desc' => __( 'This tool will install all the missing mb_hms pages. Pages already defined and set up will not be replaced.', 'wp-mb_hms' ),
					//	'type' => 'button'
					//),
					'send_test_email' => array(
						'id'   => 'send_test_email',
						'name' => esc_html__( 'Send test email', 'wp-mb_hms' ),
						'desc' => __( 'Test if your WordPress installation is sending emails correctly.', 'wp-mb_hms' ),
						'type' => 'button'
					),
					//'template_debug_mode' => array(
					//	'id'   => 'template_debug_mode',
					//	'name' => esc_html__( 'Template debug mode', 'wp-mb_hms' ),
					//	'desc' => __( 'This tool will disable template overrides for logged-in administrators for debugging purposes.', 'wp-mb_hms' ),
					//	'type' => 'checkbox'
					//),
					//'clear_sessions' => array(
					//	'id'   => 'clear_sessions',
					//	'name' => esc_html__( 'Cleanup guest sessions', 'wp-mb_hms' ),
					//	'desc' => __( 'This tool will delete all guest session data from the database (including any current live booking).', 'wp-mb_hms' ),
					//	'type' => 'button'
					//),
					//'delete_completed_bookings' => array(
					//	'id'   => 'delete_completed_bookings',
					//	'name' => esc_html__( 'Delete completed bookings', 'wp-mb_hms' ),
					//	'desc' => __( 'This tool will delete all completed bookings from the database.', 'wp-mb_hms' ),
					//	'type' => 'button'
					//),
					//'remove_data_uninstall' => array(
					//	'id'   => 'remove_data_uninstall',
					//	'name' => esc_html__( 'Remove data on uninstall', 'wp-mb_hms' ),
					//	'desc' => __( 'This tool will remove all mb_hms, Rooms and Reservations data when using the "Delete" link on the plugins screen.', 'wp-mb_hms' ),
					//	'type' => 'checkbox'
					//),






//'rooms-and-reservations' => apply_filters( 'mb_hms_settings_rooms_and_reservations',
//				array(
//				)
//			),


























				)
			),
			/* Room Settings 
			'rooms-and-reservations' => apply_filters( 'mb_hms_settings_rooms_and_reservations',
				array(
					'room_settings' => array(
						'id'   => 'room_settings',
						'name' => '<strong>' . esc_html__( 'Room settings', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'room_size_unit' => array(
						'id'      => 'room_size_unit',
						'name'    => esc_html__( 'Room size unit', 'wp-mb_hms' ),
						'type'    => 'select',
						'options' => self::get_room_size_options()
					),
					'listing_settings' => array(
						'id'   => 'listing_settings',
						'name' => '<strong>' . esc_html__( 'Listing settings', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'listing_sorting' => array(
						'id'      => 'listing_sorting',
						'name'    => esc_html__( 'Default room sorting', 'wp-mb_hms' ),
						'type'    => 'select',
						'options' => self::get_listing_sorting()
					),
					'low_room_threshold' => array(
						'id'   => 'low_room_threshold',
						'name' => esc_html__( 'Low room availability threshold', 'wp-mb_hms' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '2'
					),
					'room_unavailable_visibility' => array(
						'id'   => 'room_unavailable_visibility',
						'name' => esc_html__( 'Show rooms unavailable', 'wp-mb_hms' ),
						'desc' => __( 'Show rooms that are unavailable for the selected dates.', 'wp-mb_hms' ),
						'type' => 'checkbox'
					),
					'book_now_redirect_to_booking_page' => array(
						'id'   => 'book_now_redirect_to_booking_page',
						'name' => esc_html__( 'Book now behaviour', 'wp-mb_hms' ),
						'desc' => __( 'Redirect to the booking page after successful addition (this will not allow multiple rooms on the same reservations).', 'wp-mb_hms' ),
						'type' => 'checkbox'
					),
					'book_now_allow_quantity_selection' => array(
						'id'   => 'book_now_allow_quantity_selection',
						'name' => esc_html__( 'Allow quantity selection', 'wp-mb_hms' ),
						'desc' => __( 'Allow quantity selection, then redirect to the booking page.', 'wp-mb_hms' ),
						'type' => 'checkbox'
					),
					'room_images' => array(
						'id'   => 'room_images',
						'name' => '<strong>' . esc_html__( 'Room images', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'room_images_description' => array(
						'id'   => 'room_images_description',
						'desc' => sprintf( __( 'These settings affect the display and dimensions of images in your website, but the display on the front-end will still be affected by the CSS of your theme. After changing these settings you may need to <a href="%s">regenerate your thumbnails</a>.', 'wp-mb_hms' ), esc_url( 'http://wordpress.org/extend/plugins/regenerate-thumbnails/' ) ),
						'type' => 'description'
					),
					'room_catalog_image_size' => array(
						'id'   => 'room_catalog_image_size',
						'name' => esc_html__( 'Catalog images', 'wp-mb_hms' ),
						'desc' => __( 'This size is usually used when you list the rooms.', 'wp-mb_hms' ),
						'type' => 'image_size',
						'std'  => array(
							'width'  => '300',
							'height' => '300',
							'crop'   => 1
						),
					),
					'room_single_image_size' => array(
						'id'   => 'room_single_image_size',
						'name' => esc_html__( 'Single room image', 'wp-mb_hms' ),
						'desc' => __( 'This size is the size used on the single room page.', 'wp-mb_hms' ),
						'type' => 'image_size',
						'std'  => array(
							'width'  => '600',
							'height' => '600',
							'crop'   => 1
						),
					),
					'room_thumbnail_image_size' => array(
						'id'   => 'room_thumbnail_image_size',
						'name' => esc_html__( 'Room thumbnails', 'wp-mb_hms' ),
						'desc' => __( 'This size is usually used for the gallery of images on the room page.', 'wp-mb_hms' ),
						'type' => 'image_size',
						'std'  => array(
							'width'  => '75',
							'height' => '75',
							'crop'   => 1
						),
					),
					'room_lightbox' => array(
						'id'   => 'room_lightbox',
						'name' => esc_html__( 'Enable lightbox for room images', 'wp-mb_hms' ),
						'desc' => __( 'Room gallery images will open in a lightbox.', 'wp-mb_hms' ),
						'type' => 'checkbox',
						'std'  => true
					),
					'reservation_settings' => array(
						'id'   => 'reservation_settings',
						'name' => '<strong>' . esc_html__( 'Reservation settings', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'booking_mode' => array(
						'id'      => 'booking_mode',
						'name'    => esc_html__( 'Booking mode', 'wp-mb_hms' ),
						'desc'    => __( '<ul><li><strong>No booking</strong>Show only the room details.</li><li><strong>Manual booking</strong>Guests will be able to request a reservation and the admin will approve or reject the booking manually.</li><li><strong>Instant booking</strong>Guests will be able to make a reservation without manual approval from the admin.</li></ul>', 'wp-mb_hms' ),
						'type'    => 'radio',
						'std'     => 'manual-booking',
						'options' => self::get_booking_mode_options()
					),
					'booking_additional_information' => array(
						'id'   => 'booking_additional_information',
						'name' => esc_html__( 'Show additional information', 'wp-mb_hms' ),
						'desc' => __( 'Show the "arrival estimated time" and the "special requests" field in the booking form.', 'wp-mb_hms' ),
						'type' => 'checkbox',
						'std'  => true,
					),
					'booking_number_of_guests_selection' => array(
						'id'   => 'booking_number_of_guests_selection',
						'name' => esc_html__( 'Show number of guests selection', 'wp-mb_hms' ),
						'desc' => __( 'Show a dropdown in the booking form where the guest specifies the number of adults and children for each room.', 'wp-mb_hms' ),
						'type' => 'checkbox',
						'std'  => true,
					),
					'booking_months_advance' => array(
						'id'   => 'booking_months_advance',
						'name' => esc_html__( 'Months in advance', 'wp-mb_hms' ),
						'desc' => __( 'Only allow reservations for "XX" months from current date (0 unlimited).', 'wp-mb_hms' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '0'
					),
					'booking_arrival_date' => array(
						'id'   => 'booking_arrival_date',
						'name' => esc_html__( 'Arrival date', 'wp-mb_hms' ),
						'desc' => __( 'Arrival date must be "XX" days from current date.', 'wp-mb_hms' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '0'
					),
					'booking_minimum_nights' => array(
						'id'   => 'booking_minimum_nights',
						'name' => esc_html__( 'Minimum nights', 'wp-mb_hms' ),
						'desc' => __( 'Minimum number of nights a guest can book.', 'wp-mb_hms' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '1'
					),
					'booking_maximum_nights' => array(
						'id'   => 'booking_maximum_nights',
						'name' => esc_html__( 'Maximum nights', 'wp-mb_hms' ),
						'desc' => __( 'Maximum number of nights a guest can book (0 unlimited).', 'wp-mb_hms' ),
						'type' => 'number',
						'size' => 'small',
						'std'  => '0'
					),
					'booking_hold_minutes' => array(
						'id'   => 'booking_hold_minutes',
						'name' => esc_html__( 'Hold reservation (minutes)', 'wp-mb_hms' ),
						'desc' => __( 'Hold reservation (for unpaid reservations that require a deposit) for "XX" minutes. When this limit is reached, the pending reservation will be cancelled. Type "0" to disable. Reservations created by admin will be not cancelled.', 'wp-mb_hms' ),
						'type' => 'booking_hold_minutes',
						'size' => 'small',
						'std'  => '60'
					),
				)
			),*/
			/* Seasonal Prices Settings */
			'seasonal-prices' => apply_filters( 'mb_hms_settings_seasonal_prices',
				array(
					
				)
			),
			/* Payment Settings */
			'payment' => apply_filters( 'mb_hms_settings_payment',
				array(
					'payment_gateways' => array(
						'id'   => 'payment_gateways',
						'name' => esc_html__( 'Payment gateways', 'wp-mb_hms' ),
						'type' => 'gateways',
						'options' => MBH()->payment_gateways()->payment_gateways()
					),
					'default_gateway' => array(
						'id'   => 'default_gateway',
						'name' => esc_html__( 'Default gateway', 'wp-mb_hms' ),
						'type' => 'gateway_select',
						'options' => MBH()->payment_gateways()->payment_gateways()
					)
				)
			),
			/* General Settings 
			'tax' => apply_filters( 'mb_hms_settings_tax',
				array(
					
				)
			),*/
			/* Emails Settings */
			'emails' => apply_filters( 'mb_hms_settings_emails',
				array(
					'emails_general_options' => array(
						'id'   => 'emails_general_options',
						'name' => '<strong>' . esc_html__( 'Email options', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'emails_admin_notice' => array(
						'id'       => 'emails_admin_notice',
						'name'     => esc_html__( 'Reservation notification emails', 'wp-mb_hms' ),
						'desc'     => sprintf( __( 'Enter the email address(es) (comma separated) that should receive a notification anytime a reservation is made. Default to <code>%s</code>', 'wp-mb_hms' ), get_option( 'admin_email' ) ),
						'type'     => 'email',
						'multiple' => true,
						'std'      => get_option( 'admin_email' ),
					),
					'emails_from_name' => array(
						'id'   => 'emails_from_name',
						'name' => esc_html__( '"From" name', 'wp-mb_hms' ),
						'type' => 'text',
						'std'  => get_bloginfo( 'name', 'display' ),
					),
					'emails_from_email_address' => array(
						'id'   => 'emails_from_email_address',
						'name' => esc_html__( '"From" email address', 'wp-mb_hms' ),
						'type' => 'email',
						'std'  => get_option( 'admin_email' ),
					),
					'emails_type' => array(
						'id'      => 'emails_type',
						'name'    => esc_html__( 'Email type', 'wp-mb_hms' ),
						'type'    => 'select',
						'options' => self::get_emails_type_options(),
						'std'     => 'html'
					),
					'emails_logo' => array(
						'id'   => 'emails_logo',
						'name' => esc_html__( 'Email logo', 'wp-mb_hms' ),
						'desc' => __( 'Upload or choose a logo to be displayed at the top of mb_hms emails. Displayed on HTML emails only.', 'wp-mb_hms' ),
						'type' => 'upload',
					),
					'emails_footer_text' => array(
						'id'   => 'emails_footer_text',
						'name' => esc_html__( 'Email footer text', 'wp-mb_hms' ),
						'desc' => __( 'The text to appear in the footer of mb_hms emails.', 'wp-mb_hms' ),
						'type' => 'text',
						'std'  => get_bloginfo( 'name', 'display' ) . ' - ' . __( 'Powered Hotel management system', 'wp-mb_hms' ),
					),
					'emails_new_reservation' => array(
						'id'   => 'emails_new_reservation',
						'name' => '<strong>' . esc_html__( 'New reservation', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'emails_new_reservation_description' => array(
						'id'   => 'emails_new_reservation_description',
						'desc' => __( 'New reservation emails are sent to the admin when a reservation (or a booking request in "manual" mode) is made.', 'wp-mb_hms' ),
						'type' => 'description'
					),
					'emails_new_reservation_enabled' => array(
						'id'   => 'emails_new_reservation_enabled',
						'name' => esc_html__( 'Enable/disable', 'wp-mb_hms' ),
						'desc' => __( 'Enable this email notification.', 'wp-mb_hms' ),
						'type' => 'checkbox',
						'std'  => true,
					),
					'emails_new_reservation_subject' => array(
						'id'   => 'emails_new_reservation_subject',
						'name' => esc_html__( 'Email subject', 'wp-mb_hms' ),
						'desc' => __( 'This controls the email subject line. Default: <code>{site_title} - New hotel reservation #{reservation_number}</code>', 'wp-mb_hms' ),
						'type' => 'text',
						'std'  => esc_html__( '{site_title} - New hotel reservation #{reservation_number}', 'wp-mb_hms' ),
					),
					'emails_new_reservation_heading' => array(
						'id'   => 'emails_new_reservation_heading',
						'name' => esc_html__( 'Email heading', 'wp-mb_hms' ),
						'desc' => __( 'This controls the main heading contained within the email notification.', 'wp-mb_hms' ),
						'type' => 'text',
						'std'  => esc_html__( 'New hotel reservation', 'wp-mb_hms' ),
					),
					'emails_request_received' => array(
						'id'   => 'emails_request_received',
						'name' => '<strong>' . esc_html__( 'Request received', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'emails_request_received_description' => array(
						'id'   => 'emails_request_received_description',
						'desc' => __( 'Request received emails are sent to guests when they request a booking ("manual" booking mode only).', 'wp-mb_hms' ),
						'type' => 'description'
					),
					'emails_request_received_enabled' => array(
						'id'   => 'emails_request_received_enabled',
						'name' => esc_html__( 'Enable/disable', 'wp-mb_hms' ),
						'desc' => __( 'Enable this email notification.', 'wp-mb_hms' ),
						'type' => 'checkbox',
						'std'  => true,
					),
					'emails_request_received_subject' => array(
						'id'   => 'emails_request_received_subject',
						'name' => esc_html__( 'Email subject', 'wp-mb_hms' ),
						'desc' => __( 'This controls the email subject line. Default: <code>Your reservation for {site_title}</code>', 'wp-mb_hms' ),
						'type' => 'text',
						'std'  => esc_html__( 'Your reservation for {site_title}', 'wp-mb_hms' ),
					),
					'emails_request_received_heading' => array(
						'id'   => 'emails_request_received_heading',
						'name' => esc_html__( 'Email heading', 'wp-mb_hms' ),
						'desc' => __( 'This controls the main heading contained within the email notification.', 'wp-mb_hms' ),
						'type' => 'text',
						'std'  => esc_html__( 'Request received', 'wp-mb_hms' ),
					),
					'emails_confirmed_reservation' => array(
						'id'   => 'emails_confirmed_reservation',
						'name' => '<strong>' . esc_html__( 'Confirmed reservation', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'emails_confirmed_reservation_description' => array(
						'id'   => 'emails_confirmed_reservation_description',
						'desc' => __( 'Reservation confirmed emails are sent to guests when their reservations are marked confirmed. By the admin (when sent manually) or automatically (after payment if required or immediately in "instant" booking mode).', 'wp-mb_hms' ),
						'type' => 'description'
					),
					'emails_confirmed_reservation_enabled' => array(
						'id'   => 'emails_confirmed_reservation_enabled',
						'name' => esc_html__( 'Enable/disable', 'wp-mb_hms' ),
						'desc' => __( 'Enable this email notification.', 'wp-mb_hms' ),
						'type' => 'checkbox',
						'std'  => true,
					),
					'emails_confirmed_reservation_subject' => array(
						'id'   => 'emails_confirmed_reservation_subject',
						'name' => esc_html__( 'Email subject', 'wp-mb_hms' ),
						'desc' => __( 'This controls the email subject line. Default: <code>Your reservation for {site_title}</code>', 'wp-mb_hms' ),
						'type' => 'text',
						'std'  => esc_html__( 'Your reservation for {site_title}', 'wp-mb_hms' ),
					),
					'emails_confirmed_reservation_heading' => array(
						'id'   => 'emails_confirmed_reservation_heading',
						'name' => esc_html__( 'Email heading', 'wp-mb_hms' ),
						'desc' => __( 'This controls the main heading contained within the email notification.', 'wp-mb_hms' ),
						'type' => 'text',
						'std'  => esc_html__( 'Thank you for your reservation', 'wp-mb_hms' ),
					),
					'emails_guest_invoice' => array(
						'id'   => 'emails_guest_invoice',
						'name' => '<strong>' . esc_html__( 'Guest invoice', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'emails_guest_invoice_description' => array(
						'id'   => 'emails_guest_invoice_description',
						'desc' => __( 'Guest invoice emails can be sent to guests containing their reservation information and payment links. Use these emails when you (the admin) create a reservation manually that requires a payment (deposit).', 'wp-mb_hms' ),
						'type' => 'description'
					),
					'emails_guest_invoice_enabled' => array(
						'id'   => 'emails_guest_invoice_enabled',
						'name' => esc_html__( 'Enable/disable', 'wp-mb_hms' ),
						'desc' => __( 'Enable this email notification.', 'wp-mb_hms' ),
						'type' => 'checkbox',
						'std'  => true,
					),
					'emails_guest_invoice_subject' => array(
						'id'   => 'emails_guest_invoice_subject',
						'name' => esc_html__( 'Email subject', 'wp-mb_hms' ),
						'desc' => __( 'This controls the email subject line. Default: <code>Invoice for reservation #{reservation_number}</code>', 'wp-mb_hms' ),
						'type' => 'text',
						'std'  => esc_html__( 'Invoice for reservation #{reservation_number}', 'wp-mb_hms' ),
					),
					'emails_guest_invoice_heading' => array(
						'id'   => 'emails_guest_invoice_heading',
						'name' => esc_html__( 'Email heading', 'wp-mb_hms' ),
						'desc' => __( 'This controls the main heading contained within the email notification.', 'wp-mb_hms' ),
						'type' => 'text',
						'std'  => esc_html__( 'Invoice for reservation #{reservation_number}', 'wp-mb_hms' ),
					),
					'emails_cancelled_reservation' => array(
						'id'   => 'emails_cancelled_reservation',
						'name' => '<strong>' . esc_html__( 'Cancelled reservation', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'emails_cancelled_reservation_description' => array(
						'id'   => 'emails_cancelled_reservation_description',
						'desc' => __( 'Cancelled reservation emails are sent to the admin when the guest cancels his reservation.', 'wp-mb_hms' ),
						'type' => 'description'
					),
					'emails_cancelled_reservation_enabled' => array(
						'id'   => 'emails_cancelled_reservation_enabled',
						'name' => esc_html__( 'Enable/disable', 'wp-mb_hms' ),
						'desc' => __( 'Enable this email notification.', 'wp-mb_hms' ),
						'type' => 'checkbox',
						'std'  => true,
					),
					'emails_cancelled_reservation_subject' => array(
						'id'   => 'emails_cancelled_reservation_subject',
						'name' => esc_html__( 'Email subject', 'wp-mb_hms' ),
						'desc' => __( 'This controls the email subject line. Default: <code>{site_title} - Cancelled reservation #{reservation_number}</code>', 'wp-mb_hms' ),
						'type' => 'text',
						'std'  => esc_html__( '{site_title} - Cancelled reservation #{reservation_number}', 'wp-mb_hms' ),
					),
					'emails_cancelled_reservation_heading' => array(
						'id'   => 'emails_cancelled_reservation_heading',
						'name' => esc_html__( 'Email heading', 'wp-mb_hms' ),
						'desc' => __( 'This controls the main heading contained within the email notification.', 'wp-mb_hms' ),
						'type' => 'text',
						'std'  => esc_html__( 'Cancelled reservation', 'wp-mb_hms' ),
					),



					'emails_guest_cancelled_reservation' => array(
						'id'   => 'emails_guest_cancelled_reservation',
						'name' => '<strong>' . esc_html__( 'Guest cancelled reservation', 'wp-mb_hms' ) . '</strong>',
						'type' => 'header'
					),
					'emails_guest_cancelled_reservation_description' => array(
						'id'   => 'emails_guest_cancelled_reservation_description',
						'desc' => __( 'Cancelled reservation emails are sent to guests when reservations have been marked cancelled.', 'wp-mb_hms' ),
						'type' => 'description'
					),
					'emails_guest_cancelled_reservation_enabled' => array(
						'id'   => 'emails_guest_cancelled_reservation_enabled',
						'name' => esc_html__( 'Enable/disable', 'wp-mb_hms' ),
						'desc' => __( 'Enable this email notification.', 'wp-mb_hms' ),
						'type' => 'checkbox',
						'std'  => true,
					),
					'emails_guest_cancelled_reservation_subject' => array(
						'id'   => 'emails_guest_cancelled_reservation_subject',
						'name' => esc_html__( 'Email subject', 'wp-mb_hms' ),
						'desc' => __( 'This controls the email subject line. Default: <code>Your reservation for {site_title}</code>', 'wp-mb_hms' ),
						'type' => 'text',
						'std'  => esc_html__( 'Your reservation for {site_title}', 'wp-mb_hms' ),
					),
					'emails_guest_cancelled_reservation_heading' => array(
						'id'   => 'emails_guest_cancelled_reservation_heading',
						'name' => esc_html__( 'Email heading', 'wp-mb_hms' ),
						'desc' => __( 'This controls the main heading contained within the email notification.', 'wp-mb_hms' ),
						'type' => 'text',
						'std'  => esc_html__( 'Cancelled reservation', 'wp-mb_hms' ),
					),
				)
			),
			/* License Settings 
			'licenses' => apply_filters( 'mb_hms_settings_licenses',
				array()
			),*/
			/* Tools Settings 
			'tools' => apply_filters( 'mb_hms_settings_tools',
				array(
				)
			),*/
		);

		return apply_filters( 'mb_hms_settings_fields', $settings );
	}
}

endif;
