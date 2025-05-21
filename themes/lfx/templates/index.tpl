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

    {block name='page_content_container'}
      <section id="content" class="page-home">

        {block name='page_content_top'}{/block}

        
        {block name='page_content'}
          {block name='hook_home'}
            {$HOOK_HOME nofilter}
          {/block}
        {/block}
      </section>
      <div class="marquee">
     <span>
         {hook h='displayID1Customhtml7'}
     </span>
</div>
<!--       {block name='page_seo_top'}
            <div class="index_page_seo_top">
              <div class="container">
                <div class="row">
                  <div class="col-sm-12">
                <h1><b>LXF Motors - La Référence en Motos, Quads, Draisiennes et véhicules de loisirs.</b></h1>
                <p>Chez LXF, chaque ride est une promesse d'émotion. Du premier tour de roue des minis riders aux performances extrêmes des pilotes aguerris, nous concevons des machines qui transforment chaque sortie en moment inoubliable.</p>
                  <p>
Découvrez notre large gamme de <b>Motocross, Quads, Dirt Bikes, et Draisiennes Électriques pour enfants, adolescents et adultes.</b> Nos <b>motos tout-terrain, quads sportifs, mini motos</b> et <b>véhicules électriques puissants</b> sont conçus pour offrir <b>performance, sécurité et sensations fortes</b> sur tous types de terrains. Que vous <b>soyez débutant, amateur ou pilote expérimenté,</b> trouvez le modèle parfait pour vos aventures en <b>off-road, cross ou randonnée.</b></p>
</div>
</div>
</div>
            </div>
        {/block} -->
      {hook h='displayCatshow'}

      
      <div class="most_viewed_products container">
        {hook h='displayFilterproducts3'}
      </div>


<!--       <div class="video-section">
        {hook h='displayID1Customhtml'}
      </div> -->
      {hook h='displayID1Customhtml5'}

{hook h='displayHomeBanner'}







      <div class="find_parts_sec">
        {hook h='displayID1Customhtml2'}
      </div>


{hook h='displayID1Customhtml6'}

      
    {/block}
