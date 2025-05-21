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
			<td>{l s='Number of orders' mod='allinone_rewards'}</td>
			<td>{l s='Customers' mod='allinone_rewards'}</td>
			<td class='price'>{l s='Rewards waiting for validation' mod='allinone_rewards'}</td>
			<td class='price'>{l s='Rewards validated' mod='allinone_rewards'}</td>
			<td class='price'>{l s='Total rewards' mod='allinone_rewards'}</td>
		</tr>
		<tr>
			<td>{$stats['nb_orders']|intval}</td>
			<td>{$stats['nb_customers']|intval}</td>
			<td class='price'>{displayPrice price=$stats['total_rewards_invalid']|floatval}</td>
			<td class='price'>{displayPrice price=$stats['total_rewards_valid']|floatval}</td>
			<td class='price'>{displayPrice price=($stats['total_rewards_invalid']|floatval + $stats['total_rewards_valid']|floatval)}</td>
		</tr>
	</table>

	<div class='title'>{l s='Details by customer' mod='allinone_rewards'}</div>
	<table class='tablesorter tablesorter-ice'>
		<thead>
			<tr>
				<th>{l s='Name' mod='allinone_rewards'}</th>
				<th>{l s='Number of orders' mod='allinone_rewards'}</th>
				<th class='price'>{l s='Rewards waiting for validation' mod='allinone_rewards'}</th>
				<th class='price'>{l s='Rewards validated' mod='allinone_rewards'}</th>
				<th class='price'>{l s='Total rewards' mod='allinone_rewards'}</th>
			</tr>
		</thead>
		<tbody>
{if isset($stats['customers'])}
	{foreach from=$stats['customers'] key=id_customer item=customer}
			<tr>
				<td class='left'><a target='_blank' href='?tab=AdminCustomers&id_customer={$id_customer|intval}&viewcustomer&token={$token|escape:'html':'UTF-8'}'>{$customer['lastname']|escape:'htmlall':'UTF-8'} {$customer['firstname']|escape:'htmlall':'UTF-8'}</a></td>
				<td>{$customer['nb_orders']|intval}</td>
				<td class='right'>{displayPrice price=$customer['credits_invalid']|floatval}</td>
				<td class='right'>{displayPrice price=$customer['credits_valid']|floatval}</td>
				<td class='right'>{displayPrice price=($customer['credits_invalid']|floatval + $customer['credits_valid']|floatval)}</td>
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