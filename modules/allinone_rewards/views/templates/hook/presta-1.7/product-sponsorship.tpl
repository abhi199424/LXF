{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2025 Yann BONNAILLIE - ByWEB
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}
<div id="sponsorship_link">
	<a class="fancybox.inline" href="#sponsorship_product"><i class="material-icons">&#xE80D;</i> {l s='Sponsor for this product' mod='allinone_rewards'}</a>
</div>
<div style="display:none">
	<div id="sponsorship_product">
		{l s='You can share the URL of this product with your sponsorship included.' mod='allinone_rewards'}<br/><br/>
		<span id="link_to_share">{$sponsorship_link|escape:'html':'UTF-8'}</span><br><br>
		<div class="btn btn-primary" style="display: none;" id="sponsorship_copy_btn" data-clipboard-target="#link_to_share">{l s='Copy to clipboard' mod='allinone_rewards'}</div>
		<div class="btn btn-primary" style="display: none;" id="sponsorship_share_btn">{l s='Share the link' mod='allinone_rewards'}</div>
	</div>
</div>