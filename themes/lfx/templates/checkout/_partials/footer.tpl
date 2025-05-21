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
<section class="footer_sec">
   <div class="container">
      <div class="row main">
         <div class="footer_top">
            <div class="region_sec box">
               <div class="heading">
                  <h5>Région</h5>
               </div>
               <div class="country">
                  <h6><img class="flag" src="{$urls.child_img_url}france_flag.png" alt="#">Site 100% Français</h6>
               </div>
            </div>
            <div class="row">
               <div class="box newsletter">
                  <div class="heading">
                     <h5>Newsletter</h5>
                  </div>
                  <p>Souscrivez à la Newsletter pour recevoir en exclusivité les dernières actualités.</p>
                  <div class="button">
                     <a href="#">S'abonner</a>
                  </div>
               </div>
               <div class="box contact">
                  <div class="heading">
                     <h5>Contact</h5>
                  </div>
                  <p>Avez-vous des questions ?</p>
                  <div class="button">
                     <a href="#">Formulaire de contact</a>
                  </div>
               </div>
               <div class="box social">
                  <div class="heading">
                     <h5>Réseaux sociaux</h5>
                  </div>
                  <p>Contactez-nous via les réseaux sociaux.</p>
                  <ul class="footer_social">
                     <li><a href="#"><img class="insta" src="{$urls.child_img_url}instagram.png"></a></li>
                     <li><a href="#"><img class="linkedin" src="{$urls.child_img_url}linkedin.png"></a></li>
                  </ul>
               </div>
               <div class="box payment">
                  <div class="heading">
                     <h5>Moyens de paiement</h5>
                  </div>
                  <p>Choisissez votre option préférée</p>
                  <div class="payment_platforms">
                     <img src="{$urls.child_img_url}payment_options.png" alt="#">
                  </div>
               </div>
                {block name='hook_footer'}
                  {hook h='displayFooter'}
                {/block}
         </div>
         <div class="copyright">
            <div class="row">
               <p class="copyright_txt">© 2024 LXF en France. </p>
               <p>*Le Lorem Ipsum est simplement du faux texte employé dans la composition et la mise en page avant impression. Le Lorem Ipsum est le faux texte standard de l'imprimerie depuis les années 1500, quand un imprimeur anonyme assembla ensemble des morceaux de texte pour réaliser un livre spécimen de polices de texte. Il n'a pas fait que survivre cinq siècles, mais s'est aussi adapté à la bureautique informatique, sans que son contenu n'en soit modifié. Il a été popularisé dans les années 1960 grâce à la vente de feuilles Letraset contenant des passages du Lorem Ipsum, et, plus récemment, par son inclusion dans des applications de mise en page de texte, comme Aldus PageMaker.</p>
               <div class="footer_logo"><a href="#"><img src="{$urls.child_img_url}footer_logo.png" alt="#"></a></div>
            </div>
         </div>
      </div>
   </div>
</section>