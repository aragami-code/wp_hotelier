<?php
/**
 
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Admin_Settings_Fields' ) ) :

/**
 * _Admin_Settings_Fields Class
 */
class MBH_Admin_Settings_Fields {

	/**
    * Holds the values to be used in the fields callbacks
    */
   private $options = array();

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->options = get_option( 'mb_hms_settings' );
		$this->pages   = $this->get_mb_hms_pages();

		// Fields callback (HTML)
		add_filter( 'mb_hms_settings_header_callback', array( $this, 'print_header' ), 10, 2 );
		add_filter( 'mb_hms_settings_description_callback', array( $this, 'print_description' ), 10, 2 );
		add_filter( 'mb_hms_settings_text_callback', array( $this, 'print_text' ), 10, 2 );
		add_filter( 'mb_hms_settings_textarea_callback', array( $this, 'print_textarea' ), 10, 2 );
		add_filter( 'mb_hms_settings_email_callback', array( $this, 'print_email' ), 10, 2 );
		add_filter( 'mb_hms_settings_upload_callback', array( $this, 'print_upload' ), 10, 2 );
		add_filter( 'mb_hms_settings_number_callback', array( $this, 'print_number' ), 10, 2 );
		add_filter( 'mb_hms_settings_booking_hold_minutes_callback', array( $this, 'print_booking_hold_minutes' ), 10, 2 );
		add_filter( 'mb_hms_settings_select_callback', array( $this, 'print_select' ), 10, 2 );
		add_filter( 'mb_hms_settings_checkbox_callback', array( $this, 'print_checkbox' ), 10, 2 );
		add_filter( 'mb_hms_settings_multi_checkbox_callback', array( $this, 'print_multi_checkbox' ), 10, 2 );
		add_filter( 'mb_hms_settings_radio_callback', array( $this, 'print_radio' ), 10, 2 );
		add_filter( 'mb_hms_settings_button_callback', array( $this, 'print_button' ), 10, 2 );
		add_filter( 'mb_hms_settings_card_icons_callback', array( $this, 'print_card_icons' ), 10, 2 );
		add_filter( 'mb_hms_settings_gateways_callback', array( $this, 'print_gateways' ), 10, 2 );
		add_filter( 'mb_hms_settings_gateway_select_callback', array( $this, 'print_gateway_select' ), 10, 2 );
		add_filter( 'mb_hms_settings_image_size_callback', array( $this, 'print_image_size' ), 10, 2 );
		add_filter( 'mb_hms_settings_from_to_callback', array( $this, 'print_from_to' ), 10, 2 );
		add_filter( 'mb_hms_settings_info_callback', array( $this, 'print_info' ), 10, 2 );
		add_filter( 'mb_hms_settings_seasonal_prices_table_callback', array( $this, 'print_seasonal_prices_table' ), 10, 2 );
		add_filter( 'mb_hms_settings_license_key_callback', array( $this, 'print_license_key' ), 10, 2 );
		add_filter( 'mb_hms_settings_percentage_callback', array( $this, 'print_percentage' ), 10, 2 );

		// Fields validation
		add_filter( 'mb_hms_settings_sanitize_text', array( $this, 'sanitize_text' ) );
		add_filter( 'mb_hms_settings_sanitize_textarea', array( $this, 'sanitize_text' ) );
		add_filter( 'mb_hms_settings_sanitize_email', array( $this, 'sanitize_email' ) );
		add_filter( 'mb_hms_settings_sanitize_upload', array( $this, 'sanitize_upload' ) );
		add_filter( 'mb_hms_settings_sanitize_number', array( $this, 'sanitize_number' ), 10, 2 );
		add_filter( 'mb_hms_settings_sanitize_select', array( $this, 'sanitize_select' ), 10, 2 );
		add_filter( 'mb_hms_settings_sanitize_booking_hold_minutes', array( $this, 'sanitize_booking_hold_minutes' ) );
		add_filter( 'mb_hms_settings_sanitize_image_size', array( $this, 'sanitize_image_size' ) );
		add_filter( 'mb_hms_settings_sanitize_seasonal_prices_table', array( $this, 'sanitize_seasonal_prices_table' ) );
		add_filter( 'mb_hms_settings_sanitize_percentage', array( $this, 'sanitize_percentage' ) );

