{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2025 Yann BONNAILLIE - ByWEB
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}

{if $payments|count > 0}
<div class='payments'>
	<table class='tablesorter tablesorter-ice'>
		<thead>
			<tr>
				<th>{l s='Request date' mod='allinone_rewards'}</th>
				<th>{l s='Customer' mod='allinone_rewards'}</th>
				<th>{l s='Value' mod='allinone_rewards'}</th>
				<th>{l s='Details' mod='allinone_rewards'}</th>
				<th class='filter-false sorter-false'>{l s='Invoice' mod='allinone_rewards'}</th>
				<th class='filter-false sorter-false'>{l s='Action' mod='allinone_rewards'}</th>
			</tr>
		</thead>
		<tbody>
{foreach from=$payments item=payment}
			<tr>
				<td>{dateFormat date=$payment['date_add'] full=1}</td>
				<td><a target='_blank' href='?tab=AdminCustomers&id_customer={$payment['id_customer']|intval}&viewcustomer&token={$token|escape:'html':'UTF-8'}'>{$payment['lastname']|escape:'htmlall':'UTF-8'} {$payment['firstname']|escape:'htmlall':'UTF-8'}</a></td>
				<td align='right'>{displayPrice price=$payment['credits']}</td>
				<td>{$payment['detail']|escape:'htmlall':'UTF-8'|nl2br}</td>
				<td align='center'>{if $payment['invoice']}<a href='{$module_template_dir|escape:'html':'UTF-8'}uploads/{$payment['invoice']|escape:'html':'UTF-8'}' download='Invoice{pathinfo($payment['invoice']|escape:'html':'UTF-8', PATHINFO_EXTENSION)}'>{l s='View' mod='allinone_rewards'}</a>{else}-{/if}</td>
				<td align='center'><a href='#' class='payment_validation' id='{$payment['id_payment']|intval}'>{l s='Mark as paid' mod='allinone_rewards'}</a></td>
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
</div>
<script>
	$('.payments').on('click', '.payment_validation', function(){
		var obj = $(this).parent().parent();
		$.ajax({
			type	: 'POST',
			cache	: false,
			url		: '{$module->getCurrentPage($this->name, true)|escape:'html':'UTF-8'}&payments=1&accept_payment='+$(this).attr('id'),
			dataType: 'html',
			success : function(data) {
				obj.remove();
				$('.tablesorter').trigger('update');
			}
		});
		return false;
	});

	var footer_pager = "{l s='{startRow} to {endRow} of {totalRows} rows' mod='allinone_rewards' js=1}";
	initTableSorter();
</script>
{else}
<div class='payments'>{l s='No request found' mod='allinone_rewards'}</div>
{/if}