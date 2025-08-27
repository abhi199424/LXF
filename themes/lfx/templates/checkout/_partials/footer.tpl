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
                          <div class="FeatureitemIcon"><img src="{$urls.img_url}f-icon-1.svg"></div>
                          <div class="FeatureitemContent">
                          <h3><a href="#">Livraison gratuite.</a></h3>
                          <p>à partir de 29€ sur les pièces détachées</p>
                        </div>
                      </div>

                      <div class="Featureitem">
                          <div class="FeatureitemIcon"><img src="{$urls.img_url}f-icon-2.svg"></div>
                          <div class="FeatureitemContent">
                          <h3><a href="#">Retrait possible en entrepôt.</a></h3>
                          <p>Récupérez votre produit sous 2H.</p>
                        </div>
                      </div>

                      <div class="Featureitem">
                          <div class="FeatureitemIcon"><img src="{$urls.img_url}f-icon-3.svg"></div>
                          <div class="FeatureitemContent">
                          <h3><a href="#">Paiement 100% sécurisé.</a></h3>
                          <p>Réglez en 3x, 4x</p>
                        </div>
                      </div>

                     <!--  <div class="Featureitem">
                          <div class="FeatureitemIcon"><img src="{$urls.img_url}f-icon-4.svg"></div>
                          <div class="FeatureitemContent">
                          <h3><a href="#">Retrait possible en entrepôt.</a></h3>
                          <p>Récupérez votre produit sous 2H.</p>
                        </div>
                      </div> -->

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
                  </ul>


            </div>

            <div class="copyright-toppart">
                <h4>Livraison dans les pays suivants :</h4>

                <div class="footer_languagetop"><span><img src="{$urls.img_url}france-flag-png-xl.png">France métropolitaine & Corse</span> <span class="belgique_flug"><img src="{$urls.img_url}Belgique.png"> Belgique</span></div>

            </div>

            <div class="copyright">

              <div class="copyright-left">© 2025 LXF FRANCE - Tous droits réservés<br>Magasin Motocross, Minimoto, Dirt bike / Pit bike, Pocket cross, Pocket bike, Quads, Pockets quads, Draisiennes électriques, voitures électriques.</div>
              <div class="copyright-right">Paiement 100% Sécurisé - <a href="https://lxfstore.fr/conditions-generales-8">Conditions générales de vente</a> - <a href="https://lxfstore.fr/mentions-legales-6">Mentions légales</a> - <a href="https://lxfstore.fr/cookie-policy-10">Données personnelles et cookies</a>  -  <a href="#">Plan du site</a></div>

            </div>

            <div class="qui_sommes_cont">
              <h4>Qui sommes-nous ?</h4>
             <p>Chez <a href="http://lxfstore.fr/">LXFstore.fr</a>, nous sommes passionnés par les loisirs motorisés et nous mettons tout notre savoir-faire au service de nos clients. Basée en Normandie, notre entreprise française dispose d’un entrepôt en France pour assurer une livraison rapide et un service client réactif. Que vous soyez amateur de sensations fortes ou parent à la recherche d’un véhicule adapté pour votre enfant, vous trouverez chez LXF® Store  un large choix de modèles performants, sécurisés et au meilleur rapport qualité/prix : Dirt bike / Pit bike, Minimoto, Quad, Pocket quad, Draisienne électrique, ou encore toutes nos Pockets bikes type cross / route.</p>

             <p> Notre catalogue comprend les incontournables <a href="https://lxfstore.fr/motocross-thermique-">Dirt bike / Pit bike 125cc 12/14"</a>, <a href="https://lxfstore.fr/dirt-bike-pit-bike-125cc-1417">125cc 14/17"</a>, pour les pilotes confirmés, ainsi que les <a href="https://lxfstore.fr/dirt-bike-pit-bike-150cc-1417">Dirt bike / Pit bike 150cc 14/17"</a>, idéales pour s’initier ou se perfectionner. Pour les plus jeunes pilotes, nous proposons des <a href="https://lxfstore.fr/pocket-cross-thermique-">Pocket cross 49cc 2T en version thermique</a> – également appelées minimoto enfant – au design sportif, ainsi que des <a href="https://lxfstore.fr/pocket-cross-%C3%A9lectrique-">Pockets cross électriques 550W</a> silencieuses et écologiques, parfaites pour débuter la conduite tout-terrain sans bruit ni émissions.</p>

             <p>Le motocross électrique n'est plus une mode, c'est une nouvelle gamme de produits proposée par LXF®. Découvrez une gamme complète de Dirt bike / Pit bike / Minimx (ou "mini motocross") électriques : des modèles puissants et performants. Nous équipons les modèles LXF® des meilleurs motorisations : <a href="https://lxfstore.fr/motocross-%C3%A9lectrique-1000w-enfant">1000W</a> , idéal pour les débutants et jeunes pilotes, <a href="https://lxfstore.fr/motocross-%C3%A9lectrique-1300w-enfant">1300W</a>, un compromis parfait entre maniabilité et puissance en franchissant un cap. Enfin, commandez votre <a href="https://lxfstore.fr/motocross-%C3%A9lectrique-2000w-enfant">Dirt bike / Pit bike électrique LXF 2000W</a> : la référence pour les amateurs de sensations fortes offrant une accélération fulgurante et une autonomie optimisée.</p>

             <p>Côté Quads, notre gamme inclut des <a href="https://lxfstore.fr/pocket-quad">Pocket quads 49cc 2T pour enfants</a>, maniables et sécurisés. Nous proposons également un large choix de <a href="https://lxfstore.fr/pocket-quad-800w-%C3%A9lectrique-">pocket quad électrique 800W</a> pour les enfants à partir de 3 ans, ou du quad LXF version S en 1000W pour les enfants à partir de 5 ans, jusqu'à 10 ans.</p>

