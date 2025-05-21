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
<span id="aior_add_to_cart_available_display">{$aior_total_available_display|escape:'htmlall':'UTF-8'}</span>
<span id="aior_add_to_cart_available_real">{$aior_total_available_real|floatval}</span>
<script type="text/javascript">
	var aior_product_purchase_url="{$link->getModuleLink('allinone_rewards', 'product_purchase')|escape:'javascript':'UTF-8'}";
	var aior_purchase_confirm_message0="{l s='Do you want to use your rewards to buy this product ?' mod='allinone_rewards' js=1}";
	var aior_purchase_confirm_message1="{l s='Your available balance is' mod='allinone_rewards' js=1}";
	var aior_purchase_confirm_message2="{l s='will be deducted immediately from your rewards account.' mod='allinone_rewards' js=1}";
	var aior_purchase_confirm_message3="{l s='Your available balance will then be' mod='allinone_rewards' js=1}";
	var aior_purchase_confirm_message4="{l s='This action can not be canceled, do you confirm ?' mod='allinone_rewards' js=1}";
	var aior_purchase_confirm_message5="{l s='Cancel' mod='allinone_rewards' js=1}";
	var aior_purchase_confirm_message6="{l s='Confirm' mod='allinone_rewards' js=1}";
	var aior_success_message="{l s='This product has been added to your cart.' mod='allinone_rewards' js=1}";
	var aior_success_message2="{l s='Your available balance is' mod='allinone_rewards' js=1}";
	var aior_success_message3="{l s='Continue Shopping' mod='allinone_rewards' js=1}";
	var aior_success_message4="{l s='Checkout' mod='allinone_rewards' js=1}";
	var aior_cart_url="{$aior_cart_url|escape:'html':'UTF-8'}";
</script>
<!-- END : MODULE allinone_rewards -->