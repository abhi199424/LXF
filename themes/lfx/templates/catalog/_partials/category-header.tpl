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
<div id="js-product-list-header">
    {if $listing.pagination.items_shown_from == 1}
        <div class="sec_heading title">
            <h1 class="h1">{$category.name}</h1>
        </div>
    {/if}
    {if $category.description}
        <div class="cat-desc-section">
            <div class="cat-desc-short">
                {$category.description|truncate:370:"..." nofilter}
            </div>
            <div class="cat-desc-full" style="display: none;">
                {$category.description nofilter}
            </div>
            <span class="read-more-toggle" onclick="toggleCatDesc(this)">Lire la suite</span>
        </div>
    {/if}


    
    {if $subcategories}
        <div class="row category-thumb">
            {foreach from=$subcategories item=subcategory}
            <div class="col">                
                    <a class="category-thumb-item" href="{$link->getCategoryLink($subcategory.id_category, $subcategory.link_rewrite)|escape:'html':'UTF-8'}">
                        <div class="category-item-picture" style="background-image: url(/img/c/{$subcategory.id_category}.jpg);">
                            
                        </div>
                        <div class="category-thumb-item-heading"><h3>{$subcategory.name|escape:'html':'UTF-8'}</h3></div>
                    </a>
                
            </div>
            {/foreach}
        </div>
    {/if}


</div>
