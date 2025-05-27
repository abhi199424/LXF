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
{extends file=$layout}

{block name='head' append}
  <meta property="og:type" content="product">
  {if $product.cover}
    <meta property="og:image" content="{$product.cover.large.url}">
  {/if}

  {if $product.show_price}
    <meta property="product:pretax_price:amount" content="{$product.price_tax_exc}">
    <meta property="product:pretax_price:currency" content="{$currency.iso_code}">
    <meta property="product:price:amount" content="{$product.price_amount}">
    <meta property="product:price:currency" content="{$currency.iso_code}">
  {/if}
  {if isset($product.weight) && ($product.weight != 0)}
  <meta property="product:weight:value" content="{$product.weight}">
  <meta property="product:weight:units" content="{$product.weight_unit}">
  {/if}
{/block}

{block name='head_microdata_special'}
  {include file='_partials/microdata/product-jsonld.tpl'}
{/block}

{block name='content'}

  <section id="main">

    <meta content="{$product.url}">

    <div class="row product-container js-product-container product_details_sec">     
    {block name='breadcrumb'}
        {include file='_partials/breadcrumb.tpl'}
      {/block}
      <div class="col-md-9">
        {block name='page_content_container'}
          <section class="page-content" id="content">
          {if Context::getContext()->getDevice() == 1}
            {block name='page_content'}
              {include file='catalog/_partials/product-flags.tpl'}

              {block name='product_cover_thumbnails'}
                {include file='catalog/_partials/product-cover-thumbnails.tpl'}
              {/block}
              <div class="scroll-box-arrows">
                <i class="material-icons left">&#xE314;</i>
                <i class="material-icons right">&#xE315;</i>
              </div>

            {/block}
          {/if}
          </section>
        {/block}
        <div class="custom-content-pro">
        {hook h="displayProductExtraContent" product=$product}
        </div>
        <div class="accordion" id="proAccordian">
          <span id="description" class="description_selflink"></span>
          <!-- First Item -->
          <div class="card">
              <div class="card-header" id="headingOne">
                  <h5 class="mb-0">
                      <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        {l s='Description' d='Shop.Theme.Catalog'}
                      </button>
                  </h5>
              </div>

              <div id="collapseOne" class="collapse in" aria-labelledby="headingOne" data-parent="#proAccordian">
                  <div class="card-body">
                    {$product.description nofilter}
                  </div>
              </div>
          </div>

          {*<!-- Second Item -->
          <div class="card">
              <div class="card-header" id="headingTwo">
                  <h5 class="mb-0">
                      <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        {l s='Product Details' d='Shop.Theme.Catalog'}
                      </button>
                  </h5>
              </div>

              <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#proAccordian">
                  <div class="card-body">
                  {block name='product_details'}
                    {include file='catalog/_partials/product-details.tpl'}
                  {/block}
                  </div>
              </div>
          </div>*}

          <!-- Third Item -->
          {capture name="phtml_content"}
              {widget name='phtmlmanager' id_product=$product.id}
          {/capture}
          
          {assign var="phtml_content_output" value=$smarty.capture.phtml_content|trim}
          {if $phtml_content_output != ''}
          <div class="card">
              <div class="card-header" id="headingThree">
                  <h5 class="mb-0">
                      <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                      {l s='Spécifications techniques' d='Shop.Theme.Catalog'}
                      </button>
                  </h5>
              </div>

              <div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#proAccordian">
                  <div class="card-body">
                    {widget name='phtmlmanager' id_product=$product.id}
                  </div>
              </div>
          </div>
          {/if}
          <!-- Seventh Item -->

          {capture name="sparepartspec_content"}
              {widget name='sparepartspec' id_product=$product.id}
          {/capture}
          
          {assign var="sparepartspec_output" value=$smarty.capture.sparepartspec_content|trim}
          {if $sparepartspec_output != ''}
          <div class="card">
              <div class="card-header" id="headingSeventh">
                  <h5 class="mb-0">
                      <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseSeventh" aria-expanded="false" aria-controls="collapseSeventh">
                      {l s="Spare Part Spec" d='Shop.Theme.Catalog'}
                      </button>
                  </h5>
              </div>

              <div id="collapseSeventh" class="collapse" aria-labelledby="headingSeventh" data-parent="#proAccordian">
                  <div class="card-body">
                    {widget name='sparepartspec' id_product=$product.id}
                  </div>
              </div>
          </div>
          {/if}
          
          <!-- Fourth Item -->

          {capture name="prospecgeohtml_content"}
              {widget name='prospecgeohtml' id_product=$product.id}
          {/capture}
          
          {assign var="prospecgeohtml_output" value=$smarty.capture.prospecgeohtml_content|trim}
          {if $prospecgeohtml_output != ''}
          <div class="card">
              <div class="card-header" id="headingFour">
                  <h5 class="mb-0">
                      <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                      {l s='Géométries' d='Shop.Theme.Catalog'}
                      </button>
                  </h5>
              </div>

              <div id="collapseFour" class="collapse" aria-labelledby="headingFour" data-parent="#proAccordian">
                  <div class="card-body">
                    {widget name='prospecgeohtml' id_product=$product.id}
                  </div>
              </div>
          </div>
          {/if}

           <!-- Fifth Item -->

          {capture name="productmanualtechhtml_content"}
              {widget name='productmanualtechhtml' id_product=$product.id}
          {/capture}
          
          {assign var="productmanualtechhtml_output" value=$smarty.capture.productmanualtechhtml_content|trim}
          {if $productmanualtechhtml_output != ''}
          <div class="card">
              <div class="card-header" id="headingFifth">
                  <h5 class="mb-0">
                      <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseFifth" aria-expanded="false" aria-controls="collapseFifth">
                      {l s="Manuel d'utilisation" d='Shop.Theme.Catalog'}
                      </button>
                  </h5>
              </div>

              <div id="collapseFifth" class="collapse" aria-labelledby="headingFour" data-parent="#proAccordian">
                  <div class="card-body">
                    {widget name='productmanualtechhtml' id_product=$product.id}
                  </div>
              </div>
          </div>
          {/if}
          <!-- Sixth Item -->

          {capture name="utilisationreco_content"}
              {widget name='utilisationreco' id_product=$product.id}
          {/capture}
          
          {assign var="utilisationreco_output" value=$smarty.capture.utilisationreco_content|trim}
          {if $utilisationreco_output != ''}
          <div class="card">
              <div class="card-header" id="headingSixth">
                  <h5 class="mb-0">
                      <button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseSixth" aria-expanded="false" aria-controls="collapseSixth">
                      {l s="Recommandations d’utilisation" d='Shop.Theme.Catalog'}
                      </button>
                  </h5>
              </div>

              <div id="collapseSixth" class="collapse" aria-labelledby="headingSixth" data-parent="#proAccordian">
                  <div class="card-body">
                    {widget name='utilisationreco' id_product=$product.id}
                  </div>
              </div>
          </div>
          {/if}
          
        </div>

        </div>
        <div class="col-md-4">
            <div id="sidebar">
          {if Context::getContext()->getDevice() != 1}
            {block name='page_content'}
              {include file='catalog/_partials/product-flags.tpl'}

              {block name='product_cover_thumbnails'}
                {include file='catalog/_partials/product-cover-thumbnails.tpl'}
              {/block}
              <div class="scroll-box-arrows">
                <i class="material-icons left">&#xE314;</i>
                <i class="material-icons right">&#xE315;</i>
              </div>

            {/block}
          {/if}
          {block name='page_header_container'}
            {block name='page_header'}
              <h1 class="h1 product_heading">{block name='page_title'}{$product.name}{/block}</h1>
            {/block}
          {/block}
          {if $product.reference}
          <span class="reference-section">Réf. : {$product.reference}</span>
          {/if}
          {block name='product_prices'}
            {include file='catalog/_partials/product-prices.tpl'}
          {/block}
          <div class="trust-piolet-section">
            Trust piolet section
          </div>
          {block name='product_description_short'}
            <div id="product-description-short-{$product.id}" class="product-description">{$product.description_short nofilter}</div>
          {/block}
          <a class="product_descrp" href="#description"><span>Présentation produit</span><i class="fa-solid fa-arrow-down-long" style="color: #000000;"></i></a>

          <div class="product-information">
            {if $product.is_customizable && count($product.customizations.fields)}
              {block name='product_customization'}
                {include file="catalog/_partials/product-customization.tpl" customizations=$product.customizations}
              {/block}
            {/if}

            <div class="product-actions js-product-actions">
              {block name='product_buy'}
                <form action="{$urls.pages.cart}" method="post" id="add-to-cart-or-refresh">
                  <input type="hidden" name="token" value="{$static_token}">
                  <input type="hidden" name="id_product" value="{$product.id}" id="product_page_product_id">
                  <input type="hidden" name="id_customization" value="{$product.id_customization}" id="product_customization_id" class="js-product-customization-id">

                  {block name='product_variants'}
                    {include file='catalog/_partials/product-variants.tpl'}
                  {/block}

                  {block name='product_pack'}
                    {if $packItems}
                      <section class="product-pack">
                        <p class="h4">{l s='This pack contains' d='Shop.Theme.Catalog'}</p>
                        {foreach from=$packItems item="product_pack"}
                          {block name='product_miniature'}
                            {include file='catalog/_partials/miniatures/pack-product.tpl' product=$product_pack showPackProductsPrice=$product.show_price}
                          {/block}
                        {/foreach}
                    </section>
                    {/if}
                  {/block}

                  {block name='product_discounts'}
                    {include file='catalog/_partials/product-discounts.tpl'}
                  {/block}
                  {widget name='chtmlmanager' id_product=$product.id}

                  {block name='product_add_to_cart'}
                    {include file='catalog/_partials/product-add-to-cart.tpl'}
                  {/block}
                  <div class="payment_express"><img src="{$urls.child_img_url}basket_icon.png" alt="#">Paiement Express</div>
                  <div class="payment_option">
                      <a class="gpay" href="#"><img src="{$urls.child_img_url}gpay.png" alt="#"></a>
                      <a class="applepay" href="#"><img src="{$urls.child_img_url}apple-pay.png" alt="#"></a>
                  </div>
                  <div class="sec-btm-express" style="display: none;">
                  <span>En plaçant une commande avec le Paiement Express, vous acceptez <a href="#">les Conditions d'Utilisation</a> de LXF (mises à jour le 16 avril 2024) et vous acceptez que LXF utilise vos informations selon sa <a href="#">Politique de Confidentialite</a></span>

                  </div>
                  {block name='product_additional_info'}
                    {include file='catalog/_partials/product-additional-info.tpl'}
                  {/block}

                  {* Input to refresh product HTML removed, block kept for compatibility with themes *}
                  {block name='product_refresh'}{/block}
                </form>
              {/block}

            </div>

            {block name='hook_display_reassurance'}
              {hook h='displayReassurance'}
            {/block}

            
        </div>

      <!--  {block name="liversion"}
       <div class="pd-liversion">
             <img src="/themes/lfx/assets/img/lvcar-1.png" alt="" width="28" height="auto">Nous vous livrons ! Dites simplement quand et comment. <a href="#">En savoir plus</a></div>
             {/block}
             {block name="ajouter"}
       <div class="pd-ajouter">
             <img src="/themes/lfx/assets/img/heart-icon-2.png" alt="" width="28" height="auto">Ajouter l'article à ma liste de souhaits
      </div>
          {/block} -->
      </div>
          
      </div>
           


      </div>
    </div>
    {if $accessories}
    <div class="you_may_like_products">
      <div class="container">
          <div class="sec_heading title">
              <h4>Vous aimerez aussi</h4>
          </div>
          <div class="row">
              <div class="col-lg-12 col-12">
                  <div class="owl-carousel owl-theme maylike_slider">
                    {$slider = true}
                    {foreach from=$accessories item="product_accessory" key="position"}
                      <div class="item">
                      {block name='product_miniature'}
                        {include file='catalog/_partials/miniatures/product.tpl' slider=$slider product=$product_accessory position=$position productClasses="col-xs-12 col-sm-6 col-lg-4 col-xl-3"}
                      {/block}
                      </div>
                    {/foreach}
                  </div>
              </div>
          </div>
      </div>
    </div>
    {/if}
    {*<div class="product_description_sec">
      <div class="container-fluid p-0">
          <div class="row main">
          <div class="tabsz">
                  <div class="top_sec">
                      <div class="container">
                          <ul class="tab nav nav-tabs" role="tablist">
                            {if $product.description}
                            <li class="nav-item">
                                <a
                                  class="nav-link{if $product.description} active js-product-nav-active{/if}"
                                  data-toggle="tab"
                                  href="#description"
                                  role="tab"
                                  aria-controls="description"
                                  {if $product.description} aria-selected="true"{/if}>{l s='Description' d='Shop.Theme.Catalog'}</a>
                            </li>
                            {/if}
                            <li class="nav-item">
                              <a
                                class="nav-link{if !$product.description} active js-product-nav-active{/if}"
                                data-toggle="tab"
                                href="#product-details"
                                role="tab"
                                aria-controls="product-details"
                                {if !$product.description} aria-selected="true"{/if}>{l s='Product Details' d='Shop.Theme.Catalog'}</a>
                            </li>
                            <li class="nav-item">
                              <a
                                class="nav-link{if !$product.description} active js-product-nav-active{/if}"
                                data-toggle="tab"
                                href="#tech-details"
                                role="tab"
                                aria-controls="product-details"
                                {if !$product.description} aria-selected="true"{/if}>{l s='Fiche technique détaillée' d='Shop.Theme.Catalog'}</a>
                            </li>
                            {if $product.attachments}
                              <li class="nav-item">
                                <a
                                  class="nav-link"
                                  data-toggle="tab"
                                  href="#attachments"
                                  role="tab"
                                  aria-controls="attachments">{l s='Attachments' d='Shop.Theme.Catalog'}</a>
                              </li>
                            {/if}
                            {foreach from=$product.extraContent item=extra key=extraKey}
                              <li class="nav-item">
                                <a
                                  class="nav-link"
                                  data-toggle="tab"
                                  href="#extra-{$extraKey}"
                                  role="tab"
                                  aria-controls="extra-{$extraKey}">{$extra.title}</a>
                              </li>
                            {/foreach}  
                          </ul>
                      </div>
                  </div>
                  <div class="btm_sec">
                      <div class="container">
                        <div class="tab-content" id="tab-content">
                          <div class="tab-pane fade in{if $product.description} active js-product-tab-active{/if}" id="description" role="tabpanel">
                            {block name='product_description'}
                              <div class="product-description">{$product.description nofilter}</div>
                            {/block}
                          </div>

                          {block name='product_details'}
                            {include file='catalog/_partials/product-details.tpl'}
                          {/block}

                        <div class="js-product-details tab-pane fade{if !$product.description} in active{/if}"
                            id="tech-details"
                            data-product="{$product.embedded_attributes|json_encode}"
                            role="tabpanel"
                        >
                            {widget name='phtmlmanager' id_product=$product.id}
                          </div>

                          {block name='product_attachments'}
                            {if $product.attachments}
                              <div class="tab-pane fade in" id="attachments" role="tabpanel">
                                <section class="product-attachments">
                                  <p class="h5 text-uppercase">{l s='Download' d='Shop.Theme.Actions'}</p>
                                  {foreach from=$product.attachments item=attachment}
                                    <div class="attachment">
                                      <h4><a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">{$attachment.name}</a></h4>
                                      <p>{$attachment.description}</p>
                                      <a href="{url entity='attachment' params=['id_attachment' => $attachment.id_attachment]}">
                                        {l s='Download' d='Shop.Theme.Actions'} ({$attachment.file_size_formatted})
                                      </a>
                                    </div>
                                  {/foreach}
                                </section>
                              </div>
                            {/if}
                          {/block}

                          {foreach from=$product.extraContent item=extra key=extraKey}
                          <div class="tab-pane fade in {$extra.attr.class}" id="extra-{$extraKey}" role="tabpanel" {foreach $extra.attr as $key => $val} {$key}="{$val}"{/foreach}>
                            {$extra.content nofilter}
                          </div>
                          {/foreach}
                        </div>
                      </div>
                  </div>
              
          </div>
          </div>
      </div>
    </div>*}

    {block name='product_footer'}
      {hook h='displayFooterProduct' product=$product category=$category}
    {/block}

    {block name='product_images_modal'}
      {include file='catalog/_partials/product-images-modal.tpl'}
    {/block}

    {block name='page_footer_container'}
      <footer class="page-footer">
        {block name='page_footer'}
          <!-- Footer content -->
        {/block}
      </footer>
    {/block}
  </section>

{/block}
