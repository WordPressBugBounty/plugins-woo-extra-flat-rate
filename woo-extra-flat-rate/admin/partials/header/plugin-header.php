<?php

// If this file is called directly, abort.
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
global $afrsfw_fs;
$plugin_slug = '';
$plugin_slug = 'basic_flat_rate';
$afrsm_admin_object = new Advanced_Flat_Rate_Shipping_For_WooCommerce_Pro_Admin('', '');
$current_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_FULL_SPECIAL_CHARS );
$getting_started = ( isset( $current_page ) && 'afrsm-pro-get-started' === $current_page ? 'active' : '' );
$onboarding_completed = get_option( 'afrsm_setup_wizard_notice_closed' );
?>
<div class="wrap">
    <div id="dotsstoremain" class="afrsm-section">
        <div class="all-pad">
            <?php 
$afrsm_admin_object->afrsm_get_promotional_bar( $plugin_slug );
?>
            <hr class="wp-header-end" />
            <header class="dots-header">
                <div class="dots-plugin-details">
                    <div class="dots-header-left">
                        <div class="dots-logo-main">
                            <img src="<?php 
echo esc_url( AFRSM_PRO_PLUGIN_URL . 'admin/images/advance-flat-rate.png' );
?>">
                        </div>
                        <div class="plugin-name">
                            <div class="title"><?php 
echo esc_html( AFRSM_PRO_PLUGIN_NAME );
?></div>
                        </div>
                        <span class="version-label <?php 
echo esc_attr( $plugin_slug );
?>"><?php 
echo esc_html( AFRSM_VERSION_LABEL );
?></span>
                        <span class="version-number"><?php 
echo esc_html( AFRSM_PRO_PLUGIN_VERSION );
?></span>
                    </div>
                    <div class="dots-header-right">
                        <div class="button-dots">
                            <a target="_blank" href="<?php 
echo esc_url( 'http://www.thedotstore.com/support/?utm_source=plugin_header_menu_link&utm_medium=header_menu&utm_campaign=plugin&utm_id=menu_link_flat_rate_shipping' );
?>">
                                <?php 
