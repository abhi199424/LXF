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
<div id="blockcart-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
<div class="cart_pop_container modal-fact" style="opacity: 1; display: block;transform: unset;">
<button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' d='Shop.Theme.Global'}">
  <img style="width: 25px; margin-right: 20px;" src="/modules/minicart/views/img/close-icon.svg">
</button>
<div class="cart_pop_heading">
   Mon panier ({$cart.products|count})
</div>
{if $cart.products}
<div class="upcart-products-section">
   <div class="upcart-products-section-items">
     {assign var="total_discount" value=0}
     {foreach $cart.products as $prd}
      <div class="upcart-products-section-item">
         <div class="delete-icon">
             <a
                 class                       = "remove-from-cart"
                 rel                         = "nofollow"
                 href                        = "{$prd.remove_from_cart_url}"
                 data-link-action            = "delete-from-cart"
                 data-id-product             = "{$prd.id_product|escape:'javascript'}"
                 data-id-product-attribute   = "{$prd.id_product_attribute|escape:'javascript'}"
                 data-id-customization   	  = "{$prd.id_customization|escape:'javascript'}"
             >
                 <i class="material-icons float-xs-left">delete</i>
             </a>
         </div>
         <div class="Picture__item">              
             <img
                 src="{$prd.default_image.medium.url}"
                 data-full-size-image-url="{$prd.default_image.large.url}"
                 title="{$prd.default_image.legend}"
                 alt="{$prd.default_image.legend}"
                 loading="lazy"
                 class="product-image"
             >
         </div>
         <div class="cart__item">
            <h3><a href="{$prd.url}">{$prd.name}</a></h3>
            {foreach from=$prd.attributes key="attribute" item="value"}
              <div class="product-line-info {$attribute|lower}">
                <span class="label">{$attribute}:</span>
                <span class="value">{$value}</span>
              </div>
            {/foreach}
            <div class="qnty_item_section">
                 <div class="product-qtycont">
                     <div class="qty-container">
                         <div class="qty-box">
                             <input
                                 class="js-cart-line-product-quantity"
                                 data-down-url="{$prd.down_quantity_url}"
                                 data-up-url="{$prd.up_quantity_url}"
                                 data-update-url="{$prd.update_quantity_url}"
                                 data-product-id="{$prd.id_product}"
                                 type="number"
                                 value="{$prd.quantity}"
                                 name="product-quantity-spin"
                             />
                         </div>
                     </div>
                 </div>
                 <div class="total-pro-section">
                     {if $prd.has_discount}
                     {hook h='displayProductPriceBlock' product=$prd type="old_price"}
                     {math assign="price_total_regular" equation="((x * y))" x=$prd.quantity y=$prd.price_without_reduction}
                     <span class="regular-price" aria-label="{l s='Regular price' d='Shop.Theme.Catalog'}">{Tools::displayPrice($price_total_regular)}</span>
                   {/if}
                   <span class="price" aria-label="{l s='Price' d='Shop.Theme.Catalog'}">
                     {capture name='custom_price'}{hook h='displayProductPriceBlock' product=$prd type='custom_price' hook_origin='products_list'}{/capture}
                     {if '' !== $smarty.capture.custom_price}
                     {$smarty.capture.custom_price nofilter}
                     {else}
                      {math assign="price_total" equation="((x * y))" x=$prd.quantity y=$prd.price_wt}
                      {Tools::displayPrice($price_total)}
                     {/if}
                 </span>
                 {if $prd.has_discount}
                     {math assign="price_disc" equation="((y - x))" x=$prd.price_amount y=$prd.regular_price_amount}
                     {math assign="price_disc_final" equation="((x * y))" x=$price_disc y=$prd.quantity}
                     {math assign="total_discount" equation="x + y" x=$total_discount y=$price_disc_final}
                     <span class="discount-text-ecs">{l s='Économisez' d='Shop.Theme.Catalog'} {Tools::displayPrice($price_disc_final)}</span>
                 {/if}
                 </div>
            </div>
         </div>
      </div>
     {/foreach}
   </div>
</div>
{else}
  <span class="empty-cart-text">Votre panier est vide</span>
{/if}
<div class="cart-popup-bottom">
{$cart.totals.discounts.amount}
 {if $total_discount}
     <div class="discount-text-ecs">{l s='Économisez' d='Shop.Theme.Catalog'} {Tools::displayPrice($total_discount)}</div>
 {/if}
   <div class="bottom_total_amount">Sous- total: <span style="color: #d10404">{$cart.totals.total_including_tax.value}</span></div>
   <div class="cart-popup-bottom-btn_group">
      <a href="{$base_url}" class="btn btn-dark-outline">Continuer vos achats</a>
      <a href="{$cart_url}" class="btn btn-primary"><img src="/themes/lfx/assets/img/check.png" alt="cart-btn" class="btn-ticker"> Voir le panier</a>
   </div>
</div>
</div>
</div>

{literal}
  <script>
    $(document).ready(function () {
        setTimeout(function () {
          $('.js-cart-line-product-quantity').each(function () {
            if (!$(this).parent().hasClass('bootstrap-touchspin')) {
              $(this).TouchSpin({
                verticalbuttons: true,
                buttondown_class: 'btn btn-touchspin js-touchspin-down',
                buttonup_class: 'btn btn-touchspin js-touchspin-up',
                min: 1
              });
            }
          });
        }, 200);
      });
  </script>
  {/literal}
