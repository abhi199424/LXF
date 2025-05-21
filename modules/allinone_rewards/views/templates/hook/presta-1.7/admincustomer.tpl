{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2025 Yann BONNAILLIE - ByWEB
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}
<!-- MODULE allinone_rewards -->
<div class="col-lg-12" id="admincustomer">
{if version_compare($smarty.const._PS_VERSION_,'1.7.6','<')}
	<div class="panel">
		<div class="panel-heading">{l s='Rewards account' mod='allinone_rewards'}</div>
		{if $msg}{$msg nofilter}{/if}
{else}
	<div class="card">
		<h3 class="card-header">{l s='Rewards account' mod='allinone_rewards'}</h3>
		{if $msg}{$msg nofilter}{/if}
{/if}
		<div class="{if version_compare($smarty.const._PS_VERSION_,'1.7.6','>=')}card-body{/if}">
			<form id="template_change" method="post">
				<input type="hidden" name="action" />
				{l s='Template used for "Rewards account"' mod='allinone_rewards'}&nbsp;
				<select class="change_template form-control" name="core_template" style="display: inline; width: auto;">
					<option value='0'>{l s='Default template' mod='allinone_rewards'}</option>
					{foreach from=$core_templates item=template}
						<option {if $template['id_template']==$core_template_id}selected{/if} value='{$template['id_template']|intval}'>{$template['name']|escape:'htmlall':'UTF-8'}</option>
					{/foreach}
				</select>
				<span style="padding-left: 50px">
					{l s='Template used for "Loyalty program"' mod='allinone_rewards'}&nbsp;
					<select class="change_template form-control" name="loyalty_template" style="display: inline; width: auto;">
						<option value='0'>{l s='Default template' mod='allinone_rewards'}</option>
						{foreach from=$loyalty_templates item=template}
							<option {if $template['id_template']==$loyalty_template_id}selected{/if} value='{$template['id_template']|intval}'>{$template['name']|escape:'htmlall':'UTF-8'}</option>
						{/foreach}
					</select>
				</span>
			</form>
			<br/><br/>
{if $rewards && $rewards|@count > 0}
			<form id="rewards_reminder" method="post">
	{if $rewards_account->remind_active}
				<button class="btn btn-primary" name="submitRewardReminderOff" type="submit">{l s='Disable reminder email' mod='allinone_rewards'}</button>
		{if (float)$totals[RewardsStateModel::getValidationId()] > 0}
				<button class="btn btn-primary" name="submitRewardReminder" type="submit">{l s='Send an email with account balance :' mod='allinone_rewards'} {displayPrice price=$totals[RewardsStateModel::getValidationId()]}</button> {if $rewards_account->date_last_remind && $rewards_account->date_last_remind != '0000-00-00 00:00:00'} ({l s='last email :' mod='allinone_rewards'} {dateFormat date=$rewards_account->date_last_remind full=1}){/if}
		{/if}
	{else}
				<button class="btn btn-primary" name="submitRewardReminderOn" type="submit">{l s='Enable reminder email' mod='allinone_rewards'}</button>
	{/if}
			</form><br>
			<table cellspacing="0" cellpadding="0" class="table">
				<thead>
					<tr style="background-color: #EEEEEE">
						<th class="text-center">{l s='Total rewards' mod='allinone_rewards'}</th>
						<th class="text-center">{l s='Already converted' mod='allinone_rewards'}</th>
						<th class="text-center">{l s='Paid' mod='allinone_rewards'}</th>
						<th class="text-center">{l s='Available' mod='allinone_rewards'}</th>
						<th class="text-center">{l s='Awaiting validation' mod='allinone_rewards'}</th>
						<th class="text-center">{l s='Awaiting payment' mod='allinone_rewards'}</th>
					</tr>
				</thead>
				<tr>
					<td class="text-center">{displayPrice price=$totals['total']}</td>
					<td class="text-center">{displayPrice price=$totals[RewardsStateModel::getConvertId()]}</td>
					<td class="text-center">{displayPrice price=$totals[RewardsStateModel::getPaidId()]}</td>
					<td class="text-center">{displayPrice price=$totals[RewardsStateModel::getValidationId()]}</td>
					<td class="text-center">{displayPrice price=$totals[RewardsStateModel::getDefaultId()] + $totals[RewardsStateModel::getReturnPeriodId()]}</td>
					<td class="text-center">{displayPrice price=$totals[RewardsStateModel::getWaitingPaymentId()]}</td>
				</tr>
			</table>
{else}
			{l s='This customer has no reward' mod='allinone_rewards'}
{/if}

