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
			<fieldset>
				<legend>{l s='General settings' mod='allinone_rewards'}</legend>
				<label>{l s='Activate loyalty program' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="loyalty_active_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="loyalty_active_on" name="rloyalty_active" value="1" {if $rloyalty_active}checked="checked"{/if} /> <label class="t" for="loyalty_active_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="loyalty_active_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="loyalty_active_off" name="rloyalty_active" value="0" {if !$rloyalty_active}checked="checked"{/if} /> <label class="t" for="loyalty_active_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear not_templated">
					<label>{l s='Customers groups allowed to get loyalty rewards' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<select name="rloyalty_groups[]" multiple="multiple" class="multiselect">
{foreach from=$groups item=group}
							<option {if is_array($allowed_groups) && in_array($group['id_group'], $allowed_groups)}selected{/if} value="{$group['id_group']|intval}"> {$group['name']|escape:'htmlall':'UTF-8'}</option>
{/foreach}
						</select>
					</div>
				</div>
				<div class="clear"></div>
				<label>{l s='Display the reward in the product page' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="loyalty_display_product_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="loyalty_display_product_on" name="rloyalty_display_product" value="1" {if $rloyalty_display_product}checked="checked"{/if} /> <label class="t" for="loyalty_display_product_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="loyalty_display_product_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="loyalty_display_product_off" name="rloyalty_display_product" value="0" {if !$rloyalty_display_product}checked="checked"{/if} /> <label class="t" for="loyalty_display_product_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label>{l s='Display the reward in the cart summary' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="loyalty_display_cart_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="loyalty_display_cart_on" name="rloyalty_display_cart" value="1" {if $rloyalty_display_cart}checked="checked"{/if} /> <label class="t" for="loyalty_display_cart_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="loyalty_display_cart_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="loyalty_display_cart_off" name="rloyalty_display_cart" value="0" {if !$rloyalty_display_cart}checked="checked"{/if} /> <label class="t" for="loyalty_display_cart_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label>{l s='Display the reward in the PDF invoice' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="loyalty_display_invoice_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="loyalty_display_invoice_on" name="rloyalty_display_invoice" value="1" {if $rloyalty_display_invoice}checked="checked"{/if} /> <label class="t" for="loyalty_display_invoice_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="loyalty_display_invoice_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="loyalty_display_invoice_off" name="rloyalty_display_invoice" value="0" {if !$rloyalty_display_invoice}checked="checked"{/if} /> <label class="t" for="loyalty_display_invoice_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label>{l s='How is calculated the reward ?' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="radio" class="with_options" id="loyalty_type_range" name="rloyalty_type" value="0" {if $rloyalty_type==0}checked="checked"{/if} /> <label class="t" for="loyalty_type_range">{l s='Based on the total of the cart' mod='allinone_rewards'}</label>
					&nbsp;<input type="radio" class="with_options" id="loyalty_type_percentage" name="rloyalty_type" value="1" {if $rloyalty_type==1}checked="checked"{/if} /> <label class="t" for="loyalty_type_percentage">{l s='% of the total of the cart' mod='allinone_rewards'}</label>
					&nbsp;<input type="radio" class="with_options" id="loyalty_type_product" name="rloyalty_type" value="2" {if $rloyalty_type==2}checked="checked"{/if} /> <label class="t" for="loyalty_type_product">{l s='Product per product' mod='allinone_rewards'}</label>
				</div>
				<div class="clear indent optional rloyalty_type_optional_0 rloyalty_type_optional_1">
					<label>{l s='Deduce vouchers before calculating the total' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<label class="t" for="rloyalty_deduce_vouchers_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
						<input type="radio" id="rloyalty_deduce_vouchers_on" name="rloyalty_deduce_vouchers" value="1" {if $rloyalty_deduce_vouchers}checked="checked"{/if} /> <label class="t" for="rloyalty_deduce_vouchers_on">{l s='Yes' mod='allinone_rewards'}</label>
						<label class="t" for="rloyalty_deduce_vouchers_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
						<input type="radio" id="rloyalty_deduce_vouchers_off" name="rloyalty_deduce_vouchers" value="0" {if !$rloyalty_deduce_vouchers}checked="checked"{/if} /> <label class="t" for="rloyalty_deduce_vouchers_off">{l s='No' mod='allinone_rewards'}</label>
					</div>
				</div>
				<div class="clear indent optional rloyalty_type_optional_0">
					<label>{l s='For every' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input type="text" size="8" id="rloyalty_point_rate" name="rloyalty_point_rate" value="{$rloyalty_point_rate|floatval}" /> <label class="t">{$currency->sign|escape:'htmlall':'UTF-8'} {l s='spent on the shop' mod='allinone_rewards'}</label>
					</div>
					<div class="clear"></div>
					<label>{l s='Customer gets' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input class="notvirtual" type="text" size="8" name="rloyalty_point_value" id="rloyalty_point_value" value="{$rloyalty_point_value|floatval}" onBlur="showVirtualValue(this, {$currency->id|intval}, true)" /> <label class="t">{$currency->sign|escape:'htmlall':'UTF-8'} <span class="virtualvalue"></span></label>
					</div>
				</div>
				<div class="clear indent optional rloyalty_type_optional_1">
					<label>{l s='Percentage' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input type="text" size="8" name="rloyalty_percentage" value="{$rloyalty_percentage|floatval}" /> %
					</div>
				</div>
				<div class="clear indent optional rloyalty_type_optional_2">
					<label></label>
					<div class="margin-form">{l s='You can configure each product individually from the product sheet' mod='allinone_rewards'}</div>
					<div class="clear"></div>
					<label>{l s='Default reward for product with no custom value' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input class="notvirtual product_per_product" type="text" size="3" name="rloyalty_default_product_reward" value="{$rloyalty_default_product_reward|floatval}" onBlur="showVirtualValue(this, {$currency->id|intval}, true)" />
						<select class="product_per_product" name="rloyalty_default_product_type" onChange="showVirtualValue(this, {$currency->id|intval}, true)">
							<option {if !$rloyalty_default_product_type}selected{/if} value="0">% {l s='of its own price' mod='allinone_rewards'}</option>
							<option {if $rloyalty_default_product_type}selected{/if} value="1">{$currency->sign|escape:'htmlall':'UTF-8'}</option>
						</select>
						&nbsp;<span class="virtualvalue"></span>
					</div>
					<div class="clear"></div>
					<label>{l s='Coefficient multiplier (all rewards will be multiplied by this coefficient)' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input type="text" size="3" name="rloyalty_multiplier" value="{$rloyalty_multiplier|floatval}" />
					</div>
				</div>
				<div class="clear indent optional rloyalty_type_optional_0 rloyalty_type_optional_1">
					<label>{l s='Categories of products allowing to get loyalty rewards' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input class="with_options" type="radio" id="all_categories_on" name="rloyalty_all_categories" value="0" {if !$rloyalty_all_categories}checked="checked"{/if} /> <label class="t" for="all_categories_on">{l s='Choose categories' mod='allinone_rewards'}</label>&nbsp;
						<input class="with_options" type="radio" id="all_categories_off" name="rloyalty_all_categories" value="1" {if $rloyalty_all_categories}checked="checked"{/if} /> <label class="t" for="all_categories_off">{l s='All categories' mod='allinone_rewards'}</label>
						<div class="optional rloyalty_all_categories_optional_0" style="padding-top: 15px">
							{$categories nofilter}
						</div>
					</div>
				</div>
				<div class="clear"></div>
				<label>{l s='Price to use to calculate the reward (when the customer pays the VAT)' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="radio" id="rloyalty_tax_off" name="rloyalty_tax" value="0" {if !$rloyalty_tax}checked="checked"{/if} /> <label class="t" for="rloyalty_tax_off">{l s='VAT Excl.' mod='allinone_rewards'}</label>
					<input type="radio" id="rloyalty_tax_on" name="rloyalty_tax" value="1" {if $rloyalty_tax}checked="checked"{/if} /> <label class="t" for="rloyalty_tax_on">{l s='VAT Incl.' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label>{l s='Give rewards on discounted products' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="rloyalty_discounted_allowed_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="rloyalty_discounted_allowed_on" name="rloyalty_discounted_allowed" value="1" {if $rloyalty_discount_allowed}checked="checked"{/if} /> <label class="t" for="rloyalty_discounted_allowed_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="rloyalty_discounted_allowed_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="rloyalty_discounted_allowed_off" name="rloyalty_discounted_allowed" value="0" {if !$rloyalty_discount_allowed}checked="checked"{/if} /> <label class="t" for="rloyalty_discounted_allowed_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label>{l s='Maximum reward granted for each order (0=unlimited)' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input class="notvirtual" type="text" size="8" name="rloyalty_max_reward" id="rloyalty_max_reward" value="{$rloyalty_max_reward|floatval}" onBlur="showVirtualValue(this, {$currency->id|intval}, true)" /> <label class="t">{$currency->sign|escape:'htmlall':'UTF-8'} <span class="virtualvalue"></span></label>
				</div>
			</fieldset>
			<div class="clear center"><input type="submit" name="submitLoyalty" id="submitLoyalty" value="{l s='Save settings' mod='allinone_rewards'}" class="button" /></div>
		</form>
	</div>
	<div id="tabs-{$object->name|escape:'htmlall':'UTF-8'}-2" class="not_templated">
		<form action="{$module->getCurrentPage($object->name)|escape:'html':'UTF-8'}" method="post">
			<input type="hidden" name="tabs-{$object->name|escape:'htmlall':'UTF-8'}" value="tabs-{$object->name|escape:'htmlall':'UTF-8'}-2" />
			<fieldset>
				<legend>{l s='Notifications' mod='allinone_rewards'}</legend>
				<label>{l s='Send a mail to the customer on reward validation/cancellation' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="rloyalty_mail_validation_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="rloyalty_mail_validation_on" name="rloyalty_mail_validation" value="1" {if $rloyalty_mail_validation}checked="checked"{/if} /> <label class="t" for="rloyalty_mail_validation_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="rloyalty_mail_validation_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="rloyalty_mail_validation_off" name="rloyalty_mail_validation" value="0"  {if !$rloyalty_mail_validation}checked="checked"{/if} /> <label class="t" for="rloyalty_mail_validation_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear"></div>
				<label>{l s='Send a mail to the customer on reward modification' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="rloyalty_mail_cancel_product_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="rloyalty_mail_cancel_product_on" name="rloyalty_mail_cancel_product" value="1" {if $rloyalty_mail_cancel_product}checked="checked"{/if} /> <label class="t" for="rloyalty_mail_cancel_product_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="rloyalty_mail_cancel_product_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="rloyalty_mail_cancel_product_off" name="rloyalty_mail_cancel_product" value="0" {if !$rloyalty_mail_cancel_product}checked="checked"{/if} /> <label class="t" for="rloyalty_mail_cancel_product_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
			</fieldset>
			<div class="clear center"><input class="button" name="submitLoyaltyNotifications" id="submitLoyaltyNotifications" value="{l s='Save settings' mod='allinone_rewards'}" type="submit" /></div>
		</form>
	</div>
</div>