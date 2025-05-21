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
<div class="osm-img-wrapper col-lg-3 col-md-6{if !$footerPreviewUrl} hidden{/if}">
    <div class="image-preview-label klarna-font">{l s='Footer preview' mod='klarnapayment'}</div>
    <div>
        <img class="osm-img osm-img-footer" data-key="{$footerPlacementKey}" src="{$footerPreviewUrl}">
    </div>
</div>
<div class="osm-img-wrapper col-lg-3 col-md-6{if !$topPreviewUrl} hidden{/if}">
    <div class="image-preview-label klarna-font">{l s='Top of page preview' mod='klarnapayment'}</div>
    <div>
        <img class="osm-img osm-img-topstrip" data-key="{$topPlacementKey}" src="{$topPreviewUrl}">
    </div>
</div>
<div class="osm-img-wrapper col-lg-3 col-md-6{if !$productPreviewUrl} hidden{/if}">
    <div class="image-preview-label klarna-font">{l s='Product page preview' mod='klarnapayment'}</div>
    <div>
        <img class="osm-img osm-img-product" data-key="{$productPlacementKey}" src="{$productPreviewUrl}">
    </div>
</div>
<div class="osm-img-wrapper col-lg-3 col-md-6{if !$cartPreviewUrl} hidden{/if}">
    <div class="image-preview-label klarna-font">{l s='Cart page preview' mod='klarnapayment'}</div>
    <div>
        <img class="osm-img osm-img-cart" data-key="{$cartPlacementKey}" src="{$cartPreviewUrl}">
    </div>
</div>
