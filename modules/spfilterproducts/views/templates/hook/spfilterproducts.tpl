{*
 * package SP Filter Products
 *
 * @version 1.0.0
 * @author    MagenTech http://www.magentech.com
 * @copyright (c) 2018 YouTech Company. All Rights Reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*}
{*{if isset($list) && !empty($list)}
	 {foreach from=$list item=items}
		{$params = $items['params']}
		{$products = $items['products']}
		<div class="spfilter-products {$params['SPFP_CLS_SFX']}" id="spfilter_products_{$params['SPFP_IDMODULE']}">
        	
			<div class="spfp-wrap"
				{foreach from=$params item=param key=k} 
					{if !is_array($param)}
						data-{$k} = "{$param}" 
					{/if}
				{/foreach}
				>
				{if $params['SPFP_DISPLAY_TITLE']}
					 <div class="title_line">
						<h1 class="h1">{$params['SPFP_TITLE_MODULE'][$id_lang]}</h1>
					</div>
				{/if}
                {if $allLinkURL}
                <div class="AllProductLink">
					<a href="{$allLinkURL}">{if $language.iso_code == 'en'} {l s='View all products' d='Shop.Theme.Global'}  {else} {l s='Voir tous les produits' d='Shop.Theme.Global'} {/if} <i class="fa icon-long-arrow-right" aria-hidden="true"></i></a>
				</div>
                {/if}
				{if !empty($products)}
					{if ($params['SPFP_SELECT_SOURCE'] == 'countdown_products')}
						<div class="spfp-countdown cf">	
						{include file="module:spfilterproducts/views/templates/hook/extends_countdown.tpl" specialPriceToDate=$params['SPFP_DATE_TO']}
						</div>
					{/if}
					{assign var="count_spproducts" value=count($products)}
					{assign var="row_check" value=0}
					<!--<div class="products spfp-products {(($params['SPFP_TYPE_SHOW'] == 2) ? 'owl-carousel ' : '')}">-->
						{if $page.page_name == 'index'}
					<div class="spfp_box_static">
						<ul class="itemsTatic product_list grid">
						{foreach from=$products item="product" name="spproducts"}
						{if $row_check % $params['SPFP_ROW'] == 0 || $params['SPFP_ROW'] == 1}
							<li class="item productlistitem">
						{/if}
								{include file="module:spfilterproducts/views/templates/hook/product.tpl" product=$product params=$params}
						{assign var="row_check" value=$row_check + 1}
						{if $row_check % $params['SPFP_ROW'] == 0 || $params['SPFP_ROW'] == 1 || $smarty.foreach.spproducts.iteration == $count_spproducts}
							</li>
						{/if}
						{/foreach}
                        </ul>
					<!--</div>-->
                    </div>
                    {/if}

                    <div class="spfp_box">
						<ul id="owl-slider" class="owl-carousel product_list grid">
						{foreach from=$products item="product" name="spproducts"}
						{if $row_check % $params['SPFP_ROW'] == 0 || $params['SPFP_ROW'] == 1}
							<li class="item">
						{/if}
								{include file="module:spfilterproducts/views/templates/hook/product.tpl" product=$product params=$params}
						{assign var="row_check" value=$row_check + 1}
						{if $row_check % $params['SPFP_ROW'] == 0 || $params['SPFP_ROW'] == 1 || $smarty.foreach.spproducts.iteration == $count_spproducts}
							</li>
						{/if}
						{/foreach}
                        </ul>
					<!--</div>-->
                    </div>
				{else}
					{l s="Has no content to show! - Module SP Filter Products - ID {$params['SPFP_IDMODULE']}"}
				{/if}
			</div>
            
		</div>
	{/foreach}
{/if}*}

