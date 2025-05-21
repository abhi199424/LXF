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
<div style="{$customStyles|default:''}" class="alert alert-warning" role="alert">
    {$warningBox.message|escape:'htmlall':'UTF-8'}<br>
    {if isset($warningBox.description)}
        {$warningBox.description|escape:'htmlall':'UTF-8'}
    {/if}
</div>