<p>
Nous travaillons quotidiennement afin de vous proposer des véhicules de loisirs qualitatifs. Nous vous proposerons bientôt des Quads 125cc 4T et 250cc pour adolescents et adultes, aussi performants sur chemins que sur terrains plus exigeants. Nous proposons également des modèles électriques, idéaux pour une conduite propre et silencieuse.</p>

             <p>Pour initier les plus petits en douceur, LXF® a sélectionné des <a href="https://lxfstore.fr/draisiennes-electriques">draisiennes électriques</a> <a href="https://lxfstore.fr/12-pouces"> 12"</a> et <a href="https://lxfstore.fr/16-pouces">16"</a> légères et maniables, idéales pour développer l’équilibre avant de passer à un deux-roues motorisé. Ces modèles sont équipés d'un moteur 250w, et d'un potentiomètre pour régler la vitesse. Ces draisiennes électriques enfant, aussi appelés "balance bikes" électriques, offrent une première expérience de pilotage ludique et sécurisée. Pour les plus grands en quête d'aventures nous avons développé la <a href="https://lxfstore.fr/18-pouces">draisienne électrique enfant 18"</a> LXF enfant munie de pneus cross, d'une fourche avant hydraulique réglable, et d'un puissant moteur 550w (jusqu'à 600w) avec 5 niveaux de vitesses !</p>

             <p>En choisissant LXF®, vous bénéficiez non seulement d’une large sélection de véhicules de loisirs, mais aussi d’un stock de <a href="https://lxfstore.fr/pieces-detachees-motocross-quads-pocket-bike-pocket-cross-dirt-bike-pit-bike">pièces détachées</a> pour votre Dirt bike, Pocket quad, et d’accessoires pour <a href="https://lxfstore.fr/161-produits-d-entretien-">entretenir</a>, réparer ou personnaliser votre dirt bike / pit bike / quad / draisienne électrique enfant. Chaque véhicule est contrôlé avant expédition, garantissant qualité et fiabilité. LXF® met à votre disposition un catalogue complet avec vues éclatées des véhicules avec notre page <a href="https://www.lxfstore.fr/pieces-detachees-microfiches/">Microfiches</a> afin d'identifier votre Dirt bike / Pocket bike / Quad / pocket quad / draisienne et commander la pièce détachée de votre véhicule.</p>

             <p>Commander sur <a href="http://lxfstore.fr/">LXFstore.fr</a>, c’est opter pour une entreprise française qui connaît vos besoins, vous conseille, et vous livre rapidement depuis la Normandie. Que vous cherchiez un pocket bike pas cher, un quad thermique puissant, une minimoto enfant de qualité ou une draisienne électrique, LXF® est votre partenaire de confiance pour vivre pleinement votre passion du tout-terrain et des loisirs motorisés.</p>

            </div>

      </div>





    </div>


<!-- 
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
</script> -->

<script type="text/javascript" charset="UTF-8" src="//cdn.cookie-script.com/s/9ac90296fa49588b837c6ae5432b0e8b.js"></script>



