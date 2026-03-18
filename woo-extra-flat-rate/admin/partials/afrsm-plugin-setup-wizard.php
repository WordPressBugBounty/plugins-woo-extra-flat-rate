<?php
/**
 * Dotstore setup wizard – reusable 6-step wizard.
 * Steps 3 (Choose template) and 4 (Configure settings) are driven by $wizard_config.
 * Copy this file + ds-setup-wizard-config.php + wizard JS/CSS to another plugin; implement your own config and save handler.
 *
 * Required when including: $connect_content, $initial_step, $license_activated, $wizard_path, $list_url, $add_url, $wizard_config
 * $wizard_config: see ds-setup-wizard-config.php for structure (templates, template_fields, fields, template_defaults, strings, save_action, cookie_name, mark_completed_action).
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! isset( $wizard_config ) && file_exists( __DIR__ . '/ds-setup-wizard-config.php' ) ) {
	$wizard_config = require __DIR__ . '/ds-setup-wizard-config.php';
}
$wizard_config   = isset( $wizard_config ) && is_array( $wizard_config ) ? $wizard_config : array();
$connect_content = isset( $connect_content ) ? $connect_content : '';
$initial_step    = isset( $initial_step ) ? max( 1, min( 6, (int) $initial_step ) ) : 1;
$license_activated = isset( $license_activated ) ? (bool) $license_activated : false;
$wizard_path     = isset( $wizard_path ) && in_array( $wizard_path, array( 'use_template', 'from_scratch' ), true ) ? $wizard_path : '';
$list_url        = isset( $list_url ) ? $list_url : admin_url( 'admin.php' );
$add_url         = isset( $add_url ) ? $add_url : admin_url( 'admin.php' );
$templates       = isset( $wizard_config['templates'] ) ? $wizard_config['templates'] : array();
$template_fields = isset( $wizard_config['template_fields'] ) ? $wizard_config['template_fields'] : array();
$fields          = isset( $wizard_config['fields'] ) ? $wizard_config['fields'] : array();
$strings         = isset( $wizard_config['strings'] ) ? $wizard_config['strings'] : array();
$step3_title     = isset( $strings['step3_title'] ) ? $strings['step3_title'] : __( 'Choose a template', 'advanced-flat-rate-shipping-for-woocommerce' );
$step3_subtitle  = isset( $strings['step3_subtitle'] ) ? $strings['step3_subtitle'] : __( 'Select a pre-configured option to get started', 'advanced-flat-rate-shipping-for-woocommerce' );
$step4_intro     = isset( $strings['step4_intro'] ) ? $strings['step4_intro'] : __( 'Settings auto-filled from your template. Adjust as needed.', 'advanced-flat-rate-shipping-for-woocommerce' );
$step5_title     = isset( $strings['step5_title'] ) ? $strings['step5_title'] : __( 'Activate and test', 'advanced-flat-rate-shipping-for-woocommerce' );
$step5_subtitle  = isset( $strings['step5_subtitle'] ) ? $strings['step5_subtitle'] : __( 'Review before activation', 'advanced-flat-rate-shipping-for-woocommerce' );
$step5_button    = isset( $strings['step5_button'] ) ? $strings['step5_button'] : __( 'Activate Rule', 'advanced-flat-rate-shipping-for-woocommerce' );
$step6_message   = isset( $strings['step6_message'] ) ? $strings['step6_message'] : __( 'Your first rule has been successfully published!', 'advanced-flat-rate-shipping-for-woocommerce' ) . ' 🎉';
$cta_create      = isset( $strings['cta_create_another'] ) ? $strings['cta_create_another'] : __( 'Create Another Rule', 'advanced-flat-rate-shipping-for-woocommerce' );
$cta_create_sub  = isset( $strings['cta_create_another_sub'] ) ? $strings['cta_create_another_sub'] : '';
$cta_list        = isset( $strings['cta_go_to_list'] ) ? $strings['cta_go_to_list'] : __( 'Go to List', 'advanced-flat-rate-shipping-for-woocommerce' );
$cta_list_sub    = isset( $strings['cta_go_to_list_sub'] ) ? $strings['cta_go_to_list_sub'] : '';
$activate_text	 = afrsfw_fs()->is_premium() ? __( 'Activate License', 'advanced-flat-rate-shipping-for-woocommerce' ) : __( 'Activate Plugin', 'advanced-flat-rate-shipping-for-woocommerce' );

$get_currency = get_woocommerce_currency_symbol();
$get_currency_symbol = empty($get_currency) ? '$' : $get_currency;
?>
<div class="ds-plugin-setup-wizard-main" data-initial-step="<?php echo esc_attr( $initial_step ); ?>" data-license-activated="<?php echo empty( $connect_content ) ? '1' : '0'; ?>" data-wizard-path="<?php echo esc_attr( $wizard_path ); ?>">
	<!-- Progress: Readymade path -->
	<div class="ds-wizard-progress-wrap-container ds-wizard-progress-readymade ds-wizard-progress-hidden">
		<div class="ds-wizard-progress-wrap">
			<div class="ds-wizard-progress-bar">
				<div class="ds-wizard-progress-step-wrap" data-step="2"><span class="ds-wizard-progress-circle"><span class="ds-wizard-progress-num">1</span></span><span class="ds-wizard-progress-label"><?php echo esc_html( $activate_text ); ?></span></div>
				<span class="ds-wizard-progress-arrow" aria-hidden="true">→</span>
				<div class="ds-wizard-progress-step-wrap" data-step="3"><span class="ds-wizard-progress-circle"><span class="ds-wizard-progress-num">2</span></span><span class="ds-wizard-progress-label"><?php esc_html_e( 'Choose Template', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></span></div>
				<span class="ds-wizard-progress-arrow" aria-hidden="true">→</span>
				<div class="ds-wizard-progress-step-wrap" data-step="4"><span class="ds-wizard-progress-circle"><span class="ds-wizard-progress-num">3</span></span><span class="ds-wizard-progress-label"><?php esc_html_e( 'Configure Settings', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></span></div>
				<span class="ds-wizard-progress-arrow" aria-hidden="true">→</span>
				<div class="ds-wizard-progress-step-wrap" data-step="5"><span class="ds-wizard-progress-circle"><span class="ds-wizard-progress-num">4</span></span><span class="ds-wizard-progress-label"><?php esc_html_e( 'Preview & Activate', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></span></div>
			</div>
		</div>
	</div>
	<!-- Progress: From Scratch path -->
	<div class="ds-wizard-progress-wrap-container ds-wizard-progress-from-scratch ds-wizard-progress-hidden">
		<div class="ds-wizard-progress-wrap">
			<div class="ds-wizard-progress-bar">
				<div class="ds-wizard-progress-step-wrap" data-step="1"><span class="ds-wizard-progress-circle"><span class="ds-wizard-progress-num">1</span></span><span class="ds-wizard-progress-label"><?php esc_html_e( 'Choose Path', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></span></div>
				<span class="ds-wizard-progress-arrow" aria-hidden="true">→</span>
				<div class="ds-wizard-progress-step-wrap" data-step="2"><span class="ds-wizard-progress-circle"><span class="ds-wizard-progress-num">2</span></span><span class="ds-wizard-progress-label"><?php echo esc_html( $activate_text ); ?></span></div>
			</div>
		</div>
	</div>
	<div class="wizard-tab-content">
		<!-- Step 1: Path selection -->
		<div class="tab-panel" id="step1">
			<div class="ds-wizard-wrap">
				<div class="ds-wizard-content ds-wizard-step1-content">
					<div class="ds-wizard-step1-icon">
						<img class="ds-wizard-logo" src="<?php echo esc_url( plugin_dir_url( dirname( __FILE__, 2 ) ) . 'admin/images/plugin-icon.svg' ); ?>"/>
					</div>
					<h2 class="cta-title ds-wizard-step1-title"><?php esc_html_e( 'Create your first', 'advanced-flat-rate-shipping-for-woocommerce' ); ?> <span class="ds-wizard-highlight"><?php esc_html_e( 'shipping rule', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></span> <?php esc_html_e( 'fast', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></h2>
					<p class="ds-wizard-step1-subtitle"><?php esc_html_e( 'Set up intelligent shipping rules in seconds and start optimizing your revenue', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></p>
					<div class="ds-wizard-creation-options">
						<label class="ds-wizard-option-card ds-wizard-option-readymade">
							<input type="radio" name="ds_wizard_creation_method" value="use_template" />
							<span class="ds-wizard-option-icon ds-wizard-option-icon-lightning"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="currentColor"><path d="M13 2L3 14h9l-1 8 10-12h-9l1-8z"/></svg></span>
							<span class="ds-wizard-option-title"><?php esc_html_e( 'Readymade template', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></span>
							<span class="ds-wizard-option-desc"><?php esc_html_e( 'Use pre-configured shipping rules for quick setup.', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></span>
							<ul class="ds-wizard-option-bullets">
								<li><?php esc_html_e( '3 proven templates', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></li>
								<li><?php esc_html_e( 'Industry best practices', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></li>
								<li><?php esc_html_e( 'Fully customizable', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></li>
							</ul>
						</label>
						<label class="ds-wizard-option-card ds-wizard-option-scratch">
							<input type="radio" name="ds_wizard_creation_method" value="from_scratch" />
							<span class="ds-wizard-option-icon ds-wizard-option-icon-wrench"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></span>
							<span class="ds-wizard-option-title"><?php esc_html_e( 'Create from Scratch', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></span>
							<span class="ds-wizard-option-desc"><?php esc_html_e( 'Create a shipping rule from scratch for complete control.', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></span>
							<ul class="ds-wizard-option-bullets">
								<li><?php esc_html_e( 'Complete flexibility', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></li>
								<li><?php esc_html_e( 'Step-by-step guidance', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></li>
								<li><?php esc_html_e( 'Advanced conditions', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></li>
							</ul>
						</label>
					</div>
				</div>
				<div class="ds-wizard-next-step ds-wizard-step1-actions" style="display:none;">
					<button type="button" class="btn btn-primary ds-wizard-next-from-step1 ds-wizard-btn-getting-started"><?php esc_html_e( 'Getting Started', 'advanced-flat-rate-shipping-for-woocommerce' ); ?>
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="11.877" viewBox="0 0 20 11.877"><g transform="translate(0 -17.112)"><path d="M19.062,230.9H.937a.937.937,0,0,1,0-1.875H19.062a.937.937,0,0,1,0,1.875Z" transform="translate(0 -206.909)" fill="#fff"/><path d="M224.637,155.643a.938.938,0,0,1-.663-1.6l4.337-4.337-4.337-4.337a.938.938,0,0,1,1.326-1.326l5,5a.938.938,0,0,1,0,1.326l-5,5A.93.93,0,0,1,224.637,155.643Z" transform="translate(-210.575 -126.655)" fill="#fff"/></g></svg>
					</button>
				</div>
			</div>
		</div>
		<!-- Step 2: License -->
		<div id="step2" class="tab-panel ds-wizard-license-step">
			<div class="ds-wizard-wrap">
				<div class="ds-wizard-content">
					<div class="ds-wizard-header">
						<div class="ds-wizard-step-icon ds-wizard-step-icon-license">
							<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-key w-6 h-6 text-white" aria-hidden="true" data-fg-edax9=":23.4508:/components/LicenseStep.tsx:43:17:1432:38:e:Key::::::ELGn"><path d="m15.5 7.5 2.3 2.3a1 1 0 0 0 1.4 0l2.1-2.1a1 1 0 0 0 0-1.4L19 4"></path><path d="m21 2-9.6 9.6"></path><circle cx="7.5" cy="15.5" r="5.5"></circle></svg>
						</div>
						<h2 class="cta-title"><?php echo esc_html( $activate_text ); ?></h2>
					</div>
				</div>
				<?php if ( $connect_content ) : ?>
					<div class="ds-wizard-step2-connect"><?php echo $connect_content; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></div>
				<?php elseif ( 'use_template' === $wizard_path ) : ?>
					<div class="ds-wizard-content">
						<p><?php esc_html_e( 'You have already activated. Continue to choose a template and create your first shipping rule.', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></p>
						<button type="button" class="btn btn-primary ds-wizard-license-continue-to-template"><?php esc_html_e( 'Continue to Choose Template', 'advanced-flat-rate-shipping-for-woocommerce' ); ?>
							<svg xmlns="http://www.w3.org/2000/svg" width="20" height="11.877" viewBox="0 0 20 11.877"><g transform="translate(0 -17.112)"><path d="M19.062,230.9H.937a.937.937,0,0,1,0-1.875H19.062a.937.937,0,0,1,0,1.875Z" transform="translate(0 -206.909)" fill="#fff"/><path d="M224.637,155.643a.938.938,0,0,1-.663-1.6l4.337-4.337-4.337-4.337a.938.938,0,0,1,1.326-1.326l5,5a.938.938,0,0,1,0,1.326l-5,5A.93.93,0,0,1,224.637,155.643Z" transform="translate(-210.575 -126.655)" fill="#fff"/></g></svg>
						</button>
					</div>
				<?php elseif ( 'from_scratch' === $wizard_path || '' === $wizard_path ) : ?>
					<div class="ds-wizard-content">
						<p><?php esc_html_e( 'You have already activated. You can go to the shipping method list.', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></p>
						<a href="<?php echo esc_url( $list_url ); ?>" class="btn btn-primary ds-wizard-skip-license"><?php esc_html_e( 'Go to Shipping Method List', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></a>
					</div>
				<?php endif; ?>
			</div>
		</div>
		<!-- Step 3: Choose template (config-driven) -->
		<div class="tab-panel" id="step3">
			<div class="ds-wizard-wrap">
				<div class="ds-wizard-content ds-wizard-step3-content">
					<div class="ds-wizard-header">
						<div class="ds-wizard-step-icon ds-wizard-step-icon-doc">
							<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
						</div>
						<h2 class="cta-title"><?php echo esc_html( $step3_title ); ?></h2>
					</div>
					<p class="ds-wizard-step3-subtitle"><?php echo esc_html( $step3_subtitle ); ?></p>
					<div class="ds-wizard-templates">
						<?php foreach ( $templates as $tmpl ) : ?>
							<?php
							$val = isset( $tmpl['value'] ) ? $tmpl['value'] : '';
							$tit = isset( $tmpl['title'] ) ? $tmpl['title'] : '';
							$desc = isset( $tmpl['description'] ) ? $tmpl['description'] : '';
							if ( ! $val ) {
								continue;
							}
							?>
							<label class="ds-wizard-template-card">
								<input type="radio" name="ds_wizard_template" value="<?php echo esc_attr( $val ); ?>" />
								<span class="ds-wizard-template-radio"></span>
								<span class="ds-wizard-template-title"><?php echo esc_html( $tit ); ?></span>
								<span class="ds-wizard-template-desc"><?php echo esc_html( $desc ); ?></span>
							</label>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="ds-wizard-next-step ds-wizard-step3-actions" style="display:none;">
					<button type="button" class="btn btn-primary next-step"><?php esc_html_e( 'Continue', 'advanced-flat-rate-shipping-for-woocommerce' ); ?>
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="11.877" viewBox="0 0 20 11.877"><g transform="translate(0 -17.112)"><path d="M19.062,230.9H.937a.937.937,0,0,1,0-1.875H19.062a.937.937,0,0,1,0,1.875Z" transform="translate(0 -206.909)" fill="#fff"/><path d="M224.637,155.643a.938.938,0,0,1-.663-1.6l4.337-4.337-4.337-4.337a.938.938,0,0,1,1.326-1.326l5,5a.938.938,0,0,1,0,1.326l-5,5A.93.93,0,0,1,224.637,155.643Z" transform="translate(-210.575 -126.655)" fill="#fff"/></g></svg>
					</button>
				</div>
			</div>
		</div>
		<!-- Step 4: Configure settings (config-driven) -->
		<div class="tab-panel" id="step4">
			<div class="ds-wizard-wrap">
				<div class="ds-wizard-content ds-wizard-step4-content">
					<div class="ds-wizard-header ds-wizard-step4-header">
						<div class="ds-wizard-step-icon ds-wizard-step-icon-gear">
							<svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 0 0 .33 1.82l.06.06a2 2 0 0 1 0 2.83 2 2 0 0 1-2.83 0l-.06-.06a1.65 1.65 0 0 0-1.82-.33 1.65 1.65 0 0 0-1 1.51V21a2 2 0 0 1-2 2 2 2 0 0 1-2-2v-.09A1.65 1.65 0 0 0 9 19.4a1.65 1.65 0 0 0-1.82.33l-.06.06a2 2 0 0 1-2.83 0 2 2 0 0 1 0-2.83l.06-.06a1.65 1.65 0 0 0 .33-1.82 1.65 1.65 0 0 0-1.51-1H3a2 2 0 0 1-2-2 2 2 0 0 1 2-2h.09A1.65 1.65 0 0 0 4.6 9a1.65 1.65 0 0 0-.33-1.82l-.06-.06a2 2 0 0 1 0-2.83 2 2 0 0 1 2.83 0l.06.06a1.65 1.65 0 0 0 1.82.33H9a1.65 1.65 0 0 0 1-1.51V3a2 2 0 0 1 2-2 2 2 0 0 1 2 2v.09a1.65 1.65 0 0 0 1 1.51 1.65 1.65 0 0 0 1.82-.33l.06-.06a2 2 0 0 1 2.83 0 2 2 0 0 1 0 2.83l-.06.06a1.65 1.65 0 0 0-.33 1.82V9a1.65 1.65 0 0 0 1.51 1H21a2 2 0 0 1 2 2 2 2 0 0 1-2 2h-.09a1.65 1.65 0 0 0-1.51 1z"/></svg>
						</div>
						<h2 class="cta-title"><?php esc_html_e( 'Configure Settings', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></h2>
					</div>
					<p class="ds-wizard-config-intro"><?php echo esc_html( $step4_intro ); ?></p>
					<div class="ds-wizard-config-fields">
						<?php
						foreach ( $fields as $field_key => $field_def ) {
							$field_type  = isset( $field_def['type'] ) ? $field_def['type'] : 'text';
							$label = isset( $field_def['label'] ) ? $field_def['label'] : $field_key;
							$default = isset( $field_def['default'] ) ? $field_def['default'] : '';
							$placeholder = isset( $field_def['placeholder'] ) ? $field_def['placeholder'] : '';
							$field_id = 'ds_wizard_' . $field_key;
							$show_for = array();

							if( ! $license_activated && 'delivery' === $field_key ) {
								continue;
							}
							
							foreach ( $template_fields as $tval => $keys ) {
								if ( in_array( $field_key, $keys, true ) ) {
									$show_for[] = $tval;
								}
							}
							$classes = 'ds-wizard-field ds-wizard-field-' . esc_attr( $field_key );
							foreach ( $show_for as $tval ) {
								$classes .= ' ds-wizard-field-' . esc_attr( $tval );
							}
							?>
							<div class="<?php echo esc_attr( $classes ); ?>">
								<label for="<?php echo esc_attr( $field_id ); ?>"><?php echo esc_html( $label ); ?></label>
								<?php if ( 'select' === $field_type ) : ?>
									<select id="<?php echo esc_attr( $field_id ); ?>" class="ds-wizard-input ds-wizard-select" name="ds_wizard_<?php echo esc_attr( $field_key ); ?>">
										<?php
										$opts = isset( $field_def['options'] ) ? $field_def['options'] : array();
										foreach ( $opts as $opt_val => $opt_label ) {
											echo '<option value="' . esc_attr( $opt_val ) . '"' . selected( $default, $opt_val, false ) . '>' . esc_html( $opt_label ) . '</option>';
										}
										?>
									</select>
								<?php else : 
									if ( 'number' === $field_type ) {
										?><span class="ds-wizard-input-unit"><?php echo esc_html( $get_currency_symbol ); ?></span><?php
									}
									?>
									<input type="<?php echo esc_attr( 'number' === $field_type ? 'number' : 'text' ); ?>" id="<?php echo esc_attr( $field_id ); ?>" class="ds-wizard-input" name="ds_wizard_<?php echo esc_attr( $field_key ); ?>" value="<?php echo esc_attr( $default ); ?>" placeholder="<?php echo esc_attr( $placeholder ); ?>"
										<?php
										if ( 'number' === $field_type ) {
											if ( isset( $field_def['min'] ) ) {
												echo ' min="' . esc_attr( $field_def['min'] ) . '"';
											}
											if ( isset( $field_def['max'] ) ) {
												echo ' max="' . esc_attr( $field_def['max'] ) . '"';
											}
											if ( isset( $field_def['step'] ) ) {
												echo ' step="' . esc_attr( $field_def['step'] ) . '"';
											}
										}
										?>
									/>
								<?php endif; ?>
							</div>
						<?php } ?>
					</div>
				</div>
				<div class="ds-wizard-next-step">
					<button type="button" class="btn btn-primary next-step ds-wizard-btn-check-preview"><?php esc_html_e( 'Check Preview & Activate', 'advanced-flat-rate-shipping-for-woocommerce' ); ?>
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="11.877" viewBox="0 0 20 11.877"><g transform="translate(0 -17.112)"><path d="M19.062,230.9H.937a.937.937,0,0,1,0-1.875H19.062a.937.937,0,0,1,0,1.875Z" transform="translate(0 -206.909)" fill="#fff"/><path d="M224.637,155.643a.938.938,0,0,1-.663-1.6l4.337-4.337-4.337-4.337a.938.938,0,0,1,1.326-1.326l5,5a.938.938,0,0,1,0,1.326l-5,5A.93.93,0,0,1,224.637,155.643Z" transform="translate(-210.575 -126.655)" fill="#fff"/></g></svg>
					</button>
				</div>
			</div>
		</div>
		<!-- Step 5: Preview & Activate -->
		<div class="tab-panel" id="step5">
			<div class="ds-wizard-wrap">
				<div class="ds-wizard-content ds-wizard-step5-content">
					<div class="ds-wizard-step-icon ds-wizard-step-icon-check">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="m9 12 2 2 4-4"/></svg>
					</div>
					<h2 class="cta-title"><?php echo esc_html( $step5_title ); ?></h2>
					<p class="ds-wizard-step5-subtitle"><?php echo esc_html( $step5_subtitle ); ?></p>
					<div class="ds-wizard-preview" id="ds_wizard_preview_content"></div>
				</div>
				<div class="ds-wizard-next-step">
					<button type="button" class="btn btn-primary ds-wizard-activate-rule"><?php echo esc_html( $step5_button ); ?>
						<svg xmlns="http://www.w3.org/2000/svg" width="20" height="11.877" viewBox="0 0 20 11.877"><g transform="translate(0 -17.112)"><path d="M19.062,230.9H.937a.937.937,0,0,1,0-1.875H19.062a.937.937,0,0,1,0,1.875Z" transform="translate(0 -206.909)" fill="#fff"/><path d="M224.637,155.643a.938.938,0,0,1-.663-1.6l4.337-4.337-4.337-4.337a.938.938,0,0,1,1.326-1.326l5,5a.938.938,0,0,1,0,1.326l-5,5A.93.93,0,0,1,224.637,155.643Z" transform="translate(-210.575 -126.655)" fill="#fff"/></g></svg>
					</button>
				</div>
			</div>
		</div>
		<!-- Step 6: Congratulations -->
		<div class="tab-panel" id="step6">
			<div class="ds-wizard-wrap">
				<div class="ds-wizard-content ds-wizard-step6-content">
					<div class="ds-wizard-step-icon ds-wizard-step-icon-trophy">
						<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10 14.66v1.626a2 2 0 0 1-.976 1.696A5 5 0 0 0 7 21.978"/><path d="M14 14.66v1.626a2 2 0 0 0 .976 1.696A5 5 0 0 1 17 21.978"/><path d="M18 9h1.5a1 1 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M6 9a6 6 0 0 0 12 0V3a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1z"/><path d="M6 9H4.5a1 1 0 0 1 0-5H6"/></svg>
					</div>
					<h2 class="cta-title"><?php esc_html_e( 'Congratulations!', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></h2>
					<p class="ds-wizard-step6-message"><?php echo esc_html( $step6_message ); ?></p>
					<div class="ds-wizard-live-badge">
						<svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><path d="m9 12 2 2 4-4"></path></svg>
						<span><?php esc_html_e( 'Live & Active', 'advanced-flat-rate-shipping-for-woocommerce' ); ?></span>
					</div>
				</div>
			</div>
			<div class="ds-wizard-wrap">
				<div class="ds-wizard-final-actions">
					<a href="<?php echo esc_url( $add_url ); ?>" class="ds-wizard-cta-card ds-wizard-cta-create-another ds-wizard-create-another">
						<span class="ds-wizard-cta-main">
							<svg class="ds-wizard-cta-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
							<span class="ds-wizard-cta-label"><?php echo esc_html( $cta_create ); ?></span>
							<svg class="ds-wizard-cta-arrow" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
						</span>
						<?php if ( $cta_create_sub ) : ?><span class="ds-wizard-cta-sub"><?php echo esc_html( $cta_create_sub ); ?></span><?php endif; ?>
					</a>
					<a href="<?php echo esc_url( $list_url ); ?>" class="ds-wizard-cta-card ds-wizard-cta-go-to-list ds-wizard-go-to-list">
						<span class="ds-wizard-cta-main">
							<svg class="ds-wizard-cta-icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
							<span class="ds-wizard-cta-label"><?php echo esc_html( $cta_list ); ?></span>
						</span>
						<?php if ( $cta_list_sub ) : ?><span class="ds-wizard-cta-sub"><?php echo esc_html( $cta_list_sub ); ?></span><?php endif; ?>
					</a>
				</div>
			</div>
		</div>
	</div>
</div>
