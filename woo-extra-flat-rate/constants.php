<?php

/**
 * Define the plugin constants
 *
 * @since 1.0.0
 */
if ( !defined( 'AFRSM_PRO_PLUGIN_URL' ) ) {
    define( 'AFRSM_PRO_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
}
if ( !defined( 'AFRSM_PLUGIN_DIR' ) ) {
    define( 'AFRSM_PLUGIN_DIR', dirname( __FILE__ ) );
}
if ( !defined( 'AFRSM_PRO_PLUGIN_DIR_PATH' ) ) {
    define( 'AFRSM_PRO_PLUGIN_DIR_PATH', plugin_dir_path( __FILE__ ) );
}
if ( !defined( 'AFRSM_PRO_PLUGIN_NAME' ) ) {
    define( 'AFRSM_PRO_PLUGIN_NAME', 'Flat Rate Shipping' );
}
if ( !defined( 'AFRSM_VERSION_LABEL' ) ) {
    define( 'AFRSM_VERSION_LABEL', 'FREE' );
}
if ( !defined( 'AFRSM_STORE_URL' ) ) {
    define( 'AFRSM_STORE_URL', 'https://www.thedotstore.com/' );
}
if ( !defined( 'AFRSM_DEBUG' ) ) {
    define( 'AFRSM_DEBUG', false );
}
/** Dotstore Marketing Plugin IDs from Freemius */
if ( !defined( 'AFRSM_OTHER_PLUGIN_IDS' ) ) {
    define( 'AFRSM_OTHER_PLUGIN_IDS', array(
        'woo-conditional-product-fees-for-checkout',
        'woo-blocker-lite-prevent-fake-orders-and-blacklist-fraud-customers',
        'hide-shipping-method-for-woocommerce',
        'woo-product-attachment',
        'woo-advanced-product-size-chart',
        'local-pickup-for-woocommerce',
        'woo-conditional-discount-rules-for-checkout',
        'conditional-payments',
        'woo-checkout-for-digital-goods',
        'banner-management-for-woocommerce',
        'min-and-max-quantity-for-woocommerce',
        'woo-ecommerce-tracking-for-google-and-facebook',
        'mass-pagesposts-creator',
        'woo-shipping-display-mode',
        'local-pickup-for-woocommerce'
    ) );
}
/** Dotstore Marketing Plugin IDs from Freemius */
if ( !defined( 'AFRSM_PLUGIN_IDS' ) ) {
    define( 'AFRSM_PLUGIN_IDS', array(
        3390 => array(
            'marketing_title'        => esc_html__( 'Apply Extra Fees with Free Shipping', 'advanced-flat-rate-shipping-for-woocommerce' ),
            'marketing_tooltip'      => esc_html__( 'This will give you premium plugin on discount!', 'advanced-flat-rate-shipping-for-woocommerce' ),
            'marketing_help_url'     => esc_url( 'https://docs.thedotstore.com/article/949-beginners-guide-for-extra-fees' ),
            'marketing_button_text'  => esc_html__( 'Improves Profit Margins Today', 'advanced-flat-rate-shipping-for-woocommerce' ),
            'marketing_plugin_path'  => 'woocommerce-conditional-product-fees-for-checkout-premium/woocommerce-conditional-product-fees-for-checkout.php',
            'marketing_coupon_code'  => 82897,
            'marketing_feature_list' => array(esc_html__( 'Set additional fees for express or special delivery methods.', 'advanced-flat-rate-shipping-for-woocommerce' ), esc_html__( 'Apply fixed or percentage-based charges automatically during checkout.', 'advanced-flat-rate-shipping-for-woocommerce' ), esc_html__( 'Create conditional rules based on cart total, product type, or shipping location.', 'advanced-flat-rate-shipping-for-woocommerce' )),
        ),
    ) );
}
if ( !defined( 'AFRSM_PRO_PERTICULAR_FEE_AMOUNT_NOTICE' ) ) {
    define( 'AFRSM_PRO_PERTICULAR_FEE_AMOUNT_NOTICE', esc_html__( 'You can turn off this button, if you do not need to apply this shipping amount.', 'advanced-flat-rate-shipping-for-woocommerce' ) );
}