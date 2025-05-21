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
{extends file='page.tpl'}

{block name='page_title'}
  {l s='Create an account' d='Shop.Theme.Customeraccount'}
{/block}

{block name='page_content'}
  {if $page.page_name == 'checkout'}
  <div class="login_pagesec register">
  <p class="lxf_logo">     
        <a href="{$urls.base_url}">
          <img class="logo img-fluid" src="https://shop.lxf-motors.fr/img/logo-1736402655.jpg" alt="LFX" width="414" height="145">
        </a>
      </p>
    {block name='register_form_container'}
      {$hook_create_account_top nofilter}

      <section class="register-form form_sec">
        {*<p>{l s='Already have an account?' d='Shop.Theme.Customeraccount'} <a href="{$urls.pages.authentication}">{l s='Log in instead!' d='Shop.Theme.Customeraccount'}</a></p>*}
        <div class="heading">
            <h3>Créez votre LXF</h3>
        </div>
        {render file='customer/_partials/customer-form.tpl' ui=$register_form}
      </section>
      <h6 class="btm_heading">Déjà membre ? <a href="/connexion">Connectez-vous</a> ici</h6>
    {/block}
  </div>
  {else}
    <div class="row main-abs-section">
      <div class="col-md-6 abs-section-left">
          {hook h='displayID1Customhtml9'}
      </div>


      <div class="col-md-6 abs-section-right">
        <div class="Retour_back"><a href="/connectez-vousmaintenant"><img src="/img/arrow.png" width="10" height="auto">Retour</a></div>
          <div class="login_pagesec register">
            <p class="lxf_logo">     
                  <a href="{$urls.base_url}">
                    <img class="logo img-fluid" src="https://shop.lxf-motors.fr/img/logo-1736402655.jpg" alt="LFX" width="414" height="145">
                  </a>
                </p>
              {block name='register_form_container'}
                {$hook_create_account_top nofilter}

                <section class="register-form form_sec">
                  {*<p>{l s='Already have an account?' d='Shop.Theme.Customeraccount'} <a href="{$urls.pages.authentication}">{l s='Log in instead!' d='Shop.Theme.Customeraccount'}</a></p>*}
                  <div class="heading">
                      <h3>Créez votre LXF</h3>
                  </div>
                  {render file='customer/_partials/customer-form.tpl' ui=$register_form}
                </section>
                <h6 class="btm_heading">Déjà membre ? <a href="/connexion">Connectez-vous</a> ici</h6>
              {/block}
          </div>
      </div>
  </div>
  {/if}
{/block}
