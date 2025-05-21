{if $display}
{if !$ajax_loyalty}
	<!-- MODULE allinone_rewards -->
	<script type="text/javascript">
		var url_allinone_loyalty = "{url entity='module' name='allinone_rewards' controller='loyalty'}";
	</script>
	<!-- Entire clickable block -->
	<div 
		id="loyalty" 
		class="point-reward-sec align_justify reward_alert_message" 
		{if !$display}style="display: none"{/if}
		data-toggle="modal" 
  		data-target="#loyaltyModal"
		style="cursor: pointer;"
	>
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
				{l s='Your basket must contain at least %s of products in order to get loyalty rewards.' sprintf=[$minimum] mod='allinone_rewards'}
			{/if}
		{/if}
	{/if}
	{if !$ajax_loyalty}
	</div>
{/if}
{/if}