{**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License 3.0 (AFL-3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/AFL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/AFL-3.0 Academic Free License 3.0 (AFL-3.0)
 *}
<div class="variant-links">
{assign var='total_variants' value=$variants|@count}
{assign var='display_limit' value=4}
{assign var='index' value=0}

{foreach from=$variants item=variant name=colorloop}
    {if $smarty.foreach.colorloop.index < $display_limit}
        {assign var='image_id' value=Product::getFirstAttributeImageId($variant.id_product_attribute)}
        <a href="#"
            class="{$variant.type} {*{if $product.id_product_attribute == $variant.id_product_attribute}selected-combination{/if}*}"
            title="{$variant.name}"
            aria-label="{$variant.name}"
            data-id-product-attribute="{$variant.id_product_attribute}"
            onclick="selectCombination({$variant.id_product_attribute}, this); return false;"
            {if isset($image_id)}
                data-img-str-label="{$link->getImageLink($product->link_rewrite, $image_id, 'cat_new')}"
            {/if}
            {if $variant.texture}
                style="background-image: url({$variant.texture})"
            {elseif $variant.html_color_code}
                style="background-color: {$variant.html_color_code}"
            {/if}
        ></a>
    {/if}
{/foreach}

{if $total_variants > $display_limit}
    <a href="{$product.url}" class="more-colors" title="View all colors">
        +{$total_variants - $display_limit}
    </a>
{/if}
</div>
