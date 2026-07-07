<?php
/**
 * WooCommerce Store API integration for block cart and checkout.
 *
 * @package Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register free shipping notice data on the cart Store API endpoint.
 */
class AFRSM_Store_API {

	/**
	 * Boot Store API hooks.
	 */
	public static function init() {
		add_action( 'woocommerce_blocks_loaded', array( __CLASS__, 'register_endpoint_data' ) );

		if ( did_action( 'woocommerce_blocks_loaded' ) ) {
			self::register_endpoint_data();
		}
	}

	/**
	 * Register cart endpoint extension data.
	 */
	public static function register_endpoint_data() {
		if ( ! function_exists( 'woocommerce_store_api_register_endpoint_data' ) ) {
			return;
		}

		if ( ! afrsfw_fs()->is__premium_only() || ! afrsfw_fs()->can_use_premium_code() ) {
			return;
		}

		woocommerce_store_api_register_endpoint_data(
			array(
				'endpoint'        => self::get_cart_endpoint_identifier(),
				'namespace'       => 'afrsm',
				'schema_callback' => array( __CLASS__, 'schema_callback' ),
				'data_callback'   => array( __CLASS__, 'data_callback' ),
			)
		);
	}

	/**
	 * Cart endpoint identifier.
	 *
	 * @return string
	 */
	private static function get_cart_endpoint_identifier() {
		if ( class_exists( '\Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema' ) ) {
			return \Automattic\WooCommerce\StoreApi\Schemas\V1\CartSchema::IDENTIFIER;
		}

		return 'cart';
	}

	/**
	 * Schema for extension data.
	 *
	 * @return array
	 */
	public static function schema_callback() {
		return array(
			'notices' => array(
				'description' => __( 'Free shipping notices for block cart and checkout.', 'advanced-flat-rate-shipping-for-woocommerce' ),
				'type'        => 'array',
				'context'     => array( 'view', 'edit' ),
				'readonly'    => true,
			),
		);
	}

	/**
	 * Extension data returned with cart responses.
	 *
	 * @return array
	 */
	public static function data_callback() {
		if ( function_exists( 'WC' ) && WC()->cart ) {
			WC()->cart->calculate_totals();
		}

		Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Admin::afrsm_free_shipping_notice_session_handler__premium_only();

		return array(
			'notices' => Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Admin::afrsm_get_free_shipping_notices_for_blocks__premium_only(),
		);
	}
}
