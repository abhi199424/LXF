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
<div id="_desktop_user_info">
  <div class="user-info">
    {if $logged}
      <a class="dropdown-toggle" href="javascript:void(0)" role="button" data-toggle="dropdown" aria-expanded="false">
        <img src="{$urls.child_img_url}profile_icon.svg" alt="customericon"><br>
        <span class="se_connecter">{$customerName}</span>
      </a>
        <div class="dropdown-menu" aria-labelledby="dropdownAccount">

          <div class="compte-liste-container">
<div class="compte-liste-box">
<ul>
<li>
<a
class="account"
href="{$urls.pages.my_account}"
title="{l s='View my customer account' d='Shop.Theme.Customeraccount'}"
rel="nofollow"
>
Mon compte</a>
</li>
<li><a class="history-link" id="history-link" href="{$urls.pages.history}"> Mes commandes</a></li>
<li><a class="order-slips-link" id="order-slips-link" href="{$urls.pages.order_slip}"> Mes bons d'achats</a></li>
<li><a class="mes-avoirs" id="mes-avoirs" href="#"> Mes avoirs</a></li>
</ul>
</div>
</div>

        <div class="compte-login" rel="fr" title="Connexion"> 
            <a class="compte-logout-boutton" href="{$urls.actions.logout}" rel="nofollow" >{l s='Sign out' d='Shop.Theme.Actions'}</a>            
        </div>

    </div>
    <div id="screen-overlay" style="display:none;"></div>                     

    {else}
      <a class="dropdown-toggle" href="javascript:void(0)" role="button" data-toggle="dropdown" aria-expanded="false">
        <img src="{$urls.child_img_url}profile_icon.svg" alt="customericon"><br>
        <span class="se_connecter">Se connecter</span>
      </a>
        <div class="dropdown-menu" aria-labelledby="dropdownAccount">
    <div class="compte-login" rel="fr" title="Connexion"> 
    <a class="compte-login-boutton"  href="https://lxfstore.fr/connectez-vousmaintenant"
            title="{l s='Log in to your customer account' d='Shop.Theme.Customeraccount'}"
            rel="nofollow">Connexion</a>
    <div class="compte-news">Nouveau client ? <a href="https://lxfstore.fr/connectez-vousmaintenant" data-link-action="display-register-form"> Créer un compte</a></div>
    </div>
    </div>
    <div id="screen-overlay" style="display:none;"></div>                     

    {/if}
  </div>



</div>
