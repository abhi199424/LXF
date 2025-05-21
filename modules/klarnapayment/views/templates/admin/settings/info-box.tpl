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
<div class="alert alert-info" role="alert">
    {$infoBox.message|escape:'htmlall':'UTF-8'}<br>
    <a href="{$infoBox.link|escape:'htmlall':'UTF-8'}" target="_blank">{$infoBox.linkText|escape:'htmlall':'UTF-8'}</a>{if isset($infoBox.link2) && isset($infoBox.linkText2)} | <a href="{$infoBox.link2|escape:'htmlall':'UTF-8'}" target="_blank">{$infoBox.linkText2|escape:'htmlall':'UTF-8'}</a>{/if}
</div>
