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
		<li><a href="#tabs-{$object->name|escape:'htmlall':'UTF-8'}-3">{l s='Texts' mod='allinone_rewards'}</a></li>
		<li class="not_templated"><a href="{$module->getCurrentPage($object->name, true)|escape:'html':'UTF-8'}&stats=1">{l s='Statistics' mod='allinone_rewards'}</a></li>
	</ul>
	<div id="tabs-{$object->name|escape:'htmlall':'UTF-8'}-1">
		<form action="{$module->getCurrentPage($object->name)|escape:'html':'UTF-8'}" method="post">
			<input type="hidden" name="tabs-{$object->name|escape:'htmlall':'UTF-8'}" value="tabs-{$object->name|escape:'htmlall':'UTF-8'}-1" />
			<fieldset>
				<legend>{l s='General settings' mod='allinone_rewards'}</legend>
				<div>
					<label>{l s='Activate sponsorship program' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<label class="t" for="sponsorship_active_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
						<input type="radio" id="sponsorship_active_on" name="sponsorship_active" value="1" {if $sponsorship_active}checked="checked"{/if} /> <label class="t" for="sponsorship_active_on">{l s='Yes' mod='allinone_rewards'}</label>
						<label class="t" for="sponsorship_active_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
						<input type="radio" id="sponsorship_active_off" name="sponsorship_active" value="0" {if !$sponsorship_active}checked="checked"{/if} /> <label class="t" for="sponsorship_active_off">{l s='No' mod='allinone_rewards'}</label>
					</div>
				</div>
				<div class="clear"></div>
				<div>
					<label>{l s='Detect the sponsor code if it is entered in the voucher field in the cart summary' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<label class="t" for="rsponsorship_use_voucher_field_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
						<input type="radio" id="rsponsorship_use_voucher_field_on" name="rsponsorship_use_voucher_field" value="1" {if $rsponsorship_use_voucher_field}checked="checked"{/if} /> <label class="t" for="rsponsorship_use_voucher_field_on">{l s='Yes' mod='allinone_rewards'}</label>
						<label class="t" for="rsponsorship_use_voucher_field_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
						<input type="radio" id="rsponsorship_use_voucher_field_off" name="rsponsorship_use_voucher_field" value="0" {if !$rsponsorship_use_voucher_field}checked="checked"{/if} /> <label class="t" for="rsponsorship_use_voucher_field_off">{l s='No' mod='allinone_rewards'}</label>
					</div>
				</div>
				<div class="clear"></div>
				<div>
					<label>{l s='Allow to sponsor a customer already sponsored by another sponsor (affiliation)' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<label class="t" for="rsponsorship_multiple_sponsor_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
						<input type="radio" id="rsponsorship_multiple_sponsor_on" name="rsponsorship_multiple_sponsor" value="1" {if $rsponsorship_multiple_sponsor}checked="checked"{/if} /> <label class="t" for="rsponsorship_multiple_sponsor_on">{l s='Yes' mod='allinone_rewards'}</label>
						<label class="t" for="rsponsorship_multiple_sponsor_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
						<input type="radio" id="rsponsorship_multiple_sponsor_off" name="rsponsorship_multiple_sponsor" value="0" {if !$rsponsorship_multiple_sponsor}checked="checked"{/if} /> <label class="t" for="rsponsorship_multiple_sponsor_off">{l s='No' mod='allinone_rewards'}</label>
					</div>
				</div>
				<div class="clear"></div>
				<div>
					<label>{l s='Anonymize the sponsored information in the sponsor\'s account' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<label class="t" for="rsponsorship_anonymize_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
						<input type="radio" id="rsponsorship_anonymize_on" name="rsponsorship_anonymize" value="1" {if $rsponsorship_anonymize}checked="checked"{/if} /> <label class="t" for="rsponsorship_anonymize_on">{l s='Yes' mod='allinone_rewards'}</label>
						<label class="t" for="rsponsorship_anonymize_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
						<input type="radio" id="rsponsorship_anonymize_off" name="rsponsorship_anonymize" value="0" {if !$rsponsorship_anonymize}checked="checked"{/if} /> <label class="t" for="rsponsorship_anonymize_off">{l s='No' mod='allinone_rewards'}</label>
					 	<label class="t"> - {l s='The firstname / lastname / email will be replaced with * characters' mod='allinone_rewards'}</label>
					</div>
				</div>
				<div class="clear"></div>
				<label>{l s='Give a reward to the sponsor for his friends\' registrations' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="reward_registration_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" class="with_options" id="reward_registration_on" name="reward_registration" value="1" {if $reward_registration}checked="checked"{/if} /> <label class="t" for="reward_registration_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="reward_registration_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" class="with_options" id="reward_registration_off" name="reward_registration" value="0" {if !$reward_registration}checked="checked"{/if} /> <label class="t" for="reward_registration_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label>{l s='Give a reward to the sponsor for his friends\' orders' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="reward_order_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" class="with_options" id="reward_order_on" name="reward_order" value="1" {if $reward_order}checked="checked"{/if} /> <label class="t" for="reward_order_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="reward_order_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" class="with_options" id="reward_order_off" name="reward_order" value="0" {if !$reward_order}checked="checked"{/if} /> <label class="t" for="reward_order_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label>{l s='Give a welcome voucher to the sponsored friend' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="discount_gc_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" class="with_options" id="discount_gc_on" name="discount_gc" value="1" {if $discount_gc}checked="checked"{/if} /> <label class="t" for="discount_gc_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="discount_gc_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" class="with_options" id="discount_gc_off" name="discount_gc" value="0" {if !$discount_gc}checked="checked"{/if} /> <label class="t" for="discount_gc_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label>{l s='Display a sponsorship link on the product page' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="product_share_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="product_share_on" name="sponsorship_product_share" value="1" {if $sponsorship_product_share}checked="checked"{/if} /> <label class="t" for="product_share_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="product_share_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="product_share_off" name="sponsorship_product_share" value="0" {if !$sponsorship_product_share}checked="checked"{/if} /> <label class="t" for="product_share_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label>{l s='Propose the sponsorship program on the order confirmation page' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="after_order_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="after_order_on" name="after_order" value="1" {if $after_order}checked="checked"{/if} /> <label class="t" for="after_order_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="after_order_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="after_order_off" name="after_order" value="0" {if !$after_order}checked="checked"{/if} /> <label class="t" for="after_order_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label>{l s='Open a popup to propose sponsorship program to customers' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="popup_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" class="with_options" id="popup_on" name="popup" value="1" {if $popup}checked="checked"{/if} /> <label class="t" for="popup_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="popup_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" class="with_options" id="popup_off" name="popup" value="0" {if !$popup}checked="checked"{/if} /> <label class="t" for="popup_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear indent optional popup_optional_1">
					<div class="clear"></div>
					<label>{l s='Reset the last opening date for all customers' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<label class="t" for="popup_reset_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
						<input type="radio" id="popup_reset_on" name="popup_reset" value="1" /> <label class="t" for="popup_reset_on">{l s='Yes' mod='allinone_rewards'}</label>
						<label class="t" for="popup_reset_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
						<input type="radio" id="popup_reset_off" name="popup_reset" value="0" checked /> <label class="t" for="popup_reset_off">{l s='No' mod='allinone_rewards'}</label>
					</div>
					<div class="clear"></div>
					<label>{l s='Delay before opening the popup again for the same customer (in days)' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input type="text" size="2" maxlength="2" name="popup_delay" id="popup_delay" value="{$popup_delay|intval}" />
					</div>
				</div>
				<div class="clear"></div>
				<label class="t">{l s='Settings for sponsorship shared on Facebook' mod='allinone_rewards'}</label>
				<div class="clear" style="padding-top: 5px"></div>
				<label class="indent">{l s='Title to force for Facebook share' mod='allinone_rewards'}</label>
				<div class="margin-form translatable">
{foreach from=$languages item=language}
					<div class="lang_{$language['id_lang']|intval}" id="share_title_{$language['id_lang']|intval}" style="display: {if $language['id_lang']==$current_language_id}block{else}none{/if}; float: left;">
						<input size="60" type="text" name="share_title[{$language['id_lang']|intval}]" value="{$share_title[$language['id_lang']]|escape:'htmlall':'UTF-8'}" />
					</div>
{/foreach}
				</div>
				<div class="clear"></div>
				<label class="indent">{l s='Description to force for Facebook share' mod='allinone_rewards'}</label>
				<div class="margin-form translatable">
{foreach from=$languages item=language}
					<div class="lang_{$language['id_lang']|intval}" id="share_description_{$language['id_lang']|intval}" style="display: {if $language['id_lang']==$current_language_id}block{else}none{/if}; float: left;">
						<input size="60" type="text" name="share_description[{$language['id_lang']|intval}]" value="{$share_description[$language['id_lang']]|escape:'htmlall':'UTF-8'}" />
					</div>
{/foreach}
				</div>
				<div class="clear"></div>
				<label class="indent">{l s='Url of the image to force for Facebook share (at least 200*200px)' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="text" size="60" name="share_image_url" id="share_image_url" value="{$share_image_url|escape:'html':'UTF-8'}" />
					<br/><a href="https://developers.facebook.com/tools/debug/sharing/?q={$share_url|escape:'html':'UTF-8'}" target="_blank">{l s='Flush Facebook cache and test your settings' mod='allinone_rewards'}</a> ({l s='save your settings first' mod='allinone_rewards'})
				</div>
				<div class="clear"></div>
				<label>{l s='Number of lines displayed in the invitation form' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="text" size="3" maxlength="3" name="nb_friends" id="nb_friends" value="{$nb_friends|intval}" />
				</div>
				<div class="clear"></div>
				<label>{l s='Number of orders to be able to become a sponsor (0 is allowed)' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="text" size="3" maxlength="3" name="order_quantity_s" id="order_quantity_s" value="{$order_quantity_s|intval}" />
				</div>
				<div class="clear not_templated">
					<label>{l s='Customers groups allowed to sponsor their friends' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<select name="rsponsorship_groups[]" multiple="multiple" class="multiselect">
{foreach from=$groups item=group}
	{if !in_array($group['id_group'], $groups_off)}
							<option {if is_array($allowed_groups) && in_array($group['id_group'], $allowed_groups)}selected{/if} value="{$group['id_group']|intval}"> {$group['name']|escape:'htmlall':'UTF-8'}</option>
	{/if}
{/foreach}
						</select>
					</div>
				</div>
				<div class="clear"></div>
				<label>{l s='Redirection of the sponsorship link' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<select name="sponsorship_redirect" id="sponsorship_redirect">
						<option {if $sponsorship_redirect=='home'}selected{/if} value="home">{l s='No redirection' mod='allinone_rewards'}</option>
						<option {if $sponsorship_redirect=='form'}selected{/if} value="form">{l s='Subscription form' mod='allinone_rewards'}</option>
						<optgroup label="{l s='CMS page' mod='allinone_rewards'}">
{foreach from=$cms_list item=cms_file}
    						<option {if $sponsorship_redirect==$cms_file['id_cms']}selected{/if} value="{$cms_file['id_cms']|intval}" style="text-indent: 20px">{$cms_file['meta_title']|escape:'htmlall':'UTF-8'}</option>
{/foreach}
						</optgroup>
					</select>
				</div>
				<div class="clear"></div>
				<label>{l s='During subscription, add the sponsored customer into the following customers groups' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<select name="rsponsorship_child_groups[]" multiple="multiple" class="multiselect">
{foreach from=$groups item=group}
	{if !in_array($group['id_group'], $groups_off) && $group['id_group']!=$customer_group}
						<option {if is_array($child_groups) && in_array($group['id_group'], $child_groups)}selected{/if} value="{$group['id_group']|intval}"> {$group['name']|escape:'htmlall':'UTF-8'}</option>
	{/if}
{/foreach}
					</select>
				</div>
				<div class="clear"></div>
				<label>{l s='During subscription, modify the sponsored customer\'s default group' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<select name="rsponsorship_child_default_group" id="rsponsorship_child_default_group">
						<option value="0">{l s='No change' mod='allinone_rewards'}</option>
{foreach from=$groups item=group}
	{if !in_array($group['id_group'], $groups_off) && $group['id_group']!=$customer_group}
					<option {if $rsponsorship_child_default_group==$group['id_group']}selected{/if} value="{$group['id_group']|intval}"> {$group['name']|escape:'htmlall':'UTF-8'}</option>
	{/if}
{/foreach}
					</select>
				</div>
			</fieldset>

			<fieldset class="optional reward_registration_optional_1">
				<legend>{l s='Sponsor\'s settings - Rewards for registrations' mod='allinone_rewards'}</legend>
				<table class="reward_for_registration">
					<thead>
						<tr>
							<th>&nbsp;</th>
							<th>{l s='Nb of friends registrations' mod='allinone_rewards'}</th>
							<th>{l s='Repeat while it\'s lower or equal to' mod='allinone_rewards'}</th>
							<th>{l s='Reward by registration' mod='allinone_rewards'}</th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody>
{if $sponsorship_registr_multiple|count > 0}
	{foreach from=$sponsorship_registr_multiple key=key item=value}
						<tr>
							<td>{l s='Rule #' mod='allinone_rewards'}<span class="numrule">{$key+1|intval}</span></td>
							<td>{l s='is multiple of' mod='allinone_rewards'} <input type="text" name="rsponsorship_registr_multiple[]" value="{$sponsorship_registr_multiple[$key]|intval}" size="8" maxlength="8"></td>
							<td><input type="text" name="rsponsorship_registr_repeat[]" value="{$sponsorship_registr_repeat[$key]|intval}" size="8" maxlength="8"> {l s='(0 = unlimited)' mod='allinone_rewards'}</td>
							<td><input class="notvirtual" type="text" size="8" maxlength="8" name="rsponsorship_registr_value[]" value="{$sponsorship_registr_value[$key]|floatval}" onBlur="showVirtualValue(this, {$currency->id|intval}, true)" /> <label class="t">{$currency->sign|escape:'htmlall':'UTF-8'} <span class="virtualvalue"></span></label></td>
							<td><a href="#" onClick="return delSponsorshipRegistrationRule(this)"><img src="../img/admin/delete.gif" alt="{l s='Delete this rule' mod='allinone_rewards'}" align="absmiddle"></a></td>
						</tr>
	{/foreach}
{/if}
					</tbody>
				</table>
				<div class="clear center">
					<input class="button" style="margin-top: 10px" id="add_rule" value="{l s='Add a rule' mod='allinone_rewards'}" type="button" />
				</div>
			</fieldset>

			<fieldset class="optional reward_order_optional_1">
				<legend>{l s='Sponsor\'s settings - Rewards for orders' mod='allinone_rewards'}</legend>
				<label>{l s='Duration of the sponsorship (in days, 0=unlimited)' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="text" size="4" maxlength="4" id="rsponsorship_duration" name="rsponsorship_duration" value="{$rsponsorship_duration|intval}" />
				</div>
				<div class="clear"></div>
				<label>{l s='Get a reward for every orders from a sponsored friend, when total > 0 (shipping excluded)' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="discount_gc_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="reward_on_every_order_on" name="reward_on_every_order" value="1" {if $reward_on_every_order}checked="checked"{/if} /> <label class="t" for="discount_gc_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="discount_gc_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="reward_on_every_order_off" name="reward_on_every_order" value="0" {if !$reward_on_every_order}checked="checked"{/if} /> <label class="t" for="discount_gc_off">{l s='No, only for the first one' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label>{l s='Take in account the discounted products to calculate the total' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="rsponsorship_discounted_allowed_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="rsponsorship_discounted_allowed_on" name="rsponsorship_discounted_allowed" value="1" {if $rsponsorship_discounted_allowed}checked="checked"{/if} /> <label class="t" for="rsponsorship_discounted_allowed_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="rsponsorship_discounted_allowed_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="rsponsorship_discounted_allowed_off" name="rsponsorship_discounted_allowed" value="0" {if !$rsponsorship_discounted_allowed}checked="checked"{/if} /> <label class="t" for="rsponsorship_discounted_allowed_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label>{l s='Price to use to calculate the total (when the customer pays the VAT)' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input type="radio" id="rsponsorship_tax_off" name="rsponsorship_tax" value="0" {if !$rsponsorship_tax}checked="checked"{/if} /> <label class="t" for="rsponsorship_tax_off">{l s='VAT Excl.' mod='allinone_rewards'}</label>
						<input type="radio" id="rsponsorship_tax_on" name="rsponsorship_tax" value="1" {if $rsponsorship_tax}checked="checked"{/if} /> <label class="t" for="rsponsorship_tax_on">{l s='VAT Incl.' mod='allinone_rewards'}</label>
					</div>
				<div class="clear" style="padding-top: 5px"></div>
				<label class="t" style="width: 100% !important"><strong>{l s='Minimum amount required for the sponsored\'s order to unlock the sponsor\'s reward (discounted products included)' mod='allinone_rewards'}</strong></label>
				<div class="clear" style="padding-top: 5px"></div>
				<label class="indent">{l s='Calculated using' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="radio" id="unlock_shipping_on" name="unlock_shipping" value="1" {if $unlock_shipping}checked="checked"{/if} /> <label class="t" for="unlock_shipping_on" style="padding-right: 10px">{l s='Total with shipping included' mod='allinone_rewards'}</label>
					<input type="radio" id="unlock_shipping_off" name="unlock_shipping" value="0" {if !$unlock_shipping}checked="checked"{/if} /> <label class="t" for="unlock_shipping_off">{l s='Total without shipping' mod='allinone_rewards'}</label>
				</div>
				<div class="clear">
{foreach from=$currencies item=tmpcurrency}
				<div class="clear"></div>
				<label class="indent">{l s='Minimum value for an order in' mod='allinone_rewards'} {$tmpcurrency['name']|escape:'htmlall':'UTF-8'}</label>
				<div class="margin-form"><input {if $tmpcurrency['id_currency']==$currency->id}class="currency_default"{/if} type="text" size="8" maxlength="8" name="unlock_gc_{$tmpcurrency['id_currency']|intval}" id="unlock_gc_{$tmpcurrency['id_currency']|intval}" value="{$unlock_gc[$tmpcurrency['id_currency']]|floatval}" /> <label class="t">{$tmpcurrency['sign']|escape:'htmlall':'UTF-8'}</label>{if $tmpcurrency['id_currency']!=$currency->id}<a href="#" onClick="return convertCurrencyValue(this, 'unlock_gc', '{$tmpcurrency['conversion_rate']|floatval}')"><img src="{$module_template_dir|escape:'html':'UTF-8'}img/convert.gif" style="vertical-align: middle !important"></a>{/if}</div>
{/foreach}
				</div>
				<div class="clear" style="padding-top: 10px"></div>
				<label class="t" style="width: 100% !important"><strong>{l s='If you want to reward the sponsors on several levels, you can define as many levels as necessary' mod='allinone_rewards'}</strong></label>
{foreach from=$configuration['reward_type'] key=level item=reward_type}
				<div class="clear level_information">
					<label class="level"><strong>{l s='Reward\'s settings for level' mod='allinone_rewards'} <span class="numlevel">{$level + 1|intval}</span></strong> <a href="#" onClick="return delSponsorshipLevel(this)"><img src="../img/admin/delete.gif" alt="{l s='Delete this level' mod='allinone_rewards'}" align="absmiddle"></a></label>
					<div class="clear"></div>
					<label class="indent">{l s='Reward type' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input type="radio" class="with_options" name="reward_type_s[{$level|intval}]" value="1" {if $reward_type==1}checked="checked"{/if}/> <label class="t" style="padding-right: 10px">{l s='Fixed amount' mod='allinone_rewards'}</label>
						<input type="radio" class="with_options" name="reward_type_s[{$level|intval}]" value="2" {if $reward_type==2}checked="checked"{/if}/> <label class="t">{l s='% of the order\'s total (shipping excluded)' mod='allinone_rewards'}</label>
						<input type="radio" class="with_options" name="reward_type_s[{$level|intval}]" value="3" {if $reward_type==3}checked="checked"{/if}/> <label class="t">{l s='Product per product' mod='allinone_rewards'}</label>
					</div>
					<div class="clear"></div>
					<div class="optional reward_type_s[{$level|intval}]_optional_1">
	{foreach from=$currencies item=tmpcurrency}
						<div class="clear"></div>
						<label class="indent">{l s='Reward value for an order in' mod='allinone_rewards'} {$tmpcurrency['name']|escape:'htmlall':'UTF-8'}</label>
						<div class="margin-form"><input class="notvirtual {if $tmpcurrency['id_currency']==$currency->id}currency_default{/if}" type="text" size="8" maxlength="8" name="reward_value_s[{$tmpcurrency['id_currency']|intval}][{$level|intval}]}" value="{$configuration['reward_value'][$level][$tmpcurrency['id_currency']]|floatval}" onBlur="showVirtualValue(this, {$tmpcurrency['id_currency']|intval}, true)" /> <label class="t"> {$tmpcurrency['sign']|escape:'htmlall':'UTF-8'} <span class="virtualvalue"></span></label>{if $tmpcurrency['id_currency']!=$currency->id}<a href="#" onClick="return convertCurrencyValue(this, 'reward_value_s', '{$tmpcurrency['conversion_rate']|floatval}')"><img src="{$module_template_dir|escape:'html':'UTF-8'}img/convert.gif" style="vertical-align: middle !important"></a>{/if}</div>
	{/foreach}
					</div>
					<div class="clear"></div>
					<div class="optional reward_type_s[{$level|intval}]_optional_2">
						<label class="indent">{l s='Percentage' mod='allinone_rewards'}</label>
						<div class="margin-form">
							<input type="text" size="3" name="reward_percentage[{$level|intval}]" value="{$configuration['reward_percentage'][$level]|floatval}" /> %
						</div>
					</div>
					<div class="clear optional reward_type_s[{$level|intval}]_optional_3">
						<label></label>
						<div class="margin-form">{l s='You can configure each product individually from the product sheet' mod='allinone_rewards'}</div>
						<div class="clear"></div>
						<label class="indent">{l s='Default reward for product with no custom value' mod='allinone_rewards'}</label>
						<div class="margin-form">
							<input class="notvirtual product_per_product" type="text" size="3" name="rsponsorship_def_product_reward[{$level|intval}]" value="{$configuration['default_product_reward'][$level]|floatval}" onBlur="showVirtualValue(this, {$currency->id|intval}, true)" />
							<select class="product_per_product" name="rsponsorship_def_product_type[{$level|intval}]" onChange="showVirtualValue(this, {$currency->id|intval}, true)">
								<option {if $configuration['default_product_reward'][$level] == 0}selected{/if} value="0">% {l s='of its own price' mod='allinone_rewards'}</option>
								<option {if $configuration['default_product_type'][$level] == 1}selected{/if} value="1"> {$currency->sign|escape:'htmlall':'UTF-8'}</option>
							</select>
							&nbsp;<span class="virtualvalue"></span>
						</div>
					</div>
				</div>
{/foreach}
				<div class="clear">
					<label>{l s='All next levels will use the settings from level' mod='allinone_rewards'} <span id="unlimited_level">{$configuration['reward_type']|count}</span></label>
					<div class="margin-form">
						<label class="t" for="unlimited_levels_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
						<input type="radio" id="unlimited_levels_on" name="unlimited_levels" value="1" {if $configuration['unlimited']}checked="checked"{/if} /> <label class="t" for="unlimited_levels_on">{l s='Yes' mod='allinone_rewards'}</label>
						<label class="t" for="unlimited_levels_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
						<input type="radio" id="unlimited_levels_off" name="unlimited_levels" value="0" {if !$configuration['unlimited']}checked="checked"{/if} /> <label class="t" for="unlimited_levels_off">{l s='No, we don\'t need any additional levels' mod='allinone_rewards'}</label>
					</div>
					<div class="clear center">
						<input class="button" style="margin-top: 10px" id="add_level" value="{l s='Add a level' mod='allinone_rewards'}" type="button" />
					</div>
				</div>
			</fieldset>

			<fieldset id="sponsored" class="optional discount_gc_optional_1">
				<legend>{l s='Sponsored\'s settings' mod='allinone_rewards'}</legend>
				{l s='You can manually set all settings for the voucher offered to the sponsored friend, or choose a model among the pre-existing vouchers. In this case the model will be duplicated during the sponsored registration, with the same settings. That allows you a more accurate choice of the characteristics. For example, you can offer a free product, put restrictions on carriers or on products concerned by the discount...' mod='allinone_rewards'}
				<div class="clear"></div>
				<br/><label>{l s='Voucher to offer' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="radio" class="with_options" id="real_voucher_gc_off" name="real_voucher_gc" value="0" {if !$real_voucher_gc}checked="checked"{/if} /> <label class="t" for="real_voucher_gc_off">{l s='Define the settings' mod='allinone_rewards'}</label>
					<input type="radio" class="with_options" id="real_voucher_gc_on" name="real_voucher_gc" value="1" {if $real_voucher_gc}checked="checked"{/if} /> <label class="t" for="real_voucher_gc_on">{l s='Choose an existing voucher (more possibilities)' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label>{l s='Prefix for the voucher code (can be empty, or at least 3 letters long)' mod='allinone_rewards'}<br><small>{l s='If the prefix is empty, then the voucher will be automatically added to the cart' mod='allinone_rewards'}</small></label>
				<div class="margin-form">
					<input type="text" size="10" maxlength="10" id="voucher_prefix_gc" name="voucher_prefix_gc" value="{$voucher_prefix_gc|escape:'htmlall':'UTF-8'}" />
				</div>
				<div class="clear" style="padding-top: 10px"></div>
				<label>{l s='Validity of the voucher (in days)' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="text" size="4" maxlength="4" id="voucher_duration_gc" name="voucher_duration_gc" value="{$voucher_duration_gc|intval}" />
				</div>
				<div class="clear"></div>
				<label>{l s='Voucher details (will appear in cart next to voucher code)' mod='allinone_rewards'}</label>
				<div class="margin-form translatable">
{foreach from=$languages item=language}
					<div class="lang_{$language['id_lang']|intval}" id="descgc_{$language['id_lang']|intval}" style="display: {if $language['id_lang']==$current_language_id}block{else}none{/if}; float: left;">
						<input size="30" type="text" name="description_gc[{$language['id_lang']|intval}]" value="{$description_gc[$language['id_lang']]|escape:'htmlall':'UTF-8'}" />
					</div>
{/foreach}
				</div>
				<div class="clear optional real_voucher_gc_optional_1">
					<label>{l s='Code of the voucher model (must be available for all and not highlighted)' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input type="text" size="12" id="real_code_gc" name="real_code_gc" value="{$real_code_gc|escape:'htmlall':'UTF-8'}" />{if isset($cart_rule)}&nbsp<a href="?tab=AdminCartRules&id_cart_rule={$cart_rule->id|intval}&updatecart_rule&token={$token|escape:'html':'UTF-8'}" target="_blank">{l s='View or edit the voucher model' mod='allinone_rewards'}</a>{/if}
					</div>
					<div class="clear"></div>
					<label>{l s='Description of the voucher that will be used in the emails' mod='allinone_rewards'}<br/><small>{l s='Example: This voucher will give you a surprise gift' mod='allinone_rewards'}</small></label>
					<div class="margin-form translatable">
{foreach from=$languages item=language}
						<div class="lang_{$language['id_lang']|intval}" id="real_description_gc_{$language['id_lang']|intval}" style="display: {if $language['id_lang']==$current_language_id}block{else}none{/if}; float: left;">
							<textarea class="rte autoload_rte" cols="120" rows="25" name="real_description_gc[{$language['id_lang']|intval}]">{$real_description_gc[$language['id_lang']]|escape:'htmlall':'UTF-8'}</textarea>
						</div>
{/foreach}
					</div>
				</div>
				<div class="clear optional real_voucher_gc_optional_0">
					<label>{l s='Number of times the voucher can be used by the sponsored friend' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input type="text" size="4" maxlength="4" id="voucher_quantity_gc" name="voucher_quantity_gc" value="{$voucher_quantity_gc|intval}" />
					</div>
					<div class="clear"></div>
					<label>{l s='Free shipping' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<label class="t" for="freeshipping_gc_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
						<input type="radio" id="freeshipping_gc_on" name="freeshipping_gc" value="1" {if $freeshipping_gc}checked="checked"{/if} /> <label class="t" for="freeshipping_gc_on">{l s='Yes' mod='allinone_rewards'}</label>
						<label class="t" for="freeshipping_gc_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
						<input type="radio" id="freeshipping_gc_off" name="freeshipping_gc" value="0" {if !$freeshipping_gc}checked="checked"{/if} /> <label class="t" for="freeshipping_gc_off">{l s='No' mod='allinone_rewards'}</label>
					</div>
					<div class="clear"></div>
					<label>{l s='Apply a discount' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input onClick="$('#sponsored td.voucher_value').html('{l s='Voucher %' mod='allinone_rewards'}');$('#sponsored td.value_cols').show();$('#behavior_gc').hide();$('#voucher_behavior_gc').val(0)" type="radio" id="discount_type_gc_1" name="discount_type_gc" value="1" {if $discount_type_gc==1}checked="checked"{/if} /> <label class="t" for="discount_type_gc_1" style="padding-right: 10px">{l s='Percentage' mod='allinone_rewards'}</label>
						<input onClick="$('#sponsored td.value_cols').hide();$('#sponsored td.voucher_value').html('{l s='Voucher value' mod='allinone_rewards'}');$('#sponsored td.value_cols').show();$('#behavior_gc').show()" type="radio" id="discount_type_gc_2" name="discount_type_gc" value="2" {if $discount_type_gc==2}checked="checked"{/if} /> <label class="t" for="discount_type_gc_2" style="padding-right: 10px">{l s='Amount' mod='allinone_rewards'}</label>
						<input onClick="$('#sponsored td.value_cols').hide();$('#behavior_gc').hide();$('#voucher_behavior_gc').val(0)" type="radio" id="discount_type_gc_0" name="discount_type_gc" value="0" {if $discount_type_gc==0}checked="checked"{/if} /> <label class="t" for="discount_type_gc_0">{l s='None' mod='allinone_rewards'}</label>
					</div>
					<div class="clear"></div>
					<label>{l s='Allowed categories' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input class="with_options" type="radio" id="all_categories_on" name="rsponsorship_all_categories" value="0" {if !$rsponsorship_all_categories}checked="checked"{/if} /> <label class="t" for="all_categories_on">{l s='Choose categories' mod='allinone_rewards'}</label>&nbsp;
						<input class="with_options" type="radio" id="all_categories_off" name="rsponsorship_all_categories" value="1" {if $rsponsorship_all_categories}checked="checked"{/if} /> <label class="t" for="all_categories_off">{l s='All categories' mod='allinone_rewards'}</label>
						<div class="optional rsponsorship_all_categories_optional_0" style="padding-top: 15px">
							{$categories nofilter}
						</div>
					</div>
					<div class="clear"></div>
					<div id="behavior_gc" style="display:{if $discount_type_gc==2}block{else}none{/if}">
						<div class="clear"></div>
						<label>{l s='If the voucher is not depleted when used' mod='allinone_rewards'}</label>&nbsp;
						<div class="margin-form">
							<select name="voucher_behavior_gc" id="voucher_behavior_gc">
								<option {if !$voucher_behavior_gc}selected{/if} value="0">{l s='Cancel the remaining amount' mod='allinone_rewards'}</option>
								<option {if $voucher_behavior_gc}selected{/if} value="1">{l s='Create a new voucher with remaining amount' mod='allinone_rewards'}</option>
							</select>
						</div>
					</div>
					<div class="clear"></div>
					<label>{l s='Cumulative with other vouchers' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<label class="t" for="cumulative_voucher_gc_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
						<input type="radio" id="cumulative_voucher_gc_on" name="cumulative_voucher_gc" value="1" {if $cumulative_voucher_gc}checked="checked"{/if} /> <label class="t" for="cumulative_voucher_gc_on">{l s='Yes' mod='allinone_rewards'}</label>
						<label class="t" for="cumulative_voucher_gc_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
						<input type="radio" id="cumulative_voucher_gc_off" name="cumulative_voucher_gc" value="0" {if !$cumulative_voucher_gc}checked="checked"{/if} /> <label class="t" for="cumulative_voucher_gc_off">{l s='No' mod='allinone_rewards'}</label>
					</div>
					<div class="clear"></div>
					<label>{l s='The minimum order\'s amount to use the voucher includes tax' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<label class="t" for="include_tax_gc_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
						<input type="radio" id="include_tax_gc_on" name="include_tax_gc" value="1" {if $include_tax_gc}checked="checked"{/if} /> <label class="t" for="include_tax_gc_on">{l s='Yes' mod='allinone_rewards'}</label>
						<label class="t" for="include_tax_gc_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
						<input type="radio" id="include_tax_gc_off" name="include_tax_gc" value="0" {if !$include_tax_gc}checked="checked"{/if} /> <label class="t" for="include_tax_gc_off">{l s='No' mod='allinone_rewards'}</label>
					</div>
					<div class="clear"></div>
					<div>
						<table>
							<tr>
								<td class="label" style="font-weight: bold">{l s='Currency used by the sponsored when registering' mod='allinone_rewards'}</td>
								<td width="165" class="voucher_value value_cols" style="font-weight: bold; display:{if $discount_type_gc==0}none{else}block{/if}">{if $discount_type_gc==1}{l s='Voucher %' mod='allinone_rewards'}{else}{l s='Voucher value' mod='allinone_rewards'}{/if}</td>
								<td class="value_cols" style="width: 30px">&nbsp;</td>
								<td width="200" style="font-weight: bold">{l s='Minimum order\'s amount' mod='allinone_rewards'}</td>
							</tr>
{foreach from=$currencies item=tmpcurrency}
							<tr>
								<td><label class="indent">{$tmpcurrency['name']|escape:'htmlall':'UTF-8'}</label></td>
								<td align="left" class="value_cols" style="display:{if $discount_type_gc==0}none{else}block{/if}"><input {if $tmpcurrency['id_currency']==$currency->id}class="currency_default"{/if} type="text" size="8" maxlength="8" name="discount_value_gc_{$tmpcurrency['id_currency']|intval}" id="discount_value_gc_{$tmpcurrency['id_currency']|intval}" value="{$discount_value_gc[$tmpcurrency['id_currency']]|floatval}" />{if $tmpcurrency['id_currency']!=$currency->id} <a href="#" onClick="return convertCurrencyValue(this, 'discount_value_gc', '{$tmpcurrency['conversion_rate']|floatval}')"><img src="{$module_template_dir|escape:'html':'UTF-8'}img/convert.gif" style="vertical-align: middle !important"></a>{/if}</td>
								<td class="value_cols">&nbsp;</td>
								<td align="left"><input {if $tmpcurrency['id_currency']==$currency->id}class="currency_default"{/if} type="text" size="8" maxlength="8" name="minimum_value_gc_{$tmpcurrency['id_currency']|intval}" id="minimum_value_gc{$tmpcurrency['id_currency']|intval}" value="{$minimum_value_gc[$tmpcurrency['id_currency']]|floatval}" />{if $tmpcurrency['id_currency']!=$currency->id} <a href="#" onClick="return convertCurrencyValue(this, 'minimum_value_gc', '{$tmpcurrency['conversion_rate']|floatval}')"><img src="{$module_template_dir|escape:'html':'UTF-8'}img/convert.gif" style="vertical-align: middle !important"></a>{/if}</td>
							</tr>
{/foreach}
						</table>
					</div>
				</div>
			</fieldset>
			<div class="clear center"><input class="button" name="submitSponsorship" id="submitSponsorship" value="{l s='Save settings' mod='allinone_rewards'}" type="submit" /></div>
		</form>
	</div>

	<div id="tabs-{$object->name|escape:'htmlall':'UTF-8'}-2" class="not_templated">
		<form action="{$module->getCurrentPage($object->name)|escape:'html':'UTF-8'}" method="post">
			<input type="hidden" name="tabs-{$object->name|escape:'htmlall':'UTF-8'}" value="tabs-{$object->name|escape:'htmlall':'UTF-8'}-2" />
			<fieldset>
				<legend>{l s='Notifications' mod='allinone_rewards'}</legend>
				<label>{l s='Send a mail to the admin on sponsored registration' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="mail_admin_registration_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="mail_admin_registration_on" name="mail_admin_registration" value="1" {if $mail_admin_registration}checked="checked"{/if} /> <label class="t" for="mail_admin_registration_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="mail_admin_registration_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="mail_admin_registration_off" name="mail_admin_registration" value="0" {if !$mail_admin_registration}checked="checked"{/if} /> <label class="t" for="mail_admin_registration_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label>{l s='Send a mail to the admin on sponsored order' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="mail_admin_order_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="mail_admin_order_on" name="mail_admin_order" value="1" {if $mail_admin_order}checked="checked"{/if} /> <label class="t" for="mail_admin_order_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="mail_admin_order_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="mail_admin_order_off" name="mail_admin_order" value="0" {if !$mail_admin_order}checked="checked"{/if} /> <label class="t" for="mail_admin_order_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label>{l s='Send a mail to the sponsor on sponsored registration' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="mail_sponsor_registration_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="mail_sponsor_registration_on" name="mail_sponsor_registration" value="1" {if $mail_sponsor_registration}checked="checked"{/if} /> <label class="t" for="mail_sponsor_registration_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="mail_sponsor_registration_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="mail_sponsor_registration_off" name="mail_sponsor_registration" value="0" {if !$mail_sponsor_registration}checked="checked"{/if} /> <label class="t" for="mail_sponsor_registration_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<div>
					<label>{l s='Send a mail to the sponsor(s) on sponsored order' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<label class="t" for="mail_sponsor_order_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
						<input type="radio" id="mail_sponsor_order_on" name="mail_sponsor_order" value="1" {if $mail_sponsor_order}checked="checked"{/if} /> <label class="t" for="mail_sponsor_order_on">{l s='Yes' mod='allinone_rewards'}</label>
						<label class="t" for="mail_sponsor_order_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
						<input type="radio" id="mail_sponsor_order_off" name="mail_sponsor_order" value="0" {if !$mail_sponsor_order}checked="checked"{/if} /> <label class="t" for="mail_sponsor_order_off">{l s='No' mod='allinone_rewards'}</label>
					</div>
					<div class="clear"></div>
					<label>{l s='Send a mail to the sponsor(s) on reward validation/cancellation' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<label class="t" for="mail_sponsor_validation_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
						<input type="radio" id="mail_sponsor_validation_on" name="mail_sponsor_validation" value="1" {if $mail_sponsor_validation}checked="checked"{/if} /> <label class="t" for="mail_sponsor_validation_on">{l s='Yes' mod='allinone_rewards'}</label>
						<label class="t" for="mail_sponsor_validation_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
						<input type="radio" id="mail_sponsor_validation_off" name="mail_sponsor_validation" value="0" {if !$mail_sponsor_validation}checked="checked"{/if} /> <label class="t" for="mail_sponsor_validation_off">{l s='No' mod='allinone_rewards'}</label>
					</div>
					<div class="clear"></div>
					<label>{l s='Send a mail to the sponsor(s) on reward modification' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<label class="t" for="mail_sponsor_cancel_product_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
						<input type="radio" id="mail_sponsor_cancel_product_on" name="mail_sponsor_cancel_product" value="1" {if $mail_sponsor_cancel_product}checked="checked"{/if} /> <label class="t" for="mail_sponsor_cancel_product_on">{l s='Yes' mod='allinone_rewards'}</label>
						<label class="t" for="mail_sponsor_cancel_product_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
						<input type="radio" id="mail_sponsor_cancel_product_off" name="mail_sponsor_cancel_product" value="0" {if !$mail_sponsor_cancel_product}checked="checked"{/if} /> <label class="t" for="mail_sponsor_cancel_product_off">{l s='No' mod='allinone_rewards'}</label>
					</div>
				</div>
			</fieldset>
			<div class="clear center"><input class="button" name="submitSponsorshipNotifications" id="submitSponsorshipNotifications" value="{l s='Save settings' mod='allinone_rewards'}" type="submit" /></div>
		</form>
	</div>

	<div id="tabs-{$object->name|escape:'htmlall':'UTF-8'}-3">
		<form method="post" action="{$module->getCurrentPage($object->name)|escape:'html':'UTF-8'}" enctype="multipart/form-data">
			<input type="hidden" name="tabs-{$object->name|escape:'htmlall':'UTF-8'}" value="tabs-{$object->name|escape:'htmlall':'UTF-8'}-3" />
			<fieldset>
				<legend>{l s='Text for the sponsorship form displayed in the customer account' mod='allinone_rewards'}</legend>
				<div class="translatable">
{foreach from=$languages item=language}
					<div class="lang_{$language['id_lang']|intval}" id="account_{$language['id_lang']|intval}" style="display: {if $language['id_lang']==$current_language_id}block{else}none{/if};float: left;">
						<textarea class="rte autoload_rte" cols="120" rows="25" name="account_txt[{$language['id_lang']|intval}]">{$account_txt[$language['id_lang']]|escape:'htmlall':'UTF-8'}</textarea>
					</div>
{/foreach}

				</div>
			</fieldset>
			<fieldset>
				<legend>{l s='Text for the sponsorship popup displayed after an order' mod='allinone_rewards'}</legend>
				<div class="translatable">
{foreach from=$languages item=language}
					<div class="lang_{$language['id_lang']|intval}" id="order_{$language['id_lang']|intval}" style="display: {if $language['id_lang']==$current_language_id}block{else}none{/if};float: left;">
						<textarea class="rte autoload_rte" cols="120" rows="25" name="order_txt[{$language['id_lang']|intval}]">{$order_txt[$language['id_lang']]|escape:'htmlall':'UTF-8'}</textarea>
					</div>
{/foreach}
				</div>
			</fieldset>
			<fieldset>
				<legend>{l s='Text for the sponsorship popup displayed every X days' mod='allinone_rewards'}</legend>
				<div class="translatable">
{foreach from=$languages item=language}
					<div class="lang_{$language['id_lang']|intval}" id="popup_{$language['id_lang']|intval}" style="display: {if $language['id_lang']==$current_language_id}block{else}none{/if};float: left;">
						<textarea class="rte autoload_rte" cols="120" rows="25" name="popup_txt[{$language['id_lang']|intval}]">{$popup_txt[$language['id_lang']]|escape:'htmlall':'UTF-8'}</textarea>
					</div>
{/foreach}
				</div>
			</fieldset>
			<fieldset>
				<legend>{l s='Sponsorship program rules' mod='allinone_rewards'}</legend>
				<div class="translatable">
{foreach from=$languages item=language}
					<div class="lang_{$language['id_lang']|intval}" id="rules_{$language['id_lang']|intval}" style="display: {if $language['id_lang']==$current_language_id}block{else}none{/if};float: left;">
						<textarea class="rte autoload_rte" cols="120" rows="25" name="rules_txt[{$language['id_lang']|intval}]">{$rules_txt[$language['id_lang']]|escape:'htmlall':'UTF-8'}</textarea>
					</div>
{/foreach}
				</div>
			</fieldset>
			<div class="clear center"><input type="submit" name="submitSponsorshipText" value="{l s='Save settings' mod='allinone_rewards'}" class="button"/></div>
		</form>
	</div>
</div>