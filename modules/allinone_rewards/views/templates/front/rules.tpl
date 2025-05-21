{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2025 Yann BONNAILLIE - ByWEB
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}
{if $sback==1}
<div style="font-size: 12px; font-family: Arial; padding-bottom: 10px; text-align: left"><a style="color: #000000" href="#" onClick="return parent.openPopup(true)">« {l s='Back' mod='allinone_rewards'}</a></div>
{/if}
<div id="sponsorship_rules">
	<div class="rte">{$rules nofilter}</div>
</div>
