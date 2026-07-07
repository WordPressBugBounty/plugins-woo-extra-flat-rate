<?php

/**
 * Plugin Name:         Flat Rate Shipping Method for WooCommerce
 * Plugin URI:          https://www.thedotstore.com/advanced-flat-rate-shipping-method-for-woocommerce
 * Description:         Using Advanced Flat Rate Shipping plugin, you can create multiple flat rate shipping methods. Using this plugin you can configure different parameters on which a particular Flat Rate Shipping method becomes available to the customers at the time of checkout.
 * Version:             4.5.2
 * Author:              theDotstore
 * Author URI:          https://www.thedotstore.com/
 * License:             GPL-3.0+
 * License URI:         http://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:         advanced-flat-rate-shipping-for-woocommerce
 * Domain Path:         /languages
 * Requires Plugins:    woocommerce
 *
 *
 * WC requires at least: 3.0
 * WP tested up to:     7.0
 * WC tested up to:     10.9.1
 * Requires PHP:        7.2
 * Requires at least:   5.0
 */
// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
if ( function_exists( 'afrsfw_fs' ) ) {
    afrsfw_fs()->set_basename( false, __FILE__ );
} else {
    if ( !function_exists( 'afrsfw_fs' ) ) {
        // Create a helper function for easy SDK access.
        function afrsfw_fs() {
            global $afrsfw_fs;
            if ( !isset( $afrsfw_fs ) ) {
                // Activate multisite network integration.
                if ( !defined( 'WP_FS__PRODUCT_3379_MULTISITE' ) ) {
                    define( 'WP_FS__PRODUCT_3379_MULTISITE', true );
                }
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                // @phpstan-ignore-next-line
                $afrsfw_fs = fs_dynamic_init( array(
                    'id'               => '3379',
                    'slug'             => 'advanced-flat-rate-shipping-for-woocommerce',
                    'type'             => 'plugin',
                    'public_key'       => 'pk_8db0d3a414717fb20558c5268291b',
                    'is_premium'       => false,
                    'premium_suffix'   => 'Premium',
                    'has_addons'       => true,
                    'has_paid_plans'   => true,
                    'trial'            => array(
                        'days'               => 14,
                        'is_require_payment' => true,
                    ),
                    'has_affiliation'  => 'selected',
                    'menu'             => array(
                        'slug'       => 'afrsm-pro-list',
                        'first-path' => 'admin.php?page=afrsm-pro-list',
                        'contact'    => false,
                        'support'    => false,
                        'network'    => true,
                    ),
                    'is_live'          => true,
                    'is_org_compliant' => true,
                ) );
            }
            return $afrsfw_fs;
        }

        // Init Freemius.
        afrsfw_fs();
        // Signal that SDK was initiated.
        do_action( 'afrsfw_fs_loaded' );
        afrsfw_fs()->get_upgrade_url();
        function afrsfw_fs_settings_url() {
            return admin_url( 'admin.php?page=afrsm-pro-list' );
        }

        afrsfw_fs()->add_filter( 'connect_url', 'afrsfw_fs_settings_url' );
        afrsfw_fs()->add_filter( 'after_skip_url', 'afrsm_after_connect_url_by_wizard' );
        afrsfw_fs()->add_filter(
            'after_connect_url',
            'afrsm_after_connect_url_by_wizard',
            10,
            1
        );
        afrsfw_fs()->add_filter( 'after_pending_connect_url', 'afrsm_after_connect_url_by_wizard' );
    }
}
if ( !defined( 'AFRSM_PRO_PLUGIN_VERSION' ) ) {
    define( 'AFRSM_PRO_PLUGIN_VERSION', 'v4.5.2' );
}
if ( !defined( 'AFRSM_PRO_PLUGIN_BASENAME' ) ) {
    define( 'AFRSM_PRO_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
}
// Rest of constants are defined in constants.php
/**
 * Hide freemius account tab
 *
 * @since    4.2.0
 */
if ( !function_exists( 'afrsm_hide_account_tab' ) ) {
    function afrsm_hide_account_tab() {
        return true;
    }

    afrsfw_fs()->add_filter( 'hide_account_tabs', 'afrsm_hide_account_tab' );
}
/**
 * Include plugin header on freemius account page
 *
 * @since    4.2.0
 */
if ( !function_exists( 'afrsm_load_plugin_header_after_account' ) ) {
    function afrsm_load_plugin_header_after_account() {
        require_once plugin_dir_path( __FILE__ ) . 'admin/partials/header/plugin-header.php';
    }

    afrsfw_fs()->add_action( 'after_account_details', 'afrsm_load_plugin_header_after_account' );
}
/**
 * Hide billing and payments details from freemius account page
 *
 * @since    4.2.0
 */
if ( !function_exists( 'afrsm_hide_billing_and_payments_info' ) ) {
    function afrsm_hide_billing_and_payments_info() {
        return true;
    }

    afrsfw_fs()->add_action( 'hide_billing_and_payments_info', 'afrsm_hide_billing_and_payments_info' );
}
/**
 * Hide powerd by popup from freemius account page
 *
 * @since    3.9.3
 */
if ( !function_exists( 'afrsm_hide_freemius_powered_by' ) ) {
    function afrsm_hide_freemius_powered_by() {
        return true;
    }

    afrsfw_fs()->add_action( 'hide_freemius_powered_by', 'afrsm_hide_freemius_powered_by' );
}
/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-advanced-flat-rate-shipping-for-woocommerce-activator.php
 */
if ( function_exists( 'activate_advanced_flat_rate_shipping_for_woocommerce_pro' ) ) {
    /** If the free version actitivated then first deactivate it */
    deactivate_plugins( '/woo-extra-flat-rate/advanced-flat-rate-shipping-for-woocommerce.php', true );
} else {
    function activate_advanced_flat_rate_shipping_for_woocommerce_pro() {
        set_transient( 'afrsm-admin-notice', true );
        require_once plugin_dir_path( __FILE__ ) . 'includes/class-advanced-flat-rate-shipping-for-woocommerce-activator.php';
        Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Activator::activate();
    }

    /**
     * The code that runs during plugin deactivation.
     * This action is documented in includes/class-advanced-flat-rate-shipping-for-woocommerce-deactivator.php
     */
    if ( !function_exists( 'deactivate_advanced_flat_rate_shipping_for_woocommerce_pro' ) ) {
        function deactivate_advanced_flat_rate_shipping_for_woocommerce_pro() {
            require_once plugin_dir_path( __FILE__ ) . 'includes/class-advanced-flat-rate-shipping-for-woocommerce-deactivator.php';
            Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Deactivator::deactivate();
        }

    }
    register_activation_hook( __FILE__, 'activate_advanced_flat_rate_shipping_for_woocommerce_pro' );
    register_deactivation_hook( __FILE__, 'deactivate_advanced_flat_rate_shipping_for_woocommerce_pro' );
    add_action( 'admin_init', 'afrsm_pro_deactivate_plugin' );
    if ( !function_exists( 'afrsm_pro_deactivate_plugin' ) ) {
        function afrsm_pro_deactivate_plugin() {
            if ( is_multisite() ) {
                $active_plugins = get_option( 'active_plugins', array() );
                if ( is_multisite() ) {
                    $network_active_plugins = get_site_option( 'active_sitewide_plugins', array() );
                    $active_plugins = array_merge( $active_plugins, array_keys( $network_active_plugins ) );
                    $active_plugins = array_unique( $active_plugins );
                }
                if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', $active_plugins ), true ) ) {
                    deactivate_plugins( '/woo-extra-flat-rate/advanced-flat-rate-shipping-for-woocommerce.php', true );
                    //WordPress ORG name
                    deactivate_plugins( 'advanced-flat-rate-shipping-for-woocommerce/advanced-flat-rate-shipping-for-woocommerce.php', true );
                    // Freemius name
                }
            } else {
                if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ), true ) ) {
                    deactivate_plugins( '/woo-extra-flat-rate/advanced-flat-rate-shipping-for-woocommerce.php', true );
                    //WordPress ORG name
                    deactivate_plugins( 'advanced-flat-rate-shipping-for-woocommerce/advanced-flat-rate-shipping-for-woocommerce.php', true );
                    // Freemius name
                }
            }
        }

    }
    /**
     * The core plugin class that is used to define internationalization,
     * admin-specific hooks, and public-facing site hooks.
     */
    require plugin_dir_path( __FILE__ ) . 'includes/class-advanced-flat-rate-shipping-for-woocommerce.php';
    /**
     * Begins execution of the plugin.
     *
     * Since everything within the plugin is registered via hooks,
     * then kicking off the plugin from this point in the file does
     * not affect the page life cycle.
     *
     * @since    1.0.0
     */
    if ( !function_exists( 'run_advanced_flat_rate_shipping_for_woocommerce_pro' ) ) {
        function run_advanced_flat_rate_shipping_for_woocommerce_pro() {
            $plugin = new Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro();
            $plugin->run();
        }

    }
    run_advanced_flat_rate_shipping_for_woocommerce_pro();
    if ( !function_exists( 'advanced_flat_rate_shipping_for_woocommerce_pro_plugin_path' ) ) {
        function advanced_flat_rate_shipping_for_woocommerce_pro_plugin_path() {
            return untrailingslashit( plugin_dir_path( __FILE__ ) );
        }

    }
    /**
     * Helper function for logging
     *
     * For valid levels, see `WC_Log_Levels` class
     *
     * Description of levels:
     *     'emergency': System is unusable.
     *     'alert': Action must be taken immediately.
     *     'critical': Critical conditions.
     *     'error': Error conditions.
     *     'warning': Warning conditions.
     *     'notice': Normal but significant condition.
     *     'info': Informational messages.
     *     'debug': Debug-level messages.
     *
     * @param string $message
     *
     * @return mixed log
     */
    if ( !function_exists( 'advanced_flat_rate_shipping_for_woocommerce_pro_plugin_path' ) ) {
        function afrsm_log(  $message, $level = 'debug'  ) {
            $chk_enable_logging = get_option( 'chk_enable_logging' );
            if ( 'off' === $chk_enable_logging ) {
                return;
            }
            $logger = wc_get_logger();
            $context = array(
                'source' => 'advanced-flat-rate-shipping-for-woocommerce',
            );
            return $logger->log( $level, $message, $context );
        }

    }
    //HPOS Compatibility declare
    add_action( 'before_woocommerce_init', 'afrsm_hpos_compatibility_declaration' );
    if ( !function_exists( 'afrsm_hpos_compatibility_declaration' ) ) {
        function afrsm_hpos_compatibility_declaration() {
            if ( class_exists( \Automattic\WooCommerce\Utilities\FeaturesUtil::class ) ) {
                \Automattic\WooCommerce\Utilities\FeaturesUtil::declare_compatibility( 'custom_order_tables', __FILE__, true );
            }
        }

    }
    /**
     * Wrap Freemius connect template in setup wizard so the full wizard (steps 1–6) is shown
     * with the license activation in step 6. Uses the templates/connect.php filter which
     * receives the full connect HTML and returns our wrapper.
     *
     * @since    4.2.0
     */
    if ( !function_exists( 'afrsm_wrap_connect_template_with_wizard' ) ) {
        function afrsm_wrap_connect_template_with_wizard(  $connect_html  ) {
            ob_start();
            $connect_content = $connect_html;
            $initial_step = 1;
            $license_activated = function_exists( 'afrsfw_fs' ) && afrsfw_fs()->can_use_premium_code();
            require_once plugin_dir_path( __FILE__ ) . 'admin/partials/afrsm-plugin-setup-wizard.php';
            return ob_get_clean();
        }

        afrsfw_fs()->add_filter( 'templates/connect.php', 'afrsm_wrap_connect_template_with_wizard' );
    }
    if ( !function_exists( 'afrsm_after_connect_url_by_wizard' ) ) {
        function afrsm_after_connect_url_by_wizard(  $url  ) {
            $path = get_transient( 'afrsm_wizard_path' );
            $path = ( in_array( $path, array('use_template', 'from_scratch'), true ) ? $path : '' );
            $list_url = admin_url( 'admin.php?page=afrsm-pro-list' );
            if ( 'from_scratch' === $path ) {
                set_transient( 'afrsm_wizard_just_activated_from_scratch', true, 60 );
                return add_query_arg( array(
                    'wizard_path' => $path,
                ), $list_url );
            }
            if ( 'use_template' === $path ) {
                return add_query_arg( array(
                    'wizard_step' => 3,
                    'wizard_path' => $path,
                ), $list_url );
            }
            return ( $url ? $url : $list_url );
        }

    }
    /**
     * Plugin check Plugins filter for plugin specific checks.
     */
    // Filters the directories to ignore.
    function afrsm_get_directories_to_ignore(  $default_ignore_directories  ) {
        $default_ignore_directories[] = 'freemius';
        $default_ignore_directories[] = 'dotstore-analytics';
        return $default_ignore_directories;
    }

    add_filter( 'wp_plugin_check_ignore_directories', 'afrsm_get_directories_to_ignore' );
    // Filters the directories to ignore.
    function afrsm_get_files_to_ignore(  $default_ignore_directories  ) {
        $default_ignore_directories[] = '.DS_Store';
        return $default_ignore_directories;
    }

    add_filter( 'wp_plugin_check_ignore_files', 'afrsm_get_files_to_ignore' );
    // Filters the available plugin check classes.
    function afrsm_wp_plugin_check_checks(  $checks  ) {
        if ( is_array( $checks ) && isset( $checks['i18n_usage'] ) ) {
            unset($checks['i18n_usage']);
        }
        if ( is_array( $checks ) && isset( $checks['trademarks'] ) ) {
            unset($checks['trademarks']);
        }
        if ( is_array( $checks ) && isset( $checks['image_functions'] ) ) {
            unset($checks['image_functions']);
        }
        return $checks;
    }

    add_filter( 'wp_plugin_check_checks', 'afrsm_wp_plugin_check_checks' );
}
/**
 * Returns the main instance of AFRSMPA.
 *
 * @return Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Admin
 *
 * @since  3.8
 *
 * @author jb
 */
if ( !function_exists( 'AFRSMPA' ) ) {
    function AFRSMPA() {
        // phpcs:ignore WordPress.NamingConventions.ValidFunctionName.FunctionNameInvalid
        return new Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Admin('', '');
    }

    $GLOBALS['afrsfwpa'] = AFRSMPA();
}
// Override the connect message on update
if ( !function_exists( 'afrsm_connect_message_on_update' ) ) {
    afrsfw_fs()->add_filter(
        'connect_message_on_update',
        'afrsm_connect_message_on_update',
        10,
        2
    );
    function afrsm_connect_message_on_update(  $message, $plugin_name  ) {
        return __( 'Get improved features and faster fixes by sharing non-sensitive data via usage-tracking. This will help us make the plugin more compatible with your site. No personal data is tracked or stored.', 'advanced-flat-rate-shipping-for-woocommerce' );
    }

}