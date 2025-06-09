<?php

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
require_once plugin_dir_path( __FILE__ ) . 'header/plugin-header.php';
$afrsm_force_customer_to_select_sm = get_option( 'afrsm_force_customer_to_select_sm' );
?>
<div class="afrsm-section-left">
    <div class="afrsm-main-table res-cl element-shadow">
        <h2><?php 
esc_html_e( 'General Settings', 'advanced-flat-rate-shipping-for-woocommerce' );
?></h2>
        <table class="table-mastersettings table-outer" cellpadding="0" cellspacing="0">
            <tbody>
                <?php 
?>
                        <tr class="mastersettings-raw">
                            <td class="table-whattodo">
                                <?php 
esc_html_e( 'Show type of shipping method', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                <span class="afrsm-pro-label"></span>
                            </td>
                            <td>
                                <select id="what_to_do_method">
                                    <option value=""><?php 
esc_html_e( 'Allow customer to choose', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
                                    <option value="in_pro"><?php 
esc_html_e( 'Apply Highest 🔒', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
                                    <option value="in_pro"><?php 
esc_html_e( 'Apply Lowest 🔒', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
                                    <option value="in_pro"><?php 
esc_html_e( 'Force all shipping methods 🔒', 'advanced-flat-rate-shipping-for-woocommerce' );
?></option>
                                </select>
                            </td>
                        </tr>
                        <?php 
?>
                <tr valign="top" id="display_mode">
                    <td class="table-whattodo"><?php 
esc_html_e( 'Shipping Display Mode', 'advanced-flat-rate-shipping-for-woocommerce' );
?></td>
                    <td>
                        <?php 
$html = sprintf( 
    // translators: 1: Bold "Note:" label. 2: The message about the new plugin.
    '<p class="note"><b style="color: red;">%1$s</b>%2$s</p>',
    esc_html__( 'Note: ', 'advanced-flat-rate-shipping-for-woocommerce' ),
    sprintf( 
        // translators: 1: Link to the "Shipping Method Display Style" plugin.
        esc_html__( 'This feature is now part of our dedicated plugin %1$s for enhanced flexibility and control. Install it to continue using the option.', 'advanced-flat-rate-shipping-for-woocommerce' ),
        '<a href="https://wordpress.org/plugins/woo-shipping-display-mode/" target="_blank">Shipping Method Display Style</a>'
     )
 );
echo wp_kses_post( $html );
?>
                    </td>
                </tr>
                <?php 
?>
                        <tr valign="top" id="afrsm_hide_other_shipping">
                            <td class="table-whattodo">
                                <?php 
esc_html_e( 'Hide other shipping method when free shipping is available', 'advanced-flat-rate-shipping-for-woocommerce' );
?>
                                <span class="afrsm-pro-label"></span>
                            </td>
                            <td>
                                <input type="checkbox" class="" value="" disabled >
                            </td>
                        </tr>
                        <?php 
?>
                <tr valign="top" id="afrsm_force_customer_to_select_sm">
                    <td class="table-whattodo"><?php 
esc_html_e( 'Want to force customers to select a shipping method?', 'advanced-flat-rate-shipping-for-woocommerce' );
?><span class="afrsm-new-feture-master"></td>

                    <td>
                        <input type="checkbox" name="afrsm_force_customer_to_select_sm"
                        id="afrsm_force_customer_to_select_sm"
                        class="afrsm_force_customer_to_select_sm"
                        value="on" <?php 
checked( $afrsm_force_customer_to_select_sm, 'on' );
?>>
                    </td>
                </tr>
                <tr valign="top" id="afrsm_count_per_page">
                    <td class="table-whattodo"><?php 
esc_html_e( 'Number of shipping methods per page', 'advanced-flat-rate-shipping-for-woocommerce' );
?><span class="afrsm-new-feture-master"></td>
                    <td>
                        <?php 
$html = sprintf( '<p class="note"><b style="color: red;">%s</b>%s</p>', esc_html__( 'Note: ', 'advanced-flat-rate-shipping-for-woocommerce' ), esc_html__( 'This setting has been moved to "Screen Options" on listing page.', 'advanced-flat-rate-shipping-for-woocommerce' ) );
echo wp_kses_post( $html );
?>
                    </td>
                </tr>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="2">
                        <span class="button button-primary button-large" id="save_master_settings" name="save_master_settings"><?php 
esc_html_e( 'Save Master Settings', 'advanced-flat-rate-shipping-for-woocommerce' );
?></span>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<?php 
require_once plugin_dir_path( __FILE__ ) . 'header/plugin-footer.php';