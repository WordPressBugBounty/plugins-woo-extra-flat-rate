(function ($) {
    'use strict';
    $(document).ready(function () {
        var vars = typeof afrsfw_wizard_conditional_vars !== 'undefined' ? afrsfw_wizard_conditional_vars : {};
        var config = vars.ds_wizard_config || {};
        var templates = config.templates || [];
        var templateDefaults = config.template_defaults || {};
        var fieldKeys = config.field_keys || [];
        var saveAction = config.save_action || 'afrsm_wizard_create_rule';
        var saveNonce = vars.wizard_create_rule_nonce || '';
        var cookieName = config.cookie_name || 'afrsm_wizard_path';
        var markCompletedAction = config.mark_completed_action || 'afrsm_wizard_mark_completed';
        var get_currency_symbol = vars.get_currency_symbol || '$';

        $(document).on('click', '.dots-onboarding-notice .onboarding-close-btn', function () {
            var $btn = $(this);
            $btn.parents('.dots-onboarding-notice').hide();
            $.ajax({
                url: vars.ajaxurl,
                type: 'POST',
                data: {
                    action: markCompletedAction,
                    nonce: vars.setup_wizard_ajax_nonce
                },
                success: function () {
                }
            });
        });
        
        var $main = $('.ds-plugin-setup-wizard-main');
        if (!$main.length) {
            return;
        }

        document.body.classList.add('dots-setup-wizard-active');

        var urlParams = new URLSearchParams(window.location.search);
        var wizardStepParam = urlParams.get('wizard_step');
        var freemiusLicenseParam = urlParams.get('require_license');
        var initialStep = $main.attr('data-initial-step');
        if (wizardStepParam) {
            initialStep = wizardStepParam;
        }
        if (freemiusLicenseParam) {
            initialStep = 2;
        }
        initialStep = parseInt(initialStep, 10) || 1;
        var licenseActivated = $main.attr('data-license-activated') === '1';
        var wizardPath = $main.attr('data-wizard-path') || '';
        var creationMethod = '';
        var selectedTemplate = '';

        var wizardPathCookie = getCookie(cookieName);
        if (wizardPathCookie) {
            wizardPath = wizardPathCookie;
        }

        // Add custom css for setup wizard screen
        $('body .notice, body div.error, body div.updated, body .dpb-popup').css('display', 'none');
        $('html.wp-toolbar').css('padding-top', '0');

        function getCookie(name) {
            let nameEQ = name + '=';
            let ca = document.cookie.split(';');
            for (let i = 0; i < ca.length; i++) {
                let c = ca[i].trim();
                if (c.indexOf(nameEQ) === 0) {
                    return c.substring(nameEQ.length, c.length);
                }
            }
            return null;
        }

        function getFirstTemplateValue() {
            return (templates.length && templates[0].value) ? '' : '';
        }

        function getTemplateTitle(value) {
            for (var i = 0; i < templates.length; i++) {
                if (templates[i].value === value) {
                    return templates[i].title || value;
                }
            }
            return value;
        }

        function setWizardPathCookie(path) {
            var maxAge = 60 * 60 * 24;
            document.cookie = cookieName + '=' + encodeURIComponent(path) + '; path=/; max-age=' + maxAge + '; SameSite=Lax';
        }

        function setWizardPathOnServer(path) {
            if (!path || typeof vars.ajaxurl === 'undefined') {
                return;
            }
            $.post(vars.ajaxurl, {
                action: 'afrsm_wizard_set_path',
                nonce: vars.setup_wizard_ajax_nonce,
                path: path
            });
        }

        function hideAllSteps() {
            $main.find('.tab-panel').hide();
        }

        function showStep(stepId) {
            hideAllSteps();
            $main.find('#' + stepId).show();
            updateProgressBar(stepId);
            if (stepId === 'step3') {
                $main.find('.ds-wizard-step3-actions').toggle(!!selectedTemplate);
            }
        }

        function updateProgressBar(stepId) {
            var stepNum = parseInt(stepId.replace('step', ''), 10);
            if (stepNum === 6) {
                $main.addClass('ds-wizard-on-congrats');
                return;
            }
            $main.removeClass('ds-wizard-on-congrats');
            var $readymade = $main.find('.ds-wizard-progress-readymade');
            var $fromScratch = $main.find('.ds-wizard-progress-from-scratch');
            console.log(creationMethod);
            if (creationMethod === 'use_template') {
                $fromScratch.addClass('ds-wizard-progress-hidden');
                $readymade.removeClass('ds-wizard-progress-hidden');
                $readymade.find('.ds-wizard-progress-step-wrap').removeClass('active current');
                if (stepNum >= 2 && stepNum <= 5) {
                    $readymade.find('.ds-wizard-progress-step-wrap[data-step="' + stepNum + '"]').addClass('current');
                    $readymade.find('.ds-wizard-progress-step-wrap[data-step="' + stepNum + '"]').prevAll('.ds-wizard-progress-step-wrap').addClass('active');
                }
                if (stepNum === 6) {
                    $readymade.find('.ds-wizard-progress-step-wrap').addClass('active');
                    $readymade.find('.ds-wizard-progress-step-wrap[data-step="5"]').addClass('current');
                }
            } else if (creationMethod === 'from_scratch') {
                $readymade.addClass('ds-wizard-progress-hidden');
                $fromScratch.removeClass('ds-wizard-progress-hidden');
                $fromScratch.find('.ds-wizard-progress-step-wrap').removeClass('active current');
                if (stepNum === 1) {
                    $fromScratch.find('.ds-wizard-progress-step-wrap[data-step="1"]').addClass('current');
                }
                if (stepNum === 2) {
                    $fromScratch.find('.ds-wizard-progress-step-wrap[data-step="1"]').addClass('active');
                    $fromScratch.find('.ds-wizard-progress-step-wrap[data-step="2"]').addClass('current');
                }
            } else {
                $readymade.addClass('ds-wizard-progress-hidden');
                $fromScratch.addClass('ds-wizard-progress-hidden');
            }
        }

        function goNextFromStep(currentStepId) {
            var num = parseInt(currentStepId.replace('step', ''), 10);
            var nextId = 'step' + (num + 1);
            if ($main.find('#' + nextId).length) {
                showStep(nextId);
                if (nextId === 'step4') {
                    updateStep4Fields();
                }
                if (nextId === 'step5') {
                    renderPreview();
                }
            }
        }

        function updateStep4Fields() {
            var template = selectedTemplate || getFirstTemplateValue();
            var defaults = templateDefaults[template] || {};

            $main.find('.ds-wizard-field').hide();
            $main.find('.ds-wizard-field-' + template).show();

            var $ruleTitle = $main.find('#ds_wizard_rule_title');
            if ($ruleTitle.length && defaults.rule_title_placeholder !== undefined) {
                $ruleTitle.attr('placeholder', defaults.rule_title_placeholder);
            }
            for (var key in defaults) {
                if (key === 'rule_title_placeholder') {
                    continue;
                }
                var $input = $main.find('#ds_wizard_' + key);
                if ($input.length && defaults[key] !== undefined) {
                    if (!$input.val()) {
                        $input.val(defaults[key]);
                    }
                }
            }
            var defaultTitle = getTemplateTitle(template);
            if ($ruleTitle.length && !$ruleTitle.val() && defaultTitle) {
                $ruleTitle.val(defaultTitle);
            }
        }

        function getCountryLabel() {
            var $sel = $main.find('#ds_wizard_country');
            return $sel.length ? $sel.find('option:selected').text() : '';
        }

        function renderPreview() {
            var template = selectedTemplate || getFirstTemplateValue();
            var title = $main.find('#ds_wizard_rule_title').val() || getTemplateTitle(template);
            var cost = $main.find('#ds_wizard_cost').val() || '20';
            var freeAbove = $main.find('#ds_wizard_free_above').val() || '299';
            var percent = $main.find('#ds_wizard_percent').val() || '9';
            var delivery = $main.find('#ds_wizard_delivery').val() || '2-5 days';
            var countryLabel = getCountryLabel();

            var subtotal = 450;
            var shippingCost = 0;
            var shippingLabel = 'FREE';
            var showFreeMessage = false;
            if (template === 'flat_us_20') {
                shippingCost = parseFloat(cost, 10) || 20;
                shippingLabel = get_currency_symbol + (parseFloat(cost, 10) || 20).toFixed(2);
            } else if (template === 'free_above_299') {
                var threshold = parseFloat(freeAbove, 10) || 299;
                showFreeMessage = subtotal >= threshold;
            } else if (template === 'dynamic_9') {
                shippingCost = (subtotal * (parseFloat(percent, 10) || 9) / 100);
                shippingLabel = get_currency_symbol + shippingCost.toFixed(2);
            }
            var total = subtotal + shippingCost;

            var html = '<div class="ds-wizard-checkout-preview">';
            html += '<div class="ds-wizard-checkout-preview-title"><svg width="50" height="50" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M3.04047 2.29242C2.6497 2.15503 2.22155 2.36044 2.08416 2.7512C1.94678 3.14197 2.15218 3.57012 2.54295 3.7075L2.80416 3.79934C3.47177 4.03406 3.91052 4.18961 4.23336 4.34802C4.53659 4.4968 4.67026 4.61723 4.75832 4.74609C4.84858 4.87818 4.91828 5.0596 4.95761 5.42295C4.99877 5.80316 4.99979 6.29837 4.99979 7.03832L4.99979 9.64C4.99979 12.5816 5.06302 13.5523 5.92943 14.4662C6.79583 15.38 8.19028 15.38 10.9792 15.38H16.2821C17.8431 15.38 18.6236 15.38 19.1753 14.9304C19.727 14.4808 19.8846 13.7164 20.1997 12.1875L20.6995 9.76275C21.0466 8.02369 21.2202 7.15417 20.7762 6.57708C20.3323 6 18.8155 6 17.1305 6H6.49233C6.48564 5.72967 6.47295 5.48373 6.4489 5.26153C6.39517 4.76515 6.27875 4.31243 5.99677 3.89979C5.71259 3.48393 5.33474 3.21759 4.89411 3.00139C4.48203 2.79919 3.95839 2.61511 3.34187 2.39838L3.04047 2.29242ZM15.5172 8.4569C15.8172 8.74256 15.8288 9.21729 15.5431 9.51724L12.686 12.5172C12.5444 12.6659 12.3481 12.75 12.1429 12.75C11.9376 12.75 11.7413 12.6659 11.5998 12.5172L10.4569 11.3172C10.1712 11.0173 10.1828 10.5426 10.4828 10.2569C10.7827 9.97123 11.2574 9.98281 11.5431 10.2828L12.1429 10.9125L14.4569 8.48276C14.7426 8.18281 15.2173 8.17123 15.5172 8.4569Z" fill="#000"/><path d="M7.5 18C8.32843 18 9 18.6716 9 19.5C9 20.3284 8.32843 21 7.5 21C6.67157 21 6 20.3284 6 19.5C6 18.6716 6.67157 18 7.5 18Z" fill="#000"/><path d="M16.5 18.0001C17.3284 18.0001 18 18.6716 18 19.5001C18 20.3285 17.3284 21.0001 16.5 21.0001C15.6716 21.0001 15 20.3285 15 19.5001C15 18.6716 15.6716 18.0001 16.5 18.0001Z" fill="#000"/></svg>CHECKOUT PREVIEW</div>';
            html += '<div class="ds-wizard-checkout-row"><span>Subtotal</span><span>' + get_currency_symbol + subtotal.toFixed(2) + '</span></div>';
            html += '<div class="ds-wizard-checkout-row shipping-row">';
            html += '<span>Shipping<span class="ds-wizard-shipping-detail">' + delivery + '</span></span>';
            html += '<span class="ds-wizard-shipping-free">' + shippingLabel + '</span></div>';
            html += '<div class="ds-wizard-checkout-row"><span>Total</span><span>' + get_currency_symbol + total.toFixed(2) + '</span></div>';
            if (showFreeMessage) {
                html += '<div class="ds-wizard-checkout-success">🎉 You qualified for free shipping!</div>';
            }
            html += '</div>';
            html += '<div class="ds-wizard-rule-details">';
            html += '<div class="ds-wizard-checkout-preview-title"><svg height="50" width="50" version="1.1" id="_x32_" xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" viewBox="0 0 512 512"  xml:space="preserve"><g><path fill="#000" class="st0" d="M255.992,0.008C114.626,0.008,0,114.626,0,256s114.626,255.992,255.992,255.992 C397.391,511.992,512,397.375,512,256S397.391,0.008,255.992,0.008z M300.942,373.528c-10.355,11.492-16.29,18.322-27.467,29.007 c-16.918,16.177-36.128,20.484-51.063,4.516c-21.467-22.959,1.048-92.804,1.597-95.449c4.032-18.564,12.08-55.667,12.08-55.667 s-17.387,10.644-27.709,14.419c-7.613,2.782-16.225-0.871-18.354-8.234c-1.984-6.822-0.404-11.161,3.774-15.822 c10.354-11.484,16.289-18.314,27.467-28.999c16.934-16.185,36.128-20.483,51.063-4.524c21.467,22.959,5.628,60.732,0.064,87.497 c-0.548,2.653-13.742,63.627-13.742,63.627s17.387-10.645,27.709-14.427c7.628-2.774,16.241,0.887,18.37,8.242 C306.716,364.537,305.12,368.875,300.942,373.528z M273.169,176.123c-23.886,2.096-44.934-15.564-47.031-39.467 c-2.08-23.878,15.58-44.934,39.467-47.014c23.87-2.097,44.934,15.58,47.015,39.458 C314.716,152.979,297.039,174.043,273.169,176.123z"/></g></svg>INFORMATION</div>';
            html += '<div class="ds-wizard-rule-detail-row"><span>Template:</span><span>' + title + '</span></div>';
            if (countryLabel) {
                html += '<div class="ds-wizard-rule-detail-row"><span>Country:</span><span>' + countryLabel + '</span></div>';
            }
            html += '<div class="ds-wizard-rule-detail-row"><span>Delivery Time:</span><span>' + delivery + '</span></div>';
            html += '</div>';
            $main.find('#ds_wizard_preview_content').html(html);
        }

        function getWizardData() {
            var template = selectedTemplate || getFirstTemplateValue();
            var data = { template: template };
            for (var i = 0; i < fieldKeys.length; i++) {
                var key = fieldKeys[i];
                var $el = $main.find('#ds_wizard_' + key);
                if ($el.length) {
                    data[key] = $el.val() !== undefined ? $el.val() : '';
                }
            }
            if (data.rule_title === undefined || data.rule_title === '') {
                data.rule_title = getTemplateTitle(template);
            }
            return data;
        }

        function markWizardCompletedThenGo(url) {
            $.post(vars.ajaxurl, {
                action: markCompletedAction,
                nonce: vars.setup_wizard_ajax_nonce
            }).always(function () {
                window.location.href = url;
            });
        }

        if (initialStep >= 2 && initialStep <= 6) {
            if (initialStep === 2 && wizardPath === 'from_scratch') {
                creationMethod = 'from_scratch';
            } else {
                creationMethod = 'use_template';
                selectedTemplate = getFirstTemplateValue();
                if (selectedTemplate) {
                    $main.find('input[name="ds_wizard_template"][value="' + selectedTemplate + '"]').prop('checked', true);
                }
            }
            showStep('step' + initialStep);
            if (initialStep === 4) {
                updateStep4Fields();
            }
            if (initialStep === 5) {
                renderPreview();
            }
        } else {
            showStep('step1');
        }

        $main.find('input[name="ds_wizard_creation_method"]').on('change', function () {
            creationMethod = $(this).val();
            $main.find('.ds-wizard-step1-actions').toggle(!!creationMethod);
        });

        $main.on('click', '.ds-wizard-next-from-step1', function () {
            if (creationMethod === 'from_scratch') {
                setWizardPathCookie('from_scratch');
                setWizardPathOnServer('from_scratch');
                showStep('step2');
            } else {
                setWizardPathCookie('use_template');
                setWizardPathOnServer('use_template');
                if (licenseActivated) {
                    selectedTemplate = getFirstTemplateValue();
                    if (selectedTemplate) {
                        $main.find('input[name="ds_wizard_template"][value="' + selectedTemplate + '"]').prop('checked', true);
                    }
                    showStep('step3');
                } else {
                    showStep('step2');
                }
            }
        });

        $main.on('click', '.ds-wizard-license-continue-to-template', function () {
            creationMethod = 'use_template';
            selectedTemplate = getFirstTemplateValue();
            if (selectedTemplate) {
                $main.find('input[name="ds_wizard_template"][value="' + selectedTemplate + '"]').prop('checked', true);
            }
            showStep('step3');
        });

        $main.find('input[name="ds_wizard_template"]').on('change', function () {
            selectedTemplate = $(this).val();
            $main.find('.ds-wizard-step3-actions').show();
        });

        $main.on('click', '.tab-panel .btn-primary.next-step', function () {
            var $panel = $(this).closest('.tab-panel');
            var id = $panel.attr('id');
            if ($(this).hasClass('ds-wizard-go-to-license')) {
                return;
            }
            goNextFromStep(id);
        });

        $main.on('click', '.ds-wizard-activate-rule', function () {
            var $btn = $(this);
            var data = getWizardData();
            data.action = saveAction;
            data.nonce = saveNonce;

            $btn.prop('disabled', true);
            $.ajax({
                url: vars.ajaxurl,
                type: 'POST',
                data: data,
                success: function (response) {
                    if (response && response.success) {
                        showStep('step6');
                    } else {
                        alert(response && response.data ? response.data : 'Could not create rule.');
                    }
                },
                error: function () {
                    alert('Request failed. Please try again.');
                },
                complete: function () {
                    $btn.prop('disabled', false);
                }
            });
        });

        $main.on('click', '.ds-wizard-create-another', function (e) {
            e.preventDefault();
            markWizardCompletedThenGo($(this).attr('href'));
        });

        $main.on('click', '.ds-wizard-go-to-list', function (e) {
            e.preventDefault();
            markWizardCompletedThenGo($(this).attr('href'));
        });
    });
})(jQuery);
