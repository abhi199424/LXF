{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2025 Yann BONNAILLIE - ByWEB
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}
{if $xml_error}
<div style="font-weight: bold; color: red">{l s='You need to enable CURL extension or fsockopen, to be informed about new version of the module' mod='allinone_rewards'}</div>
{else if $response}
	{if $registered == -1}
<div style="margin-left: 10%; font-weight: bold; color: red">{l s='Your order reference was not valid, please try again.' mod='allinone_rewards'}</div>
	{/if}
	{if $registered < 1}
		{if $registration != ''}
<div style="margin-left: 10%; margin-bottom: 20px; font-weight: bold; color: red">{l s='Thanks for your patience, your license is being validated.' mod='allinone_rewards'}</div>
		{else}
<div style="margin-left: 10%; margin-bottom: 20px;">
	{l s='Please register by entering your order reference. This way, if you are eligible for further updates you will be able to claim for them automatically.' mod='allinone_rewards'}
	<form id="rewards_registration_form" method="post">
	<input type="text" id="rewards_registration" name="rewards_registration" value="{$registration|escape:'htmlall':'UTF-8'}" maxlength="18">
	<input type="submit" name="submitRegistration" class="button" value="{l s='Register' mod='allinone_rewards'}">
	</form>
</div>
		{/if}
	{else}
<div style="margin-left: 10%; font-weight: bold">{l s='Congratulation, your license is registered.' mod='allinone_rewards'}<br><br></div>
	{/if}

<div style="margin-left: 10%; margin-bottom: 20px">
	<b>{l s='Your version :' mod='allinone_rewards'} {$module->version|escape:'htmlall':'UTF-8'}</b>
	{if version_compare($module->version, $version, '>=')}
	<span style="color: green; font-weight: bold; display: inline-block; margin-left: 20px">{l s='You are currently using the last version of this module' mod='allinone_rewards'}</span>
	{else}
	<span style="color: red; font-weight: bold; display: inline-block; margin-left: 20px">{l s='A new version of this module is available' mod='allinone_rewards'} - {l s='Version' mod='allinone_rewards'} {$version|escape:'htmlall':'UTF-8'}</span>
		{if $registered && !$asknewversion}
	<form method="post" style="display: inline; padding-left: 10px">
		<input class="button" type="submit" name="submitNewVersion" value="{l s='Ask for the new version' mod='allinone_rewards'}">
	</form>
		{/if}
	{/if}
</div>
<div id="news_list" style="height: 500px; overflow: auto; clear: both">
	{foreach from=$articles item=article}
	<div style="float: left; width: 10%; font-weight: bold">{$article['date']|escape:'htmlall':'UTF-8'}</div>
	<div style="float: left; width: 90%">
		<div style="font-weight: bold">{if $article['new']}<img src="{$module_template_dir|escape:'html':'UTF-8'}img/new.gif">{/if}{$article['title']|escape:'htmlall':'UTF-8'}</div>
		<div style="text-align: justify">{$article['text'] nofilter}</div>
	</div>
	<div class="clear" style="padding-bottom: 20px"></div>
	{/foreach}
</div>
{/if}
