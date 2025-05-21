<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

require_once(_PS_MODULE_DIR_.'/allinone_rewards/plugins/RewardsGenericPlugin.php');

class RewardsCorePlugin extends RewardsGenericPlugin
{
	public $name = 'core';
	private static $_is_loading = false;

	public function checkWarning()
	{
		if (!Configuration::getGlobalValue('REWARDS_REGISTERED'))
			$this->instance->warning .= $this->l('You must register your module license in the 1st tab of the settings, else you won\'t be able to use the module.').' ';
	}

	public function install()
	{
		// hooks
		if (!$this->registerHook('displayHeader') || !$this->registerHook('displayFooter') || !$this->registerHook('displayCustomerAccount') || !$this->registerHook('displayShoppingCartFooter')
			|| !$this->registerHook('displayMyAccountBlock') || (version_compare(_PS_VERSION_, '8.0.0', '<') && !$this->registerHook('displayMyAccountBlockFooter'))
			|| !$this->registerHook('displayProductButtons') || !$this->registerHook('displayProductAdditionalInfo') || (version_compare(_PS_VERSION_, '1.6.1.0', '<=') && !$this->registerHook('displayProductListReviews')) || !$this->registerHook('displayProductPriceBlock')
			|| !$this->registerHook('actionCartSave')
			|| !$this->registerHook('displayAdminCustomers') || !$this->registerHook('displayAdminProductsExtra') || !$this->registerHook('actionAdminControllerSetMedia')
			|| !$this->registerHook('actionObjectCustomerDeleteAfter') || !$this->registerHook('actionObjectProductDeleteAfter') || !$this->registerHook('actionObjectAttributeDeleteAfter'))
			return false;

		// conf
		$idEn = Language::getIdByIso('en');
		$desc = array();
		$rewards_payment_txt = array();
		$reward_virtual_name = array();
		foreach (Language::getLanguages() as $language) {
			$tmp = $this->l('Loyalty reward', $language['id_lang']);
			$desc[$language['id_lang']] = isset($tmp) && !empty($tmp) ? $tmp : $this->l('Loyalty reward', $idEn);
			$tmp = $this->l('rewards_payment_txt', $language['id_lang']);
			$rewards_payment_txt[$language['id_lang']] = isset($tmp) && !empty($tmp) ? $tmp : $this->l('rewards_payment_txt', $idEn);
			$tmp = $this->l('points', $language['id_lang']);
			$reward_virtual_name[$language['id_lang']] = isset($tmp) && !empty($tmp) ? $tmp : $this->l('points', $idEn);
		}

		$groups_off = array(Configuration::get('PS_UNIDENTIFIED_GROUP'), Configuration::get('PS_GUEST_GROUP'));
		$groups_config = '';
		$groups = Group::getGroups((int)Configuration::get('PS_LANG_DEFAULT'));
		foreach ($groups as $group) {
			if (!in_array($group['id_group'], $groups_off))
				$groups_config .= (int)$group['id_group'].',';
		}
		$groups_config = rtrim($groups_config, ',');

		if (!Configuration::updateValue('REWARDS_VIRTUAL', 0)
    	|| !Configuration::updateValue('REWARDS_VIRTUAL_NAME', $reward_virtual_name)
		|| !Configuration::updateValue('REWARDS_GIFT', 0)
		|| !Configuration::updateValue('REWARDS_GIFT_NB_ORDERS', 0)
		|| !Configuration::updateValue('REWARDS_GIFT_SHOW_LINK', 1)
		|| !Configuration::updateValue('REWARDS_GIFT_LIST_BUTTON', 1)
		|| !Configuration::updateValue('REWARDS_GIFT_BUY_BUTTON', 1)
		|| !Configuration::updateValue('REWARDS_GIFT_GROUPS', $groups_config)
		|| !Configuration::updateValue('REWARDS_GIFT_TAX', 1)
		|| !Configuration::updateValue('REWARDS_GIFT_PREFIX', 'GIFT')
		|| !Configuration::updateValue('REWARDS_GIFT_DURATION', 365)
		|| !Configuration::updateValue('REWARDS_GIFT_MINIMAL_TAX', 0)
		|| !Configuration::updateValue('REWARDS_GIFT_MINIMAL_SHIPPING', 0)
		|| !Configuration::updateValue('REWARDS_GIFT_ALL_CATEGORIES', 1)
		|| !Configuration::updateValue('REWARDS_PAYMENT', 0)
		|| !Configuration::updateValue('REWARDS_PAYMENT_NB_ORDERS', 0)
		|| !Configuration::updateValue('REWARDS_PAYMENT_INVOICE',  1)
		|| !Configuration::updateValue('REWARDS_PAYMENT_RATIO',  100)
		|| !Configuration::updateValue('REWARDS_PAYMENT_TXT', $rewards_payment_txt)
		|| !Configuration::updateValue('REWARDS_VOUCHER', 1)
		|| !Configuration::updateValue('REWARDS_VOUCHER_NB_ORDERS', 0)
		|| !Configuration::updateValue('REWARDS_VOUCHER_CART_LINK', 1)
		|| !Configuration::updateValue('REWARDS_VOUCHER_GROUPS', $groups_config)
		|| !Configuration::updateValue('REWARDS_VOUCHER_TYPE', 0)
		|| !Configuration::updateValue('REWARDS_VOUCHER_MAXIMUM', 0)
		|| !Configuration::updateValue('REWARDS_VOUCHER_TAX', 1)
		|| !Configuration::updateValue('REWARDS_VOUCHER_MINIMUM', 0)
		|| !Configuration::updateValue('REWARDS_VOUCHER_MINIMUM_MULTIPLE', 1)
		|| !Configuration::updateValue('REWARDS_MINIMAL_TAX', 0)
		|| !Configuration::updateValue('REWARDS_MINIMAL_SHIPPING', 0)
		|| !Configuration::updateValue('REWARDS_VOUCHER_DETAILS', $desc)
		|| !Configuration::updateValue('REWARDS_VOUCHER_CUMUL_S', 0)
		|| !Configuration::updateValue('REWARDS_VOUCHER_PREFIX', 'FID')
		|| !Configuration::updateValue('REWARDS_VOUCHER_DURATION', 365)
		|| !Configuration::updateValue('REWARDS_VOUCHER_BEHAVIOR', 0)
		|| !Configuration::updateValue('REWARDS_DISPLAY_CART', 1)
		|| !Configuration::updateValue('REWARDS_WAIT_RETURN_PERIOD', 1)
		|| !Configuration::updateValue('REWARDS_USE_CRON', 0)
		|| !Configuration::updateValue('REWARDS_DURATION', 0)
		|| !Configuration::updateGlobalValue('REWARDS_CRON_SECURE_KEY', Tools::strtoupper(Tools::passwdGen(16)))
		|| !Configuration::updateValue('REWARDS_MAILS_IGNORED', '@marketplace.amazon,@alerts-shopping-flux')
		|| !Configuration::updateValue('REWARDS_REMINDER', 0)
		|| !Configuration::updateValue('REWARDS_REMINDER_MINIMUM', 5)
		|| !Configuration::updateValue('REWARDS_REMINDER_FREQUENCY', 30))
			return false;

		foreach ($this->instance->getCurrencies() as $currency) {
			Configuration::updateValue('REWARDS_GIFT_MIN_ORDER_'.(int)($currency['id_currency']), 0);
			Configuration::updateValue('REWARDS_PAYMENT_MIN_VALUE_'.(int)($currency['id_currency']), 0);
			Configuration::updateValue('REWARDS_VOUCHER_MIN_VALUE_'.(int)($currency['id_currency']), 0);
			Configuration::updateValue('REWARDS_VOUCHER_MIN_ORDER_'.(int)($currency['id_currency']), 0);
			Configuration::updateValue('REWARDS_VIRTUAL_VALUE_'.(int)($currency['id_currency']), 0);
		}

		// database
		Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rewards` (
			`id_reward` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_reward_state` INT UNSIGNED NOT NULL DEFAULT 1,
			`id_customer` INT UNSIGNED NOT NULL,
			`id_order` INT UNSIGNED DEFAULT NULL,
			`id_cart_rule` INT UNSIGNED DEFAULT NULL,
			`id_payment` INT UNSIGNED DEFAULT NULL,
			`credits` DECIMAL(20,2) NOT NULL DEFAULT \'0.00\',
			`plugin` VARCHAR(20) NOT NULL DEFAULT \'loyalty\',
			`reason` VARCHAR(80) DEFAULT NULL,
			`date_end` DATETIME DEFAULT \'0000-00-00 00:00:00\',
			`date_add` DATETIME NOT NULL,
			`date_upd` DATETIME NOT NULL,
			PRIMARY KEY (`id_reward`),
			INDEX index_rewards_reward_state (`id_reward_state`),
			INDEX index_rewards_order (`id_order`),
			INDEX index_rewards_cart_rule (`id_cart_rule`),
			INDEX index_rewards_customer (`id_customer`),
			INDEX index_rewards_plugin (`plugin`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rewards_history` (
			`id_reward_history` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_reward` INT UNSIGNED DEFAULT NULL,
			`id_reward_state` INT UNSIGNED NOT NULL DEFAULT 1,
			`credits` DECIMAL(20,2) NOT NULL DEFAULT \'0.00\',
			`date_add` DATETIME NOT NULL,
			PRIMARY KEY (`id_reward_history`),
			INDEX `index_rewards_history_reward` (`id_reward`),
			INDEX `index_rewards_history_reward_state` (`id_reward_state`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rewards_state` (
			`id_reward_state` INT UNSIGNED NOT NULL,
			`id_order_state` TEXT,
			PRIMARY KEY (`id_reward_state`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rewards_state_lang` (
			`id_reward_state` INT UNSIGNED NOT NULL,
			`id_lang` INT UNSIGNED NOT NULL,
			`name` varchar(64) NOT NULL,
			UNIQUE KEY `index_unique_rewards_state_lang` (`id_reward_state`,`id_lang`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rewards_payment` (
			`id_payment` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`credits` DECIMAL(20,2) NOT NULL DEFAULT \'0.00\',
			`detail` TEXT,
			`invoice` VARCHAR(100) DEFAULT NULL,
			`paid` TINYINT(1) NOT NULL DEFAULT \'0\',
			`date_add` DATETIME NOT NULL,
			`date_upd` DATETIME NOT NULL,
			PRIMARY KEY (`id_payment`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rewards_account` (
			`id_customer` INT UNSIGNED NOT NULL,
			`date_last_remind` DATETIME DEFAULT NULL,
			`remind_active` TINYINT(1) NOT NULL DEFAULT \'1\',
			`date_add` DATETIME NOT NULL,
			`date_upd` DATETIME NOT NULL,
			PRIMARY KEY (`id_customer`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rewards_template` (
			`id_template` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`name` varchar(100) NOT NULL,
			`groups` TEXT,
			`plugin` VARCHAR(20) NOT NULL,
			PRIMARY KEY (`id_template`),
			UNIQUE KEY `index_unique_rewards_template` (`name`, `plugin`),
  			INDEX `index_rewards_template_plugin` (`plugin`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rewards_template_config` (
			`id_template_config` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_template` INT UNSIGNED NOT NULL,
			`name` varchar(254) NOT NULL,
			`value` TEXT,
			PRIMARY KEY (`id_template_config`),
			UNIQUE KEY `index_unique_rewards_template_config` (`id_template`, `name`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rewards_template_config_lang` (
			`id_template_config` INT UNSIGNED NOT NULL,
			`id_lang` INT UNSIGNED NOT NULL,
			`value` TEXT,
			PRIMARY KEY (`id_template_config`, `id_lang`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rewards_template_customer` (
			`id_template` INT UNSIGNED NOT NULL,
			`id_customer` INT UNSIGNED NOT NULL,
			PRIMARY KEY (`id_template`, `id_customer`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rewards_product` (
			`id_reward_product` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_product` INT UNSIGNED NOT NULL,
			`id_template` INT NOT NULL DEFAULT \'-1\',
			`type` INT UNSIGNED NOT NULL DEFAULT \'0\',
			`value` DECIMAL(20, 2) UNSIGNED NOT NULL DEFAULT \'0\',
			`date_from` DATETIME,
			`date_to` DATETIME,
			`plugin` varchar(20) NOT NULL DEFAULT \'loyalty\',
			`level` INT UNSIGNED NOT NULL DEFAULT \'1\',
			PRIMARY KEY (`id_reward_product`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rewards_gift_product` (
  			`id_product` INT UNSIGNED NOT NULL,
  			`gift_allowed` INT UNSIGNED NOT NULL DEFAULT \'0\',
  			PRIMARY KEY (`id_product`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');

		Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rewards_gift_product_attribute` (
  			`id_product` INT UNSIGNED NOT NULL,
  			`id_product_attribute` INT UNSIGNED NOT NULL,
  			`purchase_allowed` INT UNSIGNED NOT NULL DEFAULT \'0\',
  			`price` DECIMAL(20,6) UNSIGNED NOT NULL DEFAULT \'0\',
  			PRIMARY KEY (`id_product`, `id_product_attribute`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;');

		if (!RewardsStateModel::insertDefaultData())
			return false;

		return true;
	}

	public function uninstall()
	{
		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'configuration_lang`
			WHERE `id_configuration` IN (SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE `name` LIKE \'REWARDS_%\')');

		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'configuration`
			WHERE `name` LIKE \'REWARDS_%\'');

		return true;
	}

	public function isActive()
	{
		return true;
	}

	public function isRewardsAccountVisible()
	{
		if (Db::getInstance()->getValue('SELECT 1 FROM `'._DB_PREFIX_.'rewards` WHERE id_customer='.(int)$this->context->customer->id))
			return true;

		foreach($this->instance->plugins as $plugin) {
			if (!($plugin instanceof RewardsCorePlugin) && $plugin->isRewardsAccountVisible())
				return true;
		}
		return false;
	}

	public function getTitle()
	{
		return $this->l('Rewards account');
	}

	public function getDetails($reward, $admin)
	{
		return false;
	}

	private function _createFreeGiftProduct() {
		if (!Validate::isLoadedObject(new Product((int)Configuration::getGlobalValue('REWARDS_ID_DEFAULT_GIFT_PRODUCT')))) {
        	$product = new Product();
        	$product->out_of_stock = 2;
        	$product->active = 1;
        	$product->visibility = 'none';
        	$product->is_virtual = 1;
        	$product->id_category_default = $this->context->shop->id_category;
        	foreach (Language::getLanguages(true) as $lang) {
            	$product->link_rewrite[$lang['id_lang']] = 'rewards-free-product';
            	$product->name[$lang['id_lang']] = $this->l('Free product');
        	}
        	$product->add();
        	Configuration::updateGlobalValue('REWARDS_ID_DEFAULT_GIFT_PRODUCT', $product->id);
        	StockAvailable::setQuantity($product->id, 0, 99999);
        	$product->updateCategories(array($this->context->shop->id_category));
		}
	}

	protected function postProcess($params=null)
	{
		$this->instanceDefaultStates();

		// on initialise le template à chaque chargement
		$this->initTemplate();

		if (Tools::isSubmit('submitReward')) {
			$this->_postValidation();
			if (!sizeof($this->_errors)) {
				if (empty($this->id_template)) {
					Configuration::updateValue('REWARDS_USE_CRON', (int)Tools::getValue('rewards_use_cron'));
					if ((int)Tools::getValue('rewards_use_cron') && !Configuration::getGlobalValue('REWARDS_CRON_SECURE_KEY'))
						Configuration::updateGlobalValue('REWARDS_CRON_SECURE_KEY', Tools::strtoupper(Tools::passwdGen(16)));
					Configuration::updateValue('REWARDS_GIFT_GROUPS', implode(',', (array)Tools::getValue('rewards_gift_groups')));
					Configuration::updateValue('REWARDS_VOUCHER_GROUPS', implode(',', (array)Tools::getValue('rewards_voucher_groups')));
					Configuration::updateValue('REWARDS_PAYMENT_GROUPS', implode(',', (array)Tools::getValue('rewards_payment_groups')));
					Configuration::updateValue('REWARDS_WAIT_RETURN_PERIOD', (int)Tools::getValue('wait_order_return'));
					Configuration::updateValue('REWARDS_DURATION', (int)Tools::getValue('rewards_duration'));

					$this->rewardStateValidation->id_order_state = implode(',', Tools::getValue('id_order_state_validation'));
					$this->rewardStateCancel->id_order_state = implode(',', Tools::getValue('id_order_state_cancel'));
					$this->rewardStateValidation->save();
					$this->rewardStateCancel->save();
				}

				MyConf::updateValue('REWARDS_VIRTUAL', (int)Tools::getValue('rewards_virtual'), null, $this->id_template);
				MyConf::updateValue('REWARDS_GIFT', (int)Tools::getValue('rewards_gift'), null, $this->id_template);
				if ((int)Tools::getValue('rewards_gift'))
					$this->_createFreeGiftProduct();

				MyConf::updateValue('REWARDS_GIFT_NB_ORDERS', (int)Tools::getValue('rewards_gift_nb_orders'), null, $this->id_template);
				MyConf::updateValue('REWARDS_GIFT_SHOW_LINK', (int)Tools::getValue('rewards_gift_show_link'), null, $this->id_template);
				MyConf::updateValue('REWARDS_GIFT_LIST_BUTTON', (int)Tools::getValue('rewards_gift_list_button'), null, $this->id_template);
				if (version_compare(_PS_VERSION_, '1.6', '>=') && (int)MyConf::get('REWARDS_GIFT_BUY_BUTTON', null, $this->id_template) != (int)Tools::getValue('rewards_gift_buy_button'))
					Tools::clearSmartyCache();
				MyConf::updateValue('REWARDS_GIFT_BUY_BUTTON', (int)Tools::getValue('rewards_gift_buy_button'), null, $this->id_template);
				MyConf::updateValue('REWARDS_GIFT_TAX', (int)Tools::getValue('rewards_gift_tax'), null, $this->id_template);
				MyConf::updateValue('REWARDS_GIFT_PREFIX', Tools::getValue('rewards_gift_prefix'), null, $this->id_template);
				MyConf::updateValue('REWARDS_GIFT_DURATION', (int)Tools::getValue('rewards_gift_duration'), null, $this->id_template);
				MyConf::updateValue('REWARDS_GIFT_MINIMAL_TAX', Tools::getValue('rewards_gift_min_order_include_tax'), null, $this->id_template);
				MyConf::updateValue('REWARDS_GIFT_MINIMAL_SHIPPING', Tools::getValue('rewards_gift_min_order_include_shipping'), null, $this->id_template);
				MyConf::updateValue('REWARDS_GIFT_ALL_CATEGORIES', (int)Tools::getValue('rewards_gift_all_categories'), null, $this->id_template);
				MyConf::updateValue('REWARDS_GIFT_CATEGORIES', Tools::getValue('categoryBox') ? implode(',', Tools::getValue('categoryBox')) : '', null, $this->id_template);
				MyConf::updateValue('REWARDS_PAYMENT', (int)Tools::getValue('rewards_payment'), null, $this->id_template);
				MyConf::updateValue('REWARDS_PAYMENT_NB_ORDERS', (int)Tools::getValue('rewards_payment_nb_orders'), null, $this->id_template);
				MyConf::updateValue('REWARDS_PAYMENT_INVOICE',  (int)Tools::getValue('rewards_payment_invoice'), null, $this->id_template);
				MyConf::updateValue('REWARDS_PAYMENT_RATIO', (float)Tools::getValue('rewards_payment_ratio'), null, $this->id_template);
				MyConf::updateValue('REWARDS_VOUCHER', (int)Tools::getValue('rewards_voucher'), null, $this->id_template);
				MyConf::updateValue('REWARDS_VOUCHER_NB_ORDERS', (int)Tools::getValue('rewards_voucher_nb_orders'), null, $this->id_template);
				MyConf::updateValue('REWARDS_VOUCHER_CART_LINK', (int)Tools::getValue('rewards_voucher_cart_link'), null, $this->id_template);
				MyConf::updateValue('REWARDS_VOUCHER_PREFIX', Tools::getValue('voucher_prefix'), null, $this->id_template);
				MyConf::updateValue('REWARDS_VOUCHER_DURATION', (int)Tools::getValue('voucher_duration'), null, $this->id_template);
				MyConf::updateValue('REWARDS_VOUCHER_MINIMUM', (int)Tools::getValue('rewards_voucher_minimum'), null, $this->id_template);
				MyConf::updateValue('REWARDS_VOUCHER_MINIMUM_MULTIPLE', (float)Tools::getValue('rewards_voucher_minimum_multiple'), null, $this->id_template);
				MyConf::updateValue('REWARDS_VOUCHER_TYPE', (int)Tools::getValue('rewards_voucher_type'), null, $this->id_template);
				MyConf::updateValue('REWARDS_VOUCHER_MAXIMUM', (float)Tools::getValue('rewards_voucher_maximum'), null, $this->id_template);
				MyConf::updateValue('REWARDS_VOUCHER_LIST_VALUES', Tools::getValue('rewards_voucher_list_values'), null, $this->id_template);
				MyConf::updateValue('REWARDS_VOUCHER_TAX', (int)Tools::getValue('voucher_tax'), null, $this->id_template);
				MyConf::updateValue('REWARDS_DISPLAY_CART', (int)Tools::getValue('display_cart'), null, $this->id_template);
				MyConf::updateValue('REWARDS_VOUCHER_CUMUL_S', (int)Tools::getValue('cumulative_voucher_s'), null, $this->id_template);
				MyConf::updateValue('REWARDS_MINIMAL_TAX', Tools::getValue('include_tax'), null, $this->id_template);
				MyConf::updateValue('REWARDS_MINIMAL_SHIPPING', Tools::getValue('include_shipping'), null, $this->id_template);
				MyConf::updateValue('REWARDS_VOUCHER_BEHAVIOR', (int)Tools::getValue('voucher_behavior'), null, $this->id_template);

				$arrayVirtualName = array();
				$arrayVoucherDetails = array();
				$languages = Language::getLanguages();
				foreach ($languages as $language) {
					$arrayVoucherDetails[(int)($language['id_lang'])] = Tools::getValue('voucher_details_'.(int)($language['id_lang']));
					$arrayVirtualName[(int)($language['id_lang'])] = Tools::getValue('rewards_virtual_name_'.(int)($language['id_lang']));
				}
				MyConf::updateValue('REWARDS_VOUCHER_DETAILS', $arrayVoucherDetails, null, $this->id_template);
				MyConf::updateValue('REWARDS_VIRTUAL_NAME', $arrayVirtualName, null, $this->id_template);

				$currencies = $this->instance->getCurrencies();
				foreach ($currencies as $currency) {
					MyConf::updateValue('REWARDS_VIRTUAL_VALUE_'.$currency['id_currency'], (float)Tools::getValue('rewards_virtual_value_'.$currency['id_currency']), null, $this->id_template);
					MyConf::updateValue('REWARDS_GIFT_MIN_ORDER_'.$currency['id_currency'], (float)Tools::getValue('rewards_gift_min_order_'.$currency['id_currency']), null, $this->id_template);
					MyConf::updateValue('REWARDS_VOUCHER_MIN_VALUE_'.$currency['id_currency'], (float)Tools::getValue('rewards_voucher_min_value_'.$currency['id_currency']), null, $this->id_template);
					MyConf::updateValue('REWARDS_PAYMENT_MIN_VALUE_'.$currency['id_currency'], (float)Tools::getValue('rewards_payment_min_value_'.$currency['id_currency']), null, $this->id_template);
					MyConf::updateValue('REWARDS_VOUCHER_MIN_ORDER_'.$currency['id_currency'], (float)Tools::getValue('rewards_voucher_min_order_'.$currency['id_currency']), null, $this->id_template);
				}
				$this->instance->confirmation = $this->instance->displayConfirmation($this->l('Settings updated.'));
			} else
				$this->instance->errors = $this->instance->displayError(implode('<br />', $this->_errors));
		} else if (Tools::isSubmit('submitRewardsNotifications')) {
			$this->_postValidation();
			if (!sizeof($this->_errors)) {
				Configuration::updateValue('REWARDS_MAILS_IGNORED', Tools::getValue('rewards_mails_ignored'));
				Configuration::updateValue('REWARDS_REMINDER', (int)Tools::getValue('rewards_reminder'));
				Configuration::updateValue('REWARDS_REMINDER_MINIMUM', (float)Tools::getValue('rewards_reminder_minimum'));
				Configuration::updateValue('REWARDS_REMINDER_FREQUENCY', (int)Tools::getValue('rewards_reminder_frequency'));
				$this->instance->confirmation = $this->instance->displayConfirmation($this->l('Settings updated.'));
			} else
				$this->instance->errors = $this->instance->displayError(implode('<br />', $this->_errors));
		} else if (Tools::isSubmit('submitRewardText')) {
			$this->_postValidation();

			if (!sizeof($this->_errors)) {
				if (empty($this->id_template)) {
					foreach (Language::getLanguages() as $language) {
						$this->rewardStateDefault->name[(int)($language['id_lang'])] = Tools::getValue('default_reward_state_'.(int)($language['id_lang']));
						$this->rewardStateValidation->name[(int)($language['id_lang'])] = Tools::getValue('validation_reward_state_'.(int)($language['id_lang']));
						$this->rewardStateCancel->name[(int)($language['id_lang'])] = Tools::getValue('cancel_reward_state_'.(int)($language['id_lang']));
						$this->rewardStateConvert->name[(int)($language['id_lang'])] = Tools::getValue('convert_reward_state_'.(int)($language['id_lang']));
						$this->rewardStateReturnPeriod->name[(int)($language['id_lang'])] = Tools::getValue('return_period_reward_state_'.(int)($language['id_lang']));
						$this->rewardStateWaitingPayment->name[(int)($language['id_lang'])] = Tools::getValue('waiting_payment_reward_state_'.(int)($language['id_lang']));
						$this->rewardStatePaid->name[(int)($language['id_lang'])] = Tools::getValue('paid_reward_state_'.(int)($language['id_lang']));
					}
					$this->rewardStateDefault->save();
					$this->rewardStateValidation->save();
					$this->rewardStateCancel->save();
					$this->rewardStateConvert->save();
					$this->rewardStateReturnPeriod->save();
					$this->rewardStateWaitingPayment->save();
					$this->rewardStatePaid->save();
				}

				MyConf::updateValue('REWARDS_GENERAL_TXT', Tools::getValue('rewards_general_txt'), true, $this->id_template);
				MyConf::updateValue('REWARDS_PAYMENT_TXT', Tools::getValue('rewards_payment_txt'), true, $this->id_template);
				$this->instance->confirmation = $this->instance->displayConfirmation($this->l('Settings updated.'));
			} else
				$this->instance->errors = $this->instance->displayError(implode('<br />', $this->_errors));
		} else if (Tools::getValue('accept_payment')) {
			RewardsPaymentModel::acceptPayment((int)Tools::getValue('accept_payment'));
		} else if (Tools::isSubmit('submitRewardReminder')) {
			RewardsAccountModel::sendReminder((int)$params['id_customer']);
		} else if (Tools::isSubmit('submitRewardReminderOff')) {
			Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'rewards_account` SET remind_active=0 WHERE id_customer='.(int)$params['id_customer']);
			$rewards_account = new RewardsAccountModel((int)$params['id_customer']);
			if (!Validate::isLoadedObject($rewards_account)) {
				$rewards_account->id_customer = (int)$params['id_customer'];
				$rewards_account->remind_active = 0;
				$rewards_account->save();
			}
		} else if (Tools::isSubmit('submitRewardReminderOn')) {
			Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'rewards_account` SET remind_active=1 WHERE id_customer='.(int)$params['id_customer']);
			$rewards_account = new RewardsAccountModel((int)$params['id_customer']);
			if (!Validate::isLoadedObject($rewards_account)) {
				$rewards_account->id_customer = (int)$params['id_customer'];
				$rewards_account->remind_active = 1;
				$rewards_account->save();
			}
		} else if (Tools::isSubmit('submitRewardUpdate')) {
			// manage rewards update
			$this->_postValidation();
			if (!sizeof($this->_errors)) {
				$reward = new RewardsModel((int)Tools::getValue('id_reward_to_update'));
				$reward->credits = (float)Tools::getValue('reward_value_' . Tools::getValue('id_reward_to_update'));
				$reward->date_end = Tools::getValue('reward_date_end_' . Tools::getValue('id_reward_to_update'));
				if ($reward->date_end && $reward->date_end < date('Y-m-d h:i:s'))
					$reward->id_reward_state = RewardsStateModel::getCancelId();
				else
					$reward->id_reward_state = (int)Tools::getValue('reward_state_' . Tools::getValue('id_reward_to_update'));
				if ($reward->plugin=="free")
					$reward->reason = Tools::getValue('reward_reason_' . Tools::getValue('id_reward_to_update'));
				$reward->save();
				return $this->instance->displayConfirmation($this->l('The reward has been updated.'));
			} else
				return $this->instance->displayError(implode('<br />', $this->_errors));
		} else if (Tools::isSubmit('submitNewReward')) {
			$this->_postValidation();
			if (!sizeof($this->_errors)) {
				$reward = new RewardsModel();
				$reward->id_customer = (int)$params['id_customer'];
				$reward->credits = (float)Tools::getValue('new_reward_value');
				$reward->date_end = Tools::getValue('new_reward_date_end');
				if ($reward->date_end && $reward->date_end < date('Y-m-d h:i:s'))
					$reward->id_reward_state = RewardsStateModel::getCancelId();
				else
					$reward->id_reward_state = (int)Tools::getValue('new_reward_state');
				$reward->plugin = 'free';
				$reward->reason = Tools::getValue('new_reward_reason');
				$reward->save();
				$_POST['new_reward_value'] = $_POST['new_reward_reason'] = $_POST['new_reward_state'] = $_POST['new_reward_date_end'] = null;
				return $this->instance->displayConfirmation($this->l('The new reward has been created.'));
			} else
				return $this->instance->displayError(implode('<br />', $this->_errors));
		} else if (Tools::isSubmit('submitConvertReward')) {
			$this->_postValidation();
			if (!sizeof($this->_errors)) {
				$cart_rule = RewardsModel::createDiscount((float)Tools::getValue('convert_reward_value'));
				$_POST['convert_reward_value'] = null;
				return $this->instance->displayConfirmation(sprintf($this->l('A new voucher has been generated, its code is : %s'), $cart_rule->code));
			} else
				return $this->instance->displayError(implode('<br />', $this->_errors));
		} else if (Tools::getValue('action') == 'core_template' || Tools::getValue('action') == 'loyalty_template') {
			$id_new_template = Tools::getValue(Tools::getValue('action'));
			$id_old_template = (int)MyConf::getIdTemplate(Tools::getValue('action') == 'core_template' ? 'core' : 'loyalty', (int)$params['id_customer']);
			RewardsTemplateModel::deleteCustomer($id_old_template, (int)$params['id_customer']);
			if ($id_new_template)
				RewardsTemplateModel::addCustomer((int)$id_new_template, (int)$params['id_customer']);
			return $this->instance->displayConfirmation($this->l('The template has been updated.'));
		}
	}

	private function _postValidation()
	{
		$languages = Language::getLanguages();
		if (Tools::isSubmit('submitReward')) {
			$currencies = $this->instance->getCurrencies();

			if (empty($this->id_template)) {
				$states_valid = Tools::getValue('id_order_state_validation');
				$states_cancel = Tools::getValue('id_order_state_cancel');
				if (!is_array($states_valid) || !sizeof($states_valid))
					$this->_errors[] = $this->l('You must choose the states when reward is awarded');
				if (!is_array($states_cancel) || !sizeof($states_cancel))
					$this->_errors[] = $this->l('You must choose the states when reward is cancelled');
				if (is_array($states_valid) && is_array($states_cancel) && count(array_intersect($states_valid, $states_cancel)) > 0)
					$this->_errors[] = $this->l('You can\'t choose the same state(s) for validation and cancellation');
				if (!is_numeric(Tools::getValue('rewards_duration')) || Tools::getValue('rewards_duration') < 0)
					$this->_errors[] = $this->l('The validity of the rewards is required/invalid.');
				if (Tools::getValue('rewards_gift') && !is_array(Tools::getValue('rewards_gift_groups')))
					$this->_errors[] = $this->l('Please select at least 1 customer group allowed to pick gift products');
				if (Tools::getValue('rewards_voucher') && !is_array(Tools::getValue('rewards_voucher_groups')))
					$this->_errors[] = $this->l('Please select at least 1 customer group allowed to transform rewards into vouchers');
				if (Tools::getValue('rewards_payment') && !is_array(Tools::getValue('rewards_payment_groups')))
					$this->_errors[] = $this->l('Please select at least 1 customer group allowed to ask for payment');
			}
			if (!Tools::getValue('rewards_gift') && !Tools::getValue('rewards_payment') && !Tools::getValue('rewards_voucher'))
				$this->_errors[] = $this->l('Please select at least 1 way to use the rewards');

			if (Tools::getValue('rewards_virtual')) {
				foreach ($currencies as $currency)
					if (!Tools::getValue('rewards_virtual_value_'.$currency['id_currency']) || !Validate::isUnsignedFloat(Tools::getValue('rewards_virtual_value_'.$currency['id_currency'])))
						$this->_errors[] = $this->l('The value of the virtual points is required/invalid for the currency').' '.$currency['name'];
				foreach ($languages as $language)
					if (Tools::getValue('rewards_virtual_name_'.(int)($language['id_lang'])) == '')
						$this->_errors[] = $this->l('Name of the virtual points is required for').' '.$language['name'];
			}
			if (Tools::getValue('rewards_gift')) {
				if (Tools::getValue('rewards_gift_prefix') == '' || !Validate::isCleanHtml(Tools::getValue('rewards_gift_prefix')))
					$this->_errors[] = $this->l('Prefix for the voucher code is required/invalid.');
				if (!is_numeric(Tools::getValue('rewards_gift_duration')) || Tools::getValue('rewards_gift_duration') <= 0)
					$this->_errors[] = $this->l('The validity of the voucher is required/invalid.');
				foreach ($currencies as $currency) {
					if (Tools::getValue('rewards_gift_min_order_'.$currency['id_currency'])!='' && !Validate::isUnsignedFloat(Tools::getValue('rewards_gift_min_order_'.$currency['id_currency'])))
						$this->_errors[] = $this->l('Minimum amount of the order to be able to use the voucher in the currency').' '.$currency['name'].' '.$this->l('is invalid.');
				}
				if (!Tools::getValue('rewards_gift_all_categories') && (!is_array(Tools::getValue('categoryBox')) || !sizeof(Tools::getValue('categoryBox'))))
					$this->_errors[] = $this->l('You must choose at least one category for gift products');
				if (!Tools::getValue('rewards_gift_buy_button') && Tools::getValue('rewards_gift_all_categories')==1)
					$this->_errors[] = $this->l('You can\'t choose all categories and that gift products can\'t be bought normally at the same time, else your customers won\'t be able to buy anything.');
			}
			if (Tools::getValue('rewards_payment')) {
				if (Tools::getValue('rewards_payment_nb_orders') && !Validate::isUnsignedFloat(Tools::getValue('rewards_payment_nb_orders')))
					$this->_errors[] = $this->l('Number of orders to make is required.');
				if (Tools::getValue('rewards_payment') && (!Tools::getValue('rewards_payment_ratio') || !Validate::isUnsignedFloat(Tools::getValue('rewards_payment_ratio')) || (float)Tools::getValue('rewards_payment_ratio') > 100 || (float)Tools::getValue('rewards_payment_ratio') < 1))
					$this->_errors[] = $this->l('The convertion rate must be a number between 1 and 100');
				foreach ($currencies as $currency)
					if (Tools::getValue('rewards_payment_min_value_'.$currency['id_currency'])!='' && !Validate::isUnsignedFloat(Tools::getValue('rewards_payment_min_value_'.$currency['id_currency'])))
						$this->_errors[] = $this->l('Minimum required in account for payment and the currency').' '.$currency['name'].' '.$this->l('is invalid.');
			}
			if (Tools::getValue('rewards_voucher')) {
				if (Tools::getValue('rewards_voucher_nb_orders') && !Validate::isUnsignedFloat(Tools::getValue('rewards_voucher_nb_orders')))
					$this->_errors[] = $this->l('Number of orders to make is invalid.');
				foreach ($currencies as $currency) {
					if (Tools::getValue('rewards_voucher_min_value_'.$currency['id_currency'])!='' && !Validate::isUnsignedFloat(Tools::getValue('rewards_voucher_min_value_'.$currency['id_currency'])))
						$this->_errors[] = $this->l('Minimum required in account for transformation and the currency').' '.$currency['name'].' '.$this->l('is invalid.');
				}
				if (Tools::getValue('rewards_voucher_maximum')!='' && !Validate::isUnsignedFloat(Tools::getValue('rewards_voucher_maximum')))
					$this->_errors[] = $this->l('The maximum amount is invalid.');
				if (Tools::getValue('rewards_voucher_type')==2) {
 					$list_values = explode(';', Tools::getValue('rewards_voucher_list_values'));
 					$found = array();
 					foreach($list_values as $value) {
 						if (!is_numeric($value) || $value <= 0 || isset($found[$value])) {
 							$this->_errors[] = $this->l('The list of predefined values is invalid.');
 							break;
 						}
 						$found[$value] = true;
 					}
				}
				if (Tools::getValue('rewards_voucher_minimum')==1) {
					foreach ($currencies as $currency) {
						if (!is_numeric(Tools::getValue('rewards_voucher_min_order_'.$currency['id_currency'])) || Tools::getValue('rewards_voucher_min_order_'.$currency['id_currency']) <= 0)
							$this->_errors[] = $this->l('Minimum amount of the order to be able to use the voucher in the currency').' '.$currency['name'].' '.$this->l('is invalid.');
					}
				} else if (Tools::getValue('rewards_voucher_minimum')==2) {
					if (!is_numeric(Tools::getValue('rewards_voucher_minimum_multiple')) || Tools::getValue('rewards_voucher_minimum_multiple') <= 0)
						$this->_errors[] = $this->l('Multiplier of the voucher amount is required/invalid.');
				}
				foreach ($languages as $language)
					if (Tools::getValue('voucher_details_'.(int)($language['id_lang'])) == '')
						$this->_errors[] = $this->l('Voucher description is required for').' '.$language['name'];
				if (Tools::getValue('voucher_prefix') == '' || !Validate::isCleanHtml(Tools::getValue('voucher_prefix')))
					$this->_errors[] = $this->l('Prefix for the voucher code is required/invalid.');
				if (!is_numeric(Tools::getValue('voucher_duration')) || Tools::getValue('voucher_duration') <= 0)
					$this->_errors[] = $this->l('The validity of the voucher is required/invalid.');
			}
		} else if (Tools::isSubmit('submitRewardsNotifications') && (int)Tools::getValue('rewards_reminder') == 1) {
			if (Tools::getValue('rewards_reminder_minimum') && !Validate::isUnsignedFloat(Tools::getValue('rewards_reminder_minimum')))
				$this->_errors[] = $this->l('Minimum required in account to receive a mail is required/invalid.');
			if (!is_numeric(Tools::getValue('rewards_reminder_frequency')) || Tools::getValue('rewards_reminder_frequency') <= 0)
				$this->_errors[] = $this->l('The frequency of the emails is required/invalid.');
		} else if (Tools::isSubmit('submitRewardText')) {
			foreach ($languages as $language) {
				if (Tools::getValue('default_reward_state_'.(int)($language['id_lang'])) == '')
					$this->_errors[] = $this->l('Label is required for Initial state in').' '.$language['name'];
				if (Tools::getValue('validation_reward_state_'.(int)($language['id_lang'])) == '')
					$this->_errors[] = $this->l('Label is required for validation state in').' '.$language['name'];
				if (Tools::getValue('cancel_reward_state_'.(int)($language['id_lang'])) == '')
					$this->_errors[] = $this->l('Label is required for cancellation state in').' '.$language['name'];
				if (Tools::getValue('convert_reward_state_'.(int)($language['id_lang'])) == '')
					$this->_errors[] = $this->l('Label is required for converted state in').' '.$language['name'];
				if (Tools::getValue('return_period_reward_state_'.(int)($language['id_lang'])) == '')
					$this->_errors[] = $this->l('Label is required for Return period not exceeded state in').' '.$language['name'];
				if (Tools::getValue('waiting_payment_reward_state_'.(int)($language['id_lang'])) == '')
					$this->_errors[] = $this->l('Label is required for Waiting for payment state in').' '.$language['name'];
				if (Tools::getValue('paid_reward_state_'.(int)($language['id_lang'])) == '')
					$this->_errors[] = $this->l('Label is required for Paid state in').' '.$language['name'];
			}
		} else if (Tools::isSubmit('submitRewardUpdate') && (int)Tools::getValue('id_reward_to_update') != 0) {
			if (!Validate::isUnsignedFloat(Tools::getValue('reward_value_'.Tools::getValue('id_reward_to_update'))) || (float)Tools::getValue('reward_value_'.Tools::getValue('id_reward_to_update')) == 0)
			 	$this->_errors[] = $this->l('The value of the reward is required/invalid.');
			if (Tools::getValue('reward_reason_' . Tools::getValue('id_reward_to_update'))==='')
			 	$this->_errors[] = $this->l('The reason of the reward is required/invalid.');
			if (Tools::getValue('reward_date_end_' . Tools::getValue('id_reward_to_update')) && !Validate::isDate(Tools::getValue('reward_date_end_'.Tools::getValue('id_reward_to_update'))))
				$this->_errors[] = $this->l('The date of validity is invalid.');
		} else if (Tools::isSubmit('submitNewReward')) {
			if (!Validate::isUnsignedFloat(Tools::getValue('new_reward_value')) || (float)Tools::getValue('new_reward_value') == 0)
			 	$this->_errors[] = $this->l('The value of the reward is required/invalid.');
			if (Tools::getValue('new_reward_reason') == '')
			 	$this->_errors[] = $this->l('The reason of the reward is required/invalid.');
			if (Tools::getValue('new_reward_date_end') && !Validate::isDate(Tools::getValue('new_reward_date_end')))
			 	$this->_errors[] = $this->l('The date of validity is invalid.');
		} else if (Tools::isSubmit('submitConvertReward')) {
			if (!Validate::isUnsignedFloat(Tools::getValue('convert_reward_value')) || (float)Tools::getValue('convert_reward_value') == 0)
				$this->_errors[] = $this->l('The value of the reward is required/invalid.');
			$totals = RewardsModel::getAllTotalsByCustomer((int)$this->context->customer->id);
			$total_available = isset($totals[RewardsStateModel::getValidationId()]) ? (float)$totals[RewardsStateModel::getValidationId()] : 0;
			if ((float)Tools::getValue('convert_reward_value') > $total_available)
				$this->_errors[] = $this->l('The amount is higher than the total available in the rewards account.');
		}
	}

	public function displayForm()
	{
		if (Tools::getValue('stats'))
			return $this->_getStatistics();
		else if (Tools::getValue('payments'))
			return $this->_getPayments();

		$this->postProcess();

		if ((int)Tools::getValue('rewards_gift', MyConf::get('REWARDS_GIFT', null, $this->id_template)))
			$this->_createFreeGiftProduct();

		$currencies = $this->instance->getCurrencies();
		$rewards_virtual_value = array();
		$rewards_gift_min_order = array();
		$rewards_voucher_min_value = array();
		$rewards_voucher_min_order = array();
		$rewards_payment_min_value = array();
		foreach($currencies as $currency) {
			$rewards_virtual_value[$currency['id_currency']] = (float)Tools::getValue('rewards_virtual_value_'.$currency['id_currency'], (float)MyConf::get('REWARDS_VIRTUAL_VALUE_'.$currency['id_currency'], null, $this->id_template));
			$rewards_gift_min_order[$currency['id_currency']] = (float)Tools::getValue('rewards_gift_min_order_'.$currency['id_currency'], (float)MyConf::get('REWARDS_GIFT_MIN_ORDER_'.$currency['id_currency'], null, $this->id_template));
			$rewards_voucher_min_value[$currency['id_currency']] = (float)Tools::getValue('rewards_voucher_min_value_'.$currency['id_currency'], MyConf::get('REWARDS_VOUCHER_MIN_VALUE_'.$currency['id_currency'], null, $this->id_template));
			$rewards_voucher_min_order[$currency['id_currency']] = (float)Tools::getValue('rewards_voucher_min_order_'.$currency['id_currency'], MyConf::get('REWARDS_VOUCHER_MIN_ORDER_'.$currency['id_currency'], null, $this->id_template));
			$rewards_payment_min_value[$currency['id_currency']] = (float)Tools::getValue('rewards_payment_min_value_'.$currency['id_currency'], MyConf::get('REWARDS_PAYMENT_MIN_VALUE_'.$currency['id_currency'], null, $this->id_template));
		}

		$languages = Language::getLanguages();
		$rewards_virtual_name = array();
		$voucher_details = array();
		$rewards_general_txt = Tools::getValue('rewards_general_txt', array());
		$rewards_payment_txt = Tools::getValue('rewards_payment_txt', array());
		$status = array($this->rewardStateDefault, $this->rewardStateConvert, $this->rewardStateValidation, $this->rewardStateReturnPeriod, $this->rewardStateCancel, $this->rewardStateWaitingPayment, $this->rewardStatePaid);
		foreach($languages as $language) {
			$rewards_virtual_name[$language['id_lang']] = Tools::getValue('rewards_virtual_name_'.$language['id_lang'], MyConf::get('REWARDS_VIRTUAL_NAME', $language['id_lang'], $this->id_template));
			$voucher_details[$language['id_lang']] = Tools::getValue('voucher_details_'.$language['id_lang'], MyConf::get('REWARDS_VOUCHER_DETAILS', $language['id_lang'], $this->id_template));
			if (!isset($rewards_general_txt[$language['id_lang']]))
				$rewards_general_txt[$language['id_lang']] = MyConf::get('REWARDS_GENERAL_TXT', $language['id_lang'], $this->id_template);
			if (!isset($rewards_payment_txt[$language['id_lang']]))
				$rewards_payment_txt[$language['id_lang']] = MyConf::get('REWARDS_PAYMENT_TXT', $language['id_lang'], $this->id_template);
			foreach($status as $tmpstatus) {
				if (!isset($tmpstatus->name[$language['id_lang']]))
					$tmpstatus->name[$language['id_lang']] = $tmpstatus->name[(int)Configuration::get('PS_LANG_DEFAULT')];
			}
		}

		$this->context->smarty->assign(array(
			'module' => $this->instance,
			'object' => $this,
			'currencies' => $currencies,
			'currency' => $this->context->currency,
			'languages' => $languages,
			'current_language_id' => $this->context->language->id,
			'order_states' => OrderState::getOrderStates((int)$this->context->language->id),
			'groups' => Group::getGroups($this->context->language->id),
			'groups_off' => array(Configuration::get('PS_UNIDENTIFIED_GROUP'), Configuration::get('PS_GUEST_GROUP')),
			'rewards_virtual' => (int)Tools::getValue('rewards_virtual', MyConf::get('REWARDS_VIRTUAL', null, $this->id_template)),
			'rewards_virtual_value' => $rewards_virtual_value,
			'rewards_virtual_name' => $rewards_virtual_name,
			'wait_return_period' => (int)Tools::getValue('wait_return_period', Configuration::get('REWARDS_WAIT_RETURN_PERIOD')),
			'ps_order_return' => (int)Configuration::get('PS_ORDER_RETURN'),
			'ps_order_return_nb_days' => (int)Configuration::get('PS_ORDER_RETURN_NB_DAYS'),
			'rewards_gift' => (int)Tools::getValue('rewards_gift', MyConf::get('REWARDS_GIFT', null, $this->id_template)),
			'rewards_voucher' => (int)Tools::getValue('rewards_voucher', MyConf::get('REWARDS_VOUCHER', null, $this->id_template)),
			'rewards_payment' => (int)Tools::getValue('rewards_payment', MyConf::get('REWARDS_PAYMENT', null, $this->id_template)),
			'rewards_duration' => (int)Tools::getValue('rewards_duration', Configuration::get('REWARDS_DURATION')),
			'rewards_use_cron' => (int)Tools::getValue('rewards_use_cron', Configuration::get('REWARDS_USE_CRON')),
			'rewards_cron_link' => $this->context->link->getModuleLink('allinone_rewards', 'cron', array('secure_key' => Configuration::getGlobalValue('REWARDS_CRON_SECURE_KEY')), true),
			'rewards_gift_nb_orders' => (int)Tools::getValue('rewards_gift_nb_orders', MyConf::get('REWARDS_GIFT_NB_ORDERS', null, $this->id_template)),
			'rewards_id_default_gift_product' => (int)Configuration::getGlobalValue('REWARDS_ID_DEFAULT_GIFT_PRODUCT'),
			'rewards_gift_groups' => Tools::getValue('rewards_gift_groups', explode(',', Configuration::get('REWARDS_GIFT_GROUPS'))),
			'rewards_gift_show_link' => (int)Tools::getValue('rewards_gift_show_link', MyConf::get('REWARDS_GIFT_SHOW_LINK', null, $this->id_template)),
			'rewards_gift_list_button' => (int)Tools::getValue('rewards_gift_list_button', MyConf::get('REWARDS_GIFT_LIST_BUTTON', null, $this->id_template)),
			'rewards_gift_buy_button' => (int)Tools::getValue('rewards_gift_buy_button', MyConf::get('REWARDS_GIFT_BUY_BUTTON', null, $this->id_template)),
			'rewards_gift_tax' => (int)Tools::getValue('rewards_gift_tax', MyConf::get('REWARDS_GIFT_TAX', null, $this->id_template)),
			'rewards_gift_prefix' => Tools::getValue('rewards_gift_prefix', MyConf::get('REWARDS_GIFT_PREFIX', null, $this->id_template)),
			'rewards_gift_duration' => Tools::getValue('rewards_gift_duration', MyConf::get('REWARDS_GIFT_DURATION', null, $this->id_template)),
			'rewards_gift_min_order' => $rewards_gift_min_order,
			'rewards_gift_min_order_include_tax' => (int)Tools::getValue('rewards_gift_min_order_include_tax', MyConf::get('REWARDS_GIFT_MINIMAL_TAX', null, $this->id_template)),
			'rewards_gift_min_order_include_shipping' => (int)Tools::getValue('rewards_gift_min_order_include_shipping', MyConf::get('REWARDS_GIFT_MINIMAL_SHIPPING', null, $this->id_template)),
			'rewards_gift_all_categories' => (int)Tools::getValue('rewards_gift_all_categories', MyConf::get('REWARDS_GIFT_ALL_CATEGORIES', null, $this->id_template)),
			'categories' => $this->getCategoriesTree(Tools::getValue('categoryBox', explode(',', MyConf::get('REWARDS_GIFT_CATEGORIES', null, $this->id_template)))),
			'rewards_voucher_nb_orders' => (int)Tools::getValue('rewards_voucher_nb_orders', MyConf::get('REWARDS_VOUCHER_NB_ORDERS', null, $this->id_template)),
			'rewards_voucher_groups' => Tools::getValue('rewards_voucher_groups', explode(',', Configuration::get('REWARDS_VOUCHER_GROUPS'))),
			'rewards_voucher_cart_link' => (int)Tools::getValue('rewards_voucher_cart_link', MyConf::get('REWARDS_VOUCHER_CART_LINK', null, $this->id_template)),
			'rewards_voucher_min_value' => $rewards_voucher_min_value,
			'rewards_voucher_type' => (int)Tools::getValue('rewards_voucher_type', MyConf::get('REWARDS_VOUCHER_TYPE', null, $this->id_template)),
			'rewards_voucher_maximum' => (float)Tools::getValue('rewards_voucher_maximum', MyConf::get('REWARDS_VOUCHER_MAXIMUM', null, $this->id_template)),
			'rewards_voucher_list_values' => Tools::getValue('rewards_voucher_list_values', MyConf::get('REWARDS_VOUCHER_LIST_VALUES', null, $this->id_template)),
			'voucher_tax' => Tools::getValue('voucher_tax', MyConf::get('REWARDS_VOUCHER_TAX', null, $this->id_template)),
			'voucher_prefix' => Tools::getValue('voucher_prefix', MyConf::get('REWARDS_VOUCHER_PREFIX', null, $this->id_template)),
			'voucher_duration' => (int)Tools::getValue('voucher_duration', MyConf::get('REWARDS_VOUCHER_DURATION', null, $this->id_template)),
			'voucher_details' => $voucher_details,
			'display_cart' => (int)Tools::getValue('display_cart', MyConf::get('REWARDS_DISPLAY_CART', null, $this->id_template)),
			'cumulative_voucher_s' => (int)Tools::getValue('cumulative_voucher_s', MyConf::get('REWARDS_VOUCHER_CUMUL_S', null, $this->id_template)),
			'rewards_voucher_minimum' => (int)Tools::getValue('rewards_voucher_minimum', MyConf::get('REWARDS_VOUCHER_MINIMUM', null, $this->id_template)),
			'rewards_voucher_min_order' => $rewards_voucher_min_order,
			'rewards_voucher_minimum_multiple' => (float)Tools::getValue('rewards_voucher_minimum_multiple', MyConf::get('REWARDS_VOUCHER_MINIMUM_MULTIPLE', null, $this->id_template)),
			'include_tax' => (int)Tools::getValue('include_tax', MyConf::get('REWARDS_MINIMAL_TAX', null, $this->id_template)),
			'include_shipping' => (int)Tools::getValue('include_shipping', MyConf::get('REWARDS_MINIMAL_SHIPPING', null, $this->id_template)),
			'voucher_behavior' => (int)Tools::getValue('voucher_behavior', (int)MyConf::get('REWARDS_VOUCHER_BEHAVIOR', null, $this->id_template)),
			'rewards_payment_nb_orders' => (int)Tools::getValue('rewards_payment_nb_orders', MyConf::get('REWARDS_PAYMENT_NB_ORDERS', null, $this->id_template)),
			'rewards_payment_invoice' => (int)Tools::getValue('rewards_payment_invoice', MyConf::get('REWARDS_PAYMENT_INVOICE', null, $this->id_template)),
			'rewards_payment_groups' => Tools::getValue('rewards_payment_groups', explode(',', Configuration::get('REWARDS_PAYMENT_GROUPS'))),
			'rewards_payment_ratio' => (float)Tools::getValue('rewards_payment_ratio', (float)MyConf::get('REWARDS_PAYMENT_RATIO', null, $this->id_template)),
			'rewards_payment_min_value' => $rewards_payment_min_value,
			'rewards_mails_ignored' => Tools::getValue('rewards_mails_ignored', Configuration::get('REWARDS_MAILS_IGNORED')),
			'rewards_reminder' => (int)Tools::getValue('rewards_reminder', Configuration::get('REWARDS_REMINDER')),
			'rewards_reminder_minimum' => (float)Tools::getValue('rewards_reminder_minimum', (float)Configuration::get('REWARDS_REMINDER_MINIMUM')),
			'rewards_reminder_frequency' => (int)Tools::getValue('rewards_reminder_frequency', (float)Configuration::get('REWARDS_REMINDER_FREQUENCY')),
			'rewards_general_txt' => $rewards_general_txt,
			'rewards_payment_txt' => $rewards_payment_txt,

		));
		return $this->getTemplateForm($this->l('Rewards account')).$this->instance->display($this->instance->path, 'views/templates/admin/admin-core.tpl');
	}

	private function _getStatistics()
	{
		$this->instanceDefaultStates();
		$stats = RewardsModel::getAdminStatistics();

		$this->context->smarty->assign(array(
			'module' => $this->instance,
			'object' => $this,
			'token' => Tools::getAdminToken('AdminCustomers'.(int)Tab::getIdFromClassName('AdminCustomers').(int)$this->context->employee->id),
			'stats' => $stats,
			'status' => array($this->rewardStateDefault, $this->rewardStateValidation, $this->rewardStateReturnPeriod, $this->rewardStateCancel, $this->rewardStateConvert, $this->rewardStateWaitingPayment, $this->rewardStatePaid),
			'current_language_id' => $this->context->language->id
		));
		return $this->instance->display($this->instance->path, 'views/templates/admin/admin-core-statistics.tpl');
	}

	private function _getPayments()
	{
		if (Tools::getValue('accept_payment')) {
			RewardsPaymentModel::acceptPayment((int)Tools::getValue('accept_payment'));
			die();
		}

		$this->context->smarty->assign(array(
			'module' => $this->instance,
			'object' => $this,
			'token' => Tools::getAdminToken('AdminCustomers'.(int)Tab::getIdFromClassName('AdminCustomers').(int)$this->context->employee->id),
			'payments' => RewardsPaymentModel::getPendingList()
		));
		return $this->instance->display($this->instance->path, 'views/templates/admin/admin-core-payments.tpl');
	}

	// add the css used by the module
	public function hookDisplayHeader()
	{
		$this->context = Context::getContext();
		$this->_checkGiftProduct();

		if (version_compare(_PS_VERSION_, '1.7', '>='))
			$this->context->controller->addCSS($this->instance->getPath().'css/presta-1.7/allinone_rewards-1.7.css', 'all');
		else
			$this->context->controller->addCSS($this->instance->getPath().'css/allinone_rewards.css', 'all');

		if (!Tools::getValue('content_only') && Tools::getValue('action')!='quickview' && ((version_compare(_PS_VERSION_, '1.7', '<') && ($this->context->controller instanceof CategoryController || $this->context->controller instanceof IndexController || $this->context->controller instanceof Allinone_rewardsGiftsModuleFrontController))	|| $this->context->controller instanceof ProductController) && RewardsModel::isCustomerAllowedForGiftProduct()) {
			$id_template = (int)MyConf::getIdTemplate('core', $this->context->customer->id);
			if ($this->context->controller instanceof ProductController) {
				$product = new Product((int)Tools::getValue('id_product'));
				if ($product->active && $product->available_for_order) {
					$this->context->controller->addJS($this->instance->getPath().'js/product.js');
					$this->context->controller->addJS($this->instance->getPath().'js/product-purchase-button.js');
				}
			} else if (MyConf::get('REWARDS_GIFT_LIST_BUTTON', null, $id_template))
				$this->context->controller->addJS($this->instance->getPath().'js/product-purchase-button.js');
		}

		// Convertit les récompenses à l'état ReturnPeriodId en ValidationId si la date de retour est dépassée, et envoie les mails de rappel
		if (!Configuration::get('REWARDS_USE_CRON')) {
			RewardsModel::checkRewardsStates();
			RewardsAccountModel::sendReminder();
		}

		if (($this->context->controller instanceof OrderOpcController || $this->context->controller instanceof OrderController || $this->context->controller instanceof CartController) && RewardsModel::isCustomerAllowedForGiftProduct()) {
			if (version_compare(_PS_VERSION_, '1.7', '>=')) {
				$this->context->controller->addJS($this->instance->getPath().'js/cart.js');
				Media::addJsDef(array('aior_id_default_gift_product' => Configuration::getGlobalValue('REWARDS_ID_DEFAULT_GIFT_PRODUCT')));
			} else {
				return '<style>
							tr[id*="product_'.Configuration::getGlobalValue('REWARDS_ID_DEFAULT_GIFT_PRODUCT').'"] .cart_quantity * { display: none; }
							tr[id*="product_'.Configuration::getGlobalValue('REWARDS_ID_DEFAULT_GIFT_PRODUCT').'"] .cart_delete * { display: none; }
							tr[id*="product_'.Configuration::getGlobalValue('REWARDS_ID_DEFAULT_GIFT_PRODUCT').'"] .cart_avail * { display: none; }
						</style>';
			}
		}
		return false;
	}

	// display the link to access to the rewards account
	public function hookDisplayCustomerAccount($params)
	{
		if ($this->isRewardsAccountVisible()) {
			if (version_compare(_PS_VERSION_, '1.7', '>='))
				return $this->instance->display($this->instance->path, 'presta-1.7/customer-account.tpl');
			return $this->instance->display($this->instance->path, 'customer-account.tpl');
		}
		return false;
	}

	public function hookDisplayMyAccountBlock($params)
	{
		if ($this->isRewardsAccountVisible()) {
			if (version_compare(_PS_VERSION_, '1.7', '>='))
					return $this->instance->display($this->instance->path, 'presta-1.7/my-account.tpl');
				return $this->instance->display($this->instance->path, 'my-account.tpl');
		}
		return false;
	}

	public function hookDisplayMyAccountBlockFooter($params)
	{
		return $this->hookDisplayMyAccountBlock($params);
	}

	public function hookDisplayShoppingCartFooter($params)
	{
		$id_template = (int)MyConf::getIdTemplate('core', $this->context->customer->id);
		if (MyConf::get('REWARDS_VOUCHER_CART_LINK', null, $id_template) && RewardsModel::isCustomerAllowedForVoucher() && Validate::isLoadedObject($this->context->cart) && $this->context->cart->nbProducts() > 0) {
			$totals = RewardsModel::getAllTotalsByCustomer((int)$this->context->customer->id);
			$totalAvailable = isset($totals[RewardsStateModel::getValidationId()]) ? (float)$totals[RewardsStateModel::getValidationId()] : 0;
			if ($totalAvailable > 0) {
				$totalAvailableUserCurrency = RewardsModel::getCurrencyValue($totalAvailable, $this->context->currency->id);
				$voucherMininum = (float)MyConf::get('REWARDS_VOUCHER_MIN_VALUE_'.(int)$this->context->currency->id, null, $id_template) > 0 ? (float)MyConf::get('REWARDS_VOUCHER_MIN_VALUE_'.(int)$this->context->currency->id, null, $id_template) : 0;
				if ($totalAvailableUserCurrency >= $voucherMininum) {
					$found = false;
					$voucherType = (int)MyConf::get('REWARDS_VOUCHER_TYPE', null, $id_template);
					if ($voucherType==2) {
						$list_values = explode(';', MyConf::get('REWARDS_VOUCHER_LIST_VALUES', null, $id_template));
						foreach($list_values as $value) {
							if ($value <= $totalAvailable) {
								$found = true;
								break;
							}
						}
					} else
						$found = true;
					if ($found) {
						$smarty_values = array(
							'rewards_available' => Tools::displayPrice($totalAvailableUserCurrency, $this->context->currency),
						);
						$this->context->smarty->assign($smarty_values);
						if (version_compare(_PS_VERSION_, '1.7', '>='))
							return $this->instance->display($this->instance->path, 'presta-1.7/shopping-cart-rewards.tpl');
						return $this->instance->display($this->instance->path, 'shopping-cart-rewards.tpl');
					}
				}
			}
		}
		return false;
	}

	// display rewards account information in customer admin page
	public function hookDisplayAdminCustomers($params)
	{
		$customer = new Customer((int)$params['id_customer']);
		if ($customer && !Validate::isLoadedObject($customer))
			die(Tools::displayError('Incorrect object Customer.'));

		$msg = $this->postProcess($params);
		$totals = RewardsModel::getAllTotalsByCustomer((int)$params['id_customer']);
		$rewards = RewardsModel::getAllByIdCustomer((int)$params['id_customer']);
		$payments = RewardsPaymentModel::getAllByIdCustomer((int)$params['id_customer']);
		$rewards_account = new RewardsAccountModel((int)$params['id_customer']);
		$states_for_update = array(RewardsStateModel::getDefaultId(), RewardsStateModel::getValidationId(), RewardsStateModel::getCancelId(), RewardsStateModel::getReturnPeriodId());
		$core_template_id = (int)MyConf::getIdTemplate('core', (int)$params['id_customer']);
		$core_templates = RewardsTemplateModel::getList('core');
		$loyalty_template_id = (int)MyConf::getIdTemplate('loyalty', (int)$params['id_customer']);
		$loyalty_templates = RewardsTemplateModel::getList('loyalty');

		$smarty_values = array(
			'customer' => $customer,
			'msg' => $msg,
			'totals' => $totals,
			'rewards' => $rewards,
			'payments' => $payments,
			'payment_authorized' => (int)MyConf::get('REWARDS_PAYMENT', null, $core_template_id),
			'rewards_account' => $rewards_account,
			'states_for_update' => $states_for_update,
			'sign' => $this->context->currency->sign,
			'rewardStateDefault' => $this->rewardStateDefault->name[(int)$this->context->language->id],
			'rewardStateValidation' => $this->rewardStateValidation->name[(int)$this->context->language->id],
			'rewardStateCancel' => $this->rewardStateCancel->name[(int)$this->context->language->id],
			'rewardStateConvert' => $this->rewardStateConvert->name[(int)$this->context->language->id],
			'rewardStateReturnPeriod' => $this->rewardStateReturnPeriod->name[(int)$this->context->language->id],
			'rewardStateWaitingPayment' => $this->rewardStateWaitingPayment->name[(int)$this->context->language->id],
			'rewardStatePaid' => $this->rewardStatePaid->name[(int)$this->context->language->id],
			'convert_reward_value' => (float)Tools::getValue('convert_reward_value'),
			'new_reward_value' => (float)Tools::getValue('new_reward_value'),
			'new_reward_state' => (int)Tools::getValue('new_reward_state'),
			'new_reward_reason' => Tools::getValue('new_reward_reason'),
			'new_reward_date_end' => Tools::getValue('new_reward_date_end'),
			'core_template_id' => $core_template_id,
			'core_templates' => $core_templates,
			'loyalty_template_id' => $loyalty_template_id,
			'loyalty_templates' => $loyalty_templates,
			'date_format' => $this->context->language->date_format_full
		);
		$this->context->smarty->assign($smarty_values);
		if (version_compare(_PS_VERSION_, '1.7', '>='))
			return $this->instance->display($this->instance->path, 'presta-1.7/admincustomer.tpl');
		return $this->instance->display($this->instance->path, 'admincustomer.tpl');
	}

	// Hook called in tab AdminProduct
	public function hookDisplayAdminProductsExtra($params)
	{
		$id_product = version_compare(_PS_VERSION_, '1.7', '>=') ? $params['id_product'] : Tools::getValue('id_product');
		if (Validate::isLoadedObject($product = new Product((int)$id_product))) {
			if (!$product->customizable && $product->minimal_quantity <= 1) {
				$rewards_gift_product = new RewardsGiftProductModel($product->id);

				$attributes = $product->getAttributesResume($this->context->language->id);
				if (empty($attributes))
		            $attributes[] = array('id_product_attribute' => 0, 'attribute_designation' => '');

		        $product_combinations = array();
		        foreach ($attributes as $attribute) {
	       			if ($rewards_gift_product->gift_allowed)
		            	$product_combinations[$attribute['id_product_attribute']] = RewardsGiftProductAttributeModel::getGiftProductAttribute($product->id, $attribute['id_product_attribute']);
		            else
		            	$product_combinations[$attribute['id_product_attribute']] = array('gift_allowed' => 0, 'purchase_allowed' => 0, 'price' => 0);
		            $product_combinations[$attribute['id_product_attribute']]['name'] = rtrim($product->name[$this->context->language->id].' - '.$attribute['attribute_designation'], ' - ');
		        }

		        $this->context->smarty->assign(array(
		        	'gift_allowed' => isset($rewards_gift_product->gift_allowed) ? $rewards_gift_product->gift_allowed : -1,
		        	'product_combinations' => $product_combinations
		        ));
		    }

			$smarty_values = array(
				'currency' => $this->context->currency,
				'product_rewards_url' => $this->context->link->getAdminLink('AdminProductReward').'&ajax=1&id_product='.$product->id,
				'virtual_value' => (float)Configuration::get('REWARDS_VIRTUAL_VALUE_'.(int)Configuration::get('PS_CURRENCY_DEFAULT')),
				'virtual_name' => Configuration::get('REWARDS_VIRTUAL_NAME', (int)$this->context->language->id),
				'product_loyalty_rewards' => RewardsProductModel::getProductRewardsList($product->id, 'loyalty'),
				'loyalty_templates' => RewardsTemplateModel::getList('loyalty'),
				'product_sponsorship_rewards' => RewardsProductModel::getProductRewardsList($product->id, 'sponsorship'),
				'sponsorship_templates' => RewardsTemplateModel::getList('sponsorship'),
			);
			$this->context->smarty->assign($smarty_values);
			if (version_compare(_PS_VERSION_, '1.7', '>='))
				return $this->instance->display($this->instance->path, 'presta-1.7/adminproductsextra.tpl');
			return $this->instance->display($this->instance->path, 'adminproductsextra.tpl');
		}
		return $this->l('Please, create the product first');
	}

	public function hookActionAdminControllerSetMedia($params)
	{
    	// add necessary javascript to customers back office
		if ($this->context->controller->controller_name == 'AdminCustomers') {
			$this->context->controller->addCSS($this->instance->getPath().'js/tablesorter/css/theme.ice.css', 'all');
			$this->context->controller->addCSS($this->instance->getPath().'js/tablesorter/addons/pager/jquery.tablesorter.pager.css', 'all');
			$this->context->controller->addCSS($this->instance->getPath().'css/admin-customer.css', 'all');
			$this->context->controller->addJS($this->instance->getPath().'js/tablesorter/jquery.tablesorter.min.js');
			$this->context->controller->addJS($this->instance->getPath().'js/tablesorter/jquery.tablesorter.widgets.js');
			$this->context->controller->addJS($this->instance->getPath().'js/tablesorter/addons/pager/jquery.tablesorter.pager.js');
			$this->context->controller->addJS($this->instance->getPath().'js/admin-customer.js');
			if (version_compare(_PS_VERSION_, '1.6', '<')) {
				$this->context->controller->addJqueryUI(array(
					'ui.slider',
					'ui.datepicker'
				));
				$this->context->controller->addJS($this->instance->getPath().'js/jquery-ui-1.8.16.custom.min.js');
				$this->context->controller->addCSS(_PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.css');
				$this->context->controller->addJS(_PS_JS_DIR_.'jquery/plugins/timepicker/jquery-ui-timepicker-addon.js');
			} else if (version_compare(_PS_VERSION_, '1.7', '>=')) {
    			$this->context->controller->addCSS('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
			}
			if (version_compare(_PS_VERSION_, '1.7.6', '>='))
				$this->context->controller->addJS(_PS_JS_DIR_.'jquery/jquery-migrate-1.2.1.min.js');
		}
    	if ($this->context->controller->controller_name == 'AdminProducts') {
    		if (version_compare(_PS_VERSION_, '1.7', '>=')) {
	    		$this->context->controller->addCSS('https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css');
        		$this->context->controller->addCSS($this->instance->getPath().'css/presta-1.7/admin-product.css');
    		}
        	$this->context->controller->addJS($this->instance->getPath().'js/admin-product.js');
    	}
	}

	public function hookActionObjectCustomerDeleteAfter($params)
	{
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards` WHERE `id_customer` NOT IN (SELECT `id_customer` FROM `'._DB_PREFIX_.'customer`)');
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_history` WHERE `id_reward` NOT IN (SELECT `id_reward` FROM `'._DB_PREFIX_.'rewards`)');
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_payment` WHERE `id_payment` NOT IN (SELECT `id_payment` FROM `'._DB_PREFIX_.'rewards`)');
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_account` WHERE `id_customer` NOT IN (SELECT `id_customer` FROM `'._DB_PREFIX_.'customer`)');
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_template_customer` WHERE `id_customer` NOT IN (SELECT `id_customer` FROM `'._DB_PREFIX_.'customer`)');
	}

	public function hookActionObjectProductDeleteAfter($params)
	{
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_product` WHERE `id_product`='.(int)$params['object']->id);
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_gift_product` WHERE `id_product`='.(int)$params['object']->id);
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_gift_product_attribute` WHERE `id_product`='.(int)$params['object']->id);
	}

	public function hookActionObjectAttributeDeleteAfter($params)
	{
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_gift_product_attribute` WHERE id_product_attribute!=0 AND id_product_attribute NOT IN (SELECT DISTINCT id_product_attribute FROM `'._DB_PREFIX_.'product_attribute_combination`)');
	}

	// check if the product is in a category which is allowed for free gift
	// or if a custom behavior is defined on that product
	private function _isGiftProductAllowed($id_template, $id_product, $id_product_attribute)
	{
		if (Validate::isLoadedObject($product = new Product($id_product)) && !$product->customizable && $product->minimal_quantity <= 1 && (float)$product->getPrice(false, $id_product_attribute) > 0) {
			$gift_allowed = RewardsGiftProductAttributeModel::getGiftProductAttributeAllowed($id_product, $id_product_attribute);
			switch($gift_allowed) {
				// product has no custom value defined in the product sheet
				case -1:
					// all categories
					if ((int)MyConf::get('REWARDS_GIFT_ALL_CATEGORIES', null, $id_template)==1)
						return true;
					// none
					else if ((int)MyConf::get('REWARDS_GIFT_ALL_CATEGORIES', null, $id_template)==-1)
						return false;
					else {
						$allowed_categories = array();
						$categories = explode(',', MyConf::get('REWARDS_GIFT_CATEGORIES', null, $id_template));
						foreach($categories as $category)
							$allowed_categories[] = array('id_category' => $category);
						return Product::idIsOnCategoryId($id_product, $allowed_categories);
					}
				// product is not allowed in product sheet
				case 0:
					return false;
				// product is active in product sheet
				case 1:
					return true;
			}
		}
		return false;
	}

	private function _isGiftProductPurchaseAllowed($id_template, $id_product, $id_product_attribute)
	{
		$purchase_allowed = RewardsGiftProductAttributeModel::getGiftProductAttributePurchaseAllowed($id_product, $id_product_attribute);
		if ($purchase_allowed == -1) {
			// product has no custom value defined in the product sheet
			return (bool)MyConf::get('REWARDS_GIFT_BUY_BUTTON', null, $id_template);
		} else
			return $purchase_allowed;
	}

	// add the common object to the footer for the product page, and list of products
	public function hookDisplayFooter()
	{
		if (!Tools::getValue('content_only') && Tools::getValue('action')!='quickview' && ((version_compare(_PS_VERSION_, '1.7', '<') && ($this->context->controller instanceof CategoryController || $this->context->controller instanceof IndexController || $this->context->controller instanceof Allinone_rewardsGiftsModuleFrontController))	|| $this->context->controller instanceof ProductController) && RewardsModel::isCustomerAllowedForGiftProduct()) {
			$id_template = (int)MyConf::getIdTemplate('core', $this->context->customer->id);

			if (version_compare(_PS_VERSION_, '1.7', '>='))
				$this->context->smarty->assign('aior_cart_url', $this->context->link->getPageLink('cart', null, $this->context->language->id, array('action' => 'show')));
			else
				$this->context->smarty->assign('aior_cart_url', $this->context->link->getPageLink(Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order', true));

			if (!($this->context->controller instanceof ProductController)) {
				if (MyConf::get('REWARDS_GIFT_LIST_BUTTON', null, $id_template)) {
					$totals = RewardsModel::getAllTotalsByCustomer((int)$this->context->customer->id);
					$total_available = isset($totals[RewardsStateModel::getValidationId()]) ? (float)$totals[RewardsStateModel::getValidationId()] : 0;
					if ($total_available > 0) {
						$this->context->smarty->assign('aior_total_available_display', $this->instance->getRewardReadyForDisplay($total_available, (int)$this->context->currency->id));
						$this->context->smarty->assign('aior_total_available_real', $total_available);
						return $this->instance->display($this->instance->path, 'product-footer.tpl');
					}
				}
			} else {
				$this->context->smarty->assign('aior_total_available_display', '');
				$this->context->smarty->assign('aior_total_available_real', '');
				return $this->instance->display($this->instance->path, 'product-footer.tpl');
			}
		}
		return false;
	}

	// necesary because hookDisplayProductButtons changed name in 1.7.1
	// and alias doesn't work
	public function hookDisplayProductAdditionalInfo($params)
	{
		if (version_compare(_PS_VERSION_, '1.7.1.0', '>='))
			return $this->hookDisplayProductButtons($params);
	}

	public function hookDisplayProductButtons($params)
	{
		// TODO : récupérer le purchase_allowed de la déclinaison par défaut,
		// et masquer le bouton si besoin, pour éviter de le voir disparaitre
		$this->context = Context::getContext();
		if (!Tools::getValue('content_only') && Tools::getValue('action')!='quickview' && RewardsModel::isCustomerAllowedForGiftProduct()) {
			$totals = RewardsModel::getAllTotalsByCustomer((int)$this->context->customer->id);
			$total_available = isset($totals[RewardsStateModel::getValidationId()]) ? (float)$totals[RewardsStateModel::getValidationId()] : 0;
			if ($total_available > 0) {
				if (version_compare(_PS_VERSION_, '1.7', '>='))
					return $this->instance->display($this->instance->path, 'presta-1.7/product-purchase-button.tpl');
				return $this->instance->display($this->instance->path, 'product-purchase-button.tpl');
			}
		}
	}

	// called on product page to display the button allowing to buy the selected combination from rewards account
	public function displayPurchaseButtonOnProductPage($id_product, $id_product_attribute=0)
	{
		$id_template = (int)MyConf::getIdTemplate('core', $this->context->customer->id);
		if (RewardsModel::isCustomerAllowedForGiftProduct() && $this->_isGiftProductAllowed($id_template, $id_product, $id_product_attribute)) {
			$totals = RewardsModel::getAllTotalsByCustomer((int)$this->context->customer->id);
			$total_available = isset($totals[RewardsStateModel::getValidationId()]) ? (float)$totals[RewardsStateModel::getValidationId()] : 0;

			$price = RewardsGiftProductAttributeModel::getGiftProductAttributePrice($id_product, $id_product_attribute);
			if (!$price) {
				$product = new Product((int)$id_product);
				$price = $product->getPrice(false, $id_product_attribute);
				if (MyConf::get('REWARDS_GIFT_TAX', null, $id_template))
					$price = $product->getPrice(true, $id_product_attribute);
				$price = (float)round(Tools::convertPrice($price, $this->context->currency, false), 2);
			}

			if ($total_available > 0) {
				if ($price > 0 && $total_available >= $price) {
					return json_encode(array(
						'has_error' => false,
						'aior_product_price_display' => $this->instance->getRewardReadyForDisplay($price, (int)$this->context->currency->id),
						'aior_total_available_display' => $this->instance->getRewardReadyForDisplay($total_available, (int)$this->context->currency->id),
						'aior_total_available_real' => $total_available,
						'aior_total_available_after' => $this->instance->getRewardReadyForDisplay($total_available - $price, (int)$this->context->currency->id),
						'aior_show_buy_button' => $this->_isGiftProductPurchaseAllowed($id_template, $id_product, $id_product_attribute),
					));
				}
			}
			return json_encode(array(
				'has_error' => true,
				'aior_product_price_display' => $this->instance->getRewardReadyForDisplay($price, (int)$this->context->currency->id),
				'aior_show_buy_button' => $this->_isGiftProductPurchaseAllowed($id_template, $id_product, $id_product_attribute),
			));
		}
		return json_encode(array('has_error' => true, 'aior_show_buy_button' => true));
	}

	public function hookDisplayProductListReviews($params)
	{
		if (version_compare(_PS_VERSION_, '1.6.1.0', '<=')) {
			$params['type']='after_price';
			return $this->_displayProductListButtons($params);
		}
		return false;
	}

	public function hookDisplayProductPriceBlock($params)
	{
		if ((version_compare(_PS_VERSION_, '1.6', '>=') && ($params['type'] == 'aior_crossseling' || $params['type'] == 'aior_productscategory')) || ($params['type'] == 'after_price' && !$this->context->controller instanceof ProductController))
			return $this->_displayProductListButtons($params);
		return false;
	}

	private function _displayProductListButtons($params)
	{
		$id_template = (int)MyConf::getIdTemplate('core', $this->context->customer->id);
		$id_product = (int)$params['product']['id_product'];
		$id_product_attribute = isset($params['product']) && isset($params['product']['id_product_attribute']) ? (int)$params['product']['id_product_attribute'] : 0;
		if (RewardsModel::isCustomerAllowedForGiftProduct() && $this->_isGiftProductAllowed($id_template, $id_product, $id_product_attribute)) {
			$this->context->smarty->assign('aior_id_product', $id_product);
			$this->context->smarty->assign('aior_id_product_attribute', $id_product_attribute);
			$this->context->smarty->assign('aior_show_buy_button', $this->_isGiftProductPurchaseAllowed($id_template, $id_product, $id_product_attribute));

			if ($params['type'] == 'after_price' && MyConf::get('REWARDS_GIFT_LIST_BUTTON', null, $id_template)) {
				$price = RewardsGiftProductAttributeModel::getGiftProductAttributePrice($id_product, $id_product_attribute);
				if (!$price) {
					$product = new Product((int)$id_product);
					$product->id_product_attribute = $id_product_attribute;
					$price = $product->getPrice(false, $id_product_attribute);
					if (MyConf::get('REWARDS_GIFT_TAX', null, $id_template))
						$price = $product->getPrice(true, $id_product_attribute);
					$price = (float)round(Tools::convertPrice($price, $this->context->currency, false), 2);
				}
				$this->context->smarty->assign('aior_product_price_display', $this->instance->getRewardReadyForDisplay($price, (int)$this->context->currency->id));
				$this->context->smarty->assign('aior_product_price_real', $price);
			}
			return $this->instance->display($this->instance->path, 'product-list-purchase-button.tpl');
		}
		return false;
	}

	public function purchaseProductFromRewards($id_product, $id_product_attribute=0)
	{
		$id_template = (int)MyConf::getIdTemplate('core', $this->context->customer->id);
		if (RewardsModel::isCustomerAllowedForGiftProduct() && $this->_isGiftProductAllowed($id_template, $id_product, $id_product_attribute)) {
			$totals = RewardsModel::getAllTotalsByCustomer((int)$this->context->customer->id);
			$total_available = isset($totals[RewardsStateModel::getValidationId()]) ? (float)$totals[RewardsStateModel::getValidationId()] : 0;
			if ($total_available > 0) {
				$product = new Product((int)$id_product);
				$product->id_product_attribute = $id_product_attribute;

				$price = RewardsGiftProductAttributeModel::getGiftProductAttributePrice($id_product, $id_product_attribute);
				if (!$price) {
					$price = $product->getPrice(false, $id_product_attribute);
					if (MyConf::get('REWARDS_GIFT_TAX', null, $id_template))
						$price = $product->getPrice(true, $id_product_attribute);
					$price = (float)round(Tools::convertPrice($price, $this->context->currency, false), 2);
				}

				if ($price > 0 && $total_available >= $price) {
					$result = RewardsModel::purchaseProductFromRewards($product, $price);
					if ($result === true)
						return json_encode(array('has_error' => false, 'aior_total_available_display' => $this->instance->getRewardReadyForDisplay($total_available - $price, (int)$this->context->currency->id), 'aior_total_available_real' => $total_available - $price));
					else {
						$error_msg = $this->l('A gift voucher has been generated, but it has not been added to the cart due to the following error:').'<br>'.($result===false ? $this->l('unknow error') : $result);
						$totals = RewardsModel::getAllTotalsByCustomer((int)$this->context->customer->id);
						$total_available = isset($totals[RewardsStateModel::getValidationId()]) ? (float)$totals[RewardsStateModel::getValidationId()] : 0;
						return json_encode(array('has_error' => true, 'error_msg' => $error_msg, 'aior_total_available_display' => $this->instance->getRewardReadyForDisplay($total_available, (int)$this->context->currency->id), 'aior_total_available_real' => $total_available));
					}
				} else {
					$price = $this->instance->getRewardReadyForDisplay($price, (int)$this->context->currency->id);
					$total_available_display = $this->instance->getRewardReadyForDisplay($total_available, (int)$this->context->currency->id);
					$error_msg = sprintf($this->l('You can not buy this product with your rewards. %s are required and you have only %s available.'), $price, $total_available_display);
					return json_encode(array('has_error' => true, 'error_msg' => $error_msg, 'aior_total_available_display' => $total_available_display, 'aior_total_available_real' => $total_available));
				}
			}
		}
		return json_encode(array('has_error' => true, 'error_msg' => $this->l('You are not allowed to buy this product with your rewards.')));
	}

	// add or remove the default product from the cart each time the cart is modified
	// if some product can't be bought normally, limit the quantity to the gift products quantity
	public function hookActionCartSave($params)
	{
		if (!($this->context->controller instanceof AdminController) && $this->context->customer->isLogged()) {
			$id_template = (int)MyConf::getIdTemplate('core', $this->context->customer->id);

			if (!MyConf::get('REWARDS_GIFT', null, $id_template))
				return;

			// allow to stop the standard behavior when I manually add the fake gift product to the cart, else it causes an infinite loop.
			if ((int)Configuration::getGlobalValue('REWARDS_EXIT_HOOKACTIONCARTSAVE')) {
				Configuration::updateGlobalValue('REWARDS_EXIT_HOOKACTIONCARTSAVE', 0);
				return;
			}

			if (!self::$_is_loading && Validate::isLoadedObject($this->context->cart) && Validate::isLoadedObject($product = new Product((int)Configuration::getGlobalValue('REWARDS_ID_DEFAULT_GIFT_PRODUCT'))) && $product->active) {
				// to avoid infinite loop caused by addCartRule
				self::$_is_loading = true;

				// j'ajoute systématiquement le produit et je remets sa quantité à 1, ensuite on voit s'il est toujours nécessaire
				$this->context->cart->updateQty(1, (int)Configuration::getGlobalValue('REWARDS_ID_DEFAULT_GIFT_PRODUCT'), 0);
				Db::getInstance()->execute('
								UPDATE `'._DB_PREFIX_.'cart_product`
								SET `quantity` = 1
								WHERE `id_cart` = '.(int)$this->context->cart->id.'
								AND `id_product` = '.(int)Configuration::getGlobalValue('REWARDS_ID_DEFAULT_GIFT_PRODUCT'));

				// le panier contient t'il des produits cadeaux ?
				$cart_rules = $this->context->cart->getCartRules(CartRule::FILTER_ACTION_GIFT);

				if (version_compare(_PS_VERSION_, '1.7.7.0', '<')) {
					// avant la 1.7.7.0 prestashop efface parfois tous les produits (standards + gift) quand on enlève un produit du panier qui est aussi en cadeau mais la règle du produit cadeau est toujours dans le panier.
					// Comme on peut avoir des règles liées au panier et sans le produit associé, il faut les réappliquer pour remettre le produit
					foreach($cart_rules as $cart_rule) {
						$this->context->cart->removeCartRule($cart_rule['id_cart_rule']);
					}
				}

				$cart_rules_quantities = array();
				foreach($cart_rules as $cart_rule) {
					if (version_compare(_PS_VERSION_, '1.7.7.0', '<'))
						$this->context->cart->addCartRule($cart_rule['id_cart_rule']);

					if (!isset($cart_rules_quantities[$cart_rule['gift_product'].'_'.$cart_rule['gift_product_attribute']]))
						$cart_rules_quantities[$cart_rule['gift_product'].'_'.$cart_rule['gift_product_attribute']] = 0;
					$cart_rules_quantities[$cart_rule['gift_product'].'_'.$cart_rule['gift_product_attribute']]++;
				}

				// on regarde la quantité de chaque produit dans le panier
				$cartProducts = $this->context->cart->getProducts();
				$products_quantities = array();
				foreach ($cartProducts as $product) {
					if ($product['id_product'] != (int)Configuration::getGlobalValue('REWARDS_ID_DEFAULT_GIFT_PRODUCT'))
						$products_quantities[] = array('id_product' => $product['id_product'], 'id_product_attribute' => $product['id_product_attribute'], 'quantity' => $product['cart_quantity']);
				}

				// on regarde maintenant s'il y a des produits cadeaux qui n'ont pas le droit d'être acheté normalement et qui se trouvent dans le panier.
				// si c'est le cas on limite la quantité au nombre de règles panier
				$normal_product_quantity = 0;
				foreach($products_quantities as $product) {
					$allowed_quantity = isset($cart_rules_quantities[$product['id_product'].'_'.$product['id_product_attribute']]) ? (int)$cart_rules_quantities[$product['id_product'].'_'.$product['id_product_attribute']] : 0;
					$quantity = $product['quantity'] - $allowed_quantity;
					if ($this->_isGiftProductAllowed($id_template, $product['id_product'], $product['id_product_attribute']) && !$this->_isGiftProductPurchaseAllowed($id_template, $product['id_product'], $product['id_product_attribute'])) {
						if ($quantity > 0) {
							if ($allowed_quantity > 0) {
								Db::getInstance()->execute('
									UPDATE `'._DB_PREFIX_.'cart_product`
									SET `quantity` = '.$allowed_quantity.'
									WHERE `id_cart` = '.(int)$this->context->cart->id.'
									AND `id_product` = '.$product['id_product'].'
									AND `id_product_attribute` = '.$product['id_product_attribute']);
							} else {
								Db::getInstance()->execute('
								DELETE FROM `'._DB_PREFIX_.'cart_product`
								WHERE `id_cart` = '.(int)$this->context->cart->id.'
								AND `id_product` = '.$product['id_product'].'
								AND `id_product_attribute` = '.$product['id_product_attribute']);
							}
						}
					} else
						$normal_product_quantity += $quantity;
				}

				// si il n'y a plus de règle valide ou qu'il y a des produits normaux dans le panier, plus besoin du produit
				$cart_rules = $this->context->cart->getCartRules(CartRule::FILTER_ACTION_GIFT);
				if (count($cart_rules)==0 || $normal_product_quantity > 0) {
					Db::getInstance()->execute('
						DELETE FROM `'._DB_PREFIX_.'cart_product`
						WHERE `id_product` = '.(int)Configuration::getGlobalValue('REWARDS_ID_DEFAULT_GIFT_PRODUCT').'
						AND `id_cart` = '.(int)$this->context->cart->id);
				}

				self::$_is_loading = false;
			}
		}
	}

	private function _checkGiftProduct() {
		if ((int)Configuration::getGlobalValue('REWARDS_ID_DEFAULT_GIFT_PRODUCT')) {
			// si on appelle la fiche du produit cadeau, on redirige sur le panier
			if ($this->context->controller instanceof ProductController && Tools::getValue('id_product') == (int)Configuration::getGlobalValue('REWARDS_ID_DEFAULT_GIFT_PRODUCT')) {
				Tools::redirect($this->context->link->getPageLink(Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order', true));
			}
			// si le panier ne contient que le produit cadeau (par exemple en utilisant la "re-commande", on efface le panier)
			else if ($this->context->controller instanceof OrderOpcController || $this->context->controller instanceof OrderController || $this->context->controller instanceof CartController) {
				$result = $this->context->cart->containsProduct((int)Configuration::getGlobalValue('REWARDS_ID_DEFAULT_GIFT_PRODUCT'), 0, null);
				$cartProducts = $this->context->cart->getProducts();
				if ($result!==false && !empty((float)$result['quantity']) && count($cartProducts)==1) {
					$this->context->cart->delete();
					Tools::redirect($this->context->link->getPageLink(Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order', true));
				}
			}
		}
	}
}