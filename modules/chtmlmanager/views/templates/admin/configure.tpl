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
	<h2>{l s='Choisissez la langue' mod='chtmlmanager'}</h2>
	<select class="form-control form-control-lg col-lg-2 mb-4 mlang-chtml">
	{foreach from=Language::getLanguages(true) item=language}
	  <option value="{$language['iso_code']}">{$language['name']}</option>
	{/foreach}
	</select>

	{foreach from=Language::getLanguages(true) key=k item=language}
	<div class="chtml-txt-container js-lang-chtml{$language['iso_code']} {if $k==0}js-chtml-active{/if}">
	  <div class="panel">
	    <h3>{l s='Texte personnalisé' mod='chtmlmanager'} {$language['name']}</h3>
	    <div class="form-group col-lg-12">
	      <label class="form-group col-lg-12" for="{$language['iso_code']}[sizes]">
	        <textarea id="{$language['iso_code']}-chtml" name="{$language['iso_code']}-chtml"
	                  class="form-control col-lg-12 txt-html autoload_rte">{if $c_html[$language['iso_code']]} {$c_html[$language['iso_code']]} {/if}</textarea>
	      </label>
	    </div>
	  </div>
	</div>
	{/foreach}
	<div class="form-group col-lg-12" style="display: none">

	<h3>{l s='Preset ID' mod='chtmlmanager'}</h3>
      <label class="form-group col-lg-12">
        <input id="preset-chtml" type="hidden" name="preset-chtml"
                  class="form-control col-lg-12" value="{if $id_preset}{$id_preset}{/if}"></textarea>
      </label>
    </div>
</div>
