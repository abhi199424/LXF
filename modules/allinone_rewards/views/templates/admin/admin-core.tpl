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
		<li class="not_templated"><a href="{$module->getCurrentPage($object->name, true)|escape:'html':'UTF-8'}&payments=1">{l s='Payment requests' mod='allinone_rewards'}</a></li>
	</ul>

	<div id="tabs-{$object->name|escape:'htmlall':'UTF-8'}-1">
		<form action="{$module->getCurrentPage($object->name)|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data">
			<input type="hidden" name="tabs-{$object->name|escape:'htmlall':'UTF-8'}" value="tabs-{$object->name|escape:'htmlall':'UTF-8'}-1" />
			<fieldset>
				{l s='All rewards will be calculated and stored into the database with the default currency. You can choose to use real money or "points" at any time, and you can change the values of the points without any problem, it will only affect the display of the rewards but not their real values. If you create different templates for the rewards account and set different values for the "points" on each template, the others tabs will only display the points\' value according to the default rewards template but the final value for the customer will be calculated with the value depending on the template he is linked to.' mod='allinone_rewards'}
				<label class="t" style="width: 100% !important; padding-top: 20px; display: block"><strong>{l s='Rewards type' mod='allinone_rewards'}</strong></label>
				<div class="clear" style="padding-top: 5px"></div>
				<label class="indent">{l s='What kind of rewards will be used by the module' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="radio" class="with_options" id="rewards_virtual_on" name="rewards_virtual" value="1" {if $rewards_virtual}checked="checked"{/if} /> <label class="t" for="rewards_virtual_on">{l s='Virtual points' mod='allinone_rewards'}</label>&nbsp;
					<input type="radio" class="with_options" id="rewards_virtual_off" name="rewards_virtual" value="0" {if !$rewards_virtual}checked="checked"{/if} /> <label class="t" for="rewards_virtual_off">{l s='Real money' mod='allinone_rewards'}</label>
				</div>
				<div class="clear indent optional rewards_virtual_optional_1">
					<div class="clear"></div>
					<div>
						<table>
							<tr>
								<td class="label indent">{l s='Currency used by the customer' mod='allinone_rewards'}</td>
								<td align="left">{l s='Value of the virtual points' mod='allinone_rewards'}</td>
							</tr>
{foreach from=$currencies item=tmpcurrency}
							<tr>
								<td><label class="indent">{$tmpcurrency['name']|escape:'htmlall':'UTF-8'}</label></td>
								<td align="left"><input {if $tmpcurrency['id_currency']==$currency->id}class="currency_default"{/if} type="text" size="8" maxlength="8" name="rewards_virtual_value_{$tmpcurrency['id_currency']|intval}" id="rewards_virtual_value_{$tmpcurrency['id_currency']|intval}" value="{$rewards_virtual_value[$tmpcurrency['id_currency']]|floatval}" /> <label class="t">"{l s='points' mod='allinone_rewards'}" = 1 {$tmpcurrency['sign']|escape:'htmlall':'UTF-8'}</label>{if $tmpcurrency['id_currency'] != $currency->id}<a href="#" onClick="return convertCurrencyValue(this, 'rewards_virtual_value', '{$tmpcurrency['conversion_rate']|floatval}')"><img src="{$module_template_dir|escape:'html':'UTF-8'}img/convert.gif" style="vertical-align: middle !important"></a>{/if}</td>
							</tr>
{/foreach}
						</table>
					</div>
					<div class="clear"></div>
					<label>{l s='Name of the virtual points' mod='allinone_rewards'}</label>
					<div class="margin-form translatable">
{foreach from=$languages item=language}
						<div class="lang_{$language['id_lang']|intval}" id="rewards_virtual_name_{$language['id_lang']|intval}" style="display: {if $language['id_lang']==$current_language_id}block{else}none{/if}; float: left;">
							<input size="33" type="text" name="rewards_virtual_name_{$language['id_lang']|intval}" value="{$rewards_virtual_name[$language['id_lang']]|escape:'htmlall':'UTF-8'}" />
						</div>
{/foreach}
					</div>
				</div>
				<div class="clear"></div>
				<div class="not_templated" style="padding-top: 10px;">
					<label class="t" style="width: 100% !important"><strong>{l s='Settings for rewards obtained through a command' mod='allinone_rewards'}</strong></label>
					<div class="clear" style="padding-top: 5px"></div>
					<label class="indent">{l s='Reward is awarded when the order is' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<select name="id_order_state_validation[]" multiple="multiple" class="multiselect">
{foreach from=$order_states item=order_state}
							<option {if is_array($object->rewardStateValidation->getValues()) && in_array($order_state['id_order_state'], $object->rewardStateValidation->getValues())}selected{/if} value="{$order_state['id_order_state']|intval}" style="background-color:{$order_state['color']|escape:'htmlall':'UTF-8'}"> {$order_state['name']|escape:'htmlall':'UTF-8'}</option>
{/foreach}
						</select>
					</div>
					<div class="clear"></div>
					<label class="indent">{l s='Reward is cancelled when the order is' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<select name="id_order_state_cancel[]" multiple="multiple" class="multiselect">
{foreach from=$order_states item=order_state}
							<option {if is_array($object->rewardStateCancel->getValues()) && in_array($order_state['id_order_state'], $object->rewardStateCancel->getValues())}selected{/if} value="{$order_state['id_order_state']|intval}" style="background-color:{$order_state['color']|escape:'htmlall':'UTF-8'}"> {$order_state['name']|escape:'htmlall':'UTF-8'}</option>
{/foreach}
						</select>
					</div>
					<div class="clear"></div>
					<label class="indent">{l s='Reward is validated only once the return period is exceeded' mod='allinone_rewards'}</label>&nbsp;
					<div class="margin-form">
						<label class="t" for="wait_order_return_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
						<input type="radio" id="wait_order_return_on" name="wait_order_return" value="1" {if $wait_return_period}checked="checked"{/if} /> <label class="t" for="wait_order_return_on">{l s='Yes' mod='allinone_rewards'}</label>
						<label class="t" for="wait_order_return_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
						<input type="radio" id="wait_order_return_off" name="wait_order_return" value="0" {if !$wait_return_period}checked="checked"{/if} /> <label class="t" for="wait_order_return_off">{l s='No' mod='allinone_rewards'}</label>
						- {if $ps_order_return}{l s='Order return period = ' mod='allinone_rewards'} {$ps_order_return_nb_days|intval} {l s='days' mod='allinone_rewards'}{else}{l s='Actually, order return is not allowed' mod='allinone_rewards'}{/if}
					</div>
					<div class="clear"></div>
				</div>
				<label class="t" style="width: 100% !important; padding-top: 10px; display: block"><strong>{l s='Use of the rewards' mod='allinone_rewards'}</strong></label>
				<div class="clear" style="padding-top: 5px"></div>
				<label class="indent">{l s='Allow customers to pick gift products with their rewards' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="rewards_gift_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" class="with_options" id="rewards_gift_on" name="rewards_gift" value="1" {if $rewards_gift}checked="checked"{/if} /> <label class="t" for="rewards_gift_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="rewards_gift_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" class="with_options" id="rewards_gift_off" name="rewards_gift" value="0" {if !$rewards_gift}checked="checked"{/if} /> <label class="t" for="rewards_gift_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label class="indent">{l s='Allow customers to transform rewards into vouchers' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="rewards_voucher_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" class="with_options" id="rewards_voucher_on" name="rewards_voucher" value="1" {if $rewards_voucher}checked="checked"{/if} /> <label class="t" for="rewards_voucher_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="rewards_voucher_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" class="with_options" id="rewards_voucher_off" name="rewards_voucher" value="0" {if !$rewards_voucher}checked="checked"{/if} /> <label class="t" for="rewards_voucher_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label class="indent">{l s='Allow customers to ask for payment (cash)' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="rewards_payment_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" class="with_options" id="rewards_payment_on" name="rewards_payment" value="1" {if $rewards_payment}checked="checked"{/if} /> <label class="t" for="rewards_payment_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="rewards_payment_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" class="with_options" id="rewards_payment_off" name="rewards_payment" value="0" {if !$rewards_payment}checked="checked"{/if} /> <label class="t" for="rewards_payment_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear not_templated">
					<label class="indent">{l s='Validity of the rewards before being canceled if not used (in days, 0=unlimited)' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input type="text" size="4" maxlength="4" id="rewards_duration" name="rewards_duration" value="{$rewards_duration|intval}" />
					</div>
					<div class="clear"></div>
					<label class="t" style="width: 100% !important; padding-top: 20px; display: block"><strong>{l s='Settings for automatic actions' mod='allinone_rewards'}</strong></label>
					<div class="clear" style="padding-top: 5px"></div>
					<label class="indent">{l s='How do you want to execute automatic actions' mod='allinone_rewards'}<br/><small>{l s='(unlock rewards, send reminders, cancel rewards with expired validity)' mod='allinone_rewards'}</small></label>
					<div class="margin-form">
						<label class="t" for="rewards_use_cron_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
						<input type="radio" class="with_options" id="rewards_use_cron_on" name="rewards_use_cron" value="1" {if $rewards_use_cron}checked="checked"{/if} /> <label class="t" for="rewards_use_cron_on">{l s='Crontab' mod='allinone_rewards'}</label>
						<label class="t" for="rewards_use_cron_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
						<input type="radio" class="with_options" id="rewards_use_cron_off" name="rewards_use_cron" value="0" {if !$rewards_use_cron}checked="checked"{/if} /> <label class="t" for="rewards_use_cron_off">{l s='I don\'t know' mod='allinone_rewards'}</label> - {l s='will be called on every page load' mod='allinone_rewards'}
					</div>
					<div class="clear optional rewards_use_cron_optional_1">
						<div class="margin-form" style="width: 95% !important; padding-left: 30px">{l s='Place this URL in crontab or call it manually daily :' mod='allinone_rewards'} {$rewards_cron_link|escape:'htmlall':'UTF-8'}</div>
					</div>
					<div class="clear"></div>
				</div>
			</fieldset>

			<fieldset id="rewards_gift" class="optional rewards_gift_optional_1">
				<legend>{l s='Settings applied when picking gift products with the rewards' mod='allinone_rewards'}</legend>
				{l s='A voucher for a free product will be generated with the following settings and automatically applied to the cart when the customer will decide to buy the product with his rewards. In case the cart does not contain any paid product, a default "Free product" with price=0 will be added to the cart, because Prestashop does not allow a cart with vouchers only. If you want, you can customize this product to change its name or add an image. The ID of this product is %s' sprintf={$rewards_id_default_gift_product|intval} mod='allinone_rewards'}
				<div class="clear" style="padding-top: 20px"></div>
				<label>{l s='Number of orders to make before being able to use this feature (0 is allowed)' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="text" size="3" maxlength="3" name="rewards_gift_nb_orders" id="rewards_gift_nb_orders" value="{$rewards_gift_nb_orders|intval}" />
				</div>
				<div class="clear not_templated">
					<label>{l s='Customers groups allowed to pick gift products with their rewards' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<select name="rewards_gift_groups[]" multiple="multiple" class="multiselect">
{foreach from=$groups item=group}
	{if !in_array($group['id_group'], $groups_off)}
							<option {if is_array($rewards_gift_groups) && in_array($group['id_group'], $rewards_gift_groups)}selected{/if} value="{$group['id_group']|intval}"> {$group['name']|escape:'htmlall':'UTF-8'}</option>
	{/if}
{/foreach}
						</select>
					</div>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
				<label>{l s='Display a link to the gifts list in the rewards account' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="rewards_gift_show_link_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="rewards_gift_show_link_on" name="rewards_gift_show_link" value="1" {if $rewards_gift_show_link}checked="checked"{/if} /> <label class="t" for="rewards_gift_show_link_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="rewards_gift_show_link_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="rewards_gift_show_link_off" name="rewards_gift_show_link" value="0" {if !$rewards_gift_show_link}checked="checked"{/if} /> <label class="t" for="rewards_gift_show_link_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
{if version_compare($smarty.const._PS_VERSION_,'1.7','<')}
				<div class="clear"></div>
				<label>{l s='Display a button to pick gift products on the products lists' mod='allinone_rewards'}{if version_compare($smarty.const._PS_VERSION_,'1.6','<')}<br/><small>{l s='It requires some custom modifications, please check installation guide' mod='allinone_rewards'}</small>{/if}</label>
				<div class="margin-form">
					<label class="t" for="rewards_gift_list_button_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="rewards_gift_list_button_on" name="rewards_gift_list_button" value="1" {if $rewards_gift_list_button}checked="checked"{/if} /> <label class="t" for="rewards_gift_list_button_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="rewards_gift_list_button_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="rewards_gift_list_button_off" name="rewards_gift_list_button" value="0" {if !$rewards_gift_list_button}checked="checked"{/if} /> <label class="t" for="rewards_gift_list_button_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
{/if}
				<div class="clear"></div>
				<label>{l s='Gift products can also be bought normally' mod='allinone_rewards'}{if version_compare($smarty.const._PS_VERSION_,'1.6','>=') && version_compare($smarty.const._PS_VERSION_,'1.7','<')}<br/><small>{l s='It requires some custom modifications, please check installation guide' mod='allinone_rewards'}</small>{/if}</label>
				<div class="margin-form">
					<label class="t" for="rewards_gift_buy_button_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="rewards_gift_buy_button_on" name="rewards_gift_buy_button" value="1" {if $rewards_gift_buy_button}checked="checked"{/if} /> <label class="t" for="rewards_gift_buy_button_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="rewards_gift_buy_button_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="rewards_gift_buy_button_off" name="rewards_gift_buy_button" value="0" {if !$rewards_gift_buy_button}checked="checked"{/if}/> <label class="t" for="rewards_gift_buy_button_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label>{l s='Product price to pay with the rewards' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="radio" id="rewards_gift_tax_off" name="rewards_gift_tax" value="0" {if !$rewards_gift_tax}checked="checked"{/if} /> <label class="t" for="rewards_gift_tax_off">{l s='VAT Excl.' mod='allinone_rewards'}</label>
					<input type="radio" id="rewards_gift_tax_on" name="rewards_gift_tax" value="1" {if $rewards_gift_tax}checked="checked"{/if} /> <label class="t" for="rewards_gift_tax_on">{l s='VAT Incl.' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label>{l s='Prefix for the voucher code (at least 3 letters long)' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="text" size="10" maxlength="10" id="rewards_gift_prefix" name="rewards_gift_prefix" value="{$rewards_gift_prefix|escape:'htmlall':'UTF-8'}" />
				</div>
				<div class="clear"></div>
				<label>{l s='Validity of the voucher (in days)' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="text" size="4" maxlength="4" id="rewards_gift_duration" name="rewards_gift_duration" value="{$rewards_gift_duration|intval}" />
				</div>
				<div class="clear"></div>
				<div>
					<table>
						<tr>
							<td class="label">{l s='Currency used by the member' mod='allinone_rewards'}</td>
							<td align="left">{l s='Minimum amount of the order to be able to use the voucher' mod='allinone_rewards'}</td>
						</tr>
{foreach from=$currencies item=tmpcurrency}
						<tr>
							<td><label class="indent">{$tmpcurrency['name']|escape:'htmlall':'UTF-8'}</label></td>
							<td align="left"><input {if $tmpcurrency['id_currency']==$currency->id}class="currency_default"{/if} type="text" size="8" maxlength="8" name="rewards_gift_min_order_{$tmpcurrency['id_currency']|intval}" id="rewards_gift_min_order_{$tmpcurrency['id_currency']|intval}" value="{$rewards_gift_min_order[$tmpcurrency['id_currency']]|floatval}" /> <label class="t">{$tmpcurrency['sign']|escape:'htmlall':'UTF-8'}</label>{if $tmpcurrency['id_currency'] != $currency->id}<a href="#" onClick="return convertCurrencyValue(this, 'rewards_gift_min_order', '{$tmpcurrency['conversion_rate']|floatval}')"><img src="{$module_template_dir|escape:'html':'UTF-8'}img/convert.gif" style="vertical-align: middle !important"></a>{/if}</td>
						</tr>
{/foreach}
						<tr>
							<td>&nbsp;</td>
							<td>
								<select name="rewards_gift_min_order_include_tax">
									<option {if !$rewards_gift_min_order_include_tax}selected{/if} value="0">{l s='VAT Excl.' mod='allinone_rewards'}</option>
									<option {if $rewards_gift_min_order_include_tax}selected{/if} value="1">{l s='VAT Incl.' mod='allinone_rewards'}</option>
								</select>
								<select name="rewards_gift_min_order_include_shipping">
									<option {if !$rewards_gift_min_order_include_shipping}selected{/if} value="0">{l s='Shipping Excluded' mod='allinone_rewards'}</option>
									<option {if $rewards_gift_min_order_include_shipping}selected{/if} value="1">{l s='Shipping Included' mod='allinone_rewards'}</option>
								</select>
							</td>
						</tr>
					</table>
				</div>
				<div class="clear" style="margin-top: 10px"></div>
				<label>{l s='Gift products can be picked from the following categories :' mod='allinone_rewards'}<br/><small>{l s='You can also enable/disable products or combinations individually from the product sheet' mod='allinone_rewards'}</small></label>
				<div class="margin-form">
					<input class="with_options" type="radio" id="all_categories_on" name="rewards_gift_all_categories" value="0" {if $rewards_gift_all_categories==0}checked="checked"{/if} /> <label class="t" for="all_categories_on">{l s='Choose categories' mod='allinone_rewards'}</label>&nbsp;
					<input class="with_options" type="radio" id="all_categories_off" name="rewards_gift_all_categories" value="1" {if $rewards_gift_all_categories==1}checked="checked"{/if} /> <label class="t" for="all_categories_off">{l s='All categories' mod='allinone_rewards'}</label>
					<input class="with_options" type="radio" id="all_categories_none" name="rewards_gift_all_categories" value="-1" {if $rewards_gift_all_categories==-1}checked="checked"{/if} /> <label class="t" for="all_categories_none">{l s='None' mod='allinone_rewards'}</label>
					<div class="optional rewards_gift_all_categories_optional_0" style="padding-top: 15px">
						{$categories nofilter}
					</div>
				</div>
			</fieldset>

			<fieldset id="rewards_voucher" class="optional rewards_voucher_optional_1">
				<legend>{l s='Settings applied when transforming rewards into vouchers' mod='allinone_rewards'}</legend>
				<label>{l s='Number of orders to make before being able to use this feature (0 is allowed)' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="text" size="3" maxlength="3" name="rewards_voucher_nb_orders" id="rewards_voucher_nb_orders" value="{$rewards_voucher_nb_orders|intval}" />
				</div>
				<div class="clear"></div>
				<div class="not_templated">
					<label>{l s='Customers groups allowed to transform rewards into vouchers' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<select name="rewards_voucher_groups[]" multiple="multiple" class="multiselect">
{foreach from=$groups item=group}
	{if !in_array($group['id_group'], $groups_off)}
							<option {if is_array($rewards_voucher_groups) && in_array($group['id_group'], $rewards_voucher_groups)}selected{/if} value="{$group['id_group']|intval}"> {$group['name']|escape:'htmlall':'UTF-8'}</option>
	{/if}
{/foreach}
						</select>
					</div>
					<div class="clear"></div>
				</div>
				<div class="clear"></div>
				<label>{l s='Suggest the customer to use his rewards into the cart summary' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="rewards_voucher_cart_link_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="rewards_voucher_cart_link_on" name="rewards_voucher_cart_link" value="1" {if $rewards_voucher_cart_link}checked="checked"{/if} /> <label class="t" for="rewards_voucher_cart_link_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="rewards_voucher_cart_link_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="rewards_voucher_cart_link_off" name="rewards_voucher_cart_link" value="0" {if !$rewards_voucher_cart_link}checked="checked"{/if} /> <label class="t" for="rewards_voucher_cart_link_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<div style="padding-bottom: 5px">
					<table>
						<tr>
							<td class="label">{l s='Currency used by the member' mod='allinone_rewards'}</td>
							<td align="left">{l s='Minimum required in account to be able to transform rewards into vouchers' mod='allinone_rewards'}</td>
						</tr>
{foreach from=$currencies item=tmpcurrency}
						<tr>
							<td><label class="indent">{$tmpcurrency['name']|escape:'htmlall':'UTF-8'}</label></td>
							<td align="left"><input class="notvirtual {if $tmpcurrency['id_currency']==$currency->id}currency_default{/if}" type="text" size="8" maxlength="8" name="rewards_voucher_min_value_{$tmpcurrency['id_currency']|intval}" id="rewards_voucher_min_value_{$tmpcurrency['id_currency']|intval}" value="{$rewards_voucher_min_value[$tmpcurrency['id_currency']]|floatval}" onBlur="showVirtualValue(this, {$tmpcurrency['id_currency']|intval}, false)" /> <label class="t">{$tmpcurrency['sign']|escape:'htmlall':'UTF-8'} <span class="virtualvalue"></span></label>{if $tmpcurrency['id_currency'] != $currency->id}<a href="#" onClick="return convertCurrencyValue(this, 'rewards_voucher_min_value', '{$tmpcurrency['conversion_rate']|floatval}')"><img src="{$module_template_dir|escape:'html':'UTF-8'}img/convert.gif" style="vertical-align: middle !important"></a>{/if}</td>
						</tr>
{/foreach}
					</table>
				</div>
				<div class="clear"></div>
				<label>{l s='Amount of the generated voucher' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="radio" class="with_options" id="rewards_voucher_type_full" name="rewards_voucher_type" value="0" {if $rewards_voucher_type==0}checked="checked"{/if} /> <label class="t" for="rewards_voucher_type_full">{l s='Convert the total available' mod='allinone_rewards'}</label>
					<input type="radio" class="with_options" id="rewards_voucher_type_partial" name="rewards_voucher_type" value="1" {if $rewards_voucher_type==1}checked="checked"{/if} /> <label class="t" for="rewards_voucher_type_partial">{l s='Allow the customer to enter the value to use' mod='allinone_rewards'}</label>
					<input type="radio" class="with_options" id="rewards_voucher_type_predefined" name="rewards_voucher_type" value="2" {if $rewards_voucher_type==2}checked="checked"{/if} /> <label class="t" for="rewards_voucher_type_predefined">{l s='Set a list of predefined values' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<div class="indent optional rewards_voucher_type_optional_0 rewards_voucher_type_optional_1">
					<label>{l s='Maximum (0=unlimited)' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input type="text" size="8" maxlength="8" name="rewards_voucher_maximum" id="rewards_voucher_maximum" value="{$rewards_voucher_maximum|floatval}" /> <label class="t">{$currency->sign|escape:'htmlall':'UTF-8'}</label>
					</div>
				</div>
				<div class="clear"></div>
				<div class="indent optional rewards_voucher_type_optional_2">
					<label>{l s='List of values (separated by a ";" character)' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input type="text" size="33" name="rewards_voucher_list_values" id="rewards_voucher_list_values" value="{$rewards_voucher_list_values|escape:'htmlall':'UTF-8'}" /> <label class="t">{$currency->sign|escape:'htmlall':'UTF-8'}</label> - {l s='Ex: 5;10;15;25;50;100' mod='allinone_rewards'}
					</div>
				</div>
				<div class="clear"></div>
				<label>{l s='Amount type for the generated voucher' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="radio" id="voucher_tax_off" name="voucher_tax" value="0" {if !$voucher_tax}checked="checked"{/if} /> <label class="t" for="voucher_tax_off">{l s='VAT Excl.' mod='allinone_rewards'}</label>
					<input type="radio" id="voucher_tax_on" name="voucher_tax" value="1" {if $voucher_tax}checked="checked"{/if} /> <label class="t" for="voucher_tax_on">{l s='VAT Incl.' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label>{l s='Voucher details (will appear in cart next to voucher code)' mod='allinone_rewards'}</label>
				<div class="margin-form translatable">
{foreach from=$languages item=language}
					<div class="lang_{$language['id_lang']|intval}" id="voucher_details_{$language['id_lang']|intval}" style="display: {if $language['id_lang']==$current_language_id}block{else}none{/if}; float: left;">
						<input size="33" type="text" name="voucher_details_{$language['id_lang']|intval}" value="{$voucher_details[$language['id_lang']]|escape:'htmlall':'UTF-8'}" />
					</div>
{/foreach}
				</div>
				<div class="clear" style="margin-top: 20px"></div>
				<label>{l s='Prefix for the voucher code (at least 3 letters long)' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="text" size="10" maxlength="10" id="voucher_prefix" name="voucher_prefix" value="{$voucher_prefix|escape:'htmlall':'UTF-8'}" />
				</div>
				<div class="clear"></div>
				<label>{l s='Validity of the voucher (in days)' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="text" size="4" maxlength="4" id="voucher_duration" name="voucher_duration" value="{$voucher_duration|intval}" />
				</div>
				<div class="clear"></div>
				<label>{l s='Display vouchers in the cart summary' mod='allinone_rewards'}</label>&nbsp;
				<div class="margin-form">
					<label class="t" for="display_cart_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="display_cart_on" name="display_cart" value="1" {if $display_cart}checked="checked"{/if} /> <label class="t" for="display_cart_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="display_cart_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="display_cart_off" name="display_cart" value="0" {if !$display_cart}checked="checked"{/if} /> <label class="t" for="display_cart_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label>{l s='Cumulative with other vouchers' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="cumulative_voucher_s_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="cumulative_voucher_s_on" name="cumulative_voucher_s" value="1" {if $cumulative_voucher_s}checked="checked"{/if} /> <label class="t" for="cumulative_voucher_s_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="cumulative_voucher_s_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="cumulative_voucher_s_off" name="cumulative_voucher_s" value="0" {if !$cumulative_voucher_s}checked="checked"{/if} /> <label class="t" for="cumulative_voucher_s_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label>{l s='Minimum amount of the order to be able to use the voucher' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="radio" class="with_options" id="rewards_voucher_minimum_off" name="rewards_voucher_minimum" value="0" {if $rewards_voucher_minimum==0}checked="checked"{/if} /> <label class="t" for="rewards_voucher_minimum">{l s='No minimum' mod='allinone_rewards'}</label>
					<input type="radio" class="with_options" id="rewards_voucher_minimum_fixed" name="rewards_voucher_minimum" value="1" {if $rewards_voucher_minimum==1}checked="checked"{/if} /> <label class="t" for="rewards_voucher_minimum_fixed">{l s='Fixed value' mod='allinone_rewards'}</label>
					<input type="radio" class="with_options" id="rewards_voucher_minimum_multiple" name="rewards_voucher_minimum" value="2" {if $rewards_voucher_minimum==2}checked="checked"{/if} /> <label class="t" for="rewards_voucher_minimum_multiple">{l s='Multiplier' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<div class="indent optional rewards_voucher_minimum_optional_1">
{foreach from=$currencies item=tmpcurrency}
					<div class="clear"></div>
					<label>{$tmpcurrency['name']|escape:'htmlall':'UTF-8'}</label>
					<div class="margin-form">
						<input {if $tmpcurrency['id_currency']==$currency->id}class="currency_default"{/if} type="text" size="8" maxlength="8" name="rewards_voucher_min_order_{$tmpcurrency['id_currency']|intval}" id="rewards_voucher_min_order_{$tmpcurrency['id_currency']|intval}" value="{$rewards_voucher_min_order[$tmpcurrency['id_currency']]|floatval}" /> <label class="t">{$tmpcurrency['sign']|escape:'htmlall':'UTF-8'}</label>{if $tmpcurrency['id_currency'] != $currency->id}<a href="#" onClick="return convertCurrencyValue(this, 'rewards_voucher_min_order', '{$tmpcurrency['conversion_rate']|floatval}')"><img src="{$module_template_dir|escape:'html':'UTF-8'}img/convert.gif" style="vertical-align: middle !important"></a>{/if}</td>
					</div>
{/foreach}
				</div>
				<div class="clear indent optional rewards_voucher_minimum_optional_2">
					<div class="clear"></div>
					<label>{l s='Multiplier of the voucher amount' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input type="text" size="6" maxlength="6" name="rewards_voucher_minimum_multiple" value="{$rewards_voucher_minimum_multiple|floatval}" /> {l s='Ex: if you set the multiplier to be 3 and the voucher amount is 50€, then the minimum will be 150€ (50€ * 3)' mod='allinone_rewards'}
					</div>
				</div>
				<div class="clear optional rewards_voucher_minimum_optional_1 rewards_voucher_minimum_optional_2">
					<div class="clear"></div>
					<label>&nbsp;</label>
					<div class="margin-form">
						<select name="include_tax">
							<option {if !$include_tax}selected{/if} value="0">{l s='VAT Excl.' mod='allinone_rewards'}</option>
							<option {if $include_tax}selected{/if} value="1">{l s='VAT Incl.' mod='allinone_rewards'}</option>
						</select>
						<select name="include_shipping">
							<option {if !$include_shipping}selected{/if} value="0">{l s='Shipping Excluded' mod='allinone_rewards'}</option>
							<option {if $include_shipping}selected{/if} value="1">{l s='Shipping Included' mod='allinone_rewards'}</option>
						</select>
					</div>
				</div>
				<div class="clear" style="margin-top: 10px"></div>
				<label>{l s='If the voucher is not depleted when used' mod='allinone_rewards'}</label>&nbsp;
				<div class="margin-form">
					<select name="voucher_behavior">
						<option {if !$voucher_behavior}selected{/if} value="0">{l s='Cancel the remaining amount' mod='allinone_rewards'}</option>
						<option {if $voucher_behavior}selected{/if} value="1">{l s='Create a new voucher with remaining amount' mod='allinone_rewards'}</option>
					</select>
				</div>
			</fieldset>

			<fieldset id="rewards_payment" class="optional rewards_payment_optional_1">
				<legend>{l s='Settings applied for the rewards payment' mod='allinone_rewards'}</legend>
				<label>{l s='Number of orders to make before being able to use this feature (0 is allowed)' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="text" size="3" maxlength="3" name="rewards_payment_nb_orders" id="rewards_payment_nb_orders" value="{$rewards_payment_nb_orders|intval}" />
				</div>
				<div class="clear"></div>
				<div class="not_templated">
					<label>{l s='Customers groups allowed to ask for payment' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<select name="rewards_payment_groups[]" multiple="multiple" class="multiselect">
{foreach from=$groups item=group}
	{if !in_array($group['id_group'], $groups_off)}
							<option {if is_array($rewards_payment_groups) && in_array($group['id_group'], $rewards_payment_groups)}selected{/if} value="{$group['id_group']|intval}"> {$group['name']|escape:'htmlall':'UTF-8'}</option>
	{/if}
{/foreach}
						</select>
					</div>
				</div>
				<div class="clear"></div>
				<label>{l s='An invoice must be uploaded to ask for payment' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="rewards_payment_invoice_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="rewards_payment_invoice_on" name="rewards_payment_invoice" value="1" {if $rewards_payment_invoice}checked="checked"{/if} /> <label class="t" for="rewards_payment_invoice_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="rewards_payment_invoice_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="rewards_payment_invoice_off" name="rewards_payment_invoice" value="0" {if !$rewards_payment_invoice}checked="checked"{/if} /> <label class="t" for="rewards_payment_invoice_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<div>
					<table>
						<tr>
							<td class="label">{l s='Currency used by the member' mod='allinone_rewards'}</td>
							<td align="left">{l s='Minimum required in account to be able to ask for payment' mod='allinone_rewards'}</td>
						</tr>
{foreach from=$currencies item=tmpcurrency}
						<tr>
							<td><label class="indent">{$tmpcurrency['name']|escape:'htmlall':'UTF-8'}</label></td>
							<td align="left"><input class="notvirtual {if $tmpcurrency['id_currency']==$currency->id}currency_default{/if}" type="text" size="8" maxlength="8" name="rewards_payment_min_value_{$tmpcurrency['id_currency']|intval}" id="rewards_payment_min_value_{$tmpcurrency['id_currency']|intval}" value="{$rewards_payment_min_value[$tmpcurrency['id_currency']]|floatval}" onBlur="showVirtualValue(this, {$tmpcurrency['id_currency']|intval}, false)" /> <label class="t">{$tmpcurrency['sign']|escape:'htmlall':'UTF-8'} <span class="virtualvalue"></span></label>{if $tmpcurrency['id_currency'] != $currency->id}<a href="#" onClick="return convertCurrencyValue(this, 'rewards_payment_min_value', '{$tmpcurrency['conversion_rate']|floatval}')"><img src="{$module_template_dir|escape:'html':'UTF-8'}img/convert.gif" style="vertical-align: middle !important"></a>{/if}</td>
						</tr>
{/foreach}
					</table>
				</div>
				<div class="clear"></div>
				<label>{l s='Convertion rate' mod='allinone_rewards'}<br/></label>
				<div class="margin-form">
					<input type="text" size="4" maxlength="4" id="rewards_payment_ratio" name="rewards_payment_ratio" value="{$rewards_payment_ratio|floatval}" /> - {l s='Example: for 100€ in reward account, if ratio is 75 then the customer will get only 75€ payment' mod='allinone_rewards'}
				</div>
			</fieldset>
			<div class="clear center"><input type="submit" name="submitReward" id="submitReward" value="{l s='Save settings' mod='allinone_rewards'}" class="button" /></div>
		</form>
	</div>

	<div id="tabs-{$object->name|escape:'htmlall':'UTF-8'}-2" class="not_templated">
		<form action="{$module->getCurrentPage($object->name)|escape:'html':'UTF-8'}" method="post">
			<input type="hidden" name="tabs-{$object->name|escape:'htmlall':'UTF-8'}" value="tabs-{$object->name|escape:'htmlall':'UTF-8'}-2" />
			<fieldset>
				<legend>{l s='Notifications' mod='allinone_rewards'}</legend>
				<label>{l s='Ignore list for all emails sent by the module' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="text" size="50" id="rewards_mails_ignored" name="rewards_mails_ignored" value="{$rewards_mails_ignored|escape:'htmlall':'UTF-8'}" />
					<br>{l s='You can enter some emails or mask of emails, all separated by a coma, which will never receive any emails from the module.' mod='allinone_rewards'}
					<br>{l s='For example : john@doe.com,@marketplace.amazon,@alerts-shopping-flux' mod='allinone_rewards'}
				</div>
				<div class="clear"></div>
				<label>{l s='Send a periodic email to the customer with his rewards account balance' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="rewards_reminder_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" class="with_options" id="rewards_reminder_on" name="rewards_reminder" value="1" {if $rewards_reminder}checked="checked"{/if} /> <label class="t" for="rewards_reminder_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="rewards_reminder_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" class="with_options" id="rewards_reminder_off" name="rewards_reminder" value="0" {if !$rewards_reminder}checked="checked"{/if} /> <label class="t" for="rewards_reminder_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear indent optional rewards_reminder_optional_1">
					<div class="clear"></div>
					<label>{l s='Minimum required in account to receive an email' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input type="text" size="3" name="rewards_reminder_minimum" value="{$rewards_reminder_minimum|floatval}" /> {$currency->sign|escape:'htmlall':'UTF-8'}&nbsp;
					</div>
					<div class="clear"></div>
					<label>{l s='Frequency of the emails (in days)' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input type="text" size="3" name="rewards_reminder_frequency" value="{$rewards_reminder_frequency|intval}" />
					</div>
				</div>
			</fieldset>
			<div class="clear center"><input class="button" name="submitRewardsNotifications" id="submitRewardsNotifications" value="{l s='Save settings' mod='allinone_rewards'}" type="submit" /></div>
		</form>
	</div>

	<div id="tabs-{$object->name|escape:'htmlall':'UTF-8'}-3" class="not_templated">
		<form action="{$module->getCurrentPage($object->name)|escape:'html':'UTF-8'}" method="post" enctype="multipart/form-data">
			<input type="hidden" name="tabs-{$object->name|escape:'htmlall':'UTF-8'}" value="tabs-{$object->name|escape:'htmlall':'UTF-8'}-3" />
			<fieldset class="not_templated">
				<legend>{l s='Labels of the different rewards states displayed in the rewards account' mod='allinone_rewards'}</legend>
				<label>{l s='Initial' mod='allinone_rewards'}</label>
				<div class="margin-form translatable">
{foreach from=$languages item=language}
					<div class="lang_{$language['id_lang']|intval}" id="default_reward_state_{$language['id_lang']|intval}" style="display: {if $language['id_lang']==$current_language_id}block{else}none{/if}; float: left;">
						<input size="33" type="text" name="default_reward_state_{$language['id_lang']|intval}" value="{$object->rewardStateDefault->name[$language['id_lang']]|escape:'htmlall':'UTF-8'}" />
					</div>
{/foreach}
				</div>
				<div class="clear"></div>
				<label>{l s='Converted' mod='allinone_rewards'}</label>
				<div class="margin-form translatable">
{foreach from=$languages item=language}
					<div class="lang_{$language['id_lang']|intval}" id="convert_reward_state_{$language['id_lang']|intval}" style="display: {if $language['id_lang']==$current_language_id}block{else}none{/if}; float: left;">
						<input size="33" type="text" name="convert_reward_state_{$language['id_lang']|intval}" value="{$object->rewardStateConvert->name[$language['id_lang']]|escape:'htmlall':'UTF-8'}" />
					</div>
{/foreach}
				</div>
				<div class="clear"></div>
				<label>{l s='Validation' mod='allinone_rewards'}</label>
				<div class="margin-form translatable">
{foreach from=$languages item=language}
					<div class="lang_{$language['id_lang']|intval}" id="validation_reward_state_{$language['id_lang']|intval}" style="display: {if $language['id_lang']==$current_language_id}block{else}none{/if}; float: left;">
						<input size="33" type="text" name="validation_reward_state_{$language['id_lang']|intval}" value="{$object->rewardStateValidation->name[$language['id_lang']]|escape:'htmlall':'UTF-8'}" />
					</div>
{/foreach}
				</div>
				<div class="clear"></div>
				<label>{l s='Return period not exceeded' mod='allinone_rewards'}</label>
				<div class="margin-form translatable">
{foreach from=$languages item=language}
					<div class="lang_{$language['id_lang']|intval}" id="return_period_reward_state_{$language['id_lang']|intval}" style="display: {if $language['id_lang']==$current_language_id}block{else}none{/if}; float: left;">
						<input size="33" type="text" name="return_period_reward_state_{$language['id_lang']|intval}" value="{$object->rewardStateReturnPeriod->name[$language['id_lang']]|escape:'htmlall':'UTF-8'}" />
					</div>
{/foreach}
				</div>
				<div class="clear"></div>
				<label>{l s='Cancelled' mod='allinone_rewards'}</label>
				<div class="margin-form translatable">
{foreach from=$languages item=language}
					<div class="lang_{$language['id_lang']|intval}" id="cancel_reward_state_{$language['id_lang']|intval}" style="display: {if $language['id_lang']==$current_language_id}block{else}none{/if}; float: left;">
						<input size="33" type="text" name="cancel_reward_state_{$language['id_lang']|intval}" value="{$object->rewardStateCancel->name[$language['id_lang']]|escape:'htmlall':'UTF-8'}" />
					</div>
{/foreach}
				</div>
				<div class="clear"></div>
				<label>{l s='Waiting for payment' mod='allinone_rewards'}</label>
				<div class="margin-form translatable">
{foreach from=$languages item=language}
					<div class="lang_{$language['id_lang']|intval}" id="waiting_payment_reward_state_{$language['id_lang']|intval}" style="display: {if $language['id_lang']==$current_language_id}block{else}none{/if}; float: left;">
						<input size="33" type="text" name="waiting_payment_reward_state_{$language['id_lang']|intval}" value="{$object->rewardStateWaitingPayment->name[$language['id_lang']]|escape:'htmlall':'UTF-8'}" />
					</div>
{/foreach}
				</div>
				<div class="clear"></div>
				<label>{l s='Paid' mod='allinone_rewards'}</label>
				<div class="margin-form translatable">
{foreach from=$languages item=language}
					<div class="lang_{$language['id_lang']|intval}" id="paid_reward_state_{$language['id_lang']|intval}" style="display: {if $language['id_lang']==$current_language_id}block{else}none{/if}; float: left;">
						<input size="33" type="text" name="paid_reward_state_{$language['id_lang']|intval}" value="{$object->rewardStatePaid->name[$language['id_lang']]|escape:'htmlall':'UTF-8'}" />
					</div>
{/foreach}
				</div>
			</fieldset>
			<fieldset>
				<legend>{l s='Text to display in the rewards account' mod='allinone_rewards'}</legend>
				<div class="translatable">
{foreach from=$languages item=language}
					<div class="lang_{$language['id_lang']|intval}" style="display: {if $language['id_lang']==$current_language_id}block{else}none{/if};float: left;">
						<textarea class="rte autoload_rte" cols="80" rows="25" name="rewards_general_txt[{$language['id_lang']|intval}]">{$rewards_general_txt[$language['id_lang']]|escape:'htmlall':'UTF-8'}</textarea>
					</div>
{/foreach}
				</div>
			</fieldset>
			<fieldset>
				<legend>{l s='Recommendations for the payment (bank information, invoice, delay...)' mod='allinone_rewards'}</legend>
				<div class="translatable">
{foreach from=$languages item=language}
					<div class="lang_{$language['id_lang']|intval}" style="display: {if $language['id_lang']==$current_language_id}block{else}none{/if};float: left;">
						<textarea class="rte autoload_rte" cols="80" rows="25" name="rewards_payment_txt[{$language['id_lang']|intval}]">{$rewards_payment_txt[$language['id_lang']]|escape:'htmlall':'UTF-8'}</textarea>
					</div>
{/foreach}
				</div>
			</fieldset>
			<div class="clear center"><input type="submit" name="submitRewardText" id="submitRewardText" value="{l s='Save settings' mod='allinone_rewards'}" class="button" /></div>
		</form>
	</div>
</div>