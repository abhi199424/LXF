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
    const KlarnaSDK = await Klarna.init({
        clientId: klarnapayment.interoperability.clientId,
        environment: klarnapayment.interoperability.environment,
        locale: klarnapayment.interoperability.locale,
    });

    KlarnaSDK.Interoperability.on('tokenupdate', (response) => {
        sessionStorage.setItem('klarna_interoperability_token', response.interoperabilityToken);
    });
})
