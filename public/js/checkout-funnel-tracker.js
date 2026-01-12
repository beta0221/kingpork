/**
 * 結帳流程追蹤器
 * 用於追蹤用戶在結帳流程中的行為
 */
(function() {
    'use strict';

    // 追蹤 API 端點
    const TRACK_API_URL = '/api/funnel/track';

    // 流程步驟常數
    const STEPS = {
        CART_VIEW: 'cart_view',
        CHECKOUT_START: 'checkout_start',
        CHECKOUT_FORM_SUBMIT: 'checkout_form_submit',
        ORDER_CREATED: 'order_created',
        PAYMENT_PAGE_VIEW: 'payment_page_view',
        PAYMENT_TOKEN_REQUESTED: 'payment_token_requested',
        PAYMENT_TOKEN_RECEIVED: 'payment_token_received',
        PAYMENT_FORM_SUBMIT: 'payment_form_submit',
        PAYMENT_REDIRECT: 'payment_redirect',
        PAYMENT_3D_VERIFY: 'payment_3d_verify',
        PAYMENT_COMPLETED: 'payment_completed',
        THANKYOU_PAGE_VIEW: 'thankyou_page_view'
    };

    // 取得或建立 Session ID (使用 cookie)
    function getOrCreateSessionId() {
        const cookieName = '_funnel_sid';
        let sessionId = getCookie(cookieName);

        if (!sessionId) {
            sessionId = 'funnel_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
            setCookie(cookieName, sessionId, 1); // 1天過期
        }

        return sessionId;
    }

    // 設定 Cookie
    function setCookie(name, value, days) {
        const expires = new Date();
        expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
        document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/';
    }

    // 取得 Cookie
    function getCookie(name) {
        const nameEQ = name + '=';
        const ca = document.cookie.split(';');
        for (let i = 0; i < ca.length; i++) {
            let c = ca[i];
            while (c.charAt(0) === ' ') c = c.substring(1, c.length);
            if (c.indexOf(nameEQ) === 0) return c.substring(nameEQ.length, c.length);
        }
        return null;
    }

    // 發送追蹤請求
    function track(step, options = {}) {
        const data = {
            step: step,
            status: options.status || 'success',
            error_message: options.error_message || null,
            bill_id: options.bill_id || null,
            payment_method: options.payment_method || null,
            amount: options.amount || null,
            metadata: options.metadata || null,
            _token: document.querySelector('meta[name="csrf-token"]')?.content
        };

        // 使用 fetch API 發送追蹤
        fetch(TRACK_API_URL, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': data._token || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(result => {
            if (window.CheckoutFunnel && window.CheckoutFunnel.debug) {
                console.log('[Funnel Tracker] Tracked:', step, result);
            }
        })
        .catch(error => {
            if (window.CheckoutFunnel && window.CheckoutFunnel.debug) {
                console.error('[Funnel Tracker] Error:', error);
            }
        });
    }

    // 追蹤錯誤
    function trackError(step, errorMessage, options = {}) {
        options.status = 'error';
        options.error_message = errorMessage;
        track(step, options);
    }

    // 追蹤成功
    function trackSuccess(step, options = {}) {
        options.status = 'success';
        track(step, options);
    }

    // 初始化追蹤器
    function init() {
        // 確保 Session ID 存在
        getOrCreateSessionId();

    }

    // 公開 API
    window.CheckoutFunnel = {
        track: track,
        trackError: trackError,
        trackSuccess: trackSuccess,
        STEPS: STEPS,
        debug: false, // 設為 true 啟用除錯訊息
        init: init
    };

    // 自動初始化
    init();

})();
