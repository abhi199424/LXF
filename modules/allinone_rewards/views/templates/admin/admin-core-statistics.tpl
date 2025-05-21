{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2025 Yann BONNAILLIE - ByWEB
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}
<div class='statistics'>
	<div class='title'>{l s='General synthesis' mod='allinone_rewards'}</div>
	<table class='general'>
		<tr class='title'>
			<td>{l s='Number of rewards' mod='allinone_rewards'}</td>
			<td>{l s='Rewarded customers' mod='allinone_rewards'}</td>
			<td class='right'>{l s='Total rewards' mod='allinone_rewards'}</td>
			<td class='right'>{l s='Used as a voucher during an order' mod='allinone_rewards'}</td>
		</tr>
		<tr>
			<td>{$stats['nb_rewards']|intval}</td>
			<td>{$stats['nb_customers']|intval}</td>
			<td class='right'>{displayPrice price=$stats['total_rewards']|floatval}</td>
			<td class='right'>{displayPrice price=$stats['total_cart_rules']|floatval}</td>
		</tr>
	</table>

	<div class='title'>{l s='Details by reward status' mod='allinone_rewards'}</div>
	<table class='status'>
		<tr class='title'>
			<td>{l s='Status' mod='allinone_rewards'}</td>
			<td>{l s='Number of rewards' mod='allinone_rewards'}</td>
			<td>{l s='Rewarded customers' mod='allinone_rewards'}</td>
			<td class='right'>{l s='Total rewards' mod='allinone_rewards'}</td>
		</tr>
{foreach from=$status item=current_status}
		<tr>
			<td class='left'>{$current_status->name[$current_language_id]|escape:'htmlall':'UTF-8'}</td>
			<td>{$stats['details_by_status'][$current_status->id_reward_state]['nb_rewards']|intval}</td>
			<td>{$stats['details_by_status'][$current_status->id_reward_state]['nb_customers']|intval}</td>
			<td class='right'>{displayPrice price=$stats['details_by_status'][$current_status->id_reward_state]['total_rewards']|floatval}</td>
		</tr>
{/foreach}
	</table>

	<div class='title'>{l s='Details by reward type' mod='allinone_rewards'}</div>
	<table class='status'>
		<tr class='title'>
			<td class='left'>{l s='Type' mod='allinone_rewards'}</td>
			<td>{l s='Number of rewards' mod='allinone_rewards'}</td>
			<td>{l s='Rewarded customers' mod='allinone_rewards'}</td>
			<td class='right'>{l s='Total rewards' mod='allinone_rewards'}</td>
		</tr>
{foreach from=$module->plugins item=plugin}
	{if !$plugin instanceof RewardsCorePlugin && !$plugin instanceof RewardsToolsPlugin}
		<tr>
			<td class='left'>{$plugin->getTitle()|escape:'htmlall':'UTF-8'}</td>
			<td>{$stats['details_by_plugin'][$plugin->name]['nb_rewards']|intval}</td>
			<td>{$stats['details_by_plugin'][$plugin->name]['nb_customers']|intval}</td>
			<td class='right'>{displayPrice price=$stats['details_by_plugin'][$plugin->name]['total_rewards']|floatval}</td>
		</tr>
	{/if}
{/foreach}
		<tr>
			<td class='left'>{l s='Free' mod='allinone_rewards'}</td>
			<td>{$stats['details_by_plugin']['free']['nb_rewards']|intval}</td>
			<td>{$stats['details_by_plugin']['free']['nb_customers']|intval}</td>
			<td class='right'>{displayPrice price=$stats['details_by_plugin']['free']['total_rewards']|floatval}</td>
		</tr>
	</table>

	<div class='title'>{l s='Details by customer' mod='allinone_rewards'}</div>
	<table class='tablesorter tablesorter-ice'>
		<thead>
			<tr>
				<th>{l s='Name' mod='allinone_rewards'}</th>
				<th>{l s='Number of rewards' mod='allinone_rewards'}</th>
				<th>{$object->rewardStateDefault->name[$current_language_id]|escape:'htmlall':'UTF-8'}</th>
				<th>{$object->rewardStateValidation->name[$current_language_id]|escape:'htmlall':'UTF-8'}</th>
				<th>{$object->rewardStateConvert->name[$current_language_id]|escape:'htmlall':'UTF-8'}</th>
				<th>{l s='Used during an order' mod='allinone_rewards'}</th>
				<th>{$object->rewardStateWaitingPayment->name[$current_language_id]|escape:'htmlall':'UTF-8'}</th>
				<th>{$object->rewardStatePaid->name[$current_language_id]|escape:'htmlall':'UTF-8'}</th>
				<th>{l s='Total rewards' mod='allinone_rewards'}</th>
			</tr>
		</thead>
		<tbody>
{if isset($stats['details_by_customer'])}
	{foreach from=$stats['details_by_customer'] key=id_customer item=customer}
			<tr>
				<td class='left'><a target='_blank' href='?tab=AdminCustomers&id_customer={$id_customer|intval}&viewcustomer&token={$token|escape:'html':'UTF-8'}'>{$customer['lastname']|escape:'htmlall':'UTF-8'} {$customer['firstname']|escape:'htmlall':'UTF-8'}</a></td>
				<td>{$customer['nb_rewards']|intval}</td>
				<td class='right'>{displayPrice price=($customer[$object->rewardStateDefault->id_reward_state]|floatval + $customer[$object->rewardStateReturnPeriod->id_reward_state]|floatval)}</td>
				<td class='right'>{displayPrice price=$customer[$object->rewardStateValidation->id_reward_state]|floatval}</td>
				<td class='right'>{displayPrice price=$customer[$object->rewardStateConvert->id_reward_state]|floatval}</td>
				<td class='right'>{displayPrice price=$customer['total_cart_rules_used']|floatval}</td>
				<td class='right'>{displayPrice price=$customer[$object->rewardStateWaitingPayment->id_reward_state]|floatval}</td>
				<td class='right'>{displayPrice price=$customer[$object->rewardStatePaid->id_reward_state]|floatval}</td>
				<td class='right'>{displayPrice price=$customer['total_rewards']|floatval}</td>
			</tr>
	{/foreach}
{/if}
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
</div>
<script>
	var footer_pager = "{l s='{startRow} to {endRow} of {totalRows} rows' mod='allinone_rewards' js=1}";
	initTableSorter();
</script>