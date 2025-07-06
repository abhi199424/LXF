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
<div id="js-product-list-top" class="row products-selection">
  <div class="col-lg-5 hidden-sm-down total-products">
  </div>
  <div class="col-lg-7">
    <div class="row sort-by-row">
    {if $listing.pagination.total_items > 1}
      <p>{l s='There are %product_count% products.' d='Shop.Theme.Catalog' sprintf=['%product_count%' => $listing.pagination.total_items]}</p>
    {elseif $listing.pagination.total_items > 0}
      <p>{l s='There is 1 product.' d='Shop.Theme.Catalog'}</p>
    {/if}
      {block name='sort_by'}
        {include file='catalog/_partials/sort-orders.tpl' sort_orders=$listing.sort_orders}
      {/block}
      {if !empty($listing.rendered_facets)}
        <div class="col-xs-4 col-sm-3 hidden-md-up filter-button total-products">
          <div class="filter" onclick="openNav()"><img src="{$urls.child_img_url}filter_icon.png" alt="#">Filtre</div>
        </div>
      {/if}
    </div>
  </div>
  <div class="col-sm-12 hidden-md-up text-sm-center showing">
    {l s='Showing %from%-%to% of %total% item(s)' d='Shop.Theme.Catalog' sprintf=[
    '%from%' => $listing.pagination.items_shown_from ,
    '%to%' => $listing.pagination.items_shown_to,
    '%total%' => $listing.pagination.total_items
    ]}
  </div>
  {if Context::getContext()->getDevice() == 4}
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
  {/if}
</div>

