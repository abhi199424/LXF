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
<div id="reward_loyalty" class="reward_alert_message">
	<script type="text/javascript">
		var url_allinone_loyalty="{$link->getModuleLink('allinone_rewards', 'loyalty')|escape:'javascript':'UTF-8'}";
	</script>

	{if $display_credits > 0}
		{l s='By checking out this shopping cart you will collect %s into your rewards account.' sprintf=[$credits] mod='allinone_rewards'}
	{else}
		{if $minimum > 0}
			{l s='Your basket must contain at least %s of products in order to get loyalty rewards.' sprintf=[$minimum] mod='allinone_rewards'}
		{else}
			{l s='Add some products to your shopping cart to collect some loyalty credits.' mod='allinone_rewards'}
		{/if}
	{/if}
</div>
<!-- END : MODULE allinone_rewards -->