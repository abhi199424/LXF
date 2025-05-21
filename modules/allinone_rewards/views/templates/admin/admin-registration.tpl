{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2025 Yann BONNAILLIE - ByWEB
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}
<div class="tabs" style="display: none">
	<ul>
		<li><a href="#tabs-{$object->name|escape:'htmlall':'UTF-8'}-1">{l s='Settings' mod='allinone_rewards'}</a></li>
		<li class="not_templated"><a href="#tabs-{$object->name|escape:'htmlall':'UTF-8'}-2">{l s='Notifications' mod='allinone_rewards'}</a></li>
		<li class="not_templated"><a href="{$module->getCurrentPage($object->name, true)|escape:'html':'UTF-8'}&stats=1">{l s='Statistics' mod='allinone_rewards'}</a></li>
	</ul>
	<div id="tabs-{$object->name|escape:'htmlall':'UTF-8'}-1">
		<form action="{$module->getCurrentPage($object->name)|escape:'html':'UTF-8'}" method="post">
			<input type="hidden" name="tabs-{$object->name|escape:'htmlall':'UTF-8'}" value="tabs-{$object->name|escape:'htmlall':'UTF-8'}-1" />
			<fieldset>
				<legend>{l s='General settings' mod='allinone_rewards'}</legend>
				<label>{l s='Activate the reward for account creation' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="registration_active_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="registration_active_on" name="registration_active" value="1" {if $registration_active}checked="checked"{/if} /> <label class="t" for="registration_active_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="registration_active_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="registration_active_off" name="registration_active" value="0" {if !$registration_active}checked="checked"{/if} /> <label class="t" for="registration_active_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<div>
					<label>{l s='Give the reward even if a sponsorship voucher has already been granted ?' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<label class="t" for="registration_reward_sponsored_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
						<input type="radio" id="registration_reward_sponsored_on" name="registration_reward_sponsored" value="1" {if $registration_reward_sponsored}checked="checked"{/if} /> <label class="t" for="registration_reward_sponsored_on">{l s='Yes' mod='allinone_rewards'}</label>
						<label class="t" for="registration_reward_sponsored_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
						<input type="radio" id="registration_reward_sponsored_off" name="registration_reward_sponsored" value="0" {if !$registration_reward_sponsored}checked="checked"{/if} /> <label class="t" for="registration_reward_sponsored_off">{l s='No' mod='allinone_rewards'}</label>
					</div>
				</div>
				<div class="clear">
					<table>
						<tr>
							<td class="label">{l s='Currency used by the customer' mod='allinone_rewards'}</td>
							<td align="left">{l s='Reward value' mod='allinone_rewards'}</td>
						</tr>
{foreach from=$currencies item=tmpcurrency}
						<tr>
							<td><label class="indent">{$tmpcurrency['name']|escape:'htmlall':'UTF-8'}</label></td>
							<td align="left"><input class="notvirtual {if $tmpcurrency['id_currency']==$currency->id}currency_default{/if}" type="text" size="8" maxlength="8" name="registration_reward_value_{$tmpcurrency['id_currency']|intval}" id="registration_reward_value_{$tmpcurrency['id_currency']|intval}" value="{$registration_reward_values[$tmpcurrency['id_currency']]|floatval}" onBlur="showVirtualValue(this, {$tmpcurrency['id_currency']|intval}, true)" /> <label class="t">{$tmpcurrency['sign']|escape:'htmlall':'UTF-8'} <span class="virtualvalue"></span></label>{if $tmpcurrency['id_currency'] != $currency->id}<a href="#" onClick="return convertCurrencyValue(this, 'registration_reward_value', '{$tmpcurrency['conversion_rate']|floatval}')"><img src="{$module_template_dir|escape:'html':'UTF-8'}img/convert.gif" style="vertical-align: middle !important"></a>{/if}
						</tr>
{/foreach}
					</table>
				</div>
			</fieldset>
			<div class="clear center"><input type="submit" name="submitRegistrationReward" id="submitRegistrationReward" value="{l s='Save settings' mod='allinone_rewards'}" class="button" /></div>
		</form>
	</div>
	<div id="tabs-{$object->name|escape:'htmlall':'UTF-8'}-2" class="not_templated">
		<form action="{$module->getCurrentPage($object->name)|escape:'html':'UTF-8'}" method="post">
			<input type="hidden" name="tabs-{$object->name|escape:'htmlall':'UTF-8'}" value="tabs-{$object->name|escape:'htmlall':'UTF-8'}-2" />
			<fieldset>
				<legend>{l s='Notifications' mod='allinone_rewards'}</legend>
				<label>{l s='Send a mail to the customer on reward validation' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="registration_mail_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="registration_mail_on" name="registration_mail" value="1" {if $registration_mail}checked="checked"{/if} /> <label class="t" for="registration_mail_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="registration_mail_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="registration_mail_off" name="registration_mail" value="0" {if !$registration_mail}checked="checked"{/if} /> <label class="t" for="registration_mail_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
			</fieldset>
			<div class="clear center"><input class="button" name="submitRegistrationNotifications" id="submitRegistrationNotifications" value="{l s='Save settings' mod='allinone_rewards'}" type="submit" /></div>
		</form>
	</div>
</div>