<?php
/**

 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'MBH_Privacy_Exporters' ) ) :

/**
 *MBHL_Privacy_Exporters Class
 */
class MBH_Privacy_Exporters {

	/**
	 * Finds and exports data associated with an email address.
	 *
	 * @param string $email The guest email address.
	 * @return array An array of personal data in name value pairs
	 */
	public static function reservation_data_exporter( $email ) {
		$data_to_export = array();
		$reservations   = mbh_get_reservations_by_email( $email );

		foreach ( $reservations as $reservation ) {
			$data_to_export[] = array(
				'group_id'    => 'mb_hms_reservations',
				'group_label' => __( 'Reservations', 'wp-mb_hms' ),
				'item_id'     => 'reservation-' . $reservation->id,
				'data'        => self::get_reservation_personal_data( $reservation ),
			);
		}

		return array(
			'data' => $data_to_export,
			'done' => true,
		);
	}

	/**
	 * Get personal data (key/value pairs) for a reservation object.
	 *
	 * @param mbh_Reservation $reservation Reservation object.
	 * @return array
	 */
	protected static function get_reservation_personal_data( $reservation ) {
		$personal_data   = array();
		$props_to_export = apply_filters( 'mb_hms_privacy_export_reservation_personal_data_props', array(
			'reservation_number'      => __( 'Resevation Number', 'wp-mb_hms' ),
			'date_created'            => __( 'Resevation Date', 'wp-mb_hms' ),
			'guest_ip_address'        => __( 'IP Address', 'wp-mb_hms' ),
			'formatted_guest_address' => __( 'Guest Address', 'wp-mb_hms' ),
			'guest_telephone'         => __( 'Guest Phone Number', 'wp-mb_hms' ),
			'guest_email'             => __( 'Email Address', 'wp-mb_hms' ),
		), $reservation );

		foreach ( $props_to_export as $prop => $name ) {
			$value = '';

			switch ( $prop ) {
				case 'reservation_number':
					$value = $reservation->id;
					break;
				case 'date_created':
					$value = $reservation->reservation_date;
					break;
				case 'guest_ip_address':
					$value = $reservation->reservation_guest_ip_address;
					break;
				case 'formatted_guest_address':
					$value = preg_replace( '#<br\s*/?>#i', ', ', $reservation->get_formatted_guest_address() );
					break;
				case 'guest_telephone':
					$value = $reservation->guest_telephone;
					break;
				case 'guest_email':
					$value = $reservation->guest_email;
					break;
			}

			$value = apply_filters( 'mb_hms_privacy_export_reservation_personal_data_prop', $value, $prop, $reservation );

			if ( $value ) {
				$personal_data[] = array(
					'name'  => $name,
					'value' => $value,
				);
			}
		}

		/**
		 * Allow extensions to register their own personal data for this reservation for the export.
		 *
		 * @param array    $personal_data Array of name value pairs to expose in the export.
		 * @param HTL_Reservation $reservation A reservation object.
		 */
		$personal_data = apply_filters( 'mb_hms_privacy_export_reservation_personal_data', $personal_data, $reservation );

		return $personal_data;
	}
}

endif;
