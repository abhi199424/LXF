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
<prestashop-accounts></prestashop-accounts>
<br/>
<div id="prestashop-cloudsync"></div>
<br/>
<script src="{$klarnapayment.url.psAccountsCdnUrl|escape:'htmlall':'UTF-8'}" rel=preload></script>
<script src="{$cloudSyncPathCDC|escape:'htmlall':'UTF-8'}"></script>

<script>
    window?.psaccountsVue?.init();
    // CloudSync
    const cdc = window.cloudSyncSharingConsent;
    cdc.init('#prestashop-cloudsync');
    cdc.on('OnboardingCompleted', (isCompleted) => {
        console.log('OnboardingCompleted', isCompleted);
    });
    cdc.isOnboardingCompleted((isCompleted) => {
        console.log('Onboarding is already Completed', isCompleted);
    });
</script>