{if version_compare($smarty.const._PS_VERSION_,'1.7.6','<')}
			<div class="panel-heading" style="margin-top: 30px">{l s='Convert the rewards into a voucher' mod='allinone_rewards'}</div>
{else}
			<div class="card-header" style="margin-top: 30px">{l s='Convert the rewards into a voucher' mod='allinone_rewards'}</div>
{/if}
			<form id="rewards_convertion" method="post" class="form-inline">
			<label>{l s='Rewards amount to convert' mod='allinone_rewards'}&nbsp;</label>
			<div class="input-group mr-3">
				<input name="convert_reward_value" type="text" class="form-control text-right" size="6" value="{$convert_reward_value|floatval}" style="width: 80px"/>
				<div class="input-group-addon">
            		{$sign|escape:'html':'UTF-8'}
            	</div>
            </div>
			<button type="submit" class="btn btn-primary" name="submitConvertReward">{l s='Generate a voucher' mod='allinone_rewards'}</button>
			</form>

{if version_compare($smarty.const._PS_VERSION_,'1.7.6','<')}
			<div class="panel-heading" style="margin-top: 30px">{l s='Add a new reward' mod='allinone_rewards'}</div>
{else}
			<div class="card-header" style="margin-top: 30px">{l s='Add a new reward' mod='allinone_rewards'}</div>
{/if}
			<form id="rewards_adding" method="post" class="form-inline">
				<div class="form-group mr-3">
					<label>{l s='Value' mod='allinone_rewards'}&nbsp;</label>
					<div class="input-group">
						<input name="new_reward_value" type="text" class="form-control text-right" size="6" value="{$new_reward_value|floatval}"/>
						<div class="input-group-addon">
            				{$sign|escape:'html':'UTF-8'}
            			</div>
            		</div>
				</div>
				<div class="form-group mr-3">
					<label>&nbsp;{l s='Status' mod='allinone_rewards'}&nbsp;</label>
					<select name="new_reward_state" class="form-control" style="display: inline; width: auto">
						<option {if $new_reward_state == RewardsStateModel::getDefaultId()}selected{/if} value="{RewardsStateModel::getDefaultId()|intval}">{$rewardStateDefault|escape:'htmlall':'UTF-8'}</option>
						<option {if $new_reward_state == RewardsStateModel::getValidationId()}selected{/if} value="{RewardsStateModel::getValidationId()|intval}">{$rewardStateValidation|escape:'htmlall':'UTF-8'}</option>
						<option {if $new_reward_state == RewardsStateModel::getCancelId()}selected{/if} value="{RewardsStateModel::getCancelId()|intval}">{$rewardStateCancel|escape:'htmlall':'UTF-8'}</option>
					</select>
				</div>
				<div class="form-group mr-3">
					<label>&nbsp;{l s='Reason' mod='allinone_rewards'}&nbsp;</label>
					<input name="new_reward_reason" type="text" class="form-control" size="40" maxlength="80" value="{$new_reward_reason|escape:'htmlall':'UTF-8'}"/>
				</div>
				<div class="form-group mr-3">
					<label>&nbsp;{l s='Validity' mod='allinone_rewards'}&nbsp;</label>
					<div class="input-group">
						<input name="new_reward_date_end" type="text" size="20" value="{$new_reward_date_end|escape:'htmlall':'UTF-8'}" class="form-control datetimepicker" style="display: inline; width: auto"/>
						<span class="input-group-addon"><i class="material-icons">date_range</i></span>
					</div>
				</div>
				<button type="submit" class="btn btn-primary" name="submitNewReward">{l s='Save settings' mod='allinone_rewards'}</button>
			</form>

			<form id="rewards_history" method="post">
{if $rewards && $rewards|@count > 0}
	{if version_compare($smarty.const._PS_VERSION_,'1.7.6','<')}
			<div class="panel-heading" style="margin-top: 30px;">{l s='Rewards history' mod='allinone_rewards'}</div>
	{else}
			<div class="card-header" style="margin-top: 30px">{l s='Rewards history' mod='allinone_rewards'}</div>
	{/if}
			<input type="hidden" id="id_reward_to_update" name="id_reward_to_update" />
			<table cellspacing="0" cellpadding="0" class="tablesorter tablesorter-ice" id="rewards_list">
				<thead>
					<tr style="background-color: #EEEEEE">
						<th>{l s='Event' mod='allinone_rewards'}</th>
						<th>{l s='Date' mod='allinone_rewards'}</th>
						<th>{l s='Validity' mod='allinone_rewards'}</th>
						<th class="text-nowrap">{l s='Total' mod='allinone_rewards'}</th>
						<th>{l s='Reward' mod='allinone_rewards'}</th>
						<th>{l s='Status' mod='allinone_rewards'}</th>
						<th class='filter-false sorter-false'>{l s='Action' mod='allinone_rewards'}</th>
					</tr>
				</thead>
				<tbody>
	{foreach from=$rewards item=reward name=myLoop}
		{assign var="bUpdate" value="{in_array($reward['id_reward_state'], $states_for_update)|intval}"}
					<tr class="{if ($smarty.foreach.myLoop.iteration % 2) == 0}alt_row{/if}">
						<td>{if ($bUpdate && $reward['plugin'] == "free")}<input name="reward_reason_{$reward['id_reward']|intval}" type="text" class="form-control" maxlength="80" value="{$reward['detail']|escape:'html':'UTF-8'}" />{else}{$reward['detail'] nofilter}{/if}</td>
						<td class="text-center text-nowrap">{$reward['date_add']|escape:'html':'UTF-8'}</td>
						<td class="text-center">
		{if $bUpdate}
							<div class="input-group">
								<input name="reward_date_end_{$reward['id_reward']|intval}" type="text" style="position: relative;" class="form-control datetimepicker" value="{if $reward['date_end']!='0000-00-00 00:00:00'}{$reward['date_end']|escape:'html':'UTF-8'}{/if}" />
								<span class="input-group-addon"><i class="material-icons">date_range</i></span>
							</div>
		{elseif $reward['date_end']!='0000-00-00 00:00:00'}
							{$reward['date_end']|escape:'html':'UTF-8'}
		{else}
							-
		{/if}
						</td>
						<td align="right" class="price">{if (int)$reward['id_order'] > 0}{displayPrice price=(float)$reward['total_without_shipping'] currency=$reward['id_currency']}{else}-{/if}</td>
						<td align="right">
		{if $bUpdate}
							<div class="input-group">
								<input name="reward_value_{$reward['id_reward']|intval}" type="text" class="form-control text-right" value="{$reward['credits']|string_format:'%.2f'}"/>
								<div class="input-group-addon">
                					{$sign|escape:'html':'UTF-8'}
            					</div>
            				</div>
		{else}
							{displayPrice price=$reward['credits']}
		{/if}
						</td>
						<td>
		{if $bUpdate}
							<select name="reward_state_{$reward['id_reward']|intval}" class="form-control">
								<option {if $reward['id_reward_state'] == RewardsStateModel::getDefaultId()}selected{/if} value="{RewardsStateModel::getDefaultId()|intval}">{$rewardStateDefault|escape:'htmlall':'UTF-8'}</option>
								<option {if $reward['id_reward_state'] == RewardsStateModel::getValidationId()}selected{/if} value="{RewardsStateModel::getValidationId()|intval}">{$rewardStateValidation|escape:'htmlall':'UTF-8'}</option>
								<option {if $reward['id_reward_state'] == RewardsStateModel::getCancelId()}selected{/if} value="{RewardsStateModel::getCancelId()|intval}">{$rewardStateCancel|escape:'htmlall':'UTF-8'}</option>
			{if ($reward['id_reward_state'] == RewardsStateModel::getReturnPeriodId() || ((int)$reward['id_order'] > 0 && Configuration::get('REWARDS_WAIT_RETURN_PERIOD') && Configuration::get('PS_ORDER_RETURN') && (int)Configuration::get('PS_ORDER_RETURN_NB_DAYS') > 0))}
								<option {if $reward['id_reward_state'] == RewardsStateModel::getReturnPeriodId()}selected{/if} value="{RewardsStateModel::getReturnPeriodId()|intval}">{$rewardStateReturnPeriod|escape:'htmlall':'UTF-8'} {l s='(Return period)' mod='allinone_rewards'}</option>
			{/if}
							</select>
		{else}
			{if $reward['id_reward_state']==RewardsStateModel::getConvertId()}
				{if isset($reward['order_cart_rule'])}
							{l s='Used in order' mod='allinone_rewards'} <a href="{$link->getAdminLink('AdminOrders', true, [], ['id_order' => $reward['order_cart_rule'], 'vieworder' => 1])|escape:'html':'UTF-8'}">{$reward['reference']|escape:'htmlall':'UTF-8'}</a>{if $reward['code']} (<a href="{$link->getAdminLink('AdminCartRules', true, [], ['id_cart_rule' => $reward['id_cart_rule'], 'updatecart_rule' => 1])|escape:'html':'UTF-8'}">{$reward['code']|escape:'htmlall':'UTF-8'}</a>){/if}
				{else if $reward['date_cart_rule'] < date('Y-m-d H:i:s')}
							{l s='Expired voucher' mod='allinone_rewards'}{if $reward['code']} (<a href="{$link->getAdminLink('AdminCartRules', true, [], ['id_cart_rule' => $reward['id_cart_rule'], 'updatecart_rule' => 1])|escape:'html':'UTF-8'}">{$reward['code']|escape:'htmlall':'UTF-8'}</a>){/if}
				{else}
							{$reward['state']|escape:'htmlall':'UTF-8'}{if $reward['code']} (<a href="{$link->getAdminLink('AdminCartRules', true, [], ['id_cart_rule' => $reward['id_cart_rule'], 'updatecart_rule' => 1])|escape:'html':'UTF-8'}">{$reward['code']|escape:'htmlall':'UTF-8'}</a> {l s='is available' mod='allinone_rewards'}){/if}
				{/if}
			{else}
							{$reward['state']|escape:'htmlall':'UTF-8'}
			{/if}
		{/if}
						</td>
						<td class="text-center">{if $bUpdate}<button class="btn btn-primary" name="submitRewardUpdate" type="submit" onClick="$('#id_reward_to_update').val({$reward['id_reward']|intval})">{l s='Save settings' mod='allinone_rewards'}</button>{/if}</td>
					</tr>
	{/foreach}
				</tbody>
			</table>
			<div class="pager">
		    	<img src="{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/first.png" class="first"/>
		    	<img src="{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/prev.png" class="prev"/>
		    	<span class="pagedisplay"></span> <!-- this can be any element, including an input -->
		    	<img src="{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/next.png" class="next"/>
		    	<img src="{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/last.png" class="last"/>
		    	<select class="pagesize form-control" style="width: auto; display: inline">
		      		<option value='10'>10</option>
		      		<option value='20'>20</option>
		      		<option value='50'>50</option>
		      		<option value='100'>100</option>
		      		<option value='500'>500</option>
		    	</select>
			</div>

	{if $payment_authorized}
		{if version_compare($smarty.const._PS_VERSION_,'1.7.6','<')}
			<div class="panel-heading" style="margin-top: 30px;">{l s='Payments history' mod='allinone_rewards'}</div>
		{else}
			<div class="card-header" style="margin-top: 30px">{l s='Payments history' mod='allinone_rewards'}</div>
		{/if}
		{if $payments && $payments|@count > 0}
			<table cellspacing="0" cellpadding="0" class="tablesorter tablesorter-ice" id="payments_list">
				<thead>
					<tr style="background-color: #EEEEEE">
						<th>{l s='Request date' mod='allinone_rewards'}</th>
						<th>{l s='Payment date' mod='allinone_rewards'}</th>
						<th>{l s='Value' mod='allinone_rewards'}</th>
						<th>{l s='Details' mod='allinone_rewards'}</th>
						<th class='filter-false sorter-false'>{l s='Invoice' mod='allinone_rewards'}</th>
						<th class='filter-false sorter-false'>{l s='Action' mod='allinone_rewards'}</th>
					</tr>
				</thead>
				<tbody>
			{foreach from=$payments item=payment name=myLoop}
					<tr class="{if ($smarty.foreach.myLoop.iteration % 2) == 0}alt_row{/if}">
						<td>{$payment['date_add']|escape:'html':'UTF-8'}</td>
						<td>{if $payment['paid']}{$payment['date_upd']|escape:'html':'UTF-8'}{else}-{/if}</td>
						<td class="text-right">{displayPrice price=$payment['credits']}</td>
						<td>{$payment['detail']|escape:'htmlall':'UTF-8'|nl2br}</td>
						<td class="text-center">{if $payment['invoice']}<a href="{$module_template_dir|escape:'html':'UTF-8'}uploads/{$payment['invoice']|escape:'html':'UTF-8'}" download="Invoice.pdf">{l s='View' mod='allinone_rewards'}</a>{else}-{/if}</td>
						<td class="text-center">{if !$payment['paid']}<a href="{$link->getAdminLink('AdminCustomers', true, [], ['id_customer' => $customer->id, 'viewcustomer' => 1, 'accept_payment' => $payment['id_payment']])|escape:'html':'UTF-8'}">{l s='Mark as paid' mod='allinone_rewards'}</a>{else}-{/if}</td>
					</tr>
			{/foreach}
				</tbody>
			</table>
			<div class="pager">
		    	<img src="{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/first.png" class="first"/>
		    	<img src="{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/prev.png" class="prev"/>
		    	<span class="pagedisplay"></span> <!-- this can be any element, including an input -->
		    	<img src="{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/next.png" class="next"/>
		    	<img src="{$module_template_dir|escape:'html':'UTF-8'}js/tablesorter/addons/pager/last.png" class="last"/>
		    	<select class="pagesize" style="width: auto; display: inline">
		      		<option value='10'>10</option>
		      		<option value='20'>20</option>
		      		<option value='50'>50</option>
		      		<option value='100'>100</option>
		      		<option value='500'>500</option>
		    	</select>
			</div>
		{else}
				{l s='No payment request found' mod='allinone_rewards'}
		{/if}
	{/if}
{/if}
			</form>
		</div>
		<script>
			var footer_pager = "{l s='{startRow} to {endRow} of {totalRows} rows' mod='allinone_rewards'}";
		</script>
	</div>
</div>
<!-- END : MODULE allinone_rewards -->