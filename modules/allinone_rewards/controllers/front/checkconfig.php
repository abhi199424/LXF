<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

class Allinone_rewardsCheckconfigModuleFrontController extends ModuleFrontController
{
	public $content_only = true;
	public $display_header = false;
	public $display_footer = false;

	public function init()
	{
		$this->content_only = true;
		$this->display_header = false;
		$this->display_footer = false;
		parent::init();
	}

	public function display() {
		$hooks = array(
			'displayHeader', 'displayFooter', 'displayShoppingCartFooter',
			'displayCustomerAccount', 'displayCustomerAccountForm', 'displayCustomerAccountFormTop', 'displayMyAccountBlock',
			'displayProductPriceBlock', 'displayLeftColumnProduct',
			'displayOrderConfirmation',
			'actionCartSave', 'actionValidateOrder', 'actionOrderStatusUpdate', 'actionCustomerAccountAdd',
			'displayAdminCustomers', 'displayAdminProductsExtra', 'displayAdminOrder',
			'displayPDFInvoice',
			'actionAdminControllerSetMedia',
			'actionObjectCustomerDeleteAfter', 'actionObjectProductDeleteAfter', 'actionObjectOrderDetailAddAfter', 'actionObjectOrderDetailUpdateAfter', 'actionObjectOrderDetailDeleteAfter', 'actionProductCancel'
		);
		if (version_compare($this->module->version, '5.1.0', '<')) {
			$hooks[] = 'displayTop';
			$hooks[] = 'displayLeftColumn';
			$hooks[] = 'displayRightColumn';
		}
		if (version_compare(_PS_VERSION_, '1.6.1.0', '<='))
			$hooks[] = 'displayProductListReviews';
		if (version_compare(_PS_VERSION_, '1.6.0.12', '>=') && version_compare($this->module->version, '5.1.0', '<'))
			$hooks[] = 'displayNav';
		if (version_compare(_PS_VERSION_, '1.7', '>=')) {
			$hooks[] = 'additionalCustomerFormFields';
			if (version_compare($this->module->version, '5.1.0', '<')) {
				$hooks[] = 'displayNav1';
				$hooks[] = 'displayNav2';
				$hooks[] = 'displayBeforeBodyClosingTag';
			}
			$hooks[] = 'actionFrontControllerSetMedia';
		}
		if (version_compare(_PS_VERSION_, '1.7.1.0', '>='))
			$hooks[] = 'displayProductAdditionalInfo';
		else if (version_compare(_PS_VERSION_, '1.7', '>='))
			$hooks[] = 'displayProductButtons';
		else
			$hooks[] = 'displayRightColumnProduct';
		if (version_compare(_PS_VERSION_, '1.7.7.0', '>='))
			$hooks[] = 'displayAdminOrderMainBottom';
		if (version_compare(_PS_VERSION_, '8.0.0', '<'))
			$hooks[] = 'displayMyAccountBlockFooter';

		$result = array();
		$hooks_to_add = array();

		foreach($hooks as $hook) {
			// Retrocompatibility
			$hook_back = $hook;

			if (version_compare(_PS_VERSION_, '8.0', '<') && $alias = Hook::getRetroHookName($hook))
	        	$hook = $alias;

		    // Get hook id
		    $id_hook = Hook::getIdByName($hook);

		    // If hook does not exist
		    if (!$id_hook) {
		    	if (version_compare(_PS_VERSION_, '8.0', '<') && $hook_back != $hook)
		    		$result[] = $hook_back.' / '.$hook.' doesn\'t exist in hooks list';
		    	else
		    		$result[] = $hook_back.' doesn\'t exist in hooks list';
		    	$hooks_to_add[] = $hook_back;
		    	continue;
		    }

		    $shop_list = Shop::getCompleteListOfShopsID();
		    foreach ($shop_list as $shop_id) {
		        $sql = 'SELECT hm.`id_module`
					FROM `'._DB_PREFIX_.'hook_module` hm, `'._DB_PREFIX_.'hook` h
					WHERE hm.`id_module` = '.(int)$this->module->id.' AND h.`id_hook` = '.$id_hook.'
					AND h.`id_hook` = hm.`id_hook` AND `id_shop` = '.(int)$shop_id;
		        if (!Db::getInstance()->getRow($sql)) {
		        	if (version_compare(_PS_VERSION_, '8.0', '<') && $hook_back != $hook)
		    			$result[] = $hook_back.' / '.$hook.' is missing in shop'.(int)$shop_id;
		    		else
		    			$result[] = $hook_back.' is missing  in shop '.(int)$shop_id;
		            $hooks_to_add[] = $hook_back;
		        }
		    }
		}

		if (Tools::getValue('fix') == 1) {
			foreach($hooks_to_add as $hook) {
				if ($this->module->registerHook($hook))
					$result[] = $hook.' has been added';
			}
		}

		if (count($result) > 0)
			echo implode('<br>', $result);
		else
			echo "Everything is good";
		return true;
	}
}