{*
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}
{if !empty($home_banners)}
<div class="section-padding">
   <div class="container">
      <div class="sec_heading title">
         <h2>Les incontournables</h2>
         <p>Voici la sélection des modèles choisis par les riders. Choisissez le véhicule qui convient le mieux à vos besoins.</p>
      </div>
   </div>
   <div class="content">
      <section class="owl">
         <div class="owl-carousel owl-theme loop">
         {foreach name=items from=$home_banners item=pItem}
            <div class="item">
               <img src="{$link->getMediaLink('/img/'|cat:$pItem.image)}" alt="" title="">
               <div class="index-product-details">
                  <h3>{$pItem.title}</h3>
                  {$pItem.description nofilter}
               </div>
            </div>
         {/foreach}
         </div>
      </section>
   </div>
</div>
{/if}