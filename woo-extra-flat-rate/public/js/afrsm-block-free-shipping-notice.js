(function ($) {
    'use strict';

    var domRetryCount = 0;
    var domMaxRetries = 60;
    var storeRetryCount = 0;
    var storeMaxRetries = 40;
    var lastNoticesJson = '';
    var unsubscribe = null;

    function getCartStoreKey() {
        if (window.wc && window.wc.wcBlocksData && window.wc.wcBlocksData.CART_STORE_KEY) {
            return window.wc.wcBlocksData.CART_STORE_KEY;
        }

        return 'wc/store/cart';
    }

    function isBlockCartOrCheckoutPage() {
        return $('.wp-block-woocommerce-cart, .wp-block-woocommerce-checkout, .wc-block-cart, .wc-block-checkout').length > 0;
    }

    function getNoticeWrapper() {
        var $wrapper = $('#afrsm-free-shipping-notices');

        if ($wrapper.length) {
            return $wrapper;
        }

        $wrapper = $('<div id="afrsm-free-shipping-notices" class="afrsm-free-shipping-notices"></div>');
        var $storeNotices = $('.wp-block-woocommerce-store-notices').first();

        if ($storeNotices.length) {
            $storeNotices.after($wrapper);
        } else {
            var $block = $('.wp-block-woocommerce-cart, .wp-block-woocommerce-checkout, .wc-block-cart, .wc-block-checkout').first();
            if ($block.length) {
                $block.before($wrapper);
            } else {
                $('.woocommerce').first().prepend($wrapper);
            }
        }

        return $wrapper;
    }

    function renderNotices(notices) {
        var $wrapper = getNoticeWrapper();
        var html = '';

        if (notices && notices.length) {
            notices.forEach(function (notice) {
                if (notice && notice.html) {
                    html += notice.html;
                }
            });
        }

        $wrapper.html(html);
    }

    function getNoticesFromCartStore() {
        if (!window.wp || !window.wp.data || !window.wp.data.select) {
            return null;
        }

        var cartStore = getCartStoreKey();

        try {
            var select = window.wp.data.select(cartStore);

            if (!select || typeof select.getCartData !== 'function') {
                return null;
            }

            var cart = select.getCartData();

            if (!cart) {
                return null;
            }

            if (!cart.extensions || !cart.extensions.afrsm) {
                return [];
            }

            return cart.extensions.afrsm.notices || [];
        } catch (error) {
            return null;
        }
    }

    function updateNotices() {
        var notices = getNoticesFromCartStore();

        if (null === notices) {
            return;
        }

        var noticesJson = JSON.stringify(notices);

        if (noticesJson !== lastNoticesJson) {
            lastNoticesJson = noticesJson;
            renderNotices(notices);
        }
    }

    function bindCartStoreSubscription() {
        if (!window.wp || !window.wp.data || typeof window.wp.data.subscribe !== 'function') {
            return false;
        }

        if (null === getNoticesFromCartStore() && storeRetryCount < storeMaxRetries) {
            return false;
        }

        if (unsubscribe) {
            unsubscribe();
            unsubscribe = null;
        }

        unsubscribe = window.wp.data.subscribe(updateNotices);
        updateNotices();

        return true;
    }

    function initBlockFreeShippingNotices() {
        if (!isBlockCartOrCheckoutPage()) {
            if (domRetryCount >= domMaxRetries) {
                return;
            }

            domRetryCount += 1;
            window.setTimeout(initBlockFreeShippingNotices, 250);
            return;
        }

        if (bindCartStoreSubscription()) {
            return;
        }

        if (storeRetryCount >= storeMaxRetries) {
            return;
        }

        storeRetryCount += 1;
        window.setTimeout(initBlockFreeShippingNotices, 250);
    }

    $(function () {
        initBlockFreeShippingNotices();
    });

})(jQuery);
