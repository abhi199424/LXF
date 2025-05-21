<div class="cart-hover-wrapper">
  <div id="_desktop_cart">
    <div class="blockcart cart-preview {if $cart.products_count > 0}active{else}inactive{/if}" data-refresh-url="{$refresh_url}">
      
      <div class="header">
        {if $cart.products_count > 0}
          <a rel="nofollow"
             aria-label="{l s='Shopping cart link containing %nbProducts% product(s)' sprintf=['%nbProducts%' => $cart.products_count] d='Shop.Theme.Checkout'}"
             href="{$cart_url}">
        {/if}
        
          <img src="{$urls.child_img_url}cart_icon.png" alt="cart icon"><br>
          <span class="mon_panier_link">Mon panier</span>
          <span class="cart-products-count">{$cart.products_count}</span>
        
        {if $cart.products_count > 0}
          </a>
        {/if}
      </div>

      {if $cart.products_count <= 0}
        <div class="cart-empty-message" style="background-image: url(/img/empty-box.png); display:none">
          <h3>Panier vide</h3>
        </div>
      {/if}

    </div>
  </div>
</div>

<!-- Place this outside the wrapper -->
<div id="screen-overlay-cart" style="display: none;"></div>
