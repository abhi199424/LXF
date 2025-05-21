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
<div class="osm-infopage-input">
    <label class="control-label" for="{$osmInfoPage.configName}">{l s='On-site messaging infopage path' mod='klarnapayment'}</label>
    <div class="input-group fixed-width-lg">
        <span class="input-group-addon">{$osmInfoPage.baseUrl|escape:'htmlall':'UTF-8'}</span>
        <input
                name="{$osmInfoPage.configName}"
                class="form-control"
                value="{$osmInfoPage.infopagePath|escape:'htmlall':'UTF-8'}"
                type="text"
        >
    </div>
</div>