		// Actions
		add_action( 'mb_hms_settings_hook_install_pages', array( $this, 'install_pages' ) );
		add_action( 'mb_hms_settings_hook_send_test_email', array( $this, 'send_test_email' ) );
		add_action( 'mb_hms_settings_hook_clear_sessions', array( $this, 'clear_sessions' ) );
		add_action( 'mb_hms_settings_hook_delete_completed_bookings', array( $this, 'delete_completed_bookings' ) );
		add_action( 'mb_hms_settings_info_mb_hms_version', array( $this, 'print_mb_hms_version' ) );
		add_action( 'mb_hms_settings_info_theme_name', array( $this, 'print_theme_name' ) );
		add_action( 'mb_hms_settings_info_theme_version', array( $this, 'print_theme_version' ) );
		add_action( 'mb_hms_settings_info_parent_theme_name', array( $this, 'print_parent_theme_name' ) );
		add_action( 'mb_hms_settings_info_parent_theme_version', array( $this, 'print_parent_theme_version' ) );
		add_action( 'mb_hms_settings_info_listing_page_info', array( $this, 'print_listing_page_info' ) );
		add_action( 'mb_hms_settings_info_booking_page_info', array( $this, 'print_booking_page_info' ) );
		add_action( 'mb_hms_settings_info_server_info', array( $this, 'print_server_info' ) );
		add_action( 'mb_hms_settings_info_php_version', array( $this, 'print_php_version' ) );
		add_action( 'mb_hms_settings_info_wp_memory_limit', array( $this, 'print_wp_memory_limit' ) );
		add_action( 'mb_hms_settings_info_wp_debug', array( $this, 'print_wp_debug' ) );
		add_action( 'mb_hms_settings_info_php_post_max_size', array( $this, 'print_php_post_max_size' ) );
		add_action( 'mb_hms_settings_info_php_post_max_upload_size', array( $this, 'print_php_post_max_upload_size' ) );
		add_action( 'mb_hms_settings_info_php_time_limit', array( $this, 'print_php_time_limit' ) );
		add_action( 'mb_hms_settings_info_php_max_input_vars', array( $this, 'print_php_max_input_vars' ) );
		add_action( 'mb_hms_settings_info_fsockopen_cURL', array( $this, 'print_fsockopen_cURL' ) );
		add_action( 'mb_hms_settings_info_domdocument', array( $this, 'print_domdocument' ) );
		add_action( 'mb_hms_settings_info_log_directory_writable', array( $this, 'print_log_directory_writable' ) );
	}

	/**
	 * Get required mb_hms pages
	 */
	public function get_mb_hms_pages() {
		$required_pages = array(
			'listing' => array(
				'option'    => 'mb_hms_listing_page_id',
				'shortcode' => '[' . apply_filters( 'mb_hms_listing_shortcode_tag', 'mb_hms_listing' ) . ']',
			),
			'booking' => array(
				'option'    => 'mb_hms_booking_page_id',
				'shortcode' => '[' . apply_filters( 'mb_hms_booking_shortcode_tag', 'mb_hms_booking' ) . ']',
			),
		);

		$pages = array();

		foreach ( $required_pages as $page_key => $page ) {
			$errors        = array();
			$page_id       = get_option( $page[ 'option' ] );
			$page_set      = false;
			$page_exists   = false;
			$page_visible  = false;
			$has_shortcode = false;

			// Check if the page is set
			if ( $page_id ) {
				$page_set = true;
			}

			// Check if the page exists
			if ( get_post( $page_id ) ) {
				$page_exists = true;
			}

			// Check if the page is visible
			if ( 'publish' === get_post_status( $page_id ) ) {
				$page_visible = true;
			}

			// Check if the page has the required shortcode
			if ( get_post( $page_id ) ) {
				$_page = get_post( $page_id );

				if ( strstr( $_page->post_content, $page[ 'shortcode' ] ) ) {
					$has_shortcode = true;
				}
			}

			$pages[ $page_key ] = array(
				'page_id'            => $page_id,
				'page_set'           => $page_set,
				'page_exists'        => $page_exists,
				'page_visible'       => $page_visible,
				'shortcode'          => $page[ 'shortcode' ],
				'has_shortcode'      => $has_shortcode
			);
		}

		return $pages;
	}

	/**
	 * Print header section
	 */
	public function print_header( $html, $args ) {
		echo '<hr/>';
	}

	/**
	 * Print description section
	 */
	public function print_description( $html, $args ) {
		echo '<p class="section-description">' . wp_kses_post( $args[ 'desc' ] ) . '</p>';
	}

	/**
	 * Print text input
	 */
	public function print_text( $html, $args ) {
		if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
			$value = $this->options[ $args[ 'id' ] ];
		} else {
			$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
		}

		$placeholder = isset( $args[ 'placeholder' ] ) ? $args[ 'placeholder' ] : '';

		$size     = ( isset( $args[ 'size' ] ) && ! is_null( $args[ 'size' ] ) ) ? $args[ 'size' ] : 'regular';
		$html     = '<input type="text" class="' . esc_attr( $size ) . '-text" id="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']" name="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $placeholder ) . '" />';
		$html    .= '<label for="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']"> '  . wp_kses_post( $args[ 'desc' ] ) . '</label>';

		echo $html;
	}

	/**
	 * Print text input
	 */
	public function print_textarea( $html, $args ) {
		if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
			$value = $this->options[ $args[ 'id' ] ];
		} else {
			$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
		}

		$placeholder = isset( $args[ 'placeholder' ] ) ? $args[ 'placeholder' ] : '';

		$size     = ( isset( $args[ 'size' ] ) && ! is_null( $args[ 'size' ] ) ) ? $args[ 'size' ] : 'regular';
		$html     = '<textarea class="' . esc_attr( $size ) . '-text" id="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']" name="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']" placeholder="' . esc_attr( $placeholder ) . '" style="width:350px; height: 100px;">' . esc_attr( $value ) . '</textarea><br>';
		$html    .= '<label for="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']"> '  . wp_kses_post( $args[ 'desc' ] ) . '</label>';

		echo $html;
	}

	/**
	 * Print email input
	 */
	public function print_email( $html, $args ) {
		if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
			$value = $this->options[ $args[ 'id' ] ];
		} else {
			$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
		}

		$placeholder = isset( $args[ 'placeholder' ] ) ? $args[ 'placeholder' ] : '';
		$multiple    = isset( $args[ 'multiple' ] ) ? 'multiple' : '';

		$size     = ( isset( $args[ 'size' ] ) && ! is_null( $args[ 'size' ] ) ) ? $args[ 'size' ] : 'regular';
		$html     = '<input type="email" class="' . esc_attr( $size ) . '-text" id="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']" name="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $placeholder ) . '" ' . esc_attr( $multiple ) . '/>';
		$html    .= '<label for="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']"> '  . wp_kses_post( $args[ 'desc' ] ) . '</label>';

		echo $html;
	}

	/**
	 * Print upload input
	 */
	public function print_upload( $html, $args ) {
		if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
			$value = $this->options[ $args[ 'id' ] ];
		} else {
			$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
		}

		$placeholder = isset( $args[ 'placeholder' ] ) ? $args[ 'placeholder' ] : '';

		$size     = ( isset( $args[ 'size' ] ) && ! is_null( $args[ 'size' ] ) ) ? $args[ 'size' ] : 'regular';
		$html     = '<input type="text" class="' . esc_attr( $size ) . '-text" id="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']" name="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $placeholder ) . '" />';
		$html    .= '<a href="#" class="button mbh-uploader">' . esc_html__( 'Upload', 'wp-mb_hms' ) . '</a>';
		$html    .= '<label for="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']"> '  . wp_kses_post( $args[ 'desc' ] ) . '</label>';

		echo $html;
	}

	/**
	 * Print text-number input
	 */
	public function print_number( $html, $args ) {
		if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
			$value = $this->options[ $args[ 'id' ] ];
		} else {
			$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
		}

		$size     = ( isset( $args[ 'size' ] ) && ! is_null( $args[ 'size' ] ) ) ? $args[ 'size' ] : 'regular';
		$html     = '<input type="number" class="' . esc_attr( $size ) . '-text" id="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']" name="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']" value="' . esc_attr( $value ) . '" />';
		$html    .= '<label for="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']"> '  . wp_kses_post( $args[ 'desc' ] ) . '</label>';

		echo $html;
	}

	/**
	 * Print booking_hold_minutes (text-number) input
	 */
	public function print_booking_hold_minutes( $html, $args ) {
		if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
			$value = $this->options[ $args[ 'id' ] ];
		} else {
			$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
		}

		$size     = ( isset( $args[ 'size' ] ) && ! is_null( $args[ 'size' ] ) ) ? $args[ 'size' ] : 'regular';
		$html     = '<input type="number" class="' . esc_attr( $size ) . '-text" id="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']" name="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']" value="' . esc_attr( $value ) . '" />';
		$html    .= '<label for="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']"> '  . wp_kses_post( $args[ 'desc' ] ) . '</label>';

		echo $html;
	}

	/**
	 * Print select input
	 */
	public function print_select( $html, $args ) {
		if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
			$value = $this->options[ $args[ 'id' ] ];
		} else {
			$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
		}

		$html = '<select id="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']" name="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']">';

		foreach ( $args[ 'options' ] as $option => $name ) {
			$selected = selected( $option, $value, false );
			$html .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( $name ) . '</option>';
		}

		$html .= '</select>';
		$html .= '<label for="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']"> '  . wp_kses_post( $args[ 'desc' ] ) . '</label>';

		echo apply_filters( 'mb_hms_settings_print_select', $html, $args, $value );
	}

	/**
	 * Print checkbox input
	 */
	public function print_checkbox( $html, $args ) {
		if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
			$value = $this->options[ $args[ 'id' ] ];
		} else {
			$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
		}

		$html = '<input type="checkbox" id="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']" name="mb_hms_settings[' . $args['id'] . ']" value="1" ' . checked( $value, 1, false ) . '/>';
		$html .= '<label for="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']"> '  . esc_html( $args[ 'desc' ] ) . '</label>';

		if ( $args[ 'subdesc' ] ) {
			$html .= '<p class="description subdesc"> '  . wp_kses_post( $args[ 'subdesc' ] ) . '</label>';
		}

		echo $html;
	}

	/**
	 * Print multi_checkbox input
	 */
	public function print_multi_checkbox( $html, $args ) {
		foreach ( $args[ 'options' ] as $key => $option ) {
			$enabled = ( isset( $this->options[ $args[ 'id' ] ][ $key ] ) ) ? '1' : null;

			$html = '<input name="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . '][' . $key . ']"" id="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . '][' . esc_attr( $key ) . ']" type="checkbox" value="1" ' . checked('1', $enabled, false) . '/>&nbsp;';
			$html .= '<label for="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . '][' . $key . ']">' . wp_kses_post( $option ) . '</label><br/>';
			echo $html;
		}
	}

	/**
	 * Print card_icons input
	 */
	public function print_card_icons( $html, $args ) {
		if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
			$value = $this->options[ $args[ 'id' ] ];
		} else {
			$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
		}

		foreach ( $args[ 'options' ] as $key => $option ) {
			if ( isset( $this->options[ $args[ 'id' ] ][ $key ] ) ) {
				$enabled = 1;
			} else {
				$enabled = NULL;
			}

			echo '<input name="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . '][' . esc_attr( $key ) . ']" id="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . '][' . esc_attr( $key ) . ']" class="card-icons" type="checkbox" value="1" ' . checked( 1, $enabled, false ) . ' />&nbsp;';

			// Extensions can use the ID of the 'span' to add the gateway icon with CSS
			echo '<label class="input-checkbox card-icons" for="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . '][' . esc_attr( $key ) . ']"><span class="mb_hms-accepted-cards" id="mb_hms-accepted-cards-' . esc_attr( $key ) . '">' . wp_kses_post( $option ) . '</span></label>';
		}

		echo '<div class="description cards-description">' . wp_kses_post( $args[ 'desc' ] ) . '</div>';
	}

	/**
	 * Print radio input
	 */
	public function print_radio( $html, $args ) {
		foreach ( $args[ 'options' ] as $key => $option ) {
			$checked = false;

			if ( isset( $this->options[ $args[ 'id' ] ] ) && $this->options[ $args[ 'id' ] ] == $key )
				$checked = true;
			elseif( isset( $args[ 'std' ] ) && $args[ 'std' ] == $key && ! isset( $this->options[ $args[ 'id' ] ] ) )
				$checked = true;

			echo '<input name="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']" id="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . '][' . esc_attr( $key ) . ']" type="radio" value="' . esc_attr( $key ) . '" ' . checked( true, $checked, false ) . '/>&nbsp;';
			echo '<label class="input-radio" for="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . '][' . esc_attr( $key ) . ']">' . wp_kses_post( $option ) . '</label><br/>';
		}

		echo '<div class="description radio-description">' . wp_kses_post( $args[ 'desc' ] ) . '</div>';
	}

	/**
	 * Print image_size input
	 */
	public function print_image_size( $html, $args ) {
		if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
			$value = $this->options[ $args[ 'id' ] ];
		} else {
			$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
		}

		$width   = $value[ 'width' ];
		$height  = $value[ 'height' ];
		$checked = isset( $this->options[ $args[ 'id' ] ][ 'crop' ] ) ? checked( 1, $this->options[ $args[ 'id' ] ][ 'crop' ], false ) : checked( 1, isset( $value[ 'crop' ] ), false );

		$html = '<input name="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . '][width]" id="' . esc_attr( $args[ 'id' ] ) . '-width" type="text" size="3" value="' . absint( $width ) . '" /> &times; <input name="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . '][height]" id="' . esc_attr( $args[ 'id' ] ) . '-height" type="text" size="3" value="' . absint( $height ) . '" />px';

		$html .= '<label><input name="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . '][crop]" id="' . esc_attr( $args[ 'id' ] ) . '-crop" type="checkbox" value="1" ' . $checked . ' />' . esc_html__( 'Hard crop?', 'wp-mb_hms' ) . '</label>';

		echo $html;
	}

	/**
	 * Print from_to input
	 */
	public function print_from_to( $html, $args ) {
		if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
			$value = $this->options[ $args[ 'id' ] ];
		} else {
			$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
		}

		$from = $value[ 'from' ];
		$to   = $value[ 'to' ];

		$options = array();

		for ( $i = 0; $i <= 24; $i++ ) {
			$options[ $i ] = sprintf( '%02d', $i ) . ':00';
			$options[ $i + 25 ] = sprintf( '%02d', $i ) . ':30';
		}

		$html = '<label class="from-to">' . esc_html__( 'From:', 'wp-mb_hms' ) . '<select name="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . '][from]">';

		foreach ( $options as $option => $name ) {
			$selected = selected( $option, $from, false );
			$html .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( $name ) . '</option>';
		}

		$html .= '</select></label>';

		$html .= '<label class="from-to">' . esc_html__( 'To:', 'wp-mb_hms' ) . '<select name="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . '][to]">';

		foreach ( $options as $option => $name ) {
			$selected = selected( $option, $to, false );
			$html .= '<option value="' . esc_attr( $option ) . '" ' . $selected . '>' . esc_html( $name ) . '</option>';
		}

		$html .= '</select></label>';

		echo $html;
	}

	/**
	 * Print gateways input
	 */
	public function print_gateways( $html, $args ) {
		foreach ( $args[ 'options' ] as $option ) {
			$enabled = ( isset( $this->options[ 'payment_gateways' ][ esc_attr( $option[ 'id' ] ) ] ) ) ? '1' : null;

			$html = '<input name="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . '][' . $option[ 'id' ] . ']"" id="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . '][' . esc_attr( $option[ 'id' ] ) . ']" type="checkbox" value="1" ' . checked('1', $enabled, false) . '/>&nbsp;';
			$html .= '<label for="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . '][' . $option[ 'id' ] . ']">' . wp_kses_post( $option[ 'admin_label' ] ) . '</label><br/>';
			echo $html;
		}
	}

	/**
	 * Print gateways dropdown
	 */
	public function print_gateway_select( $html, $args ) {
		$html = '<select name="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']"" id="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']">';

		foreach ( $args[ 'options' ] as $option ) {
			$selected = isset( $this->options[ $args[ 'id' ] ] ) ? selected( $option[ 'id' ], $this->options[ $args[ 'id' ] ], false ) : '';
			$html .= '<option value="' . esc_attr( $option[ 'id' ] ) . '"' . $selected . '>' . esc_html( $option[ 'admin_label' ] ) . '</option>';
		}

		echo $html;
	}

	/**
	 * Print button
	 */
	public function print_button( $html, $args ) {
		$html = '<p>';

		switch ( $args[ 'id' ] ) {
			case 'install_pages':
				$html .= '<a class="button" href="' . wp_nonce_url( admin_url( 'admin.php?page=mb_hms-settings&tab=tools&action=install_pages' ), 'tools_action' ) . '">' . esc_html__( 'Install pages', 'wp-mb_hms' ) . '</a>';
				break;

			case 'send_test_email':
				$html .= '<a class="button" href="' . wp_nonce_url( admin_url( 'admin.php?page=mb_hms-settings&tab=tools&action=send_test_email' ), 'tools_action' ) . '">' . esc_html__( 'Send email', 'wp-mb_hms' ) . '</a>';
				break;

			case 'clear_sessions':
				$html .= '<a class="button" href="' . wp_nonce_url( admin_url( 'admin.php?page=mb_hms-settings&tab=tools&action=clear_sessions' ), 'tools_action' ) . '">' . esc_html__( 'Clear sessions', 'wp-mb_hms' ) . '</a>';
				break;

			case 'delete_completed_bookings':
				$html .= '<a class="button" href="' . wp_nonce_url( admin_url( 'admin.php?page=mb_hms-settings&tab=tools&action=delete_completed_bookings' ), 'tools_action' ) . '">' . esc_html__( 'Delete bookings', 'wp-mb_hms' ) . '</a>';
				break;

			default:
				break;
		}


		$html .= '<span class="description"> '  . esc_html( $args[ 'desc' ] ) . '</span>';
		$html .= '</p>';

		echo $html;

		do_action( 'mb_hms_settings_hook_' . $args[ 'id' ] );
	}

	/**
	 * Print seasonal prices table
	 */
	public function print_seasonal_prices_table( $html, $args ) {
		if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
			$value = $this->options[ $args[ 'id' ] ];
		} else {
			$value = '';
		}

		$rules = mbh_get_option( 'seasonal_prices_schema', array() );

		$html  = '<table id="mb_hms-seasonal-schema-table" class="widefat">';
		$html .= '<tbody>';

		if ( ! empty( $rules ) ) {
			foreach( $rules as $key => $rule ) {
				$every_year = isset( $rule[ 'every_year' ] ) ? 1 : 0;

				$html .= '<tr class="rule-row" data-key="' . MBH_Formatting_Helper::sanitize_key( $key ) . '">';
				$html .= '<td class="season-dates">
							<label>' . esc_html__( 'From', 'wp-mb_hms' ) . '
							<input class="date-from" type="text" placeholder="YYYY-MM-DD" name="mb_hms_settings[seasonal_prices_schema][' . MBH_Formatting_Helper::sanitize_key( $key ) . '][from]" value="' . esc_attr__( $rule['from'] ) . '"></label>
							<label>' . esc_html__( 'To', 'wp-mb_hms' ) . '
							<input class="date-to" type="text" placeholder="YYYY-MM-DD" name="mb_hms_settings[seasonal_prices_schema][' . MBH_Formatting_Helper::sanitize_key( $key ) . '][to]" value="' . esc_attr__( $rule['to'] ) . '"></label>
							<label class="date-every-year-label">' . esc_html__( 'Every year?', 'wp-mb_hms' ) . '
							<input class="date-every-year" value="1" type="checkbox" name="mb_hms_settings[seasonal_prices_schema][' . MBH_Formatting_Helper::sanitize_key( $key ) . '][every_year]" ' . checked( $every_year, 1, false ) . '></label>
							<input class="rule-index" type="hidden" name="mb_hms_settings[seasonal_prices_schema][' . MBH_Formatting_Helper::sanitize_key( $key ) . '][index]" value="' . esc_attr__( MBH_Formatting_Helper::sanitize_key( $key ) ) . '">
						</td>';
				$html .= '<td>
							<button type="button" class="remove-rule button">' . esc_html__( 'Remove', 'wp-mb_hms' ) . '</button>
						</td>';
				$html .= '<td class="sort-rules"><i class="mbh-icon mbh-bars"></i></td>';
				$html .= '</tr>';
			}

		} else {

			$html .= '<tr class="rule-row" data-key="1">';
			$html .= '<td class="season-dates">
						<label>' . esc_html__( 'From', 'wp-mb_hms' ) . '
						<input class="date-from" type="text" placeholder="YYYY-MM-DD" name="mb_hms_settings[seasonal_prices_schema][1][from]"></label>
						<label>' . esc_html__( 'To', 'wp-mb_hms' ) . '
						<input class="date-to" type="text" placeholder="YYYY-MM-DD" name="mb_hms_settings[seasonal_prices_schema][1][to]"></label>
						<label class="date-every-year-label">' . esc_html__( 'Every year?', 'wp-mb_hms' ) . '
						<input class="date-every-year" value="1" type="checkbox" name="mb_hms_settings[seasonal_prices_schema][1][every_year]"></label>
						<input class="rule-index" type="hidden" name="mb_hms_settings[seasonal_prices_schema][1][index]" value="1">
					</td>';
			$html .= '<td>
						<button type="button" class="remove-rule button">' . esc_html__( 'Remove', 'wp-mb_hms' ) . '</button>
					</td>';
			$html .= '<td class="sort-rules"><i class="mbh-icon mbh-bars"></i></td>';
			$html .= '</tr>';
		}

		$html .= '</tbody>';
		$html .= '<tfoot>';
		$html .= '<tr><td colspan="3">
						<button type="button" class="add-rule button button-primary">' . esc_html__( 'Add new rule', 'wp-mb_hms' ) . '</button>
				</td></tr>';
		$html .= '</tfoot>';
		$html .= '</table>';
		$html .= '<span class="description seasonal-prices-table-description"> '  . esc_html( $args[ 'desc' ] ) . '</span>';

		echo $html;
	}

	/**
	 * Print license key input
	 */
	public function print_license_key( $html, $args ) {
		if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
			$value = $this->options[ $args[ 'id' ] ];
		} else {
			$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
		}

		$license  = get_option( $args[ 'id' ] . '_active' );

		if( ! empty( $license ) && is_object( $license ) ) {

			// handle errors
			if ( false === $license->success ) {

				switch( $license->error ) {

					case 'expired' :

						$class = 'expired';
						$messages[] = sprintf(
							__( 'Your license key expired on %s. Please <a href="%s" target="_blank">renew your license key</a>.', 'wp-mb_hms' ),
							date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) ),
							'https://wpmb_hms.com/checkout/?edd_license_key=' . $value
						);

						$license_status = 'license-' . $class . '-notice';

						break;

					case 'revoked' :

						$class = 'error';
						$messages[] = sprintf(
							__( 'Your license key has been disabled. Please <a href="%s" target="_blank">contact support</a> for more information.', 'wp-mb_hms' ),
							'https://wpmb_hms.com/support/'
						);

						$license_status = 'license-' . $class . '-notice';

						break;

					case 'missing' :

						$class = 'error';
						$messages[] = sprintf(
							__( 'Invalid license. Please <a href="%s" target="_blank">visit your account page</a> and verify it.', 'wp-mb_hms' ),
							'https://wpmb_hms.com/login/'
						);

						$license_status = 'license-' . $class . '-notice';

						break;

					case 'invalid' :
					case 'site_inactive' :

						$class = 'error';
						$messages[] = sprintf(
							__( 'Your %s is not active for this URL. Please <a href="%s" target="_blank">visit your account page</a> to manage your license key URLs.', 'wp-mb_hms' ),
							$args['name'],
							'https://wpmb_hms.com/login/'
						);

						$license_status = 'license-' . $class . '-notice';

						break;

					case 'item_name_mismatch' :

						$class = 'error';
						$messages[] = sprintf( __( 'This appears to be an invalid license key for %s.', 'wp-mb_hms' ), $args['name'] );

						$license_status = 'license-' . $class . '-notice';

						break;

					case 'no_activations_left':

						$class = 'error';
						$messages[] = sprintf( __( 'Your license key has reached its activation limit. <a href="%s">View possible upgrades</a> now.', 'wp-mb_hms' ), 'https://wpmb_hms.com/login/' );

						$license_status = 'license-' . $class . '-notice';

						break;

					default :

						$class = 'error';
						$error = ! empty(  $license->error ) ?  $license->error : __( 'unknown_error', 'wp-mb_hms' );
						$messages[] = sprintf( __( 'There was an error with this license key: %s. Please <a href="%s">contact our support team</a>.', 'wp-mb_hms' ), $error, 'https://wpmb_hms.com/login/' );

						$license_status = 'license-' . $class . '-notice';
						break;
				}

			} else {

				switch( $license->license ) {

					case 'valid' :
					default:

						$class = 'valid';

						$now        = current_time( 'timestamp' );
						$expiration = strtotime( $license->expires, current_time( 'timestamp' ) );

						if ( 'lifetime' === $license->expires ) {

							$messages[] = __( 'License key never expires.', 'wp-mb_hms' );

							$license_status = 'license-lifetime-notice';

						} elseif ( $expiration > $now && $expiration - $now < ( DAY_IN_SECONDS * 30 ) ) {

							$messages[] = sprintf(
								__( 'Your license key expires soon! It expires on %s. <a href="%s" target="_blank">Renew your license key</a>.', 'wp-mb_hms' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) ),
								'https://wpmb_hms.com/checkout/?edd_license_key=' . $value
							);

							$license_status = 'license-expires-soon-notice';

						} else {

							$messages[] = sprintf(
								__( 'Your license key expires on %s.', 'wp-mb_hms' ),
								date_i18n( get_option( 'date_format' ), strtotime( $license->expires, current_time( 'timestamp' ) ) )
							);

							$license_status = 'license-expiration-date-notice';

						}

						break;

				}

			}

		} else {
			$class = 'empty';

			$messages[] = sprintf(
				__( 'To receive updates, please enter your valid %s license key.', 'wp-mb_hms' ),
				$args['name']
			);

			$license_status = null;
		}

		$html    = '<label for="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']"> '  . esc_html__( 'License Key', 'wp-mb_hms' ) . '</label>';

		$html     .= '<input type="text" class="regular-text" id="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']" name="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']" value="' . esc_attr( $value ) . '" />';

		if ( ( is_object( $license ) && 'valid' == $license->license ) || 'valid' == $license ) {
			$html .= '<input type="submit" class="button button-secondary" name="' . $args[ 'id' ] . '_deactivate" value="' . esc_attr__( 'Deactivate License',  'wp-mb_hms' ) . '"/>';
		}

		if ( ! empty( $messages ) ) {
			foreach( $messages as $message ) {
				$html .= '<div class="mb_hms-license-data mb_hms-license-' . $class . ' ' . $license_status . '">';
					$html .= '<p>' . wp_kses_post( $message ) . '</p>';
				$html .= '</div>';
			}
		}

		wp_nonce_field( sanitize_text_field( $args[ 'id' ] ) . '-nonce', sanitize_text_field( $args[ 'id' ] ) . '-nonce' );

		echo $html;
	}

	/**
	 * Print percentage input
	 */
	public function print_percentage( $html, $args ) {
		if ( isset( $this->options[ $args[ 'id' ] ] ) ) {
			$value = $this->options[ $args[ 'id' ] ];
		} else {
			$value = isset( $args[ 'std' ] ) ? $args[ 'std' ] : '';
		}

		$placeholder = isset( $args[ 'placeholder' ] ) ? $args[ 'placeholder' ] : '';

		$html     = '<input type="text" class="medium-text" id="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']" name="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']" value="' . esc_attr( $value ) . '" placeholder="' . esc_attr( $placeholder ) . '" />';
		$html    .= '<br><label for="mb_hms_settings[' . esc_attr( $args[ 'id' ] ) . ']"> '  . wp_kses_post( $args[ 'desc' ] ) . '</label>';

		echo $html;
	}

	/**
	 * Install pages
	 */
	public function install_pages() {
		if ( ! empty( $_GET[ 'action' ] ) && ! empty( $_REQUEST[ '_wpnonce' ] ) && wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'tools_action' ) ) {

			if ( $_GET[ 'action' ] == 'install_pages' ) {

				MBH_Install::create_pages();
				echo '<div class="updated"><p>' . esc_html__( 'All missing mb_hms pages was installed successfully.', 'wp-mb_hms' ) . '</p></div>';
			}
		}
	}

	/**
	 * Send test email
	 */
	public function send_test_email() {
		if ( ! empty( $_GET[ 'action' ] ) && ! empty( $_REQUEST[ '_wpnonce' ] ) && wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'tools_action' ) ) {

			if ( $_GET[ 'action' ] == 'send_test_email' ) {

				$to      = get_option( 'admin_email' );
				$subject = sprintf( esc_html__( 'Test email from %s', 'wp-mb_hms'), get_bloginfo( 'name', 'display' ) );
				$message = sprintf( esc_html__( "This test email proves that your WordPress installation at %s can send emails.\n\nSent: %s", "mb_hms" ), esc_url( get_bloginfo( "url" ) ), date( "r" ) );
				$headers = 'Content-Type: text/plain';
				wp_mail( $to, $subject, $message, $headers );

				echo '<div class="updated"><p>' . sprintf( wp_kses( __( 'Email sent. This does not mean it has been delivered. See %s in the Codex for more information.', 'wp-mb_hms' ), array( 'a' => array( 'href' => array() ) ) ), '<a href="http://codex.wordpress.org/Function_Reference/wp_mail">wp_mail</a>' ) . '</p></div>';
			}
		}
	}

	/**
	 * Clear sessions
	 */
	public function clear_sessions() {
		if ( ! empty( $_GET[ 'action' ] ) && ! empty( $_REQUEST[ '_wpnonce' ] ) && wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'tools_action' ) ) {

			if ( $_GET[ 'action' ] == 'clear_sessions' ) {

				global $wpdb;

				$wpdb->query( "DELETE FROM {$wpdb->prefix}mb_hms_sessions" );

				wp_cache_flush();

				echo '<div class="updated"><p>' . esc_html__( 'Sessions successfully cleared.', 'wp-mb_hms' ) . '</p></div>';
			}
		}
	}

	/**
	 * Delete completed bookings
	 */
	public function delete_completed_bookings() {
		if ( ! empty( $_GET[ 'action' ] ) && ! empty( $_REQUEST[ '_wpnonce' ] ) && wp_verify_nonce( $_REQUEST[ '_wpnonce' ], 'tools_action' ) ) {

			if ( $_GET[ 'action' ] == 'delete_completed_bookings' ) {

				global $wpdb;

				$date        = date( 'Y-m-d' );
				$booking_ids = $wpdb->get_col( $wpdb->prepare( "SELECT reservation_id FROM {$wpdb->prefix}mb_hms_bookings WHERE checkout < %s", $date ) );

				if ( $booking_ids ) {

					foreach ( $booking_ids as $booking_id) {

						// Delete bookings from custom tables
						$wpdb->query( $wpdb->prepare( "DELETE FROM itemmeta USING {$wpdb->prefix}mb_hms_reservation_itemmeta itemmeta INNER JOIN {$wpdb->prefix}mb_hms_reservation_items items WHERE itemmeta.reservation_item_id = items.reservation_item_id and items.reservation_id = %d", $booking_id ) );
						$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}mb_hms_reservation_items WHERE reservation_id = %d", $booking_id ) );
						$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}mb_hms_rooms_bookings WHERE reservation_id = %d", $booking_id ) );
						$wpdb->query( $wpdb->prepare( "DELETE FROM {$wpdb->prefix}mb_hms_bookings WHERE reservation_id = %d", $booking_id ) );

						// Delete post
						wp_delete_post( $booking_id, true );
					}
				}

				echo '<div class="updated"><p>' . esc_html__( 'Bookings successfully deleted.', 'wp-mb_hms' ) . '</p></div>';
			}
		}
	}

	/**
	 * Print info
	 */
	public function print_info( $html, $args ) {
		echo '<p class="server-info">';
		do_action( 'mb_hms_settings_info_' . $args[ 'id' ] );
		echo '</p>';
	}

	/**
	 * Print mb_hms_version
	 */
	public function print_mb_hms_version() {
		$info = '<span>' . esc_html( MBH()->version ) . '</span>';

		echo $info;
	}

	/**
	 * Print theme_name
	 */
	public function print_theme_name() {
		$active_theme  = wp_get_theme();

		$info = '<span>' . esc_html( $active_theme->Name ) . '</span>';

		echo $info;
	}

	/**
	 * Print theme_version
	 */
	public function print_theme_version() {
		$active_theme  = wp_get_theme();

		$info = '<span>' . esc_html( $active_theme->Version ) . '</span>';

		echo $info;
	}

	/**
	 * Print parent_theme_name
	 */
	public function print_parent_theme_name() {
		$info = '<span>-</span>';

		if ( is_child_theme() ) {
			$active_theme  = wp_get_theme();
			$parent_theme = wp_get_theme( $active_theme->Template );
			$info = '<span>' . esc_html( $parent_theme->Name ) . '</span>';
		}

		echo $info;
	}

	/**
	 * Print parent_theme_version
	 */
	public function print_parent_theme_version() {
		$info = '<span>-</span>';

		if ( is_child_theme() ) {
			$active_theme  = wp_get_theme();
			$parent_theme = wp_get_theme( $active_theme->Template );
			$info = '<span>' . esc_html( $parent_theme->Version ) . '</span>';
		}

		echo $info;
	}


	/**
	 * Print listing_page_info
	 */
	public function print_listing_page_info() {
		$pages        = $this->pages;
		$listing_page = $pages[ 'listing' ];

		if ( ! $listing_page[ 'page_set' ] ) {
			$info = '<span class="info-error">' . __( 'Page not set', 'wp-mb_hms' ) . '</mark>';
		} elseif ( ! $listing_page[ 'page_exists' ] ) {
			$info = '<span class="info-error">' . __( 'The page is set, but it does not exist', 'wp-mb_hms' ) . '</mark>';
		} elseif ( ! $listing_page[ 'page_visible' ] ) {
			$info = '<span class="info-error">' . __( 'The page is set, but it is not public', 'wp-mb_hms' ) . '</mark>';
		} elseif ( ! $listing_page[ 'has_shortcode' ] ) {
			$info = '<span class="info-error">' . sprintf( __( 'The page requires this shortcode: %s', 'wp-mb_hms' ), '<code>' . $listing_page[ 'shortcode' ] . '</code>' ) . '</mark>';
		} else {
			$info = '<span class="info-success">(ID = ' . absint( $listing_page[ 'page_id' ] ) . ') ' . get_permalink( $listing_page[ 'page_id' ] ) . '</span>';
		}

		echo $info;
	}

	/**
	 * Print booking_page_info
	 */
	public function print_booking_page_info() {
		$pages        = $this->pages;
		$booking_page = $pages[ 'booking' ];

		if ( ! $booking_page[ 'page_set' ] ) {
			$info = '<span class="info-error">' . __( 'Page not set', 'wp-mb_hms' ) . '</mark>';
		} elseif ( ! $booking_page[ 'page_exists' ] ) {
			$info = '<span class="info-error">' . __( 'The page is set, but it does not exist', 'wp-mb_hms' ) . '</mark>';
		} elseif ( ! $booking_page[ 'page_visible' ] ) {
			$info = '<span class="info-error">' . __( 'The page is set, but it is not public', 'wp-mb_hms' ) . '</mark>';
		} elseif ( ! $booking_page[ 'has_shortcode' ] ) {
			$info = '<span class="info-error">' . sprintf( __( 'The page requires this shortcode: %s', 'wp-mb_hms' ), '<code>' . $listing_page[ 'shortcode' ] . '</code>' ) . '</mark>';
		} else {
			$info = '<span class="info-success">(ID = ' . absint( $booking_page[ 'page_id' ] ) . ') ' . get_permalink( $booking_page[ 'page_id' ] ) . '</span>';
		}

		echo $info;
	}

	/**
	 * Print server_info
	 */
	public function print_server_info() {
		$info = '<span>' . esc_html__( 'Not available', 'wp-mb_hms' ) . '</span>';

		if ( isset( $_SERVER[ 'SERVER_SOFTWARE' ] ) && ! empty( $_SERVER[ 'SERVER_SOFTWARE' ] ) ) {
			$info = '<span>' . $_SERVER[ 'SERVER_SOFTWARE' ] . '</span>';
		}

		echo $info;
	}

	/**
	 * Print php_version
	 */
	public function print_php_version() {
		$info = '<span>' . esc_html__( 'Not available', 'wp-mb_hms' ) . '</span>';

		if ( function_exists( 'phpversion' ) ) {
			if ( version_compare( phpversion(), '5.6.0', '<' ) ) {
				$info = '<span class="info-error">' . sprintf( esc_html__( '%s - Hotel management system requires at least PHP 5.6.0. Please update your PHP version.', 'wp-mb_hms' ), phpversion() ) . '</span>';
			} else {
				$info = '<span class="info-success">' . phpversion() . '</span>';
			}
		}

		echo $info;
	}

	/**
	 * Print wp_memory_limit
	 */
	public function print_wp_memory_limit() {
		$info = '<span>' . esc_html__( 'Not available', 'wp-mb_hms' ) . '</span>';

		$memory = MBH_Formatting_Helper::notation_to_int( WP_MEMORY_LIMIT );

		if ( $memory < 67108864 ) {
			$info = '<span class="info-error">' . sprintf( esc_html__( '%s - We recommend setting memory to at least 64MB.', 'wp-mb_hms' ), size_format( $memory ) ) . '</span>';
		} else {
			$info = '<span class="info-success">' . size_format( $memory ) . '</span>';
		}

		echo $info;
	}

	/**
	 * Print wp_debug
	 */
	public function print_wp_debug() {
		if ( defined('WP_DEBUG') && true === WP_DEBUG ) {
			$info = '<span>' . esc_html__( 'Enabled', 'wp-mb_hms' ) . '</span>';
		} else {
			$info = '<span>' . esc_html__( 'Disabled', 'wp-mb_hms' ) . '</span>';
		}

		echo $info;
	}

	/**
	 * Print php_post_max_size
	 */
	public function print_php_post_max_size() {
		$info = '<span>' . esc_html__( 'Not available', 'wp-mb_hms' ) . '</span>';

		if ( function_exists( 'ini_get' ) ) {
			$info = '<span>' . size_format( MBH_Formatting_Helper::notation_to_int( ini_get( 'post_max_size' ) ) ) . '</span>';
		}

		echo $info;
	}

	/**
	 * Print php_post_max_upload_size
	 */
	public function print_php_post_max_upload_size() {
		$info = '<span>' . size_format( wp_max_upload_size() ) . '</span>';

		echo $info;
	}

	/**
	 * Print php_time_limit
	 */
	public function print_php_time_limit() {
		$info = '<span>' . esc_html__( 'Not available', 'wp-mb_hms' ) . '</span>';

		if ( function_exists( 'ini_get' ) ) {
			$info = '<span>' . ini_get( 'max_execution_time' ) . '</span>';
		}

		echo $info;
	}

	/**
	 * Print php_max_input_vars
	 */
	public function print_php_max_input_vars() {
		$info = '<span>' . esc_html__( 'Not available', 'wp-mb_hms' ) . '</span>';

		if ( function_exists( 'ini_get' ) ) {
			$info = '<span>' . ini_get( 'max_input_vars' ) . '</span>';
		}

		echo $info;
	}

	/**
	 * Print fsockopen_cURL
	 */
	public function print_fsockopen_cURL() {
		if ( function_exists( 'fsockopen' ) && function_exists( 'curl_init' ) ) {
			$info = '<span class="info-success">' . esc_html__( 'Enabled', 'wp-mb_hms' ) . '</span>';
		} else {
			$info = '<span class="info-error">' . esc_html__( 'Your server does not have fsockopen or cURL enabled - PayPal IPN and other scripts which communicate with other servers will not work. Contact your hosting provider.', 'wp-mb_hms' ) . '</span>';
		}

		echo $info;
	}

	/**
	 * Print DOMDocument
	 */
	public function print_domdocument() {
		if ( class_exists( 'DOMDocument' ) ) {
			$info = '<span class="info-success">' . esc_html__( 'Enabled', 'wp-mb_hms' ) . '</span>';
		} else {
			$info = '<span class="info-error">' . esc_html__( 'Your server does not have the DOMDocument class enabled - Some extensions may not work without DOMDocument', 'wp-mb_hms' ) . '</span>';
		}

		echo $info;
	}

	/**
	 * Print log_directory_writable
	 */
	public function print_log_directory_writable() {
		if ( @fopen( MBH_LOG_DIR . 'test-log.log', 'a' ) ) {
			$info = '<span class="info-success">' . MBH_LOG_DIR . '</span>';
		} else {
			$info = sprintf( '<span class="info-error">' . wp_kses( __( 'To allow logging, make <code>%s</code> writable or define a custom <code>MBH_LOG_DIR</code>.', 'wp-mb_hms' ), array( 'code' => array() ) ) . '</span>', MBH_LOG_DIR );
		}

		echo $info;
	}

	/**
	 * Sanitize text input
	 */
	public function sanitize_text( $input ) {
		return sanitize_text_field( $input );
	}

	/**
	 * Sanitize select input
	 */
	public function sanitize_select( $input, $key ) {
		// Save mb_hms pages in a separate option
		if ( $key == 'listing_page' ) {
			update_option( 'mb_hms_listing_page_id', absint( $input ) );

			// clear transient
			delete_transient( 'mb_hms_cache_excluded_uris' );
		} else if ( $key == 'booking_page' ) {
			update_option( 'mb_hms_booking_page_id', absint( $input ) );

			// clear transient
			delete_transient( 'mb_hms_cache_excluded_uris' );
		}  else if ( $key == 'terms_page' ) {
			update_option( 'mb_hms_terms_page_id', absint( $input ) );
		}

		return sanitize_text_field( $input );
	}

	/**
	 * Sanitize upload input
	 */
	public function sanitize_upload( $input ) {
		return esc_url( $input );
	}

	/**
	 * Sanitize email input
	 */
	public function sanitize_email( $input ) {
		$input = explode( ',', $input );

		if ( is_array( $input ) ) {
			foreach ( $input as $key => $email ) {
				if ( ! is_email( $email ) ) {
					$input[ $key ] = '';
				}
			}

			$input = implode( ',', $input );
		}

		return $input;
	}

	/**
	 * Sanitize text-number input
	 */
	public function sanitize_number( $input, $key ) {
		// Min nights option must be at least 1
		if ( 'booking_minimum_nights' == $key && $input == 0 ) {
			return 1;
		}

		return absint( $input );
	}

	/**
	 * Sanitize booking_hold_minutes (text-number) input
	 */
	public function sanitize_booking_hold_minutes( $input ) {
		$input = absint( $input );

		wp_clear_scheduled_hook( 'mb_hms_cancel_pending_reservations' );

		if ( $input > 0 ) {
			wp_schedule_single_event( time() + ( absint( $input ) * 60 ), 'mb_hms_cancel_pending_reservations' );
		}

		return $input;
	}

	/**
	 * Sanitize image_size input
	 */
	public function sanitize_image_size( $input ) {
		return array_map( 'absint', $input );
	}

	/**
	 * Sanitize seasonal_prices_table input
	 */
	public function sanitize_seasonal_prices_table( $rules ) {
		if ( is_array( $rules ) ) {
			// ensures rules are correctly mapped to an array starting with an index of 1
			uasort( $rules, function( $a, $b ) {
				return $a[ 'index' ] - $b[ 'index' ];
			});

			$rules = array_combine( range( 1, count( $rules ) ), array_values( $rules ) );

			foreach ( $rules as $key => $rule ) {
				// Check date range
				if ( ! MBH_Formatting_Helper::is_valid_date_range( $rule[ 'from' ], $rule[ 'to' ] ) ) {
					unset( $rules[ $key ] );
					continue;
				}

				// We can't accept repeated rules that are greater than one year
				$from       = new DateTime( $rule[ 'from' ] );
				$to         = new DateTime( $rule[ 'to' ] );
				$interval   = $from->diff( $to );
				$years_diff = $interval->y;

				if ( $years_diff > 0 ) {
					$rules[ $key ] = array(
						'from' => $rule[ 'from' ],
						'to'   => $rule[ 'to' ],
					);
				}
			}
		}

		return array_combine( range( 1, count( $rules ) ), array_values( $rules ) );
	}

	/**
	 * Sanitize percentage input
	 */
	public function sanitize_percentage( $input ) {
		$input = number_format( (double) abs( $input ), 4, '.', '' );
		return $input;
	}
}

endif;

return new MBH_Admin_Settings_Fields();
