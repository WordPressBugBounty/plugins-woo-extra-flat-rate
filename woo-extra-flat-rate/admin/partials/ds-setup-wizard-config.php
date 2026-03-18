<?php
/**
 * Setup wizard config – plugin-specific.
 * Copy this file to another Dotstore plugin and change templates, fields, and strings.
 * Save handler (save_action) is registered separately in each plugin.
 *
 * @return array {
 *   @type array  $templates         List of templates: [ 'value' => '', 'title' => '', 'description' => '' ]
 *   @type array  $template_fields    Map template value => array of field keys to show
 *   @type array  $fields             Field definitions: key => [ 'type' => 'text'|'number'|'select', 'label' => '', ... ]
 *   @type array  $template_defaults  Per template: [ 'field_key' => value, 'rule_title_placeholder' => '' ]
 *   @type array  $strings            Optional step titles, subtitles, button labels (override defaults)
 *   @type string $save_action        AJAX action name for saving the rule (plugin implements handler)
 *   @type string $cookie_name        Cookie name for wizard path (e.g. 'afrsm_wizard_path')
 *   @type string $mark_completed_action Optional AJAX action for marking wizard completed
 * }
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$get_currency = get_woocommerce_currency_symbol();
$get_currency = empty($get_currency) ? '$' : $get_currency;

$countries = array( 'US' => __( 'United States', 'advanced-flat-rate-shipping-for-woocommerce' ) );
if ( function_exists( 'WC' ) && WC() && is_object( WC()->countries ) ) {
	$all = WC()->countries->get_countries();
	foreach ( $all as $code => $name ) {
		if ( 'US' !== $code ) {
			$countries[ $code ] = $name;
		}
	}
}

return array(
	'templates' => array(
		array(
			'value'       => 'flat_us_20',
			'title'       => html_entity_decode(
				sprintf(
					__( 'Flat %s20 for all US orders', 'advanced-flat-rate-shipping-for-woocommerce' ),
					$get_currency
				),
				ENT_QUOTES,
				'UTF-8'
			),
			'description' => __( 'Simple flat-rate shipping across the United States.', 'advanced-flat-rate-shipping-for-woocommerce' ),
		),
		array(
			'value'       => 'free_above_299',
			'title'       => html_entity_decode(
				sprintf(
					__( 'Free shipping above %s299', 'advanced-flat-rate-shipping-for-woocommerce' ),
					$get_currency
				),
				ENT_QUOTES,
				'UTF-8'
			),
			'description' => __( 'Encourage large orders with free shipping thresholds.', 'advanced-flat-rate-shipping-for-woocommerce' ),
		),
		array(
			'value'       => 'dynamic_9',
			'title'       => __( 'Dynamic Shipping charge (9%)', 'advanced-flat-rate-shipping-for-woocommerce' ),
			'description' => __( 'Apply the dynamic shipping charges of 9% to the total order amount.', 'advanced-flat-rate-shipping-for-woocommerce' ),
		),
	),
	'template_fields' => array(
		'flat_us_20'     => array( 'country', 'cost', 'delivery', 'rule_title' ),
		'free_above_299' => array( 'country', 'free_above', 'delivery', 'rule_title' ),
		'dynamic_9'      => array( 'country', 'percent', 'delivery', 'rule_title' ),
	),
	'fields' => array(
		'country' => array(
			'type'    => 'select',
			'label'   => __( 'Select Country:', 'advanced-flat-rate-shipping-for-woocommerce' ),
			'default' => 'US',
			'options' => $countries,
		),
		'cost' => array(
			'type'    => 'number',
			'label'   => html_entity_decode(
				sprintf(
					__( 'Shipping cost (%s)', 'advanced-flat-rate-shipping-for-woocommerce' ),
					$get_currency
				),
				ENT_QUOTES,
				'UTF-8'
			),
			'default' => '20',
			'min'     => 0,
			'step'    => '0.01',
		),
		'free_above' => array(
			'type'    => 'number',
			'label'   => __( 'Free Shipping Threshold', 'advanced-flat-rate-shipping-for-woocommerce' ),
			'default' => '299',
			'min'     => 0,
			'step'    => '0.01',
		),
		'percent' => array(
			'type'    => 'number',
			'label'   => __( 'Percentage of cart subtotal (%)', 'advanced-flat-rate-shipping-for-woocommerce' ),
			'default' => '9',
			'min'     => 0,
			'max'     => 100,
			'step'    => '0.01',
		),
		'delivery' => array(
			'type'        => 'text',
			'label'       => __( 'Estimated Delivery', 'advanced-flat-rate-shipping-for-woocommerce' ),
			'default'     => '2-5 days',
			'placeholder' => __( 'e.g. 2-5 days', 'advanced-flat-rate-shipping-for-woocommerce' ),
		),
		'rule_title' => array(
			'type'        => 'text',
			'label'       => __( 'Rule name', 'advanced-flat-rate-shipping-for-woocommerce' ),
			'default'     => '',
			'placeholder' => '',
		),
	),
	'template_defaults' => array(
		'flat_us_20' => array(
			'cost'                  => '20',
			'rule_title_placeholder' => __( 'e.g. US Flat Rate', 'advanced-flat-rate-shipping-for-woocommerce' ),
		),
		'free_above_299' => array(
			'free_above'             => '299',
			'rule_title_placeholder' => __( 'e.g. Free over $299', 'advanced-flat-rate-shipping-for-woocommerce' ),
		),
		'dynamic_9' => array(
			'percent'                => '9',
			'rule_title_placeholder' => __( 'e.g. 9% Shipping', 'advanced-flat-rate-shipping-for-woocommerce' ),
		),
	),
	'strings' => array(
		'step3_title'    => __( 'Choose a template', 'advanced-flat-rate-shipping-for-woocommerce' ),
		'step3_subtitle' => __( 'Select a pre-configured shipping rules to getting started', 'advanced-flat-rate-shipping-for-woocommerce' ),
		'step4_intro'    => __( 'Settings auto-filled from your templates. Adjust as needed.', 'advanced-flat-rate-shipping-for-woocommerce' ),
		'step5_title'    => __( 'Activate and test shipping', 'advanced-flat-rate-shipping-for-woocommerce' ),
		'step5_subtitle' => __( 'Review your shipping rule before activation', 'advanced-flat-rate-shipping-for-woocommerce' ),
		'step5_button'   => __( 'Activate Shipping Rule', 'advanced-flat-rate-shipping-for-woocommerce' ),
		'step6_message'  => __( 'Your first shipping rule has been successfully published!', 'advanced-flat-rate-shipping-for-woocommerce' ) . ' 🎉',
		'cta_create_another'      => __( 'Create Another Rule', 'advanced-flat-rate-shipping-for-woocommerce' ),
		'cta_create_another_sub'  => __( 'Build additional shipping rules for different regions or conditions', 'advanced-flat-rate-shipping-for-woocommerce' ),
		'cta_go_to_list'          => __( 'Go to Shipping Method List', 'advanced-flat-rate-shipping-for-woocommerce' ),
		'cta_go_to_list_sub'      => __( 'Check the created shipping method and start with the other shipping methods.', 'advanced-flat-rate-shipping-for-woocommerce' ),
	),
	'save_action'            => 'afrsm_wizard_create_rule',
	'cookie_name'            => 'afrsm_wizard_path',
	'mark_completed_action'  => 'afrsm_wizard_mark_completed',
);
