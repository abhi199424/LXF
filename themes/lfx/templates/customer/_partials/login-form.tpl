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
{block name='login_form'}
{if $page.page_name == 'checkout'}
  {block name='login_form_errors'}
    {include file='_partials/form-errors.tpl' errors=$errors['']}
  {/block}
  <form id="login-form" action="{block name='login_form_actionurl'}{$action}{/block}" method="post">

    <div>
      {block name='login_form_fields'}
        {foreach from=$formFields item="field"}
          {block name='form_field'}
            {form_field field=$field}
          {/block}
        {/foreach}
      {/block}
      <div class="forgot-password">
        <a href="{$urls.pages.password}" rel="nofollow">
          {l s='Forgot your password?' d='Shop.Theme.Customeraccount'}
        </a>
      </div>
    </div>

    {block name='login_form_footer'}
      <footer class="form-footer text-sm-center clearfix">
        <input type="hidden" name="submitLogin" value="1">
        {block name='form_buttons'}
          <button id="submit-login" class="btn btn-primary" data-link-action="sign-in" type="submit" class="form-control-submit">
            {l s='Sign in' d='Shop.Theme.Actions'}
          </button>
        {/block}
      </footer>
    {/block}

  </form>
{else}
  <div class="login_pagesec login">
    <div class="container">
        <div class="row main">
            <div class="form_sec">
                <!-- <p class="lxf_logo">
                  {renderLogo}
                </p> -->
                <div class="heading">
                    <h3>Bienvenue</h3>
                    <h6>Tu n'as pas encore de compte ? <a href="{$urls.pages.register}" data-link-action="display-register-form">S'inscrire ici</a></h6>
                </div>
                {block name='login_form_errors'}
                  {include file='_partials/form-errors.tpl' errors=$errors['']}
                {/block}
                <div class="form">
                    <div class="row">
                      <form id="login-form" action="{block name='login_form_actionurl'}{$action}{/block}" method="post">
                        {block name='login_form_fields'}
                          {foreach from=$formFields item="field"}
                            {block name='form_field'}
                              {form_field field=$field}
                            {/block}
                          {/foreach}
                        {/block}
                        <div class="col-lg-12">
                            <div class="forgot_password">
                              <a href="{$urls.pages.password}" rel="nofollow">
                                {l s='Mot de passe oublié' d='Shop.Theme.Customeraccount'}
                              </a>
                                <span class="required">* Champ requis</span>
                            </div>
                        </div>
                        <div class="col-lg-12">
                            <div class="button">
                              <input type="hidden" name="submitLogin" value="1">
                              {block name='form_buttons'}
                                <button id="submit-login" class="submit_btn" data-link-action="sign-in" type="submit" class="form-control-submit">
                                  {l s='Sign in' d='Shop.Theme.Actions'}
                                </button>
                              {/block}
                            </div>
                        </div>
                      </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
  </div>
{/if}


<div class="company-services">
    <div class="container">
        <div class="row">
            <div class="col-lg-3 box">
                <a href="#">
                    <figure><img src="/img/secure-shopping.png" alt="#"></figure>
                    <h6>Secure <span>shopping</span></h6>
                </a>
            </div>
            <div class="col-lg-3 box">
                <a href="#">
                    <figure><img src="/img/realtime-stock.png" alt="#"></figure>
                    <h6>Real-Time <span>stock</span></h6>
                </a>
            </div>
            <div class="col-lg-3 box">
                <a href="#">
                    <figure><img src="/img/global-delivery.png" alt="#"></figure>
                    <h6>Worldwide <span>delivery</span></h6>
                </a>
            </div>
            <div class="col-lg-3 box">
                <a href="#">
                    <figure><img src="/img/online-support.png" alt="#"></figure>
                    <h6>Expert <span>advice</span></h6>
                </a>
            </div>
        </div>
    </div>
</div>

{/block}
