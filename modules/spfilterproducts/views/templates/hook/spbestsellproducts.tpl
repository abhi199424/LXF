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
		<div class="spfilter-products 11 {$params['SPFP_CLS_SFX']}" id="spfilter_products_{$params['SPFP_IDMODULE']}">
        	
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
				{if !empty($products)}
					{if ($params['SPFP_SELECT_SOURCE'] == 'countdown_products')}
						<div class="spfp-countdown cf">	
						{include file="module:spfilterproducts/views/templates/hook/extends_countdown.tpl" specialPriceToDate=$params['SPFP_DATE_TO']}
						</div>
					{/if}
					{assign var="count_spproducts" value=count($products)}
					{assign var="row_check" value=0}
					<!--<div class="products spfp-products {(($params['SPFP_TYPE_SHOW'] == 2) ? 'owl-carousel ' : '')}">-->
                    <div class="spfp_box">
                    	{foreach from=$products item="product" name="spproducts"}
                    	<div class="col-lg-4">
                             {include file="module:spfilterproducts/views/templates/hook/productseller.tpl" product=$product params=$params}
                        </div>
                    	{/foreach}
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
			<div class="left_heading_sec">
				<h2>{$params['SPFP_TITLE_MODULE'][$id_lang]}</h2>
			</div>
			{if $allLinkURL}<a class="cta_button" href="{$allLinkURL}">Voir tous nos produits</a>{/if}
			<div class="container-fluid">
				<div class="row">
					<div class="col-lg-12 col-12">
						<div class="owl-carousel owl-theme sciage_slider">
							{foreach from=$products item="product" name="spproducts"}
							<div class="item">
								<div class="product_box">
									<img class="logo" src="/img/m/{$product.id_manufacturer}.jpg" alt="#">
									{if $product.has_discount}<span class="discount">{if $product.discount_type === 'percentage'}{$product.discount_percentage}{else}{$product.discount_amount_to_display}{/if}</span>{/if}
									<a class="picture_sec" href="{$product.url}">
										<img src="{$product.cover.bySize.large_default.url}" alt="{$product.name}">
									</a>
									<div class="text">
										<a class="title" href="{$product.url}">{$product.name}</a>
										<h6>{$product.description_short nofilter}</h6>
										<h4 class="price">{$product.price} <span>{if $product.has_discount}{$product.regular_price}{/if}</span></h4>
									</div>
									<div class="button">
										<a type="submit"><img class="cart" src="{$urls.child_img_url}shopping-cart.png" alt="#">Ajouter au panier</a>
									</div>
								</div>
							</div>
							{/foreach}
						</div>
					</div>
				</div>
			</div>
		</div>
	{/foreach}
{/if} 