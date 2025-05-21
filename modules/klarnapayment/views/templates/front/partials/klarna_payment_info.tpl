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
<div id="js-klarnapayment-wrapper" class="section">

    <div
        id="klarnapayment-container-{$klarnapayment.payment_option.payment_method_category|escape:'htmlall':'UTF-8'}-generic-error"
        class="klarnapayment-generic-error">
        <p class="alert alert-danger">
            {l s="Unexpected error occurred. Try again or contact support." mod='klarnapayment'}
        </p>
    </div>

    <div
        id="klarnapayment-container-{$klarnapayment.payment_option.payment_method_category|escape:'htmlall':'UTF-8'}-error"
        class="klarnapayment-error">
        <p class="alert alert-danger"></p>
    </div>

    <div
         id="klarnapayment-container-{$klarnapayment.payment_option.payment_method_category|escape:'htmlall':'UTF-8'}"
         class="klarnapayment-option"
         data-payment_method_category="{$klarnapayment.payment_option.payment_method_category|escape:'htmlall':'UTF-8'}">
    </div>

    <div class="klarnapayment-loading-background">
        <div class="klarnapayment-loading-spinner-wrapper">
            <div class="klarnapayment-loading-spinner"></div>
        </div>
    </div>
    {if $klarnapayment.purchase_country == 'NL'}
    <div class="klarnapayment-coc-text-block">
        <p class="klarnapayment-coc-text">
            <span>{l s="You must be at least 18+ to use this service. If you pay on time, you avoid extra costs and ensure that you can use the services of Klarna again in the future. By continuing, you accept the" mod='klarnapayment'}</span>
            <a
                    class="klarnapayment-coc-href-text"
                    target="_blank"
                    href="{$klarnapayment.termsAndConditionsUrl}">{l s='Terms and Conditions' mod='klarnapayment'}</a> {l s="and confirm that you have read the" mod='klarnapayment'}
            <a
                    class="klarnapayment-coc-href-text"
                    target="_blank"
                    href="{$klarnapayment.privacyStatementUrl}">{l s='Privacy Statement' mod='klarnapayment'}</a>  {l s="and" mod='klarnapayment'}
            <a
                    class="klarnapayment-coc-href-text"
                    target="_blank"
                    href="{$klarnapayment.cookieStatementUrl}">{l s='Cookie Statement.' mod='klarnapayment'}</a>
        </p>
    </div>
    {/if}

</div>
