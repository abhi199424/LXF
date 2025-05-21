/**
 * NOTICE OF LICENSE
 *
 * @author    Klarna Bank AB www.klarna.com
 * @copyright Copyright (c) permanent, Klarna Bank AB
 * @license   ISC
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of Klarna Bank AB
 */
window.addEventListener("load", async function () {
    clearSdkSessionKey('__klarna_sdk_default-config');

    const klarnaSDK = await Klarna.init({
        clientId: klarnapayment.signInWithKlarna.clientId,
        environment: klarnapayment.signInWithKlarna.environment,
        locale: klarnapayment.signInWithKlarna.locale,
    });

    const siwkButton = klarnaSDK.Identity.button({
        scope: klarnapayment.signInWithKlarna.scope,
        redirectUri: klarnapayment.signInWithKlarna.redirectUri,
        interactionMode: "DEVICE_BEST",
        hideOverlay: false,
        shape: klarnapayment.signInWithKlarna.shape,
        theme: klarnapayment.signInWithKlarna.theme,
        logoAlignment: klarnapayment.signInWithKlarna.alignment,
        market: klarnapayment.signInWithKlarna.market,
    });

    klarnaSDK.Identity.on("error", async (error) => {
        $('#siwk-login-error-notification').addClass('show');

        setTimeout(function() {
            $('#siwk-login-error-notification').removeClass('show');
        }, 2000);
    });

    klarnaSDK.Identity.on("signin", (data) => {
        $.ajax({
            type: 'POST',
            url: klarnapayment.signInWithKlarna.authenticationUrl,
            async: false,
            data: {
                ajax: true,
                refresh_token: data.user_account_linking.user_account_linking_refresh_token,
                id_token: data.user_account_linking.user_account_linking_id_token,
            },
            success: function () {
                $('#siwk-login-success-notification').addClass('show');

                if (prestashop.page.page_name === 'authentication') {
                    setTimeout(function() {
                        window.location.href = '/';
                    }, 1000);

                    return;
                }

                setTimeout(function() {
                    location.reload();
                }, 1000);
            },
            error: function (error) {
                const result = JSON.parse(error.responseText);

                if (!result.hasOwnProperty('success')) {
                    console.error('Unknown authentication error: ', error)

                    return;
                }

                if (!result.success) {
                    console.error('Failed to authenticate customer: ', result.errors)
                }
            }
        });
    });

    if (klarnapayment.signInWithKlarna.isCustomerLoggedIn) {
        return;
    }

    siwkButton.mount('#siwk-container');
});

const clearSdkSessionKey = (key) => {
    if (!sessionStorage.hasOwnProperty(key)) {
        return;
    }

    let sdkSession = sessionStorage.getItem(key);
    let parsedSession = JSON.parse(sdkSession);
    let sdkClientId = parsedSession.config.clientId;

    if (sdkClientId !== klarnapayment.signInWithKlarna.clientId) {
        sessionStorage.removeItem(key);
    }
}
