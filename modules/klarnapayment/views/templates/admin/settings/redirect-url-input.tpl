{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License version 3.0
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 *}

<div class="klarna-input-wrapper">
    <div class="form-group">
        <label class="control-label col-lg-4">
            {l s='Redirect URL' mod='klarnapayment'}
        </label>
        <div class="col-lg-8 parent-container">
            <span class="copy-icon-button">
                <i class="material-icons">content_copy</i>
            </span>
            <input readonly type="text" name="KLARNA_SIGN_IN_WITH_KLARNA_REDIRECT_URL" id="KLARNA_SIGN_IN_WITH_KLARNA_REDIRECT_URL" value="{$klarnapayment.signInWithKlarna.redirectUrl|escape:'html':'UTF-8'}" class="klarna-redirect-url-text-input">
            <p class="help-block">
                {l s='Please add this URL to your list of allowed redirect URLs in the "Sign in with Klarna" settings on the ' mod='klarnapayment'}<a target="_blank" href="https://portal.klarna.com/">{l s='Klarna merchant portal' mod='klarnapayment'}</a>.
            </p>
        </div>
    </div>
</div>