{if isset($list) && !empty($list)}
	{foreach from=$list item=items}
		{$params = $items['params']}
		{$products = $items['products']}
		<div class="product_slider_sec {$params['SPFP_CLS_SFX']}">
			<div class="sec_heading title">
				<h2>{$params['SPFP_TITLE_MODULE'][$id_lang]}</h2>
			</div>
			{if $allLinkURL}<a class="cta_button" href="{$allLinkURL}">Voir tous nos produits</a>{/if}
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12 col-12">
						<div class="owl-carousel owl-theme products_slider">
							{foreach from=$products item="product" name="spproducts"}
							<div class="item">
								<article class="product-miniature js-product-miniature" data-id-product="{$product.id_product}" data-id-product-attribute="{$product.id_product_attribute}">
									<div class="p_box">
										<div class="thumbnail-container">
										<div class="thumbnail_right_cont">
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
      
            {hook h='displayLiveVersionBanner' id_category=$product.id_category_default id_product=$product.id_product}

      </div>
										{block name='product_thumbnail'}
											<div class="thumbnail-wrapper">
											{*{if $product.cover}
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
											{/if}*}
											{if isset($product.images) && $product.images|@count > 1}
												<a href="{$product.url}" class="thumbnail product-thumbnail product_pic">
												<div class="product-image-swap">
													{assign var="imageCount" value=0}
													{foreach from=$product.images item=image}
													{if $imageCount < 2}
														<picture class="{if $imageCount == 0}product-img-default{else}product-img-hover{/if}">
														{if !empty($image.bySize.cat_new.sources.avif)}
															<source srcset="{$image.bySize.cat_new.sources.avif}" type="image/avif">
														{/if}
														{if !empty($image.bySize.cat_new.sources.webp)}
															<source srcset="{$image.bySize.cat_new.sources.webp}" type="image/webp">
														{/if}
														<img
															src="{$image.bySize.cat_new.url}"
															alt="{if !empty($image.legend)}{$image.legend}{else}{$product.name|truncate:30:'...'}{/if}"
															loading="lazy"
															data-full-size-image-url="{$image.large.url}"
															width="100%"
															height="auto"
														/>
														</picture>
														{assign var="imageCount" value=$imageCount+1}
													{/if}
													{/foreach}
												</div>
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
												<img class="cart" src="{$urls.child_img_url}shopping-cart.png" alt="cart-ic" style="width: 30px">+ Ajouter
												</button>
												{*{if $productQuantity > 0}
												  <span class="badge badge-info ml-2">{$productQuantity}</span>
												{/if}*}
											  </form>
											</div>
											</div>
										  {/block}
											<div class="product_lunch_date section-vhtype-date">
											{widget name='prospectextgenerator' id_product=$product.id id_category=$product.id_category_default type=1}
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
													{if $product.has_discount}
													{math assign="price_disc" equation="((y - x))" x=$product.price_amount y=$product.regular_price_amount}
													<span class="discount-text-ecs">{l s='Économisez' d='Shop.Theme.Catalog'} {Tools::displayPrice($price_disc)}</span>
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
												</div>
												{assign var=productQuantity value=0}
												{foreach from=$cart.products item=cartProduct}
												{if $cartProduct.id_product == $product.id}
													{assign var=productQuantity value=$cartProduct.quantity}
												{/if}
												{/foreach}        
												<div class="cart_button mobile_cart_button">
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
        <img class="cart" src="{$urls.child_img_url}shopping-cart.png" alt="#" style="width: 30px">+ Ajouter
        </button>
      </form>
    </div>    
											</div>
									
											<div class="product_btmsec">
											<div class="left_cont">
												<p>Payer en 3 versements de 400,00 €,  sans frais. <img src="/img/klarma.png">  <a href="#">En savoir plus</a></p>
											</div>
											<div class="right_cont">
											{if $smarty.get.liv}
											  	{widget name='listcontent' id_product=$product.id id_category=$product.id_category_default}
											{else}
												{widget name='chtmlmanager' id_product=$product.id}
											{/if}
											</div>
											</div>
											
									
										</div>
									</div>
								</article>
							</div>
							{/foreach}
							
						</div>
					</div>
					<!-- <div class="owl-nav">
								<button type="button" role="presentation" class="owl-prev"><span aria-label="Previous">‹</span>
								</button>
								<button type="button" role="presentation" class="owl-next"><span aria-label="Next">›</span>
								</button>
							</div> -->
				</div>
			</div>
		</div>
	{/foreach}
{/if}  