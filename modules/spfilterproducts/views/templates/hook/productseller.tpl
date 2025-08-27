{*
 * package SP Filter Products
 *
 * @version 1.0.0
 * @author    MagenTech http://www.magentech.com
 * @copyright (c) 2018 YouTech Company. All Rights Reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*}

{block name='product_miniature_item'}
{*hook h='displayProductListHomeReviews' product=$product*}
<div class="ProductItem ProductItemSmall row no-gutters">   
                                 <div class="col-5">
                                    <div class="ProductThumb">
                                    <div class="proMainLogo">
              <img src="/img/m/{$product.id_manufacturer}.jpg">
            </div>
            {if $product.cover}
              <img
                src = "{$product.cover.bySize.home_default.url}"
                alt = "{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name|truncate:30:'...'}{/if}"
                data-full-size-image-url = "{$product.cover.large.url}"
                class="nhover"
              >
              {if $product.images[1]}
                <img
                  src = "{$product.images[1]['bySize']['category_img']['url']}"
                  alt = "{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name}{/if}"
                  data-full-size-image-url = "{$product.images[1]}"
                  class="nhoverat"
                  style="display:none"
                >
              {else}
                <img
                  src = "{$product.images[1]}"
                  alt = "{if !empty($product.cover.legend)}{$product.cover.legend}{else}{$product.name}{/if}"
                  data-full-size-image-url = "{$product.images[1]}"
                  class="nhoverat"
                  style="display:none"
                >
              {/if}
        {else}
            <img
              src = "{$urls.no_picture_image.bySize.home_default.url}"
            >
        {/if}</div>
                                 </div>
                                 
                                 <div class="col-7 pl-2">
                                    <h2 class="product-name">
                                        <a href="{$product.url}">{$product.name}</a></h2>
                                        {hook h='displayProductPriceBlock' product=$product type="before_price"}

                                    <div class="product-review">
                                        {block name='product_reviews'}
          {hook h='displayProductListReviews' product=$product}
        {/block}
                                    </div>
                                   {* {if $smarty.get.dev}*}
              {hook h='cReviewProstardataDisplay' product=$product}
              <span class="Mn-badge">
                <span class="b1 cb1">{l s='Payez En' d='Shop.Theme.Catalog'}</span>
                <img src="/img/ou3.png" class="ou-img">
                <span class="b2 cb1">{l s='ou' d='Shop.Theme.Catalog'}</span>
                <img src="/img/ou4.png" class="ou-img">
                <span data-toggle="modal" data-target="#badge-modal1" class="b3 cb1">
                  {l s='sans frais' d='Shop.Theme.Catalog'}
                  <img src="/img/ouques.png" class="ou-img">
                </span>
              </span>
            {*{/if}*}
                                    <div class="{if $product.quantity == 0} product-out-of-stock {else} product-stock {/if}">{if $product.quantity == 0}  {l s='Rupture de stock' d='Shop.Theme.Catalog'}  {else} {l s='Disponible' d='Shop.Theme.Catalog'} {/if} </div>
                                    
                                    <hr>
                                    
                                    {block name='product_price_and_shipping'}
          {if $product.show_price}
            <div class="product-price-and-shipping">
              {if $product.has_discount}
                {hook h='displayProductPriceBlock' product=$product type="old_price"}

                <span class="sr-only">{l s='Regular price' d='Shop.Theme.Catalog'}</span>
                
                <!--{if $product.discount_type === 'percentage'}
                  <span class="discount-percentage discount-product">{$product.discount_percentage}</span>
                {elseif $product.discount_type === 'amount'}
                  <span class="discount-amount discount-product">{$product.discount_amount_to_display}</span>
                {/if}-->
              {/if}


              <span class="sr-only">{l s='Price' d='Shop.Theme.Catalog'}</span>
              <span itemprop="price" class="price">{$product.price}</span>
              {if $product.has_discount && $product.price != $product.regular_price}
			          <span class="regular-price nm">{$product.regular_price}</span>
              {/if}
              
              {hook h='displayProductPriceBlock' product=$product type='unit_price'}

              {hook h='displayProductPriceBlock' product=$product type='weight'}
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
<!--                    <i class="material-icons shopping-cart">&#xE547;</i>-->
                      <img src="/img/caddie.svg" alt="">
                        
                  </button>
                  </form>
            </div>
          {/if}
        {/block}
                                </div>
                            </div>

{/block}
