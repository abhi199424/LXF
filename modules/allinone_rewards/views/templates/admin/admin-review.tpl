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
				<label>{l s='Activate the reward for review' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="review_active_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="review_active_on" name="review_active" value="1" {if $review_active}checked="checked"{/if} /> <label class="t" for="review_active_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="review_active_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="review_active_off" name="review_active" value="0" {if !$review_active}checked="checked"{/if} /> <label class="t" for="review_active_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
				<div class="clear">
					<label>{l s='Reward for product review' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<label class="t" for="review_product_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
						<input type="radio" class="with_options" id="review_product_on" name="review_product" value="1" {if $review_product}checked="checked"{/if} /> <label class="t" for="review_product_on">{l s='Yes' mod='allinone_rewards'}</label>
						<label class="t" for="review_product_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
						<input type="radio" class="with_options" id="review_product_off" name="review_product" value="0" {if !$review_product}checked="checked"{/if} /> <label class="t" for="review_product_off">{l s='No' mod='allinone_rewards'}</label>
					</div>
				</div>
				<div class="clear">
					<label>{l s='Reward for site review' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<label class="t" for="review_site_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
						<input type="radio" class="with_options" id="review_site_on" name="review_site" value="1" {if $review_site}checked="checked"{/if} /> <label class="t" for="review_site_on">{l s='Yes' mod='allinone_rewards'}</label>
						<label class="t" for="review_site_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
						<input type="radio" class="with_options" id="review_site_off" name="review_site" value="0" {if !$review_site}checked="checked"{/if} /> <label class="t" for="review_site_off">{l s='No' mod='allinone_rewards'}</label>
					</div>
				</div>
				<div class="clear not_templated">
					<label>{l s='Customer review plateform' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<select class="with_options" name="review_api">
							<option value="0"></option>
{foreach from=$review_apis item=review_api}
							<option {if $selected_api && $selected_api->getName()==$review_api->getName()}selected{/if} value="{$review_api->getName()|escape:'htmlall':'UTF-8'}">{$review_api->getTitle()|escape:'htmlall':'UTF-8'}</option>
{/foreach}
						</select>
					</div>
				</div>
				<div class="clear not_templated indent optional review_api_optional_0">
					<div class="clear not_templated">
						<label></label>
						<div class="margin-form">
							<div class="reward_alert_message">
								{l s='If your review plateform is not yet in that list, contact us and we will try to make it compatible.' mod='allinone_rewards'} {if !$module->addons}<a href="https://www.prestaplugins.com/fr/contactez-nous" target="_blank">{l s='I contact Prestaplugins' mod='allinone_rewards'}</a>{else}<a href="https://addons.prestashop.com/en/write-to-developper?id_product=4414" target="_blank">{l s='Addons contact form' mod='allinone_rewards'}</a>{/if}
							</div>
						</div>
					</div>
				</div>
{foreach from=$review_apis item=review_api}
	{if ($object->id_template && isset($selected_api) && $selected_api->getName()==$review_api->getName()) || !$object->id_template}
				{$review_api->displayForm($id_template) nofilter}
	{/if}
{/foreach}
				<div><br><br>{l s='To generate the rewards automatically, place this URL in crontab or call it manually daily:' mod='allinone_rewards'} {$review_cron_link|escape:'htmlall':'UTF-8'}</div>
			</fieldset>
			<fieldset class="optional review_product_optional_1">
				<legend>{l s='Reward for product review' mod='allinone_rewards'}</legend>
				<label>{l s='Start rewarding product reviews from' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="text" name="review_product_from" value="{$review_product_from|escape:'htmlall':'UTF-8'}" placeholder="{l s='yyyy-mm-dd' mod='allinone_rewards'}" />
				</div>
				<div class="clear">
					<label>{l s='Maximum number of rewarded reviews per customer (0=unlimited)' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input type="text" size="5" name="review_product_max" value="{$review_product_max|intval}" />
					</div>
				</div>
				<div class="clear">
					<label>{l s='Maximum number of rewarded reviews per customer for each product (0=unlimited)' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input type="text" size="5" name="review_max_per_product" value="{$review_max_per_product|intval}" />
					</div>
				</div>
				<div class="clear">
					<label>{l s='Give a reward for ratings equals or highter to (0=all)' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<select name="review_product_rating">
							<option value="0"{if $review_product_rating == 0} selected="selected"{/if}>0</option>
							<option value="1"{if $review_product_rating == 1} selected="selected"{/if}>1</option>
							<option value="2"{if $review_product_rating == 2} selected="selected"{/if}>2</option>
							<option value="3"{if $review_product_rating == 3} selected="selected"{/if}>3</option>
							<option value="4"{if $review_product_rating == 4} selected="selected"{/if}>4</option>
							<option value="5"{if $review_product_rating == 5} selected="selected"{/if}>5</option>
						</select>
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
							<td align="left"><input class="notvirtual {if $tmpcurrency['id_currency']==$currency->id}currency_default{/if}" type="text" size="8" maxlength="8" name="review_reward_product_value_{$tmpcurrency['id_currency']|intval}" id="review_reward_product_value_{$tmpcurrency['id_currency']|intval}" value="{$review_reward_product_values[$tmpcurrency['id_currency']]|floatval}" onBlur="showVirtualValue(this, {$tmpcurrency['id_currency']|intval}, true)" /> <label class="t">{$tmpcurrency['sign']|escape:'htmlall':'UTF-8'} <span class="virtualvalue"></span></label>{if $tmpcurrency['id_currency'] != $currency->id}<a href="#" onClick="return convertCurrencyValue(this, 'review_reward_product_value', '{$tmpcurrency['conversion_rate']|floatval}')"><img src="{$module_template_dir|escape:'html':'UTF-8'}img/convert.gif" style="vertical-align: middle !important"></a>{/if}
						</tr>
{/foreach}
					</table>
				</div>
			</fieldset>
			<fieldset class="optional review_site_optional_1">
				<legend>{l s='Reward for site review' mod='allinone_rewards'}</legend>
				<label>{l s='Start rewarding site reviews from' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<input type="text" name="review_site_from" value="{$review_site_from|escape:'htmlall':'UTF-8'}" placeholder="{l s='yyyy-mm-dd' mod='allinone_rewards'}" />
				</div>
				<div class="clear">
					<label>{l s='Maximum number of rewarded reviews per customer (0=unlimited)' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<input type="text" size="5" name="review_site_max" value="{$review_site_max|intval}" />
					</div>
				</div>
				<div class="clear">
					<label>{l s='Give a reward for ratings equals or highter to (0=all)' mod='allinone_rewards'}</label>
					<div class="margin-form">
						<select name="review_site_rating">
							<option value="0"{if $review_site_rating == 0} selected="selected"{/if}>0</option>
							<option value="1"{if $review_site_rating == 1} selected="selected"{/if}>1</option>
							<option value="2"{if $review_site_rating == 2} selected="selected"{/if}>2</option>
							<option value="3"{if $review_site_rating == 3} selected="selected"{/if}>3</option>
							<option value="4"{if $review_site_rating == 4} selected="selected"{/if}>4</option>
							<option value="5"{if $review_site_rating == 5} selected="selected"{/if}>5</option>
						</select>
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
							<td align="left"><input class="notvirtual {if $tmpcurrency['id_currency']==$currency->id}currency_default{/if}" type="text" size="8" maxlength="8" name="review_reward_site_value_{$tmpcurrency['id_currency']|intval}" id="review_reward_site_value_{$tmpcurrency['id_currency']|intval}" value="{$review_reward_site_values[$tmpcurrency['id_currency']]|floatval}" onBlur="showVirtualValue(this, {$tmpcurrency['id_currency']|intval}, true)" /> <label class="t">{$tmpcurrency['sign']|escape:'htmlall':'UTF-8'} <span class="virtualvalue"></span></label>{if $tmpcurrency['id_currency'] != $currency->id}<a href="#" onClick="return convertCurrencyValue(this, 'review_reward_site_value', '{$tmpcurrency['conversion_rate']|floatval}')"><img src="{$module_template_dir|escape:'html':'UTF-8'}img/convert.gif" style="vertical-align: middle !important"></a>{/if}
						</tr>
{/foreach}
					</table>
				</div>
			</fieldset>
			<div class="clear center"><input type="submit" name="submitReview" id="submitReview" value="{l s='Save settings' mod='allinone_rewards'}" class="button" /><br><br><br></div>

{if isset($selected_api)}
			<div class='statistics'>
	{if $review_product}
		{if $nb_product_reviews !== false}
				<div class='title'>{l s='%s new product reviews validated after %s are awaiting generation of their reward' sprintf=[$nb_product_reviews|intval, $selected_api->getLastCheck($id_template, 'product')] mod='allinone_rewards'}</div>
				<table class='tablesorter tablesorter-ice' id='product_reviews'>
					<thead>
						<tr>
							<th>{l s='Date' mod='allinone_rewards'}</th>
							<th>{l s='Order ID' mod='allinone_rewards'}</th>
							<th>{l s='Product' mod='allinone_rewards'}</th>
							<th>{l s='Customer' mod='allinone_rewards'}</th>
							<th>{l s='Rating' mod='allinone_rewards'}</th>
							<th>{l s='Review' mod='allinone_rewards'}</th>
						</tr>
					</thead>
					<tbody>
			{foreach from=$product_reviews item=review}
						<tr>
							<td class='center'>{$review->date_add|escape:'htmlall':'UTF-8'}</td>
							<td class='center'>{if $review->id_order}<a target="_blank" href="?tab=AdminOrders&id_order={$review->id_order|intval}&vieworder&token={$token_order|escape:'html':'UTF-8'}">{$review->id_order|intval}</a>{else}-{/if}</td>
							<td class='center'>{$review->product_name|escape:'htmlall':'UTF-8'}</td>
							<td class='center'><a target="_blank" href="?tab=AdminCustomers&id_customer={$review->id_customer|intval}&viewcustomer&token={$token_customer|escape:'html':'UTF-8'}">{$review->customer_name|escape:'htmlall':'UTF-8'}</a></td>
							<td class='center'>{$review->rating|intval}</td>
							<td class='left'>{$review->comment nofilter}</td>
						</tr>
			{/foreach}
					</tbody>
				</table>
				<div class='pager'>
			    	<img src='{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/first.png' class='first'/>
			    	<img src='{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/prev.png' class='prev'/>
			    	<span class='pagedisplay'></span> <!-- this can be any element, including an input -->
			    	<img src='{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/next.png' class='next'/>
			    	<img src='{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/last.png' class='last'/>
			    	<select class='pagesize'>
			      		<option value='10'>10</option>
			      		<option value='20'>20</option>
			      		<option value='50'>50</option>
			      		<option value='100'>100</option>
			      		<option value='500'>500</option>
			    	</select>
				</div>
		{else}
				<br>{l s='Product reviews: a problem occured connecting to the API' mod='allinone_rewards'}<br><br>
		{/if}
	{/if}
	{if $review_site}
		{if $nb_site_reviews !== false}
				<div class='title'>{l s='%s new site reviews validated after %s are awaiting generation of their reward' sprintf=[$nb_site_reviews|intval, $selected_api->getLastCheck($id_template, 'site')] mod='allinone_rewards'}</div>
				<table class='tablesorter tablesorter-ice' id='site_reviews'>
					<thead>
						<tr>
							<th>{l s='Date' mod='allinone_rewards'}</th>
							<th>{l s='Order ID' mod='allinone_rewards'}</th>
							<th>{l s='Customer' mod='allinone_rewards'}</th>
							<th>{l s='Rating' mod='allinone_rewards'}</th>
							<th>{l s='Review' mod='allinone_rewards'}</th>
						</tr>
					</thead>
					<tbody>
			{foreach from=$site_reviews item=review}
						<tr>
							<td class='center'>{$review->date_add|escape:'htmlall':'UTF-8'}</td>
							<td class='center'>{if $review->id_order}<a target="_blank" href="?tab=AdminOrders&id_order={$review->id_order|intval}&vieworder&token={$token_order|escape:'html':'UTF-8'}">{$review->id_order|intval}</a>{else}-{/if}</td>
							<td class='center'><a target="_blank" href="?tab=AdminCustomers&id_customer={$review->id_customer|intval}&viewcustomer&token={$token_customer|escape:'html':'UTF-8'}">{$review->customer_name|escape:'htmlall':'UTF-8'}</a></td>
							<td class='center'>{$review->rating|intval}</td>
							<td class='left'>{$review->comment nofilter}</td>
						</tr>
			{/foreach}
					</tbody>
				</table>
				<div class='pager'>
			    	<img src='{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/first.png' class='first'/>
			    	<img src='{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/prev.png' class='prev'/>
			    	<span class='pagedisplay'></span> <!-- this can be any element, including an input -->
			    	<img src='{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/next.png' class='next'/>
			    	<img src='{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/last.png' class='last'/>
			    	<select class='pagesize'>
			      		<option value='10'>10</option>
			      		<option value='20'>20</option>
			      		<option value='50'>50</option>
			      		<option value='100'>100</option>
			      		<option value='500'>500</option>
			    	</select>
				</div>
		{else}
				<br>{l s='Site reviews: a problem occured connecting to the API' mod='allinone_rewards'}<br><br>
		{/if}
	{/if}
			</div>
{/if}
		</form>
	</div>
	<div id="tabs-{$object->name|escape:'htmlall':'UTF-8'}-2" class="not_templated">
		<form action="{$module->getCurrentPage($object->name)|escape:'html':'UTF-8'}" method="post">
			<input type="hidden" name="tabs-{$object->name|escape:'htmlall':'UTF-8'}" value="tabs-{$object->name|escape:'htmlall':'UTF-8'}-2" />
			<fieldset>
				<legend>{l s='Notifications' mod='allinone_rewards'}</legend>
				<label>{l s='Send a mail to the customer on reward validation' mod='allinone_rewards'}</label>
				<div class="margin-form">
					<label class="t" for="review_mail_on"><img src="../img/admin/enabled.gif" alt="{l s='Enabled' mod='allinone_rewards'}" title="{l s='Yes' mod='allinone_rewards'}" /></label>
					<input type="radio" id="review_mail_on" name="review_mail" value="1" {if $review_mail}checked="checked"{/if} /> <label class="t" for="review_mail_on">{l s='Yes' mod='allinone_rewards'}</label>
					<label class="t" for="review_mail_off" style="margin-left: 10px"><img src="../img/admin/disabled.gif" alt="{l s='Disabled' mod='allinone_rewards'}" title="{l s='No' mod='allinone_rewards'}" /></label>
					<input type="radio" id="review_mail_off" name="review_mail" value="0" {if !$review_mail}checked="checked"{/if} /> <label class="t" for="review_mail_off">{l s='No' mod='allinone_rewards'}</label>
				</div>
			</fieldset>
			<div class="clear center"><input class="button" name="submitReviewNotifications" id="submitReviewNotifications" value="{l s='Save settings' mod='allinone_rewards'}" type="submit" /></div>
		</form>
	</div>
</div>
<script>
	var footer_pager = "{l s='{startRow} to {endRow} of {totalRows} rows' mod='allinone_rewards' js=1}";
	initTableSorter();
</script>