<div class="pop-mini-load">
  <div class="overly-mini-cart-pop"></div>
  <img class="loader-mini" src="/modules/minicart/loader.gif"/>
</div>
<button type="button" class="close" data-dismiss="modal" aria-label="{l s='Close' d='Shop.Theme.Global'}">
  <img style="width: 25px; margin-right: 20px;" src="/modules/minicart/views/img/close-icon.svg">
</button>
<div class="cart_pop_heading">
  {if $cart.products|count > 1}
       Mon panier {$cart.products|count} articles
  {else if $cart.products|count == 1}
       Mon panier {$cart.products|count} article
  {else}
       Mon panier ({$cart.products|count})
  {/if}
</div>
{if $cart.products}
<div class="upcart-products-section">
   <div class="upcart-products-section-items">
     {assign var="total_discount" value=0}
     {foreach $cart.products as $prd}
      <div class="upcart-products-section-item">
         <div class="delete-icon">
             <a
                 class                       = "remove-from-cart-mini"
                 rel                         = "nofollow"
                 href                        = "javascript:void(0);"
                 data-link-action            = "delete-from-cart-mini"
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
                                 class="js-cart-line-product-quantity-mini"
                                 data-id-product="{$prd.id_product}"
                                 data-id-product-attribute ="{$prd.id_product_attribute}"
                                 type="number"
                                 value="{$prd.quantity}"
                                 name="product-quantity-spin-mini"
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
   <div class="bottom_total_amount">Total: <span style="color: #d10404">{$cart.subtotals.products['value']}</span></div>
   <div class="cart-popup-bottom-btn_group">
      <a href="{$base_url}" class="btn btn-dark-outline">Continuer vos achats</a>
      <a href="{$cart_url}" class="btn btn-primary"><img src="/themes/lfx/assets/img/check.png" alt="cart-btn" class="btn-ticker"> Voir le panier</a>
   </div>
</div>

{literal}
  <script>
    $(document).ready(function () {
        setTimeout(function () {
          $('.js-cart-line-product-quantity-mini').each(function () {
            if (!$(this).parent().hasClass('bootstrap-touchspin')) {
              $(this).TouchSpin({
                verticalbuttons: true,
                buttondown_class: 'btn btn-touchspin js-touchspin-down',
                buttonup_class: 'btn btn-touchspin js-touchspin-up',
                min: 0
              });
            }
          });
        }, 200);
      });
  </script>
  {/literal}
