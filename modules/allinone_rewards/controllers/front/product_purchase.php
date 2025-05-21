<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

class Allinone_rewardsProduct_PurchaseModuleFrontController extends ModuleFrontController
{
	public $content_only = true;
	public $display_header = false;
	public $display_footer = false;

	public function initContent()
	{
		parent::initContent();
		if (Tools::getValue('id_product')) {
			if (Tools::getValue('action')=='purchase')
				echo $this->module->core->purchaseProductFromRewards((int)Tools::getValue('id_product'), (int)Tools::getValue('id_product_attribute'));
			else
				echo $this->module->core->displayPurchaseButtonOnProductPage((int)Tools::getValue('id_product'), (int)Tools::getValue('id_product_attribute'));
		}
		exit;
	}
}