esc_html_e( 'Support', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                            </a>
                        </div>
                        <div class="button-dots">
                            <a target="_blank" href="<?php 
echo esc_url( 'https://www.thedotstore.com/feature-requests/?utm_source=plugin_header_menu_link&utm_medium=header_menu&utm_campaign=plugin&utm_id=menu_link_flat_rate_shipping' );
?>">
                                <?php 
esc_html_e( 'Suggest', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                            </a>
                        </div>
                        <div class="button-dots <?php 
echo ( $afrsfw_fs->is__premium_only() && $afrsfw_fs->can_use_premium_code() ? '' : 'last-link-button' );
?>">
                            <a target="_blank" href="<?php 
echo esc_url( 'https://docs.thedotstore.com/collection/81-flat-rate-shipping-plugin-for-woocommerce' );
?>">
                                <?php 
esc_html_e( 'Help', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                            </a>
                        </div>
                        <?php 
?>
                            <div class="button-dots">
                                <a target="_blank" class="dots-upgrade-btn" href="javascript:void(0);">
                                    <?php 
esc_html_e( 'Upgrade Now', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                </a>
                            </div>
                        <?php 
?>
                    </div>
                </div>
                <div class="dots-bottom-menu-main">
                    <?php 
$afrsm_admin_object->afrsm_pro_menus( $current_page );
?>
                    <div class="dots-getting-started">
                        <nav>
                            <ul>
                                <li>
                                    <a href="<?php 
echo esc_url( add_query_arg( array(
    'page' => 'afrsm-pro-get-started',
), admin_url( 'admin.php' ) ) );
?>" class="<?php 
echo esc_attr( $getting_started );
?>"><?php 
esc_html_e( 'Getting Started', 'advanced-flat-rate-shipping-for-woocommerce' );
?></a>
                                </li>
                            </ul>
                        </nav>
                    </div>
                </div>
            </header>

            <?php 
$convert_to_pro = filter_var( get_option( 'convert_to_pro', false ), FILTER_VALIDATE_BOOLEAN );
if ( !afrsfw_fs()->is__premium_only() && true === $convert_to_pro ) {
    $convert_to_pro_doc_url = 'https://docs.thedotstore.com/article/62-how-to-installing-and-activating-an-thedotstore-plugin';
    $convert_to_pro_dismiss_url = wp_nonce_url( add_query_arg( 'afrsm-dismiss-convert-to-pro', '1' ), 'afrsm_convert_to_pro_dismiss', '_afrsm_convert_to_pro_nonce' );
    ?>
                <div class="notice notice-warning is-dismissible afrsm-convert-to-pro-notice">
                    <a class="notice-dismiss" href="<?php 
    echo esc_url( $convert_to_pro_dismiss_url );
    ?>"></a>
                    <p><strong><?php 
    esc_html_e( 'Thank you for purchasing the plugin!', 'advanced-flat-rate-shipping-for-woocommerce' );
    ?></strong></p>
                    <p><?php 
    esc_html_e( 'You are currently using the free version of Flat Rate Shipping.', 'advanced-flat-rate-shipping-for-woocommerce' );
    ?></p>
                    <p>
                        <?php 
    echo wp_kses( __( 'To use pro features, please <strong>remove this free plugin</strong> and <strong>install and activate the premium version</strong>. Don\'t worry — this will not remove any of your shipping rules or settings. Once you activate the premium version, all your settings will be automatically restored.', 'advanced-flat-rate-shipping-for-woocommerce' ), array(
        'strong' => array(),
    ) );
    ?>
                    </p>
                    <p>
                        <a href="<?php 
    echo esc_url( $convert_to_pro_doc_url );
    ?>" class="button button-primary" target="_blank" rel="noopener noreferrer">
                            <?php 
    esc_html_e( 'View step-by-step guide', 'advanced-flat-rate-shipping-for-woocommerce' );
    ?>
                        </a>
                    </p>
                </div>
                <?php 
}
?>

            <?php 
if ( empty( $onboarding_completed ) || 'afrsm-pro-get-started' === $current_page ) {
    ?>
                <div class="demo-dpd-popup dpb-popup dots-onboarding-notice">
                    <div class="dpb-popup-meta">
                        <span><?php 
    esc_html_e( 'Go to Onboarding', 'advanced-flat-rate-shipping-for-woocommerce' );
    ?></span>
                        <p><?php 
    esc_html_e( 'Start your onboarding to get started with Flat Rate Shipping plugin!', 'advanced-flat-rate-shipping-for-woocommerce' );
    ?></p>
                    </div>
                    <a href="<?php 
    echo esc_url( add_query_arg( array(
        'page'         => 'afrsm-pro-list',
        'setup_wizard' => '1',
    ), admin_url( 'admin.php' ) ) );
    ?>" class="onboarding-button"><?php 
    esc_html_e( 'Onboarding', 'advanced-flat-rate-shipping-for-woocommerce' );
    ?></a>
                    <?php 
    if ( 'afrsm-pro-get-started' !== $current_page ) {
        ?>
                        <a href="javascript:void(0);" class="onboarding-close-btn"><svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 10 10"><path id="Icon_material-close" data-name="Icon material-close" d="M17.5,8.507,16.493,7.5,12.5,11.493,8.507,7.5,7.5,8.507,11.493,12.5,7.5,16.493,8.507,17.5,12.5,13.507,16.493,17.5,17.5,16.493,13.507,12.5Z" transform="translate(-7.5 -7.5)" fill="#acacac"></path></svg></a>
                        <?php 
    }
    ?>
                </div>
                <?php 
}
?>
            
            <!-- Upgrade to pro popup -->
            <?php 
if ( !(afrsfw_fs()->is__premium_only() && afrsfw_fs()->can_use_premium_code()) ) {
    require_once AFRSM_PRO_PLUGIN_DIR_PATH . 'admin/partials/afrsm-upgrade-popup.php';
}
?>
            <div class="dots-settings-inner-main">
                <div class="dots-settings-left-side">
                    <?php 
$afrsm_admin_object->afrsm_submenus( $current_page );