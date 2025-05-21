{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2025 Yann BONNAILLIE - ByWEB
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}
{if ($rewards|@count)}
<!-- MODULE allinone_rewards -->
	<div class="{if version_compare($smarty.const._PS_VERSION_,'1.7.7.0','<')}col-lg-7{else}card mt-2{/if}" id="adminorders_sponsorship">
	{if version_compare($smarty.const._PS_VERSION_,'1.7.7.0','<')}
		<div class="panel" style="overflow: auto">
			<div class="panel-heading">{l s='Sponsorship rewards for this order' mod='allinone_rewards'}</div>
	{else}
			<div class="card-header">
    			<h3 class="card-header-title">
      				{l s='Sponsorship rewards for this order' mod='allinone_rewards'}
    			</h3>
  			</div>
	{/if}
	{if version_compare($smarty.const._PS_VERSION_,'1.7.7.0','>=')}
			<div class="card-body">
	{/if}
				<table style="width: 100%">
					<tr style="font-weight: bold">
						<td>{l s='Level' mod='allinone_rewards'}</td>
						<td>{l s='Name' mod='allinone_rewards'}</td>
						<td>{l s='Reward' mod='allinone_rewards'}</td>
						<td>{l s='Status' mod='allinone_rewards'}</td>
					</tr>
	{foreach from=$rewards item=reward}
					<tr>
						<td>{$reward['level_sponsorship']|intval}</td>
						<td><a href="{$link->getAdminLink('AdminCustomers', true, [], ['id_customer' => $reward['id_customer'], 'viewcustomer' => 1])|escape:'html':'UTF-8'}">{$reward['firstname']|escape:'htmlall':'UTF-8'} {$reward['lastname']|escape:'htmlall':'UTF-8'}</a></td>
						<td>{displayPrice price=$reward['credits']}</td>
						<td>{$reward['state']|escape:'htmlall':'UTF-8'}</td>
					</tr>
	{/foreach}
				</table>
	{if version_compare($smarty.const._PS_VERSION_,'1.7.7.0','>=')}
			</div>
	{/if}
	{if version_compare($smarty.const._PS_VERSION_,'1.7.7.0','<')}
		</div>
	{/if}
	</div>
<!-- END : MODULE allinone_rewards -->
{/if}