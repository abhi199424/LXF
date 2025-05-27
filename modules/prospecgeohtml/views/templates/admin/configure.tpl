{*
* 2007-2020 PrestaShop
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
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*}


<div class="panel">
	<div class="cprhtml-txt-container js-lang-cprhtml{$language['iso_code']} {if $k==0}js-cprhtml-active{/if}">
	  <div class="panel">
	    <h3>{l s='Texte personnalisé' mod='phtmlmanager'}</h3>
	    <div class="form-group col-lg-12">
	      <label class="form-group col-lg-12" for="PGH_DATA_TECHNIQUE">
	        <textarea id="PGH_DATA_TECHNIQUE" name="PGH_DATA_TECHNIQUE"
			class="form-control col-lg-12 txt-html autoload_rte">{if $pgh_html}{$pgh_html}{/if}</textarea>
	      </label>
	    </div>
	  </div>
	</div>
</div>
