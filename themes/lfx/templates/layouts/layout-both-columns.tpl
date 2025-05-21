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

{include file='_partials/helpers.tpl'}

<!doctype html>
<html lang="{$language.locale}">

  <head>
    {block name='head'}
      {include file='_partials/head.tpl'}
    {/block}
  </head>

  <body id="{$page.page_name}" class="{$page.body_classes|classnames}">
  <div class="menu-hover-overlay"></div>
    {block name='hook_after_body_opening_tag'}
      {hook h='displayAfterBodyOpeningTag'}
    {/block}

    <main>
      {block name='product_activation'}
        {include file='catalog/_partials/product-activation.tpl'}
      {/block}

      <header id="header">
        {block name='header'}
          {include file='_partials/header.tpl'}
        {/block}
      </header>

      <section id="wrapper">
        {block name='notifications'}
          {include file='_partials/notifications.tpl'}
        {/block}

        {hook h="displayWrapperTop"}
        <div class="{if $page.page_name == 'index'}container-altersection{else}container{/if}">
          {if $page.page_name != 'product' && $page.page_name != 'category'}
            {block name='breadcrumb'}
              {include file='_partials/breadcrumb.tpl'}
            {/block}
          {/if}

          {if $page.page_name == 'category'}
            {block name='breadcrumb'}
              {include file='_partials/breadcrumb.tpl'}
            {/block}
           {block name='product_list_header'}
      <h1 id="js-product-list-header" class="h2">{$listing.label}</h1>
    {/block}
          {/if}

          <div class="row product_list_wrapper">          
            {block name="left_column"}
              <div id="left-column" class="col-xs-12 col-md-4 col-lg-3">
                {if $page.page_name == 'product'}
                  {hook h='displayLeftColumnProduct' product=$product category=$category}
                {else}
                  {hook h="displayLeftColumn"}
                {/if}
              </div>
            {/block}
            
            {block name="content_wrapper"}
              <div id="content-wrapper" class="js-content-wrapper left-column right-column col-md-4 col-lg-3">
                {hook h="displayContentWrapperTop"}
                {block name="content"}
                  <p>Hello world! This is HTML5 Boilerplate.</p>
                {/block}
                {hook h="displayContentWrapperBottom"}
              </div>
            {/block}

            {block name="right_column"}
              <div id="right-column" class="col-xs-12 col-md-4 col-lg-3">
                {if $page.page_name == 'product'}
                  {hook h='displayRightColumnProduct'}
                {else}
                  {hook h="displayRightColumn"}
                {/if}
              </div>
            {/block}
          </div>
        </div>
        {hook h="displayWrapperBottom"}
      </section>

      <footer id="footer" class="js-footer">
        {block name="footer"}
          {include file="_partials/footer.tpl"}
        {/block}
      </footer>

    </main>

    {block name='javascript_bottom'}
      {include file="_partials/password-policy-template.tpl"}
      {include file="_partials/javascript.tpl" javascript=$javascript.bottom}
    {/block}

    {block name='hook_before_body_closing_tag'}
      {hook h='displayBeforeBodyClosingTag'}
    {/block}
    {hook h='displayMinicart'}
    <!-- Bootstrap Modal -->
    <div class="modal fade" id="loyaltyModal" tabindex="-1" aria-labelledby="loyaltyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
          <h5 class="modal-title" id="loyaltyModalLabel">{l s='Les Ridiz' mod='allinone_rewards'}</h5>
          <button type="button" class="close btn-close" data-dismiss="modal" aria-label="{l s='Close' mod='allinone_rewards'}"><span aria-hidden="true">&times;</span></button>

          </div>
          <div class="modal-body">
          <!-- You can add detailed loyalty information here -->
          <!-- {l s='En commandant chez LXF, tu cumules des points de fidélités.' mod='allinone_rewards'} -->

          <p>En commandant chez LXF, tu cumules des points de fidélités. Chaque article (hors promotion) vaut un certain nombre de points, affichés sur la fiche produit et peuvent être convertis en bon d'achat pour tes commandes futures.</p>
          <p>Exemple : 50 Ridiz = 50€ de réduction sur votre commande.<br>
            <p>1 Ridiz (1 point, soit 1 euro) déclenché par tranche de 33.33€ dépensé sur le site.</p>
          </div>
          <!-- <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">{l s='Close' mod='allinone_rewards'}</button>
          </div> -->
        </div>
        </div>
    </div>
  </body>

</html>
