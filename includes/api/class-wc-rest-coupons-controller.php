<?php
/**
 * REST API Coupons controller
 *
 * Handles requests to the /coupons endpoint.
 *
 * @package WooCommerce/API
 * @since   2.6.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * REST API Coupons controller class.
 *
 * @package WooCommerce/API
 * @extends WC_REST_Coupons_V2_Controller
 */
class WC_REST_Coupons_Controller extends WC_REST_Coupons_V2_Controller {

	/**
	 * Endpoint namespace.
	 *
	 * @var string
	 */
	protected $namespace = 'wc/v3';

	/**
	 * Get formatted item data.
	 *
	 * @since  3.0.0
	 * @param  WC_Data         $object  WC_Data instance.
	 * @param  WP_REST_Request $request Request object.
	 * @return array
	 */
	protected function get_formatted_item_data( $object, $request ) {
		$fields   = $this->get_fields_for_response( $request );
		$data     = array();
		$raw_data = $object->get_data();

		$format_decimal = array( 'amount', 'minimum_amount', 'maximum_amount' );
		$format_date    = array( 'date_created', 'date_modified', 'date_expires' );
		$format_null    = array( 'usage_limit', 'usage_limit_per_user', 'limit_usage_to_x_items' );

		// Format decimal values.
		foreach ( $format_decimal as $key ) {
			if ( in_array( $key, $fields, true ) ) {
				$raw_data[ $key ] = wc_format_decimal( $raw_data[ $key ], 2 );
			}
		}

		// Format date values.
		foreach ( $format_date as $key ) {
			if ( in_array( $key, $fields, true ) ) {
				$datetime                  = $raw_data[ $key ];
				$raw_data[ $key ]          = wc_rest_prepare_date_response( $datetime, false );
				$raw_data[ $key . '_gmt' ] = wc_rest_prepare_date_response( $datetime );
			}
		}

		// Format null values.
		foreach ( $format_null as $key ) {
			if ( in_array( $key, $fields, true ) ) {
				$raw_data[ $key ] = $raw_data[ $key ] ? $raw_data[ $key ] : null;
			}
		}

		// Build response.
		$proprieties = array(
			'code',
			'amount',
			'date_created',
			'date_created_gmt',
			'date_modified',
			'date_modified_gmt',
			'discount_type',
			'description',
			'date_expires',
			'date_expires_gmt',
			'usage_count',
			'individual_use',
			'product_ids',
			'excluded_product_ids',
			'usage_limit',
			'usage_limit_per_user',
			'limit_usage_to_x_items',
			'free_shipping',
			'product_categories',
			'excluded_product_categories',
			'exclude_sale_items',
			'minimum_amount',
			'maximum_amount',
			'email_restrictions',
			'used_by',
			'meta_data',
		);

		if ( in_array( 'id', $fields, true ) ) {
			$data['id'] = $object->get_id();
		}

		foreach ( $proprieties as $propriety ) {
			if ( in_array( $propriety, $fields, true ) ) {
				$data[ $propriety ] = $raw_data[ $propriety ];
			}
		}

		return $data;
	}
}
