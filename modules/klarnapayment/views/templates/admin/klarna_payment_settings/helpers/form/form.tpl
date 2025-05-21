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
{extends file="helpers/form/form.tpl"}

{block name="input_row"}
    {assign var="useOffset" value=!$input.name|in_array:['osm-images-preview', 'klarna-payments-preview', 'kec-image-preview']}

    <div class="klarna-input-wrapper{if $useOffset} col-lg-offset-3{/if}">
        {$smarty.block.parent}
    </div>
{/block}
