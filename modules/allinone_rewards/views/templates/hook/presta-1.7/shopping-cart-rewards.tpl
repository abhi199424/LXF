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
<div id="reward_use" class="reward_alert_message">
	{l s='You have %s available in your rewards account, would you like to use them as a discount on this order ?' sprintf=[$rewards_available] mod='allinone_rewards'} <a href="{url entity='module' name='allinone_rewards' controller='rewards'}" title="{l s='My rewards account' mod='allinone_rewards'}">{l s='Yes, please.' mod='allinone_rewards'}</a>
</div>
<!-- END : MODULE allinone_rewards -->