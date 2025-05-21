{*
* All-in-one Rewards Module
*
* @category  Prestashop
* @category  Module
* @author    Yann BONNAILLIE - ByWEB
* @copyright 2012-2025 Yann BONNAILLIE - ByWEB
* @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
*}
<div class="clear not_templated indent optional review_api_optional_VerifiedReviewsAPI">
	<div class="clear not_templated">
		<label>{l s='Path to the folder containing customer reviews files in XML format' mod='allinone_rewards'}</label>
		<div class="margin-form">
			<input type="text" size="100" name="review_vr_folder" value="{$review_vr_folder|escape:'htmlall':'UTF-8'}" />
			<br>{l s='You need to configure the automatic export of your customer reviews to this folder.' mod='allinone_rewards'}
			<br>{l s='To do this, you need to set up review exports via FTP and in XML format from your Verified Reviews account.' mod='allinone_rewards'}
		</div>
	</div>
</div>