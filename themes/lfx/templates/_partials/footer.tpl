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
{*<div class="container">
  <div class="row">
    {block name='hook_footer_before'}
      {hook h='displayFooterBefore'}
    {/block}
  </div>
</div>
<div class="footer-container">
  <div class="container">
    <div class="row">
      {block name='hook_footer'}
        {hook h='displayFooter'}
      {/block}
    </div>
    <div class="row">
      {block name='hook_footer_after'}
        {hook h='displayFooterAfter'}
      {/block}
    </div>
    <div class="row">
      <div class="col-md-12">
        <p class="text-sm-center">
          {block name='copyright_link'}
            <a href="https://www.prestashop-project.org/" target="_blank" rel="noopener noreferrer nofollow">
              {l s='%copyright% %year% - Ecommerce software by %prestashop%' sprintf=['%prestashop%' => 'PrestaShop™', '%year%' => 'Y'|date, '%copyright%' => '©'] d='Shop.Theme.Global'}
            </a>
          {/block}
        </p>
      </div>
    </div>
  </div>
</div>*}
{block name='hook_footer_before'}
  {hook h='displayFooterBefore'}
{/block}




    <div class="___new_searchcont">
      <div class="container">
            <div class="row">
                <div class="col-lg-12">

                      <div class="sec_heading title">
                        <h2>Que cherchez-vous ?</h2>
                      </div>

                      <div class="footersearchform">

                        <div class="footersearchformcont">
                        {hook h='displaySearch'}
                      </div>

                        {hook h='displayID1Customhtml8'}

                      </div>

                </div>
            </div>
      </div>
    </div>

    <a href="#nav-top" class="navFooterBackToTop">
      <span class="navFooterBackToTopText">
        {l s='Retour en haut' d='Shop.Theme.Global'}
      </span>
    </a>

    <div class="FeatureDescription">

      <div class="container">

          <div class="row">

              <div class="col-lg-12">

                  <div class="Featureitems">

                      <div class="Featureitem">
                          <div class="FeatureitemIcon"><img src="{$urls.img_url}f-icon-1.png"></div>
                          <div class="FeatureitemContent">
                          <h3><a href="#">Livraison gratuite.</a></h3>
                          <p>à partir de 29€ sur les pièces détachées</p>
                        </div>
                      </div>

                      <div class="Featureitem">
                          <div class="FeatureitemIcon"><img src="{$urls.img_url}f-icon-2.png"></div>
                          <div class="FeatureitemContent">
                          <h3><a href="#">Retrait possible en entrepôt.</a></h3>
                          <p>Récupérez votre produit sous 2H.</p>
                        </div>
                      </div>

                      <div class="Featureitem">
                          <div class="FeatureitemIcon"><img src="{$urls.img_url}f-icon-3.png"></div>
                          <div class="FeatureitemContent">
                          <h3><a href="#">Paiement 100% sécurisé.</a></h3>
                        </div>
                      </div>

                      <div class="Featureitem">
                          <div class="FeatureitemIcon"><img src="{$urls.img_url}f-icon-4.png"></div>
                          <div class="FeatureitemContent">
                          <h3><a href="#">Retrait possible en entrepôt.</a></h3>
                          <p>Récupérez votre produit sous 2H.</p>
                        </div>
                      </div>

                  </div>

              </div>

          </div>

      </div>

    </div>

    <div class="FeatureNewsletter">

      <div class="container">

          <div class="row">

            <div class="col-lg-6">

              <div class="FeatureNewsletterLeft">
                  <h2><span>10€ DE RÉDUCTION</span> OFFERTS SUR VOTRE<br>1ÈRE COMMANDE</h2>

                  <p class="FeatureNewsletterLeft_bb">BIENVENUE SUR LXF-MOTORS.FR</p>
                  <p>Rejoignez-nous en vous inscrivant à la newsletter lxf-motors.fr</p>
              </div>


            </div>
            <div class="col-lg-6">
              <div class="FeatureNewsletterRight">
                  <h3>Adresse e-mail</h3>
                  {hook h='displayFooterAfter'}
                  <p class="FeatureNewslettersmall">Merci de vous référer á notre politique de protection des données pour savoir comment LXF traite vos données. Vous pouvez vous désinscrire gratuitement et á tout moment.'</p>
                  <hr>
                  <p class="FeatureNewrightslettertext"><b>Conditions d' utilisation des codes promotionnels</b><br>Le bon de réduction est offert uniquement lors de la toute premiére inscription et ne pourra étre utilisé qu'une seule fois, pour une commande d' un montant minimum de 80€. Cette offre peut ne pas s'appliquer á tous nos produits et exclut les paiements par cartes cadeaux LXF.</p>

              </div>

            </div>

          </div>

      </div>

    </div>


    <div class="FooterContainer">

      <div class="container">

            <div class="footernavlinks row">
              {hook h='displayFooter'}
            </div>

            <div class="FooterSocialContact row">

             

                <ul class="payment-method">

                    <li><img src="{$urls.img_url}cb-picto.svg" alt=""></li>
                    <li><img src="{$urls.img_url}visa-picto.svg" alt=""></li>
                    <li><img src="{$urls.img_url}paypal-picto.svg" alt=""></li>
                    <li><img src="{$urls.img_url}apple-picto.svg" alt=""></li>
                    <li><img src="{$urls.img_url}gpay-picto.svg" alt=""></li>

                    <li><img src="{$urls.img_url}Klarna_Logo_black.svg.png" alt=""></li>

                </ul>

              

                  <ul class="socialmedialinks">
                      <li><a href="#"><img src="{$urls.img_url}instagram-brands.svg" width="20"></a></li>
                      <li><a href="#"><img src="{$urls.img_url}facebook-f-brands.svg" width="20"></a></li>
                      <li><a href="#"><img src="{$urls.img_url}youtube-brands.svg" width="20"></a></li>
                  </ul>


            </div>

            <div class="copyright-toppart">
                <h4>Livraison dans les pays suivants :</h4>

                <div class="footer_languagetop"><span><img src="{$urls.img_url}france-flag-png-xl.png">France métropolitaine & Corse</span> <span class="belgique_flug"><img src="{$urls.img_url}Belgique.png"> Belgique</span></div>

            </div>

            <div class="copyright">

              <div class="copyright-left">© 2025 LXF FRANCE - Tous droits réservés</div>
              <div class="copyright-right">Paiement 100% Sécurisé - <a href="#">Conditions générales de vente</a> - <a href="#">Mentions légales</a> - <a href="#">Données personnelles et cookies</a>  -  <a href="#">Plan du site</a></div>

            </div>

      </div>





    </div>



<script>
window.axeptioSettings = {
  clientId: "67c31e44e544fdc8c65b5a59",
  cookiesVersion: "lxf b2c -fr-EU",
  googleConsentMode: {
    default: {
      analytics_storage: "denied",
      ad_storage: "denied",
      ad_user_data: "denied",
      ad_personalization: "denied",
      wait_for_update: 500
    }
  }
};
(function(d, s) {
  var t = d.getElementsByTagName(s)[0], e = d.createElement(s);
  e.async = true; e.src = "//static.axept.io/sdk.js";
  t.parentNode.insertBefore(e, t);
})(document, "script");
</script>