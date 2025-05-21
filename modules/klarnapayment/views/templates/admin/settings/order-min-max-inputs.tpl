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
<div class="order-min-max-selector">
    <label class="control-label" for="minOrderValue">{l s='Order minimum value' mod='klarnapayment'}</label>
    <div class="input-group fixed-width-lg">
        <span class="input-group-addon price_unit">{$klarnapayment.currency_symbol|escape:'htmlall':'UTF-8'}</span>
        <input
                name="minOrderValue"
                class="form-control"
                value="{$klarnapayment.min_value|escape:'htmlall':'UTF-8'}"
                type="number"
                min="0"
                step="0.01"
                pattern="\d+(\.\d(1,2))?"
        >
    </div>
    <label class="control-label" for="maxOrderValue">{l s='Order maximum value' mod='klarnapayment'}</label>
    <div class="input-group fixed-width-lg">
        <span class="input-group-addon price_unit">{$klarnapayment.currency_symbol|escape:'htmlall':'UTF-8'}</span>
        <input
                name="maxOrderValue"
                class="form-control"
                value="{$klarnapayment.max_value|escape:'htmlall':'UTF-8'}"
                type="number"
                min="0"
                step="0.01"
                pattern="\d+(\.\d(1,2))?"
        >
    </div>
</div>
