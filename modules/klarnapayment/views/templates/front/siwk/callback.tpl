{**
 * NOTICE OF LICENSE
 *
 * @author    Klarna Bank AB www.klarna.com
 * @copyright Copyright (c) permanent, Klarna Bank AB
 * @license   ISC
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of Klarna Bank AB
 *}

<script defer src="{$klarnapayment.signInWithKlarna.sdkUrl}"></script>
<script type="text/javascript">
    window.onload = async function () {
        let klarnaSDK;

        try {
            klarnaSDK = await Klarna.init({
                clientId: "{$klarnapayment.signInWithKlarna.clientId|escape:'javascript':'UTF-8'}",
                locale: "{$klarnapayment.signInWithKlarna.locale|escape:'javascript':'UTF-8'}",
            });
        } catch (e) {
            const params = {
                siwkFallbackRedirect: true,
                siwkError: true,
            }

            window.location.href = buildUrl(params);
        }

        klarnaSDK.Identity.on("error", async (error) => {
            const params = {
                siwkFallbackRedirect: true,
                siwkError: true,
                siwkErrorMessage: error.toString(),
            }

            window.location.href = buildUrl(params);
        });

        klarnaSDK.Identity.on("signin", (data) => {
            const params = {
                refresh_token: data.user_account_linking.user_account_linking_refresh_token,
                id_token: data.user_account_linking.user_account_linking_id_token,
                siwkFallbackRedirect: true,
            }

            window.location.href = buildUrl(params);
        });
    }

    function buildUrl(params) {
        const baseUrl = new URL('{$klarnapayment.signInWithKlarna.authenticationUrl|escape:'htmlall':'UTF-8'}');

        const searchParams = new URLSearchParams(baseUrl.search);

        for (const [key, value] of Object.entries(params)) {
            searchParams.append(key, value);
        }

        baseUrl.search = searchParams.toString();

        return baseUrl.toString();
    }
</script>