<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

class Allinone_rewardsLoyaltyModuleFrontController extends ModuleFrontController
{
	public $content_only = true;
	public $display_header = false;
	public $display_footer = false;

	public function initContent()
	{
		parent::initContent();
		if (Tools::getValue('id_product'))
			echo $this->module->loyalty->displayRewardOnProductPage();
		else if (Tools::getValue('reload_cart'))
			echo $this->module->loyalty->displayRewardOnCartPage();
		echo '';
		exit;
	}
}