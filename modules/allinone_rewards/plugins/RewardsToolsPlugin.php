<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2020 Yann BONNAILLIE - ByWEB (https://www.prestaplugins.com)
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

require_once(_PS_MODULE_DIR_.'/allinone_rewards/plugins/RewardsGenericPlugin.php');

class RewardsToolsPlugin extends RewardsGenericPlugin
{
	public $name = 'tools';

	public function install()
	{
		return true;
	}

	public function uninstall()
	{
		return true;
	}

	public function isActive()
	{
		return true;
	}

	public function isRewardsAccountVisible()
	{
		return false;
	}

	public function getTitle()
	{
		return $this->l('Tools');
	}

	public function getDetails($reward, $admin) {
		return true;
	}

	protected function postProcess($params=null)
	{
		if (Tools::isSubmit('submitDeleteData')) {
			Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'rewards`');
			Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'rewards_account`');
			Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'rewards_history`');
			Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'rewards_payment`');
			Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'rewards_sponsorship`');
			Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'rewards_sponsorship_code`');
			Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'rewards_sponsorship_detail`');
			Db::getInstance()->execute('TRUNCATE TABLE `'._DB_PREFIX_.'rewards_template_customer`');
			$this->instance->confirmation = $this->instance->displayConfirmation($this->l('Data has been deleted'));
		} else if (Tools::isSubmit('submitMassUpdate') || Tools::isSubmit('submitMassReset')) {
			$this->_postValidation();
			if (!sizeof($this->_errors)) {
				$plugin = Tools::getValue('mass_update_plugin');
				$id_template = $plugin=='loyalty' ? (int)Tools::getValue('mass_update_loyalty_template') : (int)Tools::getValue('mass_update_sponsorship_template');
				$level = $plugin=='loyalty' ? 1 : (int)Tools::getValue('mass_update_sponsorship_level');
				$categories = Tools::getValue('categoryBox');
				$manufacturers = Tools::getValue('mass_update_manufacturers');

				$where = "1=1";
				$where .= is_array($categories) && sizeof($categories) ? ' AND id_category_default IN ('.implode(',', array_map('intval', $categories)).')' : '';
				$where .= is_array($manufacturers) && sizeof($manufacturers) ? ' AND id_manufacturer IN ('.implode(',', array_map('intval', $manufacturers)).')' : '';

				if ((int)$id_template == -1)
					Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_product` WHERE plugin=\''.pSQL($plugin).'\' AND level='.$level.' AND id_product IN (SELECT id_product FROM `'._DB_PREFIX_.'product` WHERE '.$where.')');
				else
					Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_product` WHERE plugin=\''.pSQL($plugin).'\' AND id_template IN (-1, '.$id_template.') AND level='.$level.' AND id_product IN (SELECT id_product FROM `'._DB_PREFIX_.'product` WHERE '.$where.')');
				if (Tools::isSubmit('submitMassUpdate'))
					Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'rewards_product` (id_product, id_template, type, value, plugin, level) SELECT id_product, '.$id_template.', '.(int)Tools::getValue('mass_update_type').', '.(float)Tools::getValue('mass_update_value').', \''.pSQL($plugin).'\', '.$level.' FROM `'._DB_PREFIX_.'product` WHERE '.$where);

				$this->instance->confirmation = $this->instance->displayConfirmation($this->l('Mass action executed.'));
			} else
				$this->instance->errors =  $this->instance->displayError(implode('<br />', $this->_errors));
		}
	}

	private function _postValidation($params=null)
	{
		if (Tools::isSubmit('submitMassUpdate') || Tools::isSubmit('submitMassReset')) {
			if (Tools::isSubmit('submitMassUpdate')) {
				if (!is_numeric(Tools::getValue('mass_update_value')) || Tools::getValue('mass_update_value') < 0)
					$this->_errors[] = $this->l('The reward value is required/invalid.');
			}
			if ((!is_array(Tools::getValue('categoryBox')) || !sizeof(Tools::getValue('categoryBox'))) && (!is_array(Tools::getValue('mass_update_manufacturers')) || !sizeof(Tools::getValue('mass_update_manufacturers'))))
				$this->_errors[] = $this->l('You must choose at least one manufacturer or category of products.');
		}
	}

	public function displayForm() {
		$this->postProcess();

		$this->context->smarty->assign(array(
			'module' => $this->instance,
			'object' => $this,
			'loyalty_templates' => RewardsTemplateModel::getList('loyalty'),
			'sponsorship_templates' => RewardsTemplateModel::getList('sponsorship'),
			'currency' => new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT')),
			'manufacturers_list' => Manufacturer::getManufacturers(false, (int)Configuration::get('PS_LANG_DEFAULT'), true, false, false, false, true),
			'manufacturers' => !empty($this->instance->confirmation) ? array() : Tools::getValue('mass_update_manufacturers', array()),
			'categories' => $this->getCategoriesTree(!empty($this->instance->confirmation) ? array() : Tools::getValue('categoryBox', array())),
		));
		return $this->instance->display($this->instance->path, 'views/templates/admin/admin-tools.tpl');
	}
}