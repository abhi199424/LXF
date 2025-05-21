{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2025 Yann BONNAILLIE - ByWEB
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

{extends file='customer/page.tpl'}

{block name='page_title'}
	{l s='My rewards account' mod='allinone_rewards'}
{/block}

{block name='page_content'}
<script>
	var aior_transform_confirm_message = "{l s='Are you sure you want to transform your rewards into vouchers ?' mod='allinone_rewards' js=1}";
	var aior_transform_confirm_message2 = "{l s='Cancel' mod='allinone_rewards' js=1}";
	var aior_transform_confirm_message3 = "{l s='Save' mod='allinone_rewards' js=1}";
</script>

<div id="rewards_account" class="rewards">
	<ul class="idTabs">
		<li class="col-xs-12 col-sm-4"><a href="#idTab1" {if $activeTab!='history'}class="selected"{/if}>{l s='My account' mod='allinone_rewards'}</a></li>
		<li class="col-xs-12 col-sm-4"><a href="#idTab2" {if $activeTab=='history'}class="selected"{/if}>{l s='Rewards history' mod='allinone_rewards'}</a></li>
		<li class="col-xs-12 col-sm-4"><a href="#idTab3">{l s='Vouchers history' mod='allinone_rewards'}</a></li>
	</ul>

	<div class="sheets table-responsive">
		<div id="idTab1" class="rewardsBlock">
			<div id="general_txt" style="padding-bottom: 20px">{$general_txt nofilter}</div>

{if $return_days > 0}
			<p>{l s='Rewards will be available %s days after the validation of each order.' sprintf=[$return_days|intval] mod='allinone_rewards'}</p>
{/if}
			<table class="table table-bordered">
				<thead class="thead-default">
					<tr>
						<th class="text-xs-center">{l s='Total rewards' mod='allinone_rewards'}</th>
						{if $convertColumns}
						<th class="text-xs-center">{l s='Already converted' mod='allinone_rewards'}</th>
						{/if}
						{if $paymentColumns}
						<th class="text-xs-center">{l s='Paid' mod='allinone_rewards'}</th>
						{/if}
						<th class="text-xs-center">{l s='Available' mod='allinone_rewards'}</th>
						<th class="text-xs-center">{l s='Awaiting validation' mod='allinone_rewards'}</th>
						{if $paymentColumns}
						<th class="text-xs-center">{l s='Awaiting payment' mod='allinone_rewards'}</th>
						{/if}
					</tr>
				</thead>
				<tr>
					<td class="text-xs-center" data-label="{l s='Total rewards' mod='allinone_rewards'}">{$totalGlobal|escape:'htmlall':'UTF-8'}</td>
					{if $convertColumns}
					<td class="text-xs-center" data-label="{l s='Already converted' mod='allinone_rewards'}">{$totalConverted|escape:'htmlall':'UTF-8'}</td>
					{/if}
					{if $paymentColumns}
					<td class="text-xs-center" data-label="{l s='Paid' mod='allinone_rewards'}">{$totalPaid|escape:'htmlall':'UTF-8'}</td>
					{/if}
					<td class="text-xs-center" data-label="{l s='Available' mod='allinone_rewards'}">{$totalAvailable|escape:'htmlall':'UTF-8'}</td>
					<td class="text-xs-center" data-label="{l s='Awaiting validation' mod='allinone_rewards'}">{$totalPending|escape:'htmlall':'UTF-8'}</td>
					{if $paymentColumns}
					<td class="text-xs-center" data-label="{l s='Awaiting payment' mod='allinone_rewards'}">{$totalWaitingPayment|escape:'htmlall':'UTF-8'}</td>
					{/if}
				</tr>
			</table>
{if $voucher_minimum_allowed}
			<div id="min_transform" style="clear: both">{l s='The minimum required to be able to transform your rewards into vouchers is' mod='allinone_rewards'} <b>{$voucherMinimum|escape:'htmlall':'UTF-8'}</b></div>
{/if}
{if $payment_minimum_allowed}
			<div id="min_payment" style="clear: both">{l s='The minimum required to be able to ask for a payment is' mod='allinone_rewards'} <b>{$paymentMinimum|escape:'htmlall':'UTF-8'}</b></div>
{/if}

			<div id="aior_buttons">
{if $show_link}
				<div id="gift_list">
					<a class="btn btn-primary" href="{url entity='module' name='allinone_rewards' controller='gifts'}">{l s='View the list of available gift products' mod='allinone_rewards'}</a>
				</div>
{else if $rewards && $voucher_button_allowed}
	{if $voucher_type==0}
				<div id="transform">
					<form id="transform_form" action="{$page_link|escape:'html':'UTF-8'}" method="post">
						<input type="hidden" name="transform-credits" value="1">
						<a id="transform_button" class="btn btn-primary" href="{$page_link|escape:'html':'UTF-8'}">{l s='Transform my rewards into a voucher worth' mod='allinone_rewards'} <span>{$voucher_maximum_currency|escape:'htmlall':'UTF-8'}</span></a>
					</form>
				</div>
	{else if $voucher_type==1}
				<div id="transform" class="free_value">
					<form id="transform_form" action="{$page_link|escape:'html':'UTF-8'}" method="post">
						<input type="hidden" name="transform-credits" value="1">
						<table class="table table-bordered">
							<thead class="thead-default">
								<tr>
									<th class="text-xs-center">
										{if $rewards_virtual}{l s='Your available balance is:' mod='allinone_rewards'} {$totalAvailableCurrency|escape:'htmlall':'UTF-8'}<br><br>{/if}
										{l s='Amount to convert into a voucher' mod='allinone_rewards'} {l s='(max: %s)' sprintf=[$voucher_maximum_currency]|escape:'htmlall':'UTF-8' mod='allinone_rewards'}
									</th>
								</tr>
							</thead>
							<tr>
								<td data-label="{l s='Amount to convert into a voucher' mod='allinone_rewards'} {l s='(max: %s)' sprintf=[$voucher_maximum_currency]|escape:'htmlall':'UTF-8' mod='allinone_rewards'}">
									<input type="text" class="form-control" name="value-to-transform"> {$currency.sign|escape:'htmlall':'UTF-8'} <a id="transform_button" href="#" class="btn btn-primary">{l s='Save' mod='allinone_rewards'}</a>
								</td>
							</tr>
						</table>
					</form>
				</div>
	{else if $voucher_type==2 && $voucher_list_values|count > 0}
				<div id="transform" class="free_value">
					<form id="transform_form" action="{$page_link|escape:'html':'UTF-8'}" method="post">
						<input type="hidden" name="transform-credits" value="1">
						<table class="table table-bordered">
							<thead class="thead-default">
								<tr>
									<th class="text-xs-center">
										{l s='Amount to convert into a voucher' mod='allinone_rewards'}
									</th>
								</tr>
							</thead>
							<tr>
								<td data-label="{l s='Amount to convert into a voucher' mod='allinone_rewards'}">
									<select class="form-control" name="value-to-transform">
		{foreach from=$voucher_list_values item=value name=myLoop}
										<option value="{$value['value']|floatval}">{$value['label']|escape:'htmlall':'UTF-8'}{if $value['virtual']} ({$value['virtual']|escape:'htmlall':'UTF-8'}){/if}</option>
		{/foreach}
									</select>
									<a id="transform_button" href="#" class="btn btn-primary">{l s='Save' mod='allinone_rewards'}</a>
								</td>
							</tr>
						</table>
					</form>
				</div>
	{/if}
{/if}
{if $rewards && $payment_button_allowed}
				<div id="payment">
					<a id="payment_button" class="btn btn-primary" href="#">{l s='Ask for the payment of your available rewards :' mod='allinone_rewards'} <span>{$totalForPaymentDefaultCurrency|escape:'htmlall':'UTF-8'}</span></a>
				</div>
				<form id="payment_form" method="post" action="{$page_link|escape:'html':'UTF-8'}" enctype="multipart/form-data" style="display: {if isset($smarty.post.payment_details)}block{else}none{/if}">
					<fieldset>
						<div id="payment_txt">{$payment_txt nofilter}</div>
						<div class="form-group row">
							<label class="col-md-3 form-control-label">{l s='Bank account, paypal address, address, details...' mod='allinone_rewards'} <sup>*</sup></label>
							<div class="col-md-9">
								<textarea class="form-control" id="payment_details" name="payment_details" rows="3" cols="40">{if isset($payment_details)}{$payment_details|escape:'htmlall':'UTF-8'}{/if}</textarea>
							</div>
						</div>
						<div class="form-group row">
							<label class="col-md-3 form-control-label">{l s='Invoice' mod='allinone_rewards'} ({$totalForPaymentDefaultCurrency|escape:'htmlall':'UTF-8'}) {if $payment_invoice}<sup>*</sup>{/if}</label>
							<div class="col-md-9">
								<div class="invoice">
									<span class="js-file-name">{l s='No selected file' mod='allinone_rewards'}</span>
									<input id="payment_invoice" name="payment_invoice" type="file" class="file-input js-file-input" {if $payment_invoice}required{/if}>
									<button class="btn btn-primary">{l s='Choose file' mod='allinone_rewards'}</button>
								</div>
							</div>
						</div>
						<footer class="form-footer clearfix">
							<button class="btn btn-primary" type="submit" name="submitPayment" id="submitPayment">{l s='Save' mod='allinone_rewards'}</button>
						</footer>
						<p class="required"><sup>*</sup>{l s='Required field' mod='allinone_rewards'}</p>
					</fieldset>
				</form>
{/if}
			</div>

{if $cart_rules_available}
			<h2 id="cart_rules_available">{l s='Available vouchers' mod='allinone_rewards'}</h2>
			<table class="table table-bordered">
				<thead class="thead-default">
					<tr>
						<th>{l s='Code' mod='allinone_rewards'}</th>
						<th>{l s='Date' mod='allinone_rewards'}</th>
						<th>{l s='Description' mod='allinone_rewards'}</th>
						<th>{l s='Value' mod='allinone_rewards'}</th>
						<th>{l s='Minimum' mod='allinone_rewards'}</th>
						<th class="text-nowrap">{l s='Validity' mod='allinone_rewards'}</th>
						<th></th>
					</tr>
				</thead>
				<tbody>
	{foreach from=$cart_rules_available item=cart_rule name=myLoop}
					<tr>
						<td data-label="{l s='Code' mod='allinone_rewards'}">{$cart_rule.code|escape:'htmlall':'UTF-8'}</td>
						<td data-label="{l s='Date' mod='allinone_rewards'}">{dateFormat date=$cart_rule.date full=1}</td>
		{if $cart_rule.gift_product}
						<td data-label="{l s='Description' mod='allinone_rewards'}">{l s='Gift product: %s' sprintf=[$cart_rule.product] mod='allinone_rewards'}</td>
		{else}
						<td data-label="{l s='Description' mod='allinone_rewards'}">{$cart_rule.description|escape:'htmlall':'UTF-8'}</td>
		{/if}
		{if $cart_rule.gift_product && $rewards_virtual}
						<td class="text-xs-right text-nowrap" data-label="{l s='Value' mod='allinone_rewards'}">{$cart_rule.virtual_credits|escape:'htmlall':'UTF-8'}</td>
		{else}
						<td class="text-xs-right text-nowrap" data-label="{l s='Value' mod='allinone_rewards'}">{$cart_rule.credits|escape:'htmlall':'UTF-8'} {if $cart_rule.reduction_tax}{l s='VAT Incl.' mod='allinone_rewards'}{else}{l s='VAT Excl.' mod='allinone_rewards'}{/if} {if $rewards_virtual}({$cart_rule.virtual_credits|escape:'htmlall':'UTF-8'}){/if}</td>
		{/if}
						<td class="text-nowrap" data-label="{l s='Minimum' mod='allinone_rewards'}">{if isset($cart_rule.minimal)}{$cart_rule.minimal|escape:'htmlall':'UTF-8'} {if $cart_rule.minimum_amount_tax}{l s='VAT Incl.' mod='allinone_rewards'}{else}{l s='VAT Excl.' mod='allinone_rewards'}{/if}{/if}</td>
						<td data-label="{l s='Validity' mod='allinone_rewards'}">{dateFormat date=$cart_rule.date_to full=1}</td>
						<td class="text-xs-center"><a href="{$page_link|escape:'html':'UTF-8'}?discount={$cart_rule.code|escape:'htmlall':'UTF-8'}"><span>{l s='Add to cart' mod='allinone_rewards'}</span></a></td>
					</tr>
	{/foreach}
				</tbody>
			</table>
{/if}
{if $rewards_reminder_allowed}
			<h2 class="page-subheading">{l s='Options' mod='allinone_rewards'}</h2>
			<form id="rewards_options" method="post">
			{l s='Do you agree to receive periodic reminder emails about your rewards account?' mod='allinone_rewards'}<input {if $rewards_reminder}checked{/if} name="rewards_reminder" id="rewards_reminder_on" type="radio" value="1"><label for="rewards_reminder_on">{l s='Yes' mod='allinone_rewards'}</label> <input name="rewards_reminder" {if !$rewards_reminder}checked{/if} id="rewards_reminder_off" type="radio" value="0"><label for="rewards_reminder_off">{l s='No' mod='allinone_rewards'}</label>
			</form>
{/if}
		</div>
		<div id="idTab2" class="rewardsBlock">
{if $rewards}
			<table class="table table-bordered">
				<thead class="thead-default">
					<tr>
						<th>{l s='Event' mod='allinone_rewards'}</th>
						<th>{l s='Date' mod='allinone_rewards'}</th>
						<th>{l s='Reward' mod='allinone_rewards'}</th>
						<th>{l s='Status' mod='allinone_rewards'}</th>
						<th class="text-nowrap">{l s='Validity' mod='allinone_rewards'}</th>
					</tr>
				</thead>
				<tbody>
	{foreach from=$displayrewards item=reward name=myLoop}
					<tr>
						<td>{$reward.detail|escape:'htmlall':'UTF-8'}</td>
						<td data-label="{l s='Date' mod='allinone_rewards'}">{dateFormat date=$reward.date_add full=1}</td>
						<td class="text-xs-right text-nowrap" data-label="{l s='Reward' mod='allinone_rewards'}">{$reward.credits|escape:'htmlall':'UTF-8'}</td>
						<td data-label="{l s='Status' mod='allinone_rewards'}">{$reward.state|escape:'htmlall':'UTF-8'}</td>
						<td data-label="{l s='Validity' mod='allinone_rewards'}">{if $reward.id_reward_state==RewardsStateModel::getValidationId() && $reward.date_end!='0000-00-00 00:00:00'}{dateFormat date=$reward.date_end full=1}{else}-{/if}</td>
					</tr>
	{/foreach}
				</tbody>
			</table>

	{if $max_page > 1}
			<nav class="pagination">
				<div class="col-xs-12 text-center">
    				<ul class="page-list clearfix text-xs-center">
    					<li>
    						<a class="previous {if $pagination==1}disabled js-search-link{/if}" href="{$page_link|escape:'html':'UTF-8'}?page={$pagination-1|intval}">
								<i class="material-icons">&#xE314;</i>{l s='Previous' mod='allinone_rewards'}
							</a>
						</li>
		{section name=pagination start=1 loop=$max_page+1 step=1}
						<li {if $pagination == $smarty.section.pagination.index}class="current"{/if}>
							<a href="{$page_link|escape:'html':'UTF-8'}?page={$smarty.section.pagination.index|intval}">
								{$smarty.section.pagination.index|intval}
							</a>
						</li>
		{/section}
						<li>
							<a class="next {if $pagination >= $max_page}disabled js-search-link{/if}" href="{$page_link|escape:'html':'UTF-8'}?page={$pagination+1|intval}">
								{l s='Next' mod='allinone_rewards'}<i class="material-icons">&#xE315;</i>
							</a>
						</li>
					</ul>
				</div>
			</nav>
	{/if}
{/if}
		</div>
		<div id="idTab3" class="rewardsBlock">
{if $cart_rules}
			<table class="table table-bordered">
				<thead class="thead-default">
					<tr>
						<th>{l s='Code' mod='allinone_rewards'}</th>
						<th>{l s='Date' mod='allinone_rewards'}</th>
						<th>{l s='Status' mod='allinone_rewards'}</th>
						<th>{l s='Description' mod='allinone_rewards'}</th>
						<th>{l s='Value' mod='allinone_rewards'}</th>
						<th>{l s='Minimum' mod='allinone_rewards'}</th>
						<th class="text-nowrap">{l s='Validity' mod='allinone_rewards'}</th>
					</tr>
				</thead>
				<tbody>
	{foreach from=$cart_rules item=cart_rule name=myLoop}
					<tr class="{if ($smarty.foreach.myLoop.iteration % 2) == 0}item{else}alternate_item{/if}">
						<td data-label="{l s='Code' mod='allinone_rewards'}">{$cart_rule.code|escape:'htmlall':'UTF-8'}</td>
						<td data-label="{l s='Date' mod='allinone_rewards'}">{dateFormat date=$cart_rule.date full=1}</td>
						<td data-label="{l s='Status' mod='allinone_rewards'}">
		{if $cart_rule.id_order==0}
			{if $cart_rule.active}
				{if $cart_rule.date_to < date('Y-m-d H:i:s')}
							{l s='Expired' mod='allinone_rewards'}
				{else if (int)$cart_rule.quantity==0}
							{l s='Used' mod='allinone_rewards'}
				{else}
							{l s='Available' mod='allinone_rewards'}
				{/if}
			{else}
							{l s='Canceled' mod='allinone_rewards'}
			{/if}
		{else}
							{l s='Used in order %s' sprintf=[$cart_rule.reference] mod='allinone_rewards'}
		{/if}
						</td>
		{if $cart_rule.gift_product}
						<td data-label="{l s='Description' mod='allinone_rewards'}">{l s='Gift product: %s' sprintf=[$cart_rule.product] mod='allinone_rewards'}</td>
		{else}
						<td data-label="{l s='Description' mod='allinone_rewards'}">{$cart_rule.description|escape:'htmlall':'UTF-8'}</td>
		{/if}
		{if $cart_rule.gift_product && $rewards_virtual}
						<td class="text-xs-right text-nowrap" data-label="{l s='Value' mod='allinone_rewards'}">{$cart_rule.virtual_credits|escape:'htmlall':'UTF-8'}</td>
		{else}
						<td class="text-xs-right text-nowrap" data-label="{l s='Value' mod='allinone_rewards'}">{$cart_rule.credits|escape:'htmlall':'UTF-8'} {if $cart_rule.reduction_tax}{l s='VAT Incl.' mod='allinone_rewards'}{else}{l s='VAT Excl.' mod='allinone_rewards'}{/if} {if $rewards_virtual}({$cart_rule.virtual_credits|escape:'htmlall':'UTF-8'}){/if}</td>
		{/if}
						<td class="text-xs-right text-nowrap" data-label="{l s='Minimum' mod='allinone_rewards'}">{if isset($cart_rule.minimal)}{$cart_rule.minimal|escape:'htmlall':'UTF-8'} {if $cart_rule.minimum_amount_tax}{l s='VAT Incl.' mod='allinone_rewards'}{else}{l s='VAT Excl.' mod='allinone_rewards'}{/if}{/if}</td>
						<td data-label="{l s='Validity' mod='allinone_rewards'}">{dateFormat date=$cart_rule.date_to full=1}</td>
					</tr>
	{/foreach}
				</tbody>
			</table>
{/if}
		</div>
	</div>
</div>
{/block}