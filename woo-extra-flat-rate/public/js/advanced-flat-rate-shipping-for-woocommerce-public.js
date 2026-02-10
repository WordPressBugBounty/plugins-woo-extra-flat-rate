(function($) {
    'use strict';

    /**
     * All of the code for your public-facing JavaScript source
     * should reside in this file.
     *
     * Note: It has been assumed you will write jQuery code here, so the
     * $ function reference has been prepared for usage within the scope
     * of this function.
     *
     * This enables you to define handlers, for when the DOM is ready:
     *
     * $(function() {
     *
     * });
     *
     * When the window is loaded:
     *
     * $( window ).on('load', function() {
     *
     * });
     *
     * ...and/or other possibilities.
     *
     * Ideally, it is not considered best practise to attach more than a
     * single DOM-ready or window-load handler for a particular page.
     * Although scripts in the WordPress core, Plugins and Themes may be
     * practising this, we should strive to set a better example in our own work.

     */

    $(document).ready(function () {
        $('body').on('change', 'input[name="payment_method"]', function () {
            $('body').trigger('update_checkout');
        });

        /** Checkout WC compatibility Start */
        $( document.body ).on( 'updated_checkout', function(){
            var move_element;
            $('.cfw-shipping-methods-list').addClass('afrsm_shipping');
            $('.cfw-shipping-methods-list li').each( function(){ 
                if( $(this).find('.extra-flate-tool-tip').length > 0){
                    move_element = $(this).find('.extra-flate-tool-tip');
                    $(move_element).appendTo( $(this).find('.cfw-shipping-method-inner') );
                }
                if( $(this).find('.forceall-tooltip').length > 0){
                    move_element = $(this).find('.forceall-tooltip');
                    $(move_element).appendTo( $(this).find('.cfw-shipping-method-inner') );
                }
            });
        });
        /** Checkout WC compatibility End */

        /**
         * Block Checkout Compatibility - Tooltip Feature
         * Tooltips should appear only in Order Summary (totals section) for selected shipping method
         * Following the same pattern as fees plugin
         */
        var isUpdatingTooltip = false; // Flag to prevent infinite loops
        var tooltipUpdateTimeout = null; // Debounce timeout
        
        init_shipping_tooltip();
    
        // Function to update the shipping method tooltip in Order Summary
        updateShippingLabel();

        // Also run it whenever the DOM updates (e.g., when shipping methods are updated)
        // But make it more specific to avoid infinite loops
        const observer = new MutationObserver(function(mutations) {
            // Only trigger if the mutation is not from our tooltip additions
            var shouldUpdate = false;
            mutations.forEach(function(mutation) {
                if (mutation.addedNodes.length > 0) {
                    for (var i = 0; i < mutation.addedNodes.length; i++) {
                        var node = mutation.addedNodes[i];
                        // Skip if it's our tooltip being added
                        if (node.nodeType === 1 && !$(node).hasClass('wc-afrsm-help-tip') && !$(node).find('.wc-afrsm-help-tip').length) {
                            // Check if it's a relevant change (totals item or shipping method)
                            if ($(node).closest('.wc-block-components-totals-item, .wc-block-components-shipping-rates-control').length > 0) {
                                shouldUpdate = true;
                                break;
                            }
                        }
                    }
                }
            });
            
            if (shouldUpdate && !isUpdatingTooltip) {
                // Debounce to prevent rapid fire
                clearTimeout(tooltipUpdateTimeout);
                tooltipUpdateTimeout = setTimeout(updateShippingLabel, 300);
            }
        });
        observer.observe(document.body, { childList: true, subtree: true });
        
        function updateShippingLabel() {
            // Remove all existing tooltips only if we need to update
            $('.wc-afrsm-help-tip').remove();
            $('.afrsm-shipping-subtitle').remove();

            // Prevent re-entry
            if (isUpdatingTooltip) {
                return;
            }
            
            if (typeof afrsm_public_vars === 'undefined' || !afrsm_public_vars.shipping_tooltip_data) {
                return;
            }

            isUpdatingTooltip = true; // Set flag to prevent re-entry

            // Check if tooltip already exists for current selected method
            var $existingTooltip = $('.wc-afrsm-help-tip');
            var needsUpdate = true;
            
            // Get the selected shipping method and its label
            var selectedMethodLabel = '';
            var $selectedInput = $('.wc-block-components-shipping-rates-control .wc-block-components-radio-control__option-checked');

            if ($selectedInput.length > 0) {
                var $methodLabel = $selectedInput.find('.wc-block-components-radio-control__label');
                if ($methodLabel.length > 0) {
                    selectedMethodLabel = $methodLabel.text().trim();
                }
            } else {
                selectedMethodLabel = $('.wc-block-components-totals-shipping .wc-block-components-totals-item__label').text().trim();
            }

            // If no selected method, don't show any tooltips
            if (!selectedMethodLabel) {
                isUpdatingTooltip = false;
                return;
            }

            // Create slug from selected method label (same way PHP creates slug)
            var selectedMethodSlug = selectedMethodLabel.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');

            // If we have an existing tooltip, check if it's for the current method
            if ($existingTooltip.length > 0) {
                var existingSlug = $existingTooltip.first().attr('class').match(/afrsm-help-tip-([^\s]+)/);
                if (existingSlug && existingSlug[1] === selectedMethodSlug) {
                    // Tooltip already exists for current method, no need to update
                    needsUpdate = false;
                }
            }

            if (!needsUpdate) {
                isUpdatingTooltip = false;
                return;
            }

            // Find the tooltip data for the selected shipping method
            var tooltip_html = null;
            var shipping_slug = null;
            var tooltip_type = null;
            
            $.each( afrsm_public_vars.shipping_tooltip_data, function( slug, tooltip ){
                // Match by slug
                if (slug === selectedMethodSlug) {
                    tooltip_html = tooltip.text;
                    tooltip_type = tooltip.type;
                    shipping_slug = slug;
                    return false; // break the loop
                }
            });

            // If no tooltip found for selected method, don't show anything
            if (!tooltip_html || !shipping_slug) {
                isUpdatingTooltip = false;
                return;
            }

            // Now find the selected shipping method in Order Summary totals and add tooltip
            $('.wc-block-components-totals-item').each(function() {
                var $item = $(this);
                var $label = $item.find('.wc-block-components-totals-item__label');
                var labelText = $label.text().trim();
                
                // Skip if it's subtotal, fees, or total
                if ($item.hasClass('wc-block-components-totals-item--subtotal') ||
                    $item.closest('.wc-block-components-totals-fees').length > 0 ||
                    $item.closest('.wc-block-components-totals-footer-item').hasClass('wc-block-components-totals-footer-item--total')) {
                    return true; // continue
                }
                
                // Create slug from Order Summary label text for comparison
                var labelSlug = labelText.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/^-+|-+$/g, '');

                var matches = false;
                if (labelSlug === shipping_slug || labelSlug === selectedMethodSlug) {
                    matches = true;
                } else if (labelText === selectedMethodLabel) {
                    matches = true;
                } else if (labelText.indexOf(selectedMethodLabel) !== -1) {
                    // Partial match - Order Summary label contains selected method name
                    matches = true;
                }
                
                if (matches) {
                    var $valueElement = $item.find('.wc-block-components-totals-item__value');
                    
                    if ($valueElement.length > 0 && $('.afrsm-help-tip-' + shipping_slug).length === 0) {
                        // Temporarily disconnect observer to prevent loop
                        observer.disconnect();
                        
                        if (tooltip_type === 'tooltip') {
                            let $tooltip = $('<span class="wc-afrsm-help-tip wc-block-components-tooltip afrsm-help-tip-' + shipping_slug + '" data-tip="' + tooltip_html + '"></span>');
                            $valueElement.after($tooltip);
                        } else {
                            var $description = $valueElement.next('.wc-block-components-totals-item__description');
                            let $subtitle = $('<span class="afrsm-shipping-subtitle afrsm-subtitle-' + shipping_slug + '">'+ tooltip_html +'</span>');
                            $description.html($subtitle);
                        }
                        
                        // Reconnect observer after a short delay
                        setTimeout(function() {
                            observer.observe(document.body, { childList: true, subtree: true });
                        }, 100);
                    }
                }
            });
            
            init_shipping_tooltip();
            
            // Reset flag after a short delay to allow DOM to settle
            setTimeout(function() {
                isUpdatingTooltip = false;
            }, 200);
        }

        function init_shipping_tooltip() {
            setTimeout( function(){ 
                $('.wc-afrsm-help-tip').each(function () {
                    // Check if tipTip is already initialized
                    if (!$(this).data('tiptip-initialized')) {
                        $(this).tipTip({ 
                            content: $(this).data('tip'),
                            keepAlive: true, 
                            edgeOffset: 2 
                        });
                        $(this).data('tiptip-initialized', true);
                    }
                });
            }, 1000 );
        }
    });

    $(window).on('load', function () {
        if ($('.forceall_shipping_method').length) {
            if ($('.forceall_shipping_method').is(':hidden')) {
                updateCartButton();
            }
        }

        function updateCartButton() {
            $('.forceall_shipping_method').attr('checked', true).trigger('change');
            var checked = $('.forceall_shipping_method').is(':checked');
            if (checked === 'true') {
                $('[name="update_cart"]').trigger('click');
            }
        }
    });

})(jQuery);