{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2025 Yann BONNAILLIE - ByWEB
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}
<div id="tools">
	<form action="{$module->getCurrentPage($object->name)|escape:'html':'UTF-8'}" method="post">
	<input type="hidden" name="tabs-{$object->name|escape:'htmlall':'UTF-8'}" value="tabs-{$object->name|escape:'htmlall':'UTF-8'}-1" />
	<input type="hidden" id="mass_action" name="mass_action" />
	<fieldset id="mass_update">
		<legend>{l s='Mass update products rewards, based on their default category or manufacturer' mod='allinone_rewards'}</legend>
		{l s='Be carefull, all previous rewards defined on your selection will be deleted before a mass update, and no integrity control will be checked on your settings (dates consistency, templates, levels...)' mod='allinone_rewards'}<br><br>
		<label>{l s='Reward type' mod='allinone_rewards'}</label>
		<div class="margin-form">
			<input type="radio" checked class="with_options" id="mass_update_plugin_loyalty" name="mass_update_plugin" value="loyalty" /> <label class="t" for="mass_update_plugin_loyalty">{l s='Loyalty' mod='allinone_rewards'}</label>
			<input type="radio" class="with_options" id="mass_update_plugin_sponsorship" name="mass_update_plugin" value="sponsorship" /> <label class="t" for="mass_update_plugin_sponsorship">{l s='Sponsorship' mod='allinone_rewards'}</label>
		</div>
		<div class="clear"></div>
		<label>{l s='Related to template' mod='allinone_rewards'}</label>
		<div class="margin-form">
			<select id="mass_update_loyalty_template" name="mass_update_loyalty_template" class="form-control optional mass_update_plugin_optional_loyalty">
				<option value="-1">{l s='All templates' mod='allinone_rewards'}</option>
				<option value="0">{l s='Default template' mod='allinone_rewards'}</option>';
{foreach from=$loyalty_templates item=template}
				<option value="{$template['id_template']|intval}">{$template['name']|escape:'htmlall':'UTF-8'}</option>
{/foreach}
			</select>
			<select id="mass_update_sponsorship_template" name="mass_update_sponsorship_template" class="form-control optional mass_update_plugin_optional_sponsorship">
				<option value="-1">{l s='All templates' mod='allinone_rewards'}</option>
				<option value="0">{l s='Default template' mod='allinone_rewards'}</option>';
{foreach from=$sponsorship_templates item=template}
				<option value="{$template['id_template']|intval}">{$template['name']|escape:'htmlall':'UTF-8'}</option>
{/foreach}
			</select>
		</div>
		<div id="mass_update_level" class="clear optional mass_update_plugin_optional_sponsorship">
			<label>{l s='Level of the sponsor' mod='allinone_rewards'}</label>
			<div class="margin-form">
				<select id="mass_update_sponsorship_level" name="mass_update_sponsorship_level" class="form-control">
{for $i=1 to 100}
					<option value="{$i|intval}">{$i|intval}</option>
{/for}
				</select>
			</div>
		</div>
		<div class="clear"></div>
		<label>{l s='Reward value (not needed for "Mass reset")' mod='allinone_rewards'}</label>
		<div class="margin-form">
			<input type="text" size="8" maxlength="8" id="mass_update_value" name="mass_update_value" />
			<select id="mass_update_type" name="mass_update_type" style="display: inline-block" class="form-control">
				<option value="0">{l s='% of the product price' mod='allinone_rewards'}</option>
				<option value="1">{$currency->sign|escape:'htmlall':'UTF-8'}</option>
			</select>
		</div>
		<div class="clear"></div>
		<label>{l s='Manufacturers' mod='allinone_rewards'}</label>
		<div class="margin-form">
			<select name="mass_update_manufacturers[]" multiple="multiple" class="multiselect">
{foreach from=$manufacturers_list item=manufacturer}
				<option {if is_array($manufacturers) && in_array($manufacturer['id_manufacturer'], $manufacturers)}selected{/if} value="{$manufacturer['id_manufacturer']|intval}"> {$manufacturer['name']|escape:'htmlall':'UTF-8'}</option>
{/foreach}
			</select>
		</div>
		<div class="clear"></div>
		<label>{l s='Categories' mod='allinone_rewards'}</label>
		<div class="margin-form">
			<div style="padding-top: 15px">
				{$categories nofilter}
			</div>
		</div>
		<div class="clear"></div>
		<label>&nbsp;</label>
		<div class="margin-form">
			<input type="submit" name="submitMassUpdate" id="submitMassUpdate" value="{l s='Mass update' mod='allinone_rewards'}" class="button" />&nbsp;
			<input type="submit" name="submitMassReset" id="submitMassReset" value="{l s='Mass reset' mod='allinone_rewards'}" class="button" />
		</div>
	</fieldset>
	<fieldset>
		<legend>{l s='Delete module data' mod='allinone_rewards'}</legend>
		{l s='Customer data associated with the module will be deleted (rewards, sponsorship, reward payments, ...). The module configuration won\'t be deleted.' mod='allinone_rewards'}<br><br>
		<script>
			var text1 = "{l s='Are you sure you want to completely delete all module data ? It will be irreversible.' mod='allinone_rewards'}";
			var text2 = "{l s='Delete module data' mod='allinone_rewards'}";
		</script>
		<input type="button" value="{l s='Delete module data' mod='allinone_rewards'}" class="button" onclick="deleteAllModuleData($(this), text1, text2)"/>
	</fieldset>
	</form>
</div>