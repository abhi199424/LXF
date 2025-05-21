{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2025 Yann BONNAILLIE - ByWEB
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}
<form action="{$module->getCurrentPage()|escape:'html':'UTF-8'}" method="post">
	<fieldset>
		<legend>{l s='Initial conditions' mod='allinone_rewards'}</legend>
		<div align="center" style="color: red; font-weight: bold; padding-bottom: 10px">{l s='Since this is the first time you install this module, it must be initialized.' mod='allinone_rewards'}</div>
{if $nb_loyalty > 0}
		<div class="clear" style="padding-top: 10px"></div>
		<label style="font-weight: normal; text-align: left; padding-bottom: 10px; width: 50%;">{l s='Import the existing accounts from' mod='allinone_rewards'} "{$loyalty->displayName|escape:'htmlall':'UTF-8'}"</label>
		<div class="margin-form">
			<label class="t" for="loyalty_import_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
			<input type="radio" id="loyalty_import_on" name="loyalty_import" value="1" checked /> <label class="t" for="loyalty_import_on">{l s='Yes' mod='allinone_rewards'}</label>
			<label class="t" for="loyalty_import_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
			<input type="radio" id="loyalty_import_off" name="loyalty_import" value="0" /> <label class="t" for="loyalty_import_off">{l s='Yes' mod='allinone_rewards'}</label>
		</div>
{/if}
{if isset($loyalty) && $loyalty->active}
		<div class="clear" style="padding-bottom: 10px">{l s='The module' mod='allinone_rewards'} "{$loyalty->displayName|escape:'htmlall':'UTF-8'}" {l s='is actually active, it will be disabled automatically' mod='allinone_rewards'}</div>
{/if}
{if $nb_referral > 0}
		<div class="clear" style="padding-top: 10px"></div>
		<label style="font-weight: normal; text-align: left; padding-bottom: 10px; width: 50%;">{l s='Import the existing sponsorships from' mod='allinone_rewards'} "{$referral->displayName|escape:'htmlall':'UTF-8'}"</label>
		<div class="margin-form">
			<label class="t" for="referralprogram_import_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
			<input type="radio" id="referralprogram_import_on" name="{$prefix|escape:'htmlall':'UTF-8'}referralprogram_import" value="1" checked /> <label class="t" for="referralprogram_import_on">{l s='Yes' mod='allinone_rewards'}</label>
			<label class="t" for="referralprogram_import_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
			<input type="radio" id="referralprogram_import_off" name="{$prefix|escape:'htmlall':'UTF-8'}referralprogram_import" value="0" /> <label class="t" for="referralprogram_import_off">{l s='No' mod='allinone_rewards'}</label>
		</div>
{/if}
{if isset($referral) && $referral->active}
		<div class="clear" style="padding-bottom: 10px">{l s='The module' mod='allinone_rewards'} "{$referral->displayName|escape:'htmlall':'UTF-8'}" {l s='is actually active, it will be disabled automatically' mod='allinone_rewards'}</div>
{/if}
		<div class="clear center" style="text-align: center;"><input type="submit" name="submitInitialConditions" id="submitInitialConditions" value="{l s='   Initialize the module   ' mod='allinone_rewards'}" class="button" /></div>
	</fieldset>
</form>