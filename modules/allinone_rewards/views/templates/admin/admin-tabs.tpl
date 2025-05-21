{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2025 Yann BONNAILLIE - ByWEB
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}
<div class="tabs general" style="display: none; margin-bottom: 50px">
	<ul>
		<li id="li-news"><a id="a-news" href="#tabs-news">{l s='About / News' mod='allinone_rewards'}</a></li>
{if $is_registered}
 	{foreach from=$module->plugins item=plugin}
		<li id="li-{$plugin->name|escape:'htmlall':'UTF-8'}"><a id="a-{$plugin->name|escape:'htmlall':'UTF-8'}" href="{$module->getCurrentPage($plugin->name, true)|escape:'html':'UTF-8'}">{$plugin->getTitle()|escape:'htmlall':'UTF-8'}</a></li>
	{/foreach}
{/if}
	</ul>

{if $is_registered}
	{foreach from=$module->plugins item=plugin}
		{if $current_plugin==$plugin->name}
	<div class="tabcontent ui-tabs-panel ui-widget-content ui-corner-bottom">{$plugin->content nofilter}</div>
		{/if}
	{/foreach}
{/if}

	<div id="tabs-news">
		<fieldset>
			<legend>{l s='Information' mod='allinone_rewards'}</legend>
			{l s='This module has been created by' mod='allinone_rewards'} <b>Yann BONNAILLIE - {if !$module->addons}<a href="https://www.prestaplugins.com" target="_blank">Prestaplugins</a>{else}Prestaplugins{/if}</b> <a style="margin-left: 20px" href="{$module_template_dir|escape:'html':'UTF-8'}readme_{$doc_suffix|escape:'html':'UTF-8'}.pdf" download="readme_{$doc_suffix|escape:'html':'UTF-8'}.pdf"><img src="{$module_template_dir|escape:'html':'UTF-8'}img/pdf.gif"></a><a href="{$module_template_dir|escape:'html':'UTF-8'}readme_{$doc_suffix|escape:'html':'UTF-8'}.pdf" download="readme_{$doc_suffix|escape:'html':'UTF-8'}.pdf">{l s='Installation guide' mod='allinone_rewards'}</a><br/>
{if !$module->addons}
			{l s='Contact me if you need an upgrade, custom development or bug fix on your shop.' mod='allinone_rewards'}<br/><br/>
			{l s='Please report any bug to' mod='allinone_rewards'} <a href="mailto:contact@prestaplugins.com">contact@prestaplugins.com</a>
			<a href="https://www.facebook.com/Prestaplugins" target="_blank"><img id="facebook" src="{$module_template_dir|escape:'html':'UTF-8'}img/follow_facebook{$facebook_suffix|escape:'html':'UTF-8'}.png"/></a>
{else}
			{l s='If you have any questions about this module, please contact me using' mod='allinone_rewards'} <a href="https://addons.prestashop.com/en/write-to-developper?id_product=4414" target="_blank">{l s='Addons contact form' mod='allinone_rewards'}</a>
			<br/><br/>{l s='If you bought this module on Addons and you like it, help me to improve it by giving 5 stars in your' mod='allinone_rewards'} <a href="http://addons.prestashop.com/en/ratings.php" target="_blank">{l s='Addons account' mod='allinone_rewards'}</a>
{/if}
		</fieldset>
		<fieldset>
			<legend>{l s='News' mod='allinone_rewards'}</legend>
			{$rss nofilter}
		</fieldset>
	</div>
</div>

<script>
	var checkAllText = "{l s='Check all' mod='allinone_rewards' js=1}";
	var uncheckAllText = "{l s='Uncheck all' mod='allinone_rewards' js=1}";
	var selectedText = "{l s='# value(s) checked' mod='allinone_rewards' js=1}";
	var noneSelectedText = "{l s='Choose the values' mod='allinone_rewards' js=1}";
	var virtual_value = new Array();

{foreach from=$virtual_values item=virtual_value key=key}
	virtual_value[{$key|intval}] = {$virtual_value|floatval};
{/foreach}
	var virtual_name = "{$virtual_name|escape:'htmlall':'UTF-8'}";

	var languages = new Array();
	var id_language = Number({$default_language_id|intval});
	var iso = "{$isoTinyMCE|escape:'htmlall':'UTF-8'}";
	var ad = "{$ad|escape:'html':'UTF-8'}";
	var pathCSS = "{$pathCSS|escape:'html':'UTF-8'}";
{foreach from=$languages item=language key=key}
	languages[{$key}] = {
		id_lang: {$language['id_lang']|intval},
		iso_code: "{$language['iso_code']|escape:'htmlall':'UTF-8'}",
		name: "{$language['name']|escape:'htmlall':'UTF-8'}",
		is_default: {if $language['id_lang']==$default_language_id}true{else}false{/if}
	};
{/foreach}

	var current_tab = "{$current_plugin|escape:'htmlall':'UTF-8'}";
	var current_subtab = "{$current_subtab|escape:'htmlall':'UTF-8'}";
	var version = "{$version|escape:'htmlall':'UTF-8'}";
</script>