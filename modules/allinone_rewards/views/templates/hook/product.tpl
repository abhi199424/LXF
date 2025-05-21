{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2025 Yann BONNAILLIE - ByWEB
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}
{if !$ajax_loyalty}
<!-- MODULE allinone_rewards -->
<script type="text/javascript">
	var url_allinone_loyalty="{$link->getModuleLink('allinone_rewards', 'loyalty')|escape:'javascript':'UTF-8'}";
</script>
<p id="loyalty" class="align_justify reward_alert_message" {if !$display}style="display: none"{/if}>
<!-- END : MODULE allinone_rewards -->
{/if}
{if $display}
	{if $display_credits}
		{l s='Buying this product you will collect ' mod='allinone_rewards'} <b><span id="loyalty_credits">{$credits|escape:'htmlall':'UTF-8'}</span></b> {l s=' with our loyalty program.' mod='allinone_rewards'}
		{l s='Your cart will total' mod='allinone_rewards'} <b><span id="total_loyalty_credits">{$total_credits|escape:'htmlall':'UTF-8'}</span></b>.
	{else}
		{if isset($no_pts_discounted) && $no_pts_discounted == 1}
			{l s='No reward credits for this product because there\'s already a discount.' mod='allinone_rewards'}
		{else}
			{l s='Your basket must contain at least %s of products in order to get loyalty rewards.' sprintf={$minimum|escape:'html':'UTF-8'} mod='allinone_rewards'}
		{/if}
	{/if}
{/if}
{if !$ajax_loyalty}
</p>
<br class="clear" />
{/if}