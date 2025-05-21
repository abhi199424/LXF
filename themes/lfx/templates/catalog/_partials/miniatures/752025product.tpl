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
{block name='product_miniature_item'}
{*<div class="js-product product{if !empty($productClasses)} {$productClasses}{/if}">
  <article class="product-miniature js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}">
    <div class="thumbnail-container">
      <div class="thumbnail-top">
        {block name='product_thumbnail'}
          {if $product.cover}
            <a href="{$product.url}" class="thumbnail product-thumbnail">
              <picture>
                {if !empty($product.cover.bySize.home_default.sources.avif)}<source srcset="{$product.cover.bySize.home_default.sources.avif}" type="image/avif">{/if}
                {if !empty($product.cover.bySize.home_default.sources.webp)}<source srcset="{$product.cover.bySize.home_default.sources.webp}" type="image/webp">{/if}
                <img
                  src="{$product.cover.bySize.home_default.url}"
                  alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
                  loading="lazy"
                  data-full-size-image-url="{$product.cover.large.url}"
                  width="{$product.cover.bySize.home_default.width}"
                  height="{$product.cover.bySize.home_default.height}"
                />
              </picture>
            </a>
          {else}
            <a href="{$product.url}" class="thumbnail product-thumbnail">
              <picture>
                {if !empty($urls.no_picture_image.bySize.home_default.sources.avif)}<source srcset="{$urls.no_picture_image.bySize.home_default.sources.avif}" type="image/avif">{/if}
                {if !empty($urls.no_picture_image.bySize.home_default.sources.webp)}<source srcset="{$urls.no_picture_image.bySize.home_default.sources.webp}" type="image/webp">{/if}
                <img
                  src="{$urls.no_picture_image.bySize.home_default.url}"
                  loading="lazy"
                  width="{$urls.no_picture_image.bySize.home_default.width}"
                  height="{$urls.no_picture_image.bySize.home_default.height}"
                />
              </picture>
            </a>
          {/if}
        {/block}

        <div class="highlighted-informations{if !$product.main_variants} no-variants{/if}">
          {block name='quick_view'}
            <a class="quick-view js-quick-view" href="#" data-link-action="quickview">
              <i class="material-icons search">&#xE8B6;</i> {l s='Quick view' d='Shop.Theme.Actions'}
            </a>
          {/block}

          {block name='product_variants'}
            {if $product.main_variants}
              {include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
            {/if}
          {/block}
        </div>
      </div>

      <div class="product-description">
        {block name='product_name'}
          {if $page.page_name == 'index'}
            <h3 class="h3 product-title"><a href="{$product.url}" content="{$product.url}">{$product.name|truncate:30:'...'}</a></h3>
          {else}
            <h2 class="h3 product-title"><a href="{$product.url}" content="{$product.url}">{$product.name|truncate:30:'...'}</a></h2>
          {/if}
        {/block}

        {block name='product_price_and_shipping'}
          {if $product.show_price}
            <div class="product-price-and-shipping">
              {if $product.has_discount}
                {hook h='displayProductPriceBlock' product=$product type="old_price"}

                <span class="regular-price" aria-label="{l s='Regular price' d='Shop.Theme.Catalog'}">{$product.regular_price}</span>
                {if $product.discount_type === 'percentage'}
                  <span class="discount-percentage discount-product">{$product.discount_percentage}</span>
                {elseif $product.discount_type === 'amount'}
                  <span class="discount-amount discount-product">{$product.discount_amount_to_display}</span>
                {/if}
              {/if}

              {hook h='displayProductPriceBlock' product=$product type="before_price"}

              <span class="price" aria-label="{l s='Price' d='Shop.Theme.Catalog'}">
                {capture name='custom_price'}{hook h='displayProductPriceBlock' product=$product type='custom_price' hook_origin='products_list'}{/capture}
                {if '' !== $smarty.capture.custom_price}
                  {$smarty.capture.custom_price nofilter}
                {else}
                  {$product.price}
                {/if}
              </span>

              {hook h='displayProductPriceBlock' product=$product type='unit_price'}

              {hook h='displayProductPriceBlock' product=$product type='weight'}
            </div>
          {/if}
        {/block}

        {block name='product_reviews'}
          {hook h='displayProductListReviews' product=$product}
        {/block}
      </div>

      {include file='catalog/_partials/product-flags.tpl'}
    </div>
  </article>
</div>*}

{if $slider}
<div class="p_box">
  {hook h='displayProductActions' product=$product}
  <a class="product_pic" href="{$product.url}">
    <img src="{$product.cover.bySize.large_default.url}" alt="{$product.name}">
  </a>
  <h4><a href="{$product.url}">{$product.name}</a></h4>
  <div class="btm_sec">
    <div class="price">{$product.price}</div>
    <div class="cart_button">
      <form action="{$urls.pages.cart}" method="post" class="RefreshForm">
        <input type="hidden" name="token" value="{$static_token}">
        <input type="hidden" name="id_product" value="{$product.id}">
        <input type="hidden" name="id_customization" value="{$product.id_customization}">
        <button
        class="btn btn-primary add-to-cart newimgbuttonaddtocart"
        data-button-action="add-to-cart"
        type="submit"
        {if !$product.add_to_cart_url}
        disabled
        {/if}
        >
        <img class="cart" src="{$urls.child_img_url}shopping-cart.png" alt="#" style="width: 30px">Ajouter au panier
        </button>
      </form>
    </div>
  </div>
</div>
{else}
<div class="js-product product{if !empty($productClasses)} {$productClasses}{/if}">
  <article class="product-miniature js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}">
    <div class="p_box">
      <div class="thumbnail-container">
      {if $product.has_discount}
        {if $product.discount_type === 'percentage'}
          <span class="discount-percentage discount-product">{$product.discount_percentage}</span>
        {elseif $product.discount_type === 'amount'}
          {math assign="non_formated" equation="((y - x)/y)*100" x=$product.price_amount y=$product.regular_price_amount}
          {$red_formated = $non_formated|round:0}
          <span class="discount-amount discount-product percentage-amp">-{$red_formated}%</span>
          <span class="discount-amount discount-product amount-amp" style="display: none;">{$product.discount_amount_to_display}</span>
        {/if}
      {/if}
        {block name='product_thumbnail'}
          <div class="thumbnail-wrapper">
          {if $product.cover}
            <a href="{$product.url}" class="thumbnail product-thumbnail product_pic">
              <picture>
                {if !empty($product.cover.bySize.cat_new.sources.avif)}<source srcset="{$product.cover.bySize.cat_new.sources.avif}" type="image/avif">{/if}
                {if !empty($product.cover.bySize.cat_new.sources.webp)}<source srcset="{$product.cover.bySize.cat_new.sources.webp}" type="image/webp">{/if}
                <img
                  src="{$product.cover.bySize.cat_new.url}"
                  alt="{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
                  loading="lazy"
                  data-full-size-image-url="{$product.cover.large.url}"
                  width="{$product.cover.bySize.cat_new.width}"
                  height="{$product.cover.bySize.cat_new.height}"
                />
              </picture>
            </a>
          {else}
            <a href="{$product.url}" class="thumbnail product-thumbnail product_pic">
              <picture>
                {if !empty($urls.no_picture_image.bySize.home_default.sources.avif)}<source srcset="{$urls.no_picture_image.bySize.home_default.sources.avif}" type="image/avif">{/if}
                {if !empty($urls.no_picture_image.bySize.home_default.sources.webp)}<source srcset="{$urls.no_picture_image.bySize.home_default.sources.webp}" type="image/webp">{/if}
                <img
                  src="{$urls.no_picture_image.bySize.home_default.url}"
                  loading="lazy"
                  width="{$urls.no_picture_image.bySize.home_default.width}"
                  height="{$urls.no_picture_image.bySize.home_default.height}"
                />
              </picture>
            </a>
          {/if}
          <div class="cart_button thumb-info" style="display: none;">
            <form action="{$urls.pages.cart}" method="post" class="RefreshForm">
              <input type="hidden" name="token" value="{$static_token}">
              <input type="hidden" name="id_product" value="{$product.id}">
              <input type="hidden" name="id_customization" value="{$product.id_customization}">
              <input class="pro-combination-sec" type="hidden" name="id_product_attribute" value="{$product.id_product_attribute}">
              <button
              class="btn btn-primary add-to-cart newimgbuttonaddtocart"
              data-button-action="add-to-cart"
              type="submit"
              {if !$product.add_to_cart_url}
              disabled
              {/if}
              >
              <img class="cart" src="{$urls.child_img_url}shopping-cart.png" alt="cart-ic" style="width: 30px">Ajouter au panier
              </button>
              {*{if $productQuantity > 0}
                <span class="badge badge-info ml-2">{$productQuantity}</span>
              {/if}*}
            </form>
          </div>
          </div>
        {/block}

        <div class="product_lunch_date section-vhtype-date">
          {widget name='prospectextgenerator' id_product=$product.id id_category=$category.id type=1}
        </div>
        {widget name='prospecproductwisegen' id_product=$product.id}
      </div>

      <div class="product_content">

        <div class="avr-link">
        {block name='product_variants'}
          {if $product.main_variants}
            {include file='catalog/_partials/variant-links.tpl' variants=$product.main_variants}
          {/if}
        {/block}
        <div class="trust-piolet-sec">
            <img src="/img/trustpilot.png">
        </div>
        </div>
        <h4>
          {block name='product_name'}
            <a href="{$product.url}" content="{$product.url}">{$product.name}</a>
          {/block}
        </h4>
        <div class="prose-text-sec">
        {widget name='prospectextgenerator' id_product=$product.id id_category=$product.id_category_default type=2}
        </div>
        <div class="btm_sec">
            <div class="price">
            {block name='product_price_and_shipping'}
              {if $product.show_price}
                <div class="product-price-and-shipping">
                  {if $product.has_discount}
                    {hook h='displayProductPriceBlock' product=$product type="old_price"}
    
                    <span class="regular-price" aria-label="{l s='Regular price' d='Shop.Theme.Catalog'}">{$product.regular_price}</span>
                    {if $product.discount_type === 'percentage'}
                      <span class="discount-percentage discount-product">{$product.discount_percentage}</span>
                    {elseif $product.discount_type === 'amount'}
                      <span class="discount-amount discount-product">{$product.discount_amount_to_display}</span>
                    {/if}
                  {/if}
    
                  {hook h='displayProductPriceBlock' product=$product type="before_price"}
    
                  <span class="price" aria-label="{l s='Price' d='Shop.Theme.Catalog'}">
                    {capture name='custom_price'}{hook h='displayProductPriceBlock' product=$product type='custom_price' hook_origin='products_list'}{/capture}
                    {if '' !== $smarty.capture.custom_price}
                      {$smarty.capture.custom_price nofilter}
                    {else}
                      {$product.price}
                    {/if}
                  </span>
                  {if $product.has_discount}
                    {math assign="price_disc" equation="((y - x))" x=$product.price_amount y=$product.regular_price_amount}
                    <span class="discount-text-ecs">{l s='Économisez' d='Shop.Theme.Catalog'} {Tools::displayPrice($price_disc)}</span>
                  {/if}
    
                  {hook h='displayProductPriceBlock' product=$product type='unit_price'}
    
                  {hook h='displayProductPriceBlock' product=$product type='weight'}
                </div>
              {/if}
            {/block}
            </div>
            {assign var=productQuantity value=0}
            {foreach from=$cart.products item=cartProduct}
              {if $cartProduct.id_product == $product.id}
                {assign var=productQuantity value=$cartProduct.quantity}
              {/if}
            {/foreach}            
        </div>

        <div class="product_btmsec">
          <div class="left_cont">
            <p>Payer en 3 versements de 400,00 €,  sans frais. <img src="/img/klarma.png">  <a href="#">En savoir plus</a></p>
          </div>
          <div class="right_cont">
            {widget name='chtmlmanager' id_product=$product.id}
          </div>
        </div>
        

      </div>
    </div>
  </article>
</div>
{/if}
{/block}
