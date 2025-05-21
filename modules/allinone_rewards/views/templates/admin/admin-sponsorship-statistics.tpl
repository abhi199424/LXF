{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2025 Yann BONNAILLIE - ByWEB
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}
{if !isset($id_sponsor)}
<div class='statistics'>
	<div>{l s='Only validated orders are taken in account in these statistics.' mod='allinone_rewards'}</div>
	<div class='title'>{l s='General synthesis' mod='allinone_rewards'}</div>
	<table class='general_sponsorship'>
		<tr class='title'>
			<td class='borderright' colspan='3' style='text-align: center'>{l s='Sponsors' mod='allinone_rewards'}</td>
			<td colspan='5' style='text-align: center'>{l s='Sponsored friends' mod='allinone_rewards'}</td>
		</tr>
		<tr class='title'>
			<td>{l s='Sponsors' mod='allinone_rewards'}</td>
			<td>{l s='Rewards for orders' mod='allinone_rewards'}</td>
			<td class='borderright'>{l s='Rewards for registrations' mod='allinone_rewards'}</td>
			<td>{l s='Pending' mod='allinone_rewards'}</td>
			<td>{l s='Registered' mod='allinone_rewards'}</td>
			<td>{l s='With orders' mod='allinone_rewards'}</td>
			<td>{l s='Number of orders' mod='allinone_rewards'}</td>
			<td>{l s='Total orders' mod='allinone_rewards'}</td>
		</tr>
		<tr>
			<td>{$stats['nb_sponsors']|intval}</td>
			<td>{displayPrice price=$stats['total_rewards_orders']|floatval}</td>
			<td class='borderright'>{displayPrice price=$stats['total_rewards_registrations']|floatval}</td>
			<td>{$stats['nb_pending']|intval}</td>
			<td>{$stats['nb_sponsored']|intval}</td>
			<td>{$stats['nb_buyers']|intval}</td>
			<td>{$stats['nb_orders']|intval}</td>
			<td>{displayPrice price=$stats['total_orders']|floatval}</td>
		</tr>
	</table>

	<div class='title'>{l s='Details by registration channel' mod='allinone_rewards'}</div>
	<table class='channels_sponsorship'>
		<tr class='title'>
			<td class='title'>{l s='Channels' mod='allinone_rewards'}</td>
			<td>{l s='Registered' mod='allinone_rewards'}</td>
			<td>{l s='With orders' mod='allinone_rewards'}</td>
			<td>{l s='Number of orders' mod='allinone_rewards'}</td>
			<td class='price'>{l s='Total orders' mod='allinone_rewards'}</td>
			<td class='price'>{l s='Rewards for orders' mod='allinone_rewards'}</td>
			<td class='price'>{l s='Rewards for registrations' mod='allinone_rewards'}</td>
		</tr>
		<tr>
			<td class='title'>{l s='Email invitation' mod='allinone_rewards'}</td>
			<td>{$stats['nb_sponsored1']|intval}</td>
			<td>{$stats['nb_buyers1']|intval}</td>
			<td>{$stats['nb_orders1']|intval}</td>
			<td class='price'>{displayPrice price=$stats['total_orders1']|floatval}</td>
			<td class='price'>{displayPrice price=$stats['total_rewards_orders_channel1']|floatval}</td>
			<td class='price'>{displayPrice price=$stats['total_rewards_registrations_channel1']|floatval}</td>
		</tr>
		<tr>
			<td class='title'>{l s='Sponsorship link' mod='allinone_rewards'}</td>
			<td>{$stats['nb_sponsored2']|intval}</td>
			<td>{$stats['nb_buyers2']|intval}</td>
			<td>{$stats['nb_orders2']|intval}</td>
			<td class='price'>{displayPrice price=$stats['total_orders2']|floatval}</td>
			<td class='price'>{displayPrice price=$stats['total_rewards_orders_channel2']|floatval}</td>
			<td class='price'>{displayPrice price=$stats['total_rewards_registrations_channel2']|floatval}</td>
		</tr>
		<tr>
			<td class='title'>{l s='Facebook' mod='allinone_rewards'}</td>
			<td>{$stats['nb_sponsored3']|intval}</td>
			<td>{$stats['nb_buyers3']|intval}</td>
			<td>{$stats['nb_orders3']|intval}</td>
			<td class='price'>{displayPrice price=$stats['total_orders3']|floatval}</td>
			<td class='price'>{displayPrice price=$stats['total_rewards_orders_channel3']|floatval}</td>
			<td class='price'>{displayPrice price=$stats['total_rewards_registrations_channel3']|floatval}</td>
		</tr>
		<tr>
			<td class='title'>{l s='Twitter' mod='allinone_rewards'}</td>
			<td>{$stats['nb_sponsored4']|intval}</td>
			<td>{$stats['nb_buyers4']|intval}</td>
			<td>{$stats['nb_orders4']|intval}</td>
			<td class='price'>{displayPrice price=$stats['total_orders4']|floatval}</td>
			<td class='price'>{displayPrice price=$stats['total_rewards_orders_channel4']|floatval}</td>
			<td class='price'>{displayPrice price=$stats['total_rewards_registrations_channel4']|floatval}</td>
		</tr>
	{if $stats['nb_sponsored5']|intval > 0}
		<tr>
			<td class='title'>{l s='Google +1' mod='allinone_rewards'}</td>
			<td>{$stats['nb_sponsored5']|intval}</td>
			<td>{$stats['nb_buyers5']|intval}</td>
			<td>{$stats['nb_orders5']|intval}</td>
			<td class='price'>{displayPrice price=$stats['total_orders5']|floatval}</td>
			<td class='price'>{displayPrice price=$stats['total_rewards_orders_channel5']|floatval}</td>
			<td class='price'>{displayPrice price=$stats['total_rewards_registrations_channel5']|floatval}</td>
		</tr>
	{/if}
	</table>

	<div class='title'>{l s='Details by sponsor' mod='allinone_rewards'}</div>
	<table class='customers tablesorter tablesorter-ice'>
		<thead>
			<tr>
				<th class='filter-false sorter-false' colspan='5'>{l s='Sponsors' mod='allinone_rewards'}</th>
				<th class='filter-false sorter-false' colspan='5'>{l s='Sponsored friends (Level 1)' mod='allinone_rewards'}</th>
			</tr>
			<tr>
				<th class='filter-false sorter-false'>&nbsp;</th>
				<th>{l s='Name' mod='allinone_rewards'}</th>
				<th>{l s='Rewards for orders' mod='allinone_rewards'}</th>
				<th>{l s='Rewards for registrations' mod='allinone_rewards'}</th>
				<th>{l s='Indirect rewards' mod='allinone_rewards'}</th>
				<th>{l s='Pending' mod='allinone_rewards'}</th>
				<th>{l s='Registered' mod='allinone_rewards'}</th>
				<th>{l s='With orders' mod='allinone_rewards'}</th>
				<th>{l s='Orders' mod='allinone_rewards'}</th>
				<th>{l s='Total' mod='allinone_rewards'}</th>
			</tr>
		</thead>
		<tbody>
	{if isset($stats['sponsors'])}
		{foreach from=$stats['sponsors'] item=sponsor}
			<tr id='line_{$sponsor['id_sponsor']|intval}'>
				<td>
			{if isset($stats['sponsored'][$sponsor['id_sponsor']]) && is_array($stats['sponsored'][$sponsor['id_sponsor']])}
					<a href="javascript:showDetails({$sponsor['id_sponsor']|intval}, '{$module->getCurrentPage($object->name, true)|escape:'html':'UTF-8'}')"><img src="../img/admin/details.gif"></a>
			{/if}
				</td>
				<td class='left'><a target='_blank' href='?tab=AdminCustomers&id_customer={$sponsor['id_sponsor']|intval}&viewcustomer&token={$token|escape:'html':'UTF-8'}'>{$sponsor['lastname']|escape:'htmlall':'UTF-8'} {$sponsor['firstname']|escape:'htmlall':'UTF-8'}</a></td>
				<td class='right'>{displayPrice price=$sponsor['direct_rewards_orders']|floatval}</td>
				<td class='right'>{displayPrice price=$sponsor['direct_rewards_registrations']|floatval}</td>
				<td class='right'>{displayPrice price=$sponsor['indirect_rewards']|floatval}</td>
				<td>{$sponsor['nb_pending']|intval}</td>
				<td>{$sponsor['nb_registered']|intval}</td>
				<td>{$sponsor['nb_buyers']|intval}</td>
				<td>{$sponsor['nb_orders']|intval}</td>
				<td class='right'>{displayPrice price=$sponsor['total_orders']|floatval}</td>
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
{else if isset($stats['sponsored'][$id_sponsor]) && is_array($stats['sponsored'][$id_sponsor])}
<tr class='details tablesorter-childRow'>
	<td colspan='10'>
		<table style='width: 90%; margin: 20px auto;'>
			<tr class='title'>
				<td>{l s='Levels' mod='allinone_rewards'}</td>
				<td class='left'>{l s='Channels' mod='allinone_rewards'}</td>
				<td class='left'>{l s='Name of the friends' mod='allinone_rewards'}</td>
				<td>{l s='Number of orders (granting rewards)' mod='allinone_rewards'}</td>
				<td class='right'>{l s='Total orders (granting rewards)' mod='allinone_rewards'}</td>
				<td class='right'>{l s='Total rewards' mod='allinone_rewards'}</td>
			</tr>
	{foreach from=$stats['sponsored'][$id_sponsor] item=sponsored}
			<tr>
				<td>{$sponsored['level_sponsorship']|intval}</td>
				<td class='left'>
		{if $sponsored['level_sponsorship']|intval==1}
			{if $sponsored['channel']|intval==2}
					{l s='Sponsorship link' mod='allinone_rewards'}
			{else if $sponsored['channel']|intval==3}
					{l s='Facebook' mod='allinone_rewards'}
			{else if $sponsored['channel']|intval==4}
					{l s='Twitter' mod='allinone_rewards'}
			{else if $sponsored['channel']|intval==5}
					{l s='Google +1' mod='allinone_rewards'}
			{else}
					{l s='Email invitation' mod='allinone_rewards'}
			{/if}
		{/if}
				</td>
				<td class='left'><a target='_blank' href='?tab=AdminCustomers&id_customer={$sponsored['id_sponsored']|intval}&viewcustomer&token={$token|escape:'html':'UTF-8'}'>{$sponsored['lastname']|escape:'htmlall':'UTF-8'} {$sponsored['firstname']|escape:'htmlall':'UTF-8'}</a></td>
				<td>{$sponsored['nb_orders']|intval}</td>
				<td class='right'>{displayPrice price=$sponsored['total_orders']|floatval}</td>
				<td class='right'>{displayPrice price=$sponsored['total_rewards']|floatval}</td>
			</tr>
	{/foreach}
		</table>
	</td>
</tr>
{/if}