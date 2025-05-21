<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

class AdminProductRewardController extends ModuleAdminController
{
	public function postProcess()
	{
		switch(Tools::getValue('action')) {
			case 'submit_reward':
				if (Tools::getValue('reward_product_id'))
					$reward_product = new RewardsProductModel((int)Tools::getValue('reward_product_id'));
				else {
					$reward_product = new RewardsProductModel();
					$reward_product->id_product = (int)Tools::getValue('id_product');
				}
				$reward_product->id_template = Tools::getValue('reward_product_id_template');
				$reward_product->value = Tools::getValue('reward_product_value');
				$reward_product->type = (int)Tools::getValue('reward_product_type');
				$reward_product->date_from = Tools::getValue('reward_product_from');
				$reward_product->date_to = Tools::getValue('reward_product_to');
				$reward_product->plugin = Tools::getValue('reward_product_plugin');
				$reward_product->level = (int)Tools::getValue('reward_product_level');
				$this->_postValidation($reward_product);
				if ($reward_product->save(true))
					die(json_encode(array('error' => false, 'reward_product' => $reward_product)));
				die(json_encode(array('error' => $this->l('The reward can\'t be saved.'))));
			case 'delete_reward':
				if (Tools::getValue('reward_product_id')) {
					$reward_product = new RewardsProductModel((int)Tools::getValue('reward_product_id'));
					$reward_product->delete();
					die(json_encode(array('error' => false)));
				}
				break;
			case 'submit_reward_gift':
				$this->_postValidation2();
				if ((int)Tools::getValue('rewards_gift_behavior') == -1) {
					RewardsGiftProductModel::deleteCustomization((int)Tools::getValue('id_product'));
				} else if ((int)Tools::getValue('rewards_gift_behavior') == 0) {
					RewardsGiftProductModel::deleteCustomization((int)Tools::getValue('id_product'));
					$rewards_gift_product = new RewardsGiftProductModel();
					$rewards_gift_product->id = (int)Tools::getValue('id_product');
					$rewards_gift_product->id_product = (int)Tools::getValue('id_product');
					$rewards_gift_product->gift_allowed = 0;
					$rewards_gift_product->add();
				} else if ((int)Tools::getValue('rewards_gift_behavior') == 1) {
					RewardsGiftProductModel::deleteCustomization((int)Tools::getValue('id_product'));
					if (is_array(Tools::getValue('rewards_gift_allowed'))) {
						$rewards_gift_product = new RewardsGiftProductModel();
						$rewards_gift_product->id_product = (int)Tools::getValue('id_product');
						$rewards_gift_product->gift_allowed = 1;
						$rewards_gift_product->add();

						$purchase_allowed = Tools::getValue('rewards_purchase_allowed');
						$gift_prices = Tools::getValue('rewards_gift_price');
						foreach(Tools::getValue('rewards_gift_allowed') as $id_product_attribute) {
							$rewards_gift_product_attribute = new RewardsGiftProductAttributeModel();
							$rewards_gift_product_attribute->id_product = (int)Tools::getValue('id_product');
							$rewards_gift_product_attribute->id_product_attribute = (int)$id_product_attribute;
							$rewards_gift_product_attribute->price = (float)$gift_prices[$id_product_attribute];
							if (is_array($purchase_allowed) && in_array($id_product_attribute, $purchase_allowed))
								$rewards_gift_product_attribute->purchase_allowed = 1;
							$rewards_gift_product_attribute->add();
						}
					}
				}
				die(json_encode(array('error' => false, 'msg' => $this->l('Update successful'))));
			default:
				break;
		}
	}

	private function _postValidation($reward_product)
	{
		if (!is_numeric($reward_product->value) || $reward_product->value < 0)
			die(json_encode(array('error' => $this->l('The reward value is invalid.'))));
		else if ((Tools::getValue('reward_product_from') && !Validate::isDate(Tools::getValue('reward_product_from'))) || (Tools::getValue('reward_product_to') && !Validate::isDate(Tools::getValue('reward_product_to'))))
			die(json_encode(array('error' => $this->l('The date is invalid.'))));
		else if (!$reward_product->validateTemplates())
			die(json_encode(array('error' => $this->l('You can\'t have rewards defined for \'All templates\' and for custom templates at the same time.'))));
		else if (!$reward_product->validateDates())
			die(json_encode(array('error' => $this->l('Several rewards on the same period is not allowed.'))));
	}

	private function _postValidation2()
	{
		$gift_prices = Tools::getValue('rewards_gift_price');
		foreach(Tools::getValue('rewards_gift_allowed') as $id_product_attribute) {
			if (!is_numeric($gift_prices[$id_product_attribute]) || $gift_prices[$id_product_attribute] < 0)
				die(json_encode(array('error' => $this->l('The price is not valid.'))));
		}
	}
}