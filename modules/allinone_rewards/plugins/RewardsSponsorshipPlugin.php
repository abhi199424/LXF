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
require_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsModel.php');
require_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsSponsorshipModel.php');

class RewardsSponsorshipPlugin extends RewardsGenericPlugin
{
	public $name = 'sponsorship';
	private $_configuration;
	private $_popup = false;

	public function install()
	{
		// hooks
		if (!$this->registerHook('displayHeader') || !$this->registerHook('displayFooter')
		|| !$this->registerHook('displayCustomerAccount') || !$this->registerHook('displayMyAccountBlock') || (version_compare(_PS_VERSION_, '8.0.0', '<') && !$this->registerHook('displayMyAccountBlockFooter'))
		|| !$this->registerHook('actionCustomerAccountAdd') || !$this->registerHook('displayCustomerAccountForm') || !$this->registerHook('displayCustomerAccountFormTop') || (version_compare(_PS_VERSION_, '1.7', '>=') && (!$this->registerHook('additionalCustomerFormFields') || !$this->registerHook('actionFrontControllerSetMedia')))
		|| !$this->registerHook('displayLeftColumnProduct') || !$this->registerHook('displayProductButtons') || !$this->registerHook('displayProductAdditionalInfo')
		|| !$this->registerHook('actionValidateOrder')|| !$this->registerHook('displayOrderConfirmation') || !$this->registerHook('actionOrderStatusUpdate')
		|| !$this->registerHook('displayAdminCustomers') || !$this->registerHook('displayAdminOrder') || !$this->registerHook('actionAdminControllerSetMedia') || (version_compare(_PS_VERSION_, '1.7.7.0', '>=') && !$this->registerHook('displayAdminOrderMainBottom'))
		|| !$this->registerHook('actionProductCancel') || !$this->registerHook('actionObjectOrderDetailAddAfter') || !$this->registerHook('actionObjectOrderDetailUpdateAfter') || !$this->registerHook('actionObjectOrderDetailDeleteAfter')
		|| !$this->registerHook('actionObjectCustomerDeleteAfter'))
			return false;

		$idEn = Language::getIdByIso('en');
		$desc = array();
		$account_txt = array();
		$order_txt = array();
		$popup_txt = array();
		$rules_txt = array();
		foreach (Language::getLanguages() as $language) {
			$tmp = $this->l('Sponsorship', (int)$language['id_lang']);
			$desc[(int)$language['id_lang']] = isset($tmp) && !empty($tmp) ? $tmp : $this->l('Sponsorship', $idEn);
			$tmp = $this->l('account_txt', (int)$language['id_lang']);
			$account_txt[(int)$language['id_lang']] = isset($tmp) && !empty($tmp) ? $tmp : $this->l('account_txt', $idEn);
			$tmp = $this->l('order_txt', (int)$language['id_lang']);
			$order_txt[(int)$language['id_lang']] = isset($tmp) && !empty($tmp) ? $tmp : $this->l('order_txt', $idEn);
			$tmp = $this->l('popup_txt', (int)$language['id_lang']);
			$popup_txt[(int)$language['id_lang']] = isset($tmp) && !empty($tmp) ? $tmp : $this->l('popup_txt', $idEn);
			$tmp = $this->l('rules_txt', (int)$language['id_lang']);
			$rules_txt[(int)$language['id_lang']] = isset($tmp) && !empty($tmp) ? $tmp : $this->l('rules_txt', $idEn);
		}

		$groups_off = array(Configuration::get('PS_UNIDENTIFIED_GROUP'), Configuration::get('PS_GUEST_GROUP'));
		$groups_config = '';
		$groups = Group::getGroups((int)Configuration::get('PS_LANG_DEFAULT'));
		foreach ($groups as $group)
			if (!in_array($group['id_group'], $groups_off))
				$groups_config .= (int)$group['id_group'].',';
		$groups_config = rtrim($groups_config, ',');

		if (!Configuration::updateValue('RSPONSORSHIP_ORDER_QUANTITY_S', 0)
		|| !Configuration::updateValue('RSPONSORSHIP_VOUCHER_DETAILS', $desc)
		|| !Configuration::updateValue('RSPONSORSHIP_REAL_VOUCHER_GC', 0)
		|| !Configuration::updateValue('RSPONSORSHIP_VOUCHER_PREFIX_GC', '')
		|| !Configuration::updateValue('RSPONSORSHIP_VOUCHER_DURATION_GC', 365)
		|| !Configuration::updateValue('RSPONSORSHIP_ALL_CATEGORIES', 1)
		|| !Configuration::updateValue('RSPONSORSHIP_TAX', 1)
		|| !Configuration::updateValue('RSPONSORSHIP_CATEGORIES_GC', '')
		|| !Configuration::updateValue('RSPONSORSHIP_REWARD_TYPE_S', 1)
		|| !Configuration::updateValue('RSPONSORSHIP_DISCOUNT_TYPE_GC', 2)
		|| !Configuration::updateValue('RSPONSORSHIP_FREESHIPPING_GC', 0)
		|| !Configuration::updateValue('RSPONSORSHIP_CUMUL_GC', 1)
		|| !Configuration::updateValue('RSPONSORSHIP_QUANTITY_GC', 1)
		|| !Configuration::updateValue('RSPONSORSHIP_VOUCHER_BEHAVIOR', 0)
		|| !Configuration::updateValue('RSPONSORSHIP_MINIMAL_TAX_GC', 1)
		|| !Configuration::updateValue('RSPONSORSHIP_NB_FRIENDS', 5)
		|| !Configuration::updateValue('RSPONSORSHIP_REWARD_REGISTRATION', 0)
		|| !Configuration::updateValue('RSPONSORSHIP_REWARD_ORDER', 1)
		|| !Configuration::updateValue('RSPONSORSHIP_REWARD_PERCENTAGE', 5)
		|| !Configuration::updateValue('RSPONSORSHIP_DEF_PRODUCT_REWARD', 0)
		|| !Configuration::updateValue('RSPONSORSHIP_DEF_PRODUCT_TYPE', 0)
		|| !Configuration::updateValue('RSPONSORSHIP_ON_EVERY_ORDER', 0)
		|| !Configuration::updateValue('RSPONSORSHIP_DURATION', 0)
		|| !Configuration::updateValue('RSPONSORSHIP_DISCOUNTED_ALLOWED', 1)
		|| !Configuration::updateValue('RSPONSORSHIP_DISCOUNT_GC', 1)
		|| !Configuration::updateValue('RSPONSORSHIP_UNLOCK_SHIPPING', 1)
		|| !Configuration::updateValue('RSPONSORSHIP_ACTIVE', 0)
		|| !Configuration::updateValue('RSPONSORSHIP_USE_VOUCHER_FIELD', 1)
		|| !Configuration::updateValue('RSPONSORSHIP_MULTIPLE_SPONSOR', 0)
		|| !Configuration::updateValue('RSPONSORSHIP_ANONYMIZE', 1)
		|| !Configuration::updateValue('RSPONSORSHIP_REDIRECT', 'home')
		|| !Configuration::updateValue('RSPONSORSHIP_PRODUCT_SHARE', 0)
		|| !Configuration::updateValue('RSPONSORSHIP_MAIL_REGISTRATION', 1)
		|| !Configuration::updateValue('RSPONSORSHIP_MAIL_ORDER', 1)
		|| !Configuration::updateValue('RSPONSORSHIP_MAIL_REGISTRATION_S', 1)
		|| !Configuration::updateValue('RSPONSORSHIP_MAIL_ORDER_S', 1)
		|| !Configuration::updateValue('RSPONSORSHIP_MAIL_VALIDATION_S', 1)
		|| !Configuration::updateValue('RSPONSORSHIP_MAIL_CANCELPROD_S', 1)
		|| !Configuration::updateValue('RSPONSORSHIP_UNLIMITED_LEVELS', 0)
		|| !Configuration::updateValue('RSPONSORSHIP_AFTER_ORDER', 1)
		|| !Configuration::updateValue('RSPONSORSHIP_POPUP', 1)
		|| !Configuration::updateValue('RSPONSORSHIP_POPUP_DELAY', 3)
		|| !Configuration::updateValue('RSPONSORSHIP_POPUP_KEY', Tools::passwdGen())
		|| !Configuration::updateValue('RSPONSORSHIP_ACCOUNT_TXT', $account_txt, true)
		|| !Configuration::updateValue('RSPONSORSHIP_ORDER_TXT', $order_txt, true)
		|| !Configuration::updateValue('RSPONSORSHIP_POPUP_TXT', $popup_txt, true)
		|| !Configuration::updateValue('RSPONSORSHIP_RULES_TXT', $rules_txt, true)
		|| !Configuration::updateValue('RSPONSORSHIP_GROUPS', $groups_config))
			return false;

		if (version_compare(_PS_VERSION_, '1.5.2', '<')) {
			Configuration::set('RSPONSORSHIP_ACCOUNT_TXT', $account_txt);
			Configuration::set('RSPONSORSHIP_ORDER_TXT', $order_txt);
			Configuration::set('RSPONSORSHIP_POPUP_TXT', $popup_txt);
			Configuration::set('RSPONSORSHIP_RULES_TXT', $rules_txt);
		}

		foreach ($this->instance->getCurrencies() as $currency) {
			Configuration::updateValue('RSPONSORSHIP_REWARD_VALUE_S_'.(int)($currency['id_currency']), 5);
			Configuration::updateValue('RSPONSORSHIP_VOUCHER_VALUE_GC_'.(int)($currency['id_currency']), 5);
			Configuration::updateValue('RSPONSORSHIP_MINIMUM_VALUE_GC_'.(int)($currency['id_currency']), 0);
			Configuration::updateValue('RSPONSORSHIP_UNLOCK_GC_'.(int)($currency['id_currency']), 0);
		}

		// database
		Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rewards_sponsorship` (
			`id_sponsorship` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_sponsor` INT UNSIGNED NOT NULL,
			`channel` INT UNSIGNED DEFAULT 1,
			`email` VARCHAR(255) NOT NULL,
			`lastname` VARCHAR(128) DEFAULT NULL,
			`firstname` VARCHAR(128) DEFAULT NULL,
			`id_customer` INT UNSIGNED DEFAULT NULL,
			`id_cart_rule` INT UNSIGNED DEFAULT NULL,
			`deleted` TINYINT(1) NOT NULL DEFAULT \'0\',
			`date_end` DATETIME DEFAULT \'0000-00-00 00:00:00\',
			`date_add` DATETIME NOT NULL,
			`date_upd` DATETIME NOT NULL,
			PRIMARY KEY (`id_sponsorship`),
			INDEX `index_sponsorship_email` (`email`),
			INDEX `index_id_customer_deleted` (`id_customer`,`deleted`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rewards_sponsorship_detail` (
			`id_reward` INT UNSIGNED NOT NULL,
			`id_sponsorship` INT UNSIGNED DEFAULT \'0\',
			`level_sponsorship` INT UNSIGNED DEFAULT \'0\',
			PRIMARY KEY (`id_reward`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rewards_sponsorship_code` (
			`id_sponsor` INT UNSIGNED NOT NULL,
			`code` VARCHAR(20) NOT NULL,
			PRIMARY KEY (`id_sponsor`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		// create an invisible tab so we can call an admin controller to manage the sponsor autocomplete field in the customer page
		$tab = new Tab();
		$tab->active = 1;
		$tab->class_name = "AdminSponsor";
		$tab->name = array();
		foreach (Language::getLanguages(true) as $lang)
			$tab->name[$lang['id_lang']] = 'AllinoneRewards Sponsor';
		$tab->id_parent = -1;
		$tab->module = $this->instance->name;
		if (!$tab->add())
			return false;

		return true;
	}

	public function uninstall()
	{
		//Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'rewards_sponsorship_detail`;');
		//Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'rewards_sponsorship`;');
		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'configuration_lang`
			WHERE `id_configuration` IN (SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE `name` LIKE \'RSPONSORSHIP_%\')');

		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'configuration`
			WHERE `name` LIKE \'RSPONSORSHIP_%\'');

		return true;
	}

	// get the configuration by level
	private function _initConf($id_template)
	{
		unset($this->_configuration);
		$this->_configuration['reward_type'] = explode(',', MyConf::get('RSPONSORSHIP_REWARD_TYPE_S', null, $id_template));
		$this->_configuration['reward_percentage'] = explode(',', MyConf::get('RSPONSORSHIP_REWARD_PERCENTAGE', null, $id_template));
		$this->_configuration['default_product_reward'] = explode(',', MyConf::get('RSPONSORSHIP_DEF_PRODUCT_REWARD', null, $id_template));
		$this->_configuration['default_product_type'] = explode(',', MyConf::get('RSPONSORSHIP_DEF_PRODUCT_TYPE', null, $id_template));
		$this->_configuration['unlimited'] = (int)MyConf::get('RSPONSORSHIP_UNLIMITED_LEVELS', null, $id_template);

		$currencies = $this->instance->getCurrencies();
		$tmp = Tools::getValue('reward_value_s');
		foreach ($currencies as $currency) {
			$values = explode(',', MyConf::get('RSPONSORSHIP_REWARD_VALUE_S_'.$currency['id_currency'], null, $id_template));
			foreach($this->_configuration['reward_percentage'] as $level => $percentage) {
				if (isset($tmp[$currency['id_currency']][$level]))
					$values[$level] = $tmp[$currency['id_currency']][$level];
				$this->_configuration['reward_value'][$level][$currency['id_currency']] = isset($values[$level]) ? $values[$level] : 0;
			}
		}
	}

	public function isActive()
	{
		// TODO : il faudrait aussi regarder si le parrainage est actif pour le parrain
		// car par exemple sur actionValidateOrder c'est le compte du parrain qui compte et pas du filleul
		// Que se passe t'il si le filleul qui a un compte membre est sur un template inactif, mais le parrain sur un template actif ? ActionValidateOrder se déclenche ?
		// Actuellement oui, c'est un coup de bol, car dans ActionValidateOrder isLogged renvoi false.
		// Solution : passe le nom du hook en cours a isActive et faire des cas différents (peut-être aussi le contexte front/admin)
		// si on vérifie le compte parrain (dépend de la méthode passée en param), peut-être vérifier en même temps qu'il est toujours dans un groupe autorisé à parrainer
		// ou bien alors isActive return true, et on test sur chaque hook en fonction de la personne concernée si c'est actif et s'il a le droit
		if (isset($this->context->customer) && $this->context->customer->isLogged()) {
			// si on est dans le cadre d'un changement de parrain d'un client connecté, on regarde si le template du parrain est actif et qu'il autorise le parrainage multiple
			$sponsor = null;
			if (Tools::getValue('s') && !(version_compare(_PS_VERSION_, '1.7', '>') && ($this->context->controller instanceof SearchController || $this->context->controller instanceof IqitSearchSearchiqitModuleFrontController))) {
				$sponsorship = new RewardsSponsorshipModel(RewardsSponsorshipModel::decodeSponsorshipMailLink(Tools::getValue('s')));
				if (Validate::isLoadedObject($sponsorship))
					$sponsor = new Customer($sponsorship->id_sponsor);
				else
					$sponsor = new Customer(RewardsSponsorshipModel::decodeSponsorshipLink(Tools::getValue('s')));
			} else if (($this->context->controller instanceof OrderOpcController || $this->context->controller instanceof OrderController || $this->context->controller instanceof CartController) && $sponsor_code=Tools::getValue('discount_name')) {
				$sponsor = new Customer();
				if (Validate::isEmail($sponsor_code))
					$sponsor = $sponsor->getByEmail($sponsor_code);
				else
					$sponsor = new Customer(RewardsSponsorshipModel::decodeSponsorshipLink($sponsor_code));
			}
			if (Validate::isLoadedObject($sponsor)) {
				$id_template = (int)MyConf::getIdTemplate('sponsorship', (int)$sponsor->id);
				if (MyConf::get('RSPONSORSHIP_USE_VOUCHER_FIELD', null, $id_template) && (int)MyConf::get('RSPONSORSHIP_MULTIPLE_SPONSOR', null, $id_template))
					return true;
			}

			// si le client est loggué on regarde si le parrainage est actif pour lui
			$id_template = (int)MyConf::getIdTemplate('sponsorship', $this->context->customer->id);
			return MyConf::get('RSPONSORSHIP_ACTIVE', null, $id_template);
		} else {
			// sinon, on teste si au moins un modèle est actif car dans ce cas il faut toujours afficher le champs de parrainage sur le formulaire d'inscription
			// et traiter les URL
			return Configuration::get('RSPONSORSHIP_ACTIVE') || MyConf::isActiveAtLeastOnce('RSPONSORSHIP_ACTIVE');
		}
	}

	public function isRewardsAccountVisible()
	{
		return $this->isActive() && RewardsSponsorshipModel::isCustomerAllowed($this->context->customer);
	}

	public function getTitle()
	{
		return $this->l('Sponsorship program');
	}

	public function getDetails($reward, $admin) {
		if (!$admin)
			$id_template = (int)MyConf::getIdTemplate('sponsorship', $this->context->customer->id);

		if ($row = RewardsSponsorshipModel::getRewardDetails($reward['id_reward'])) {
			if ($reward['id_order']) {
				if (!$admin) {
					if ($row['level_sponsorship'] == 1)
						return sprintf($this->l('Sponsorship - order from %s'), MyConf::get('RSPONSORSHIP_ANONYMIZE', null, $id_template) ? Tools::substr($row['order_firstname'], 0, 1).'*** '.Tools::substr($row['order_lastname'], 0, 1).'***' : $row['order_firstname'].' '.$row['order_lastname']);
					else
						return sprintf($this->l('Sponsorship - order from level %s'), $row['level_sponsorship']);
				} else {
					if (version_compare(_PS_VERSION_, '1.7', '>=')) {
						return sprintf($this->l('Sponsorship - order #%s from %s (level %d)'), '<a href="'.$this->context->link->getAdminLink('AdminOrders', true, [], ['id_order'=> $reward['id_order'], 'vieworder' => 1]).'" style="display: inline; width: auto">'.(empty($row['reference']) ? sprintf('%06d', $reward['id_order']) : $row['reference']).'</a>', '<a href="'.$this->context->link->getAdminLink('AdminCustomers', true, [], ['id_customer'=> $row['id_customer'], 'viewcustomer' => 1]).'">'.$row['order_firstname'].' '.$row['order_lastname'].'</a>', $row['level_sponsorship']);
					} else {
						$tokenCustomer = Tools::getAdminToken('AdminCustomers'.(int)Tab::getIdFromClassName('AdminCustomers').(int)$this->context->employee->id);
						return sprintf($this->l('Sponsorship - order #%s from %s (level %d)'), '<a href="'.$this->context->link->getAdminLink('AdminOrders').'&vieworder=1&id_order='.$reward['id_order'].'" style="display: inline; width: auto">'.(empty($row['reference']) ? sprintf('%06d', $reward['id_order']) : $row['reference']).'</a>', '<a href="?tab=AdminCustomers&id_customer='.$row['id_customer'].'&viewcustomer&token='.$tokenCustomer.'">'.$row['order_firstname'].' '.$row['order_lastname'].'</a>', $row['level_sponsorship']);
					}
				}
			} else {
				if (!$admin) {
					return sprintf($this->l('Sponsorship - registration from %s'), MyConf::get('RSPONSORSHIP_ANONYMIZE', null, $id_template) ? Tools::substr($row['firstname'], 0, 1).'*** '.Tools::substr($row['lastname'], 0, 1).'***' : $row['firstname'].' '.$row['lastname']);
				} else {
					if (version_compare(_PS_VERSION_, '1.7', '>=')) {
						return sprintf($this->l('Sponsorship - registration from %s'), '<a href="'.$this->context->link->getAdminLink('AdminCustomers', true, [], ['id_customer'=> $row['id_customer'], 'viewcustomer' => 1]).'">'.$row['firstname'].' '.$row['lastname'].'</a>');
					} else {
						$token = Tools::getAdminToken('AdminCustomers'.(int)Tab::getIdFromClassName('AdminCustomers').(int)$this->context->employee->id);
						return sprintf($this->l('Sponsorship - registration from %s'), '<a href="?tab=AdminCustomers&id_customer='.$row['id_customer'].'&viewcustomer&token='.$token.'">'.$row['firstname'].' '.$row['lastname'].'</a>');
					}
				}
			}
		} else
			return '';
	}

	public function duplicateReward($old_id, $id_reward) {
		return Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'rewards_sponsorship_detail` (`id_reward`, `id_sponsorship`, `level_sponsorship`)
			SELECT '.(int)$id_reward.', id_sponsorship, level_sponsorship
			FROM `'._DB_PREFIX_.'rewards_sponsorship_detail`
			WHERE id_reward='.(int)$old_id);
	}

	protected function postProcess($params=null)
	{
		// on initialise le template à chaque chargement
		$this->initTemplate();

		if (Tools::isSubmit('submitSponsorship')) {
			$this->_postValidation();
			if (!sizeof($this->_errors)) {
				if (empty($this->id_template)) {
					Configuration::updateValue('RSPONSORSHIP_GROUPS', implode(",", Tools::getValue('rsponsorship_groups')));
				}
				MyConf::updateValue('RSPONSORSHIP_UNLIMITED_LEVELS', (int)Tools::getValue('unlimited_levels') > 0 ? count(Tools::getValue('reward_type_s')) : 0, null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_REDIRECT', Tools::getValue('sponsorship_redirect'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_CHILD_GROUPS', implode(",", Tools::getValue('rsponsorship_child_groups', array())), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_CHILD_DEFAULT_GROUP', (int)Tools::getValue('rsponsorship_child_default_group'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_PRODUCT_SHARE', (int)Tools::getValue('sponsorship_product_share'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_AFTER_ORDER', (int)Tools::getValue('after_order'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_POPUP', (int)Tools::getValue('popup'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_POPUP_DELAY', (int)Tools::getValue('popup_delay'), null, $this->id_template);
				if (Tools::getValue('popup_reset'))
					MyConf::updateValue('RSPONSORSHIP_POPUP_KEY', Tools::passwdGen(), null, $this->id_template);

				MyConf::updateValue('RSPONSORSHIP_ACTIVE', (int)Tools::getValue('sponsorship_active'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_USE_VOUCHER_FIELD', (int)Tools::getValue('rsponsorship_use_voucher_field'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_MULTIPLE_SPONSOR', (int)Tools::getValue('rsponsorship_multiple_sponsor'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_ANONYMIZE', (int)Tools::getValue('rsponsorship_anonymize'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_REWARD_REGISTRATION', (int)Tools::getValue('reward_registration'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_REGISTR_MULTIPLE', implode(',', Tools::getValue('rsponsorship_registr_multiple')), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_REGISTR_REPEAT', implode(',', Tools::getValue('rsponsorship_registr_repeat')), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_REGISTR_VALUE', implode(',', Tools::getValue('rsponsorship_registr_value')), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_NB_FRIENDS', (int)Tools::getValue('nb_friends'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_ORDER_QUANTITY_S', (int)Tools::getValue('order_quantity_s'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_VOUCHER_DETAILS', Tools::getValue('description_gc'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_REAL_VOUCHER_GC', (int)Tools::getValue('real_voucher_gc'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_REAL_CODE_GC', Tools::getValue('real_code_gc'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_REAL_DESC_GC', Tools::getValue('real_description_gc'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_VOUCHER_PREFIX_GC', Tools::getValue('voucher_prefix_gc'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_VOUCHER_DURATION_GC', (int)Tools::getValue('voucher_duration_gc'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_ALL_CATEGORIES', (int)Tools::getValue('rsponsorship_all_categories'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_CATEGORIES_GC', Tools::getValue('categoryBox') ? implode(',', Tools::getValue('categoryBox')) : '', null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_CUMUL_GC', (int)Tools::getValue('cumulative_voucher_gc'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_VOUCHER_BEHAVIOR', (int)Tools::getValue('voucher_behavior_gc'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_QUANTITY_GC', (int)Tools::getValue('voucher_quantity_gc'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_MINIMAL_TAX_GC', (int)Tools::getValue('include_tax_gc'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_REWARD_ORDER', (int)Tools::getValue('reward_order'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_TAX', (int)Tools::getValue('rsponsorship_tax'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_DURATION', (int)Tools::getValue('rsponsorship_duration'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_ON_EVERY_ORDER', (int)Tools::getValue('reward_on_every_order'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_DISCOUNT_GC', (int)Tools::getValue('discount_gc'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_UNLOCK_SHIPPING', (int)Tools::getValue('unlock_shipping'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_DISCOUNT_TYPE_GC', (int)Tools::getValue('discount_type_gc'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_FREESHIPPING_GC', (int)Tools::getValue('freeshipping_gc'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_DISCOUNTED_ALLOWED', (int)Tools::getValue('rsponsorship_discounted_allowed'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_SHARE_IMAGE_URL', Tools::getValue('share_image_url'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_SHARE_TITLE', Tools::getValue('share_title'), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_SHARE_DESCRIPTION', Tools::getValue('share_description'), null, $this->id_template);

				$currencies = $this->instance->getCurrencies();
				foreach ($currencies as $currency) {
					MyConf::updateValue('RSPONSORSHIP_MINIMUM_VALUE_GC_'.$currency['id_currency'], (float)Tools::getValue('minimum_value_gc_'.$currency['id_currency']), null, $this->id_template);
					MyConf::updateValue('RSPONSORSHIP_UNLOCK_GC_'.$currency['id_currency'], (float)Tools::getValue('unlock_gc_'.$currency['id_currency']), null, $this->id_template);
					if ((int)Tools::getValue('discount_type_gc') == 0)
						MyConf::updateValue('RSPONSORSHIP_VOUCHER_VALUE_GC_'.$currency['id_currency'], 0, null, $this->id_template);
					else
						MyConf::updateValue('RSPONSORSHIP_VOUCHER_VALUE_GC_'.$currency['id_currency'], (float)Tools::getValue('discount_value_gc_'.$currency['id_currency']), null, $this->id_template);
				}
				// For levels
				MyConf::updateValue('RSPONSORSHIP_REWARD_TYPE_S', implode(',', Tools::getValue('reward_type_s')), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_REWARD_PERCENTAGE', implode(',', Tools::getValue('reward_percentage')), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_DEF_PRODUCT_REWARD', implode(',', Tools::getValue('rsponsorship_def_product_reward')), null, $this->id_template);
				MyConf::updateValue('RSPONSORSHIP_DEF_PRODUCT_TYPE', implode(',', Tools::getValue('rsponsorship_def_product_type')), null, $this->id_template);
				foreach (Tools::getValue('reward_value_s') as $id_currency => $reward_value_s) {
					if (is_array($reward_value_s)) {
						MyConf::updateValue('RSPONSORSHIP_REWARD_VALUE_S_'.(int)$id_currency, implode(",", $reward_value_s), null, $this->id_template);
					}
				}
				$this->instance->confirmation = $this->instance->displayConfirmation($this->l('Settings updated.'));
			} else
				$this->instance->errors =  $this->instance->displayError(implode('<br />', $this->_errors));
		} else if (Tools::isSubmit('submitSponsorshipNotifications')) {
			Configuration::updateValue('RSPONSORSHIP_MAIL_REGISTRATION', (int)Tools::getValue('mail_admin_registration'));
			Configuration::updateValue('RSPONSORSHIP_MAIL_ORDER', (int)Tools::getValue('mail_admin_order'));
			Configuration::updateValue('RSPONSORSHIP_MAIL_REGISTRATION_S', (int)Tools::getValue('mail_sponsor_registration'));
			Configuration::updateValue('RSPONSORSHIP_MAIL_ORDER_S', (int)Tools::getValue('mail_sponsor_order'));
			Configuration::updateValue('RSPONSORSHIP_MAIL_VALIDATION_S', (int)Tools::getValue('mail_sponsor_validation'));
			Configuration::updateValue('RSPONSORSHIP_MAIL_CANCELPROD_S', (int)Tools::getValue('mail_sponsor_cancel_product'));
			$this->instance->confirmation = $this->instance->displayConfirmation($this->l('Settings updated.'));
		} else if (Tools::isSubmit('submitSponsorshipText')) {
			MyConf::updateValue('RSPONSORSHIP_ACCOUNT_TXT', Tools::getValue('account_txt'), true, $this->id_template);
			MyConf::updateValue('RSPONSORSHIP_ORDER_TXT', Tools::getValue('order_txt'), true, $this->id_template);
			MyConf::updateValue('RSPONSORSHIP_POPUP_TXT', Tools::getValue('popup_txt'), true, $this->id_template);
			MyConf::updateValue('RSPONSORSHIP_RULES_TXT', Tools::getValue('rules_txt'), true, $this->id_template);
			$this->instance->confirmation = $this->instance->displayConfirmation($this->l('Settings updated.'));
		} else if (Tools::isSubmit('submitSponsorCustomCode')) {
			$this->_postValidation($params);
			if (!sizeof($this->_errors)) {
				$reward_sponsorship_code = new RewardsSponsorshipCodeModel((int)$params['id_customer']);
				if (Tools::getValue('sponsorship_custom_code') || Validate::isLoadedObject($reward_sponsorship_code)) {
					if (Tools::getValue('sponsorship_custom_code')) {
						$reward_sponsorship_code->id_sponsor = (int)$params['id_customer'];
						$reward_sponsorship_code->code = Tools::getValue('sponsorship_custom_code');
						$reward_sponsorship_code->save();
					} else
						$reward_sponsorship_code->delete();
					return $this->instance->displayConfirmation($this->l('The sponsor code has been updated.'));
				}
			} else
				return $this->instance->displayError(implode('<br />', $this->_errors));
		} else if (Tools::isSubmit('submitSponsor') && (int)Tools::getValue('new_sponsor')) {
			$sponsor = null;
			if ($id_sponsorship = RewardsSponsorshipModel::isSponsorised((int)$params['id_customer'], true)) {
				$sponsorship = new RewardsSponsorshipModel((int)$id_sponsorship);
				$sponsor = new Customer((int)$sponsorship->id_sponsor);
			}
			$customer = new Customer((int)$params['id_customer']);
			$new_sponsor = new Customer((int)Tools::getValue('new_sponsor'));
			if (Validate::isLoadedObject($new_sponsor)) {
				if ((!isset($sponsor) || $new_sponsor->id != $sponsor->id) && $this->_createSponsorship($new_sponsor, $customer, true, (bool)Tools::getValue('generate_voucher'), (int)Tools::getValue('generate_currency')))
					return $this->instance->displayConfirmation($this->l('The sponsor has been updated.'));
				else
					return $this->instance->displayError($this->l('The sponsor update failed.'));
			}
		} else if (Tools::isSubmit('submitSponsorshipEndDate') && (int)Tools::getValue('id_sponsorship_to_update')) {
			$this->_postValidation();
			if (!sizeof($this->_errors)) {
				$sponsorship = new RewardsSponsorshipModel((int)Tools::getValue('id_sponsorship_to_update'));
				$sponsorship->date_end = Tools::getValue('date_end_' . Tools::getValue('id_sponsorship_to_update'));
				$sponsorship->save();
				return $this->instance->displayConfirmation($this->l('The sponsorship end date has been updated.'));
			} else
				return $this->instance->displayError(implode('<br />', $this->_errors));
		} else if (Tools::getValue('action') == 'sponsorship_template') {
			$id_old_template = (int)MyConf::getIdTemplate('sponsorship', (int)$params['id_customer']);
			RewardsTemplateModel::deleteCustomer($id_old_template, (int)$params['id_customer']);
			if (Tools::getValue('sponsorship_template'))
				RewardsTemplateModel::addCustomer((int)Tools::getValue('sponsorship_template'), (int)$params['id_customer']);
			return $this->instance->displayConfirmation($this->l('The template has been updated.'));
		}
	}

	private function _postValidation($params=null)
	{
		if (Tools::isSubmit('submitSponsorship')) {
			$currencies = $this->instance->getCurrencies();

			if (Tools::getValue('popup') && (!is_numeric(Tools::getValue('popup_delay')) || Tools::getValue('popup_delay') <= 0))
				$this->_errors[] = $this->l('The number of days before opening the popup again, is invalid.');
			if (Tools::getValue('share_image_url') && !Validate::isAbsoluteUrl(Tools::getValue('share_image_url')))
				$this->_errors[] = $this->l('The url to force for Facebook share is invalid.');
			if (!is_numeric(Tools::getValue('order_quantity_s')) || Tools::getValue('order_quantity_s') < 0)
				$this->_errors[] = $this->l('The number of orders to be able to become a sponsor is invalid.');
			if (!is_numeric(Tools::getValue('nb_friends')) || Tools::getValue('nb_friends') <= 0)
				$this->_errors[] = $this->l('The number of lines displayed in the invitation form is required/invalid.');
			if (empty($this->id_template) && !is_array(Tools::getValue('rsponsorship_groups')))
				$this->_errors[] = $this->l('Please select at least 1 customer group allowed to sponsor its friends');
			if (Tools::getValue('rsponsorship_child_default_group') && (!is_array(Tools::getValue('rsponsorship_child_groups')) || !in_array(Tools::getValue('rsponsorship_child_default_group'), Tools::getValue('rsponsorship_child_groups'))))
				$this->_errors[] = $this->l('The customer\'s default group must be selected in the groups to add');

			if (Tools::getValue('reward_registration')) {
				$sponsorship_registr_multiple = Tools::getValue('rsponsorship_registr_multiple');
				$sponsorship_registr_repeat = Tools::getValue('rsponsorship_registr_repeat');
				$sponsorship_registr_value = Tools::getValue('rsponsorship_registr_value');
				foreach($sponsorship_registr_multiple as $key => $value) {
					if (!Validate::isUnsignedInt($sponsorship_registr_multiple[$key]) || $sponsorship_registr_multiple[$key] <= 0)
						$this->_errors[] = sprintf($this->l('The number of friends registrations is required/invalid for rule #%s.'), $key+1);
					if (!Validate::isUnsignedInt($sponsorship_registr_repeat[$key]))
						$this->_errors[] = sprintf($this->l('The repeat value is required/invalid for rule #%s.'), $key+1);
					if (!Validate::isUnsignedFloat($sponsorship_registr_value[$key]) || $sponsorship_registr_value[$key] <= 0)
						$this->_errors[] = sprintf($this->l('The reward value is required/invalid for rule #%s.'), $key+1);
				}
			}

			if(Tools::getValue('reward_order')) {
				$reward_type_s = Tools::getValue('reward_type_s');
				if (!is_numeric(Tools::getValue('rsponsorship_duration')) || Tools::getValue('rsponsorship_duration') < 0)
					$this->_errors[] = $this->l('The duration of the sponsorship is required/invalid.');
				foreach (Tools::getValue('reward_percentage') as $level => $reward_percentage) {
					if ($reward_type_s[$level] == 2 && !Validate::isUnsignedFloat($reward_percentage))
						$this->_errors[] = $this->l('The percentage of the sponsored\'s order is invalid for level').' '.($level+1);
				}
				foreach (Tools::getValue('rsponsorship_def_product_reward') as $level => $reward_product) {
					if ($reward_type_s[$level] == 3 && !Validate::isUnsignedFloat($reward_product))
						$this->_errors[] = $this->l('The default reward is invalid for level').' '.($level+1);
				}
				$reward_value_s = Tools::getValue('reward_value_s');
				foreach ($currencies as $currency) {
					if (Tools::getValue('unlock_gc_'.$currency['id_currency'])!='' && !Validate::isUnsignedFloat(Tools::getValue('unlock_gc_'.$currency['id_currency'])))
						$this->_errors[] = $this->l('Minimum unlock amount for the currency').' '.$currency['name'].' '.$this->l('is invalid.');

					if (is_array($reward_value_s[$currency['id_currency']])) {
						foreach($reward_value_s[$currency['id_currency']] as $level => $value) {
							if ($reward_type_s[$level] == 1) {
								if (empty($value))
									$this->_errors[] = $this->l('Reward amount for the level').' '.($level+1).' '.$this->l('and the currency').' '.$currency['name'].' '.$this->l('is empty.');
								elseif (!Validate::isUnsignedFloat($value))
									$this->_errors[] = $this->l('Reward amount for the level').' '.($level+1).' '.$this->l('and the currency').' '.$currency['name'].' '.$this->l('is invalid.');
							}
						}
					}
				}
			}
			if (Tools::getValue('discount_gc')) {
				if (Tools::getValue('voucher_prefix_gc') != '' && !Validate::isCleanHtml(Tools::getValue('voucher_prefix_gc')))
					$this->_errors[] = $this->l('Prefix for the voucher code is invalid.');
				if (!is_numeric(Tools::getValue('voucher_duration_gc')) || (int)Tools::getValue('voucher_duration_gc') <= 0)
					$this->_errors[] = $this->l('The validity of the voucher is required/invalid.');
				if (!Tools::getValue('real_voucher_gc')) {
					foreach ($currencies as $currency) {
						if ((int)Tools::getValue('discount_type_gc') != 0) {
							if (Tools::getValue('discount_value_gc_'.$currency['id_currency'])=='')
								$this->_errors[] = $this->l('Voucher value for the currency').' '.$currency['name'].' '.$this->l('is empty.');
							elseif (!Tools::getValue('discount_value_gc_'.$currency['id_currency']) || !Validate::isUnsignedFloat(Tools::getValue('discount_value_gc_'.$currency['id_currency'])))
								$this->_errors[] = $this->l('Voucher value for the currency').' '.$currency['name'].' '.$this->l('is invalid.');
							if (Tools::getValue('minimum_value_gc_'.$currency['id_currency'])!='' && !Validate::isUnsignedFloat(Tools::getValue('minimum_value_gc_'.$currency['id_currency'])))
								$this->_errors[] = $this->l('Minimum order amount for the currency').' '.$currency['name'].' '.$this->l('is invalid.');
						}
					}
					foreach (Tools::getValue('description_gc') as $id_language => $description) {
						$lang = Language::getLanguage($id_language);
						if (empty($description))
							$this->_errors[] = $this->l('Voucher description is required for').' '.$lang['name'];
					}
					if (!is_numeric(Tools::getValue('voucher_quantity_gc')) || (int)Tools::getValue('voucher_quantity_gc') <= 0)
						$this->_errors[] = $this->l('The number of times the voucher can be used is required/invalid.');
					if (!Tools::getValue('rsponsorship_all_categories') && (!is_array(Tools::getValue('categoryBox')) || !sizeof(Tools::getValue('categoryBox'))))
						$this->_errors[] = $this->l('You must choose at least one category of products');
					if ((int)Tools::getValue('discount_type_gc')==0 && (int)Tools::getValue('freeshipping_gc')==0)
						$this->_errors[] = $this->l('You must offer at least free shipping or/and discount.');
				} else {
					if (!Tools::getValue('real_code_gc') || !Validate::isCleanHtml(Tools::getValue('real_code_gc')))
						$this->_errors[] = $this->l('Code of the existing voucher is required/invalid.');
					else {
						$cart_rule = new CartRule((int)CartRule::getIdByCode(Tools::getValue('real_code_gc')));
						if (!Validate::isLoadedObject($cart_rule))
							$this->_errors[] = $this->l('That voucher doesn\'t exist.');
						else {
							if ($cart_rule->id_customer)
								$this->_errors[] = $this->l('That voucher is not valid because it\'s linked to a customer.');
							if ($cart_rule->highlight)
								$this->_errors[] = $this->l('That voucher is not valid because it\'s highlighted.');
							if ((int)$cart_rule->quantity < 1 || (int)$cart_rule->quantity_per_user < 1)
								$this->_errors[] = $this->l('That voucher is not valid because its quantity or its quantity per user is lower than 1.');
							if ($cart_rule->date_to <= date('Y-m-d :h:i:s'))
								$this->_errors[] = $this->l('That voucher is not valid because its end date is not valid.');
						}
					}
					foreach (Tools::getValue('real_description_gc') as $id_language => $description) {
						$lang = Language::getLanguage($id_language);
						if (empty($description))
							$this->_errors[] = $this->l('Voucher description is required for').' '.$lang['name'];
					}

				}
			}
		} else if (Tools::isSubmit('submitSponsorshipEndDate')) {
			if (Tools::getValue('date_end_' . Tools::getValue('id_sponsorship_to_update')) && !Validate::isDate(Tools::getValue('date_end_' . Tools::getValue('id_sponsorship_to_update'))))
				$this->_errors[] = $this->l('The date is invalid.');
		} else if (Tools::isSubmit('submitSponsorCustomCode') && Tools::getValue('sponsorship_custom_code')) {
			$id_sponsor = RewardsSponsorshipCodeModel::getIdSponsorByCode(Tools::getValue('sponsorship_custom_code'));
			if (!ctype_alnum(Tools::getValue('sponsorship_custom_code')) || Tools::strlen(Tools::getValue('sponsorship_custom_code')) < 5 || Tools::strlen(Tools::getValue('sponsorship_custom_code')) > 20) {
				$this->_errors[] = $this->l('The sponsor code is not valid, it must contain only digits or letters and length must be between 5 and 20 characters.');
			} else if ($id_sponsor && $id_sponsor != (int)$params['id_customer']) {
				$this->_errors[] = $this->l('The sponsor code is not valid, it already exists for another customer.');
			}
		}
	}

	public function displayForm() {
		if (Tools::getValue('stats')) {
			$id_sponsor = Tools::getValue('id_sponsor');
			return $this->_getStatistics(empty($id_sponsor) ? null : $id_sponsor);
		}

		$this->postProcess();
		$this->_initConf($this->id_template);

		$currencies = $this->instance->getCurrencies();
		$unlock_gc = array();
		$discount_value_gc = array();
		$minimum_value_gc = array();
		foreach($currencies as $currency) {
			$unlock_gc[$currency['id_currency']] = (float)Tools::getValue('unlock_gc_'.$currency['id_currency'], (float)MyConf::get('RSPONSORSHIP_UNLOCK_GC_'.$currency['id_currency'], null, $this->id_template));
			$discount_value_gc[$currency['id_currency']] = (float)Tools::getValue('discount_value_gc_'.$currency['id_currency'], (float)MyConf::get('RSPONSORSHIP_VOUCHER_VALUE_GC_'.$currency['id_currency'], null, $this->id_template));
			$minimum_value_gc[$currency['id_currency']] = (float)Tools::getValue('minimum_value_gc_'.$currency['id_currency'], (float)MyConf::get('RSPONSORSHIP_MINIMUM_VALUE_GC_'.$currency['id_currency'], null, $this->id_template));
		}

		$languages = Language::getLanguages();
		$share_title = Tools::getValue('share_title', array());
		$share_description = Tools::getValue('share_description', array());
		$description_gc = Tools::getValue('description_gc', array());
		$real_description_gc = Tools::getValue('real_description_gc', array());
		$account_txt = Tools::getValue('account_txt', array());
		$order_txt = Tools::getValue('order_txt', array());
		$popup_txt = Tools::getValue('popup_txt', array());
		$rules_txt = Tools::getValue('rules_txt', array());
		foreach($languages as $language) {
			if (!isset($share_title[$language['id_lang']]))
				$share_title[$language['id_lang']] = MyConf::get('RSPONSORSHIP_SHARE_TITLE', $language['id_lang'], $this->id_template);
			if (!isset($share_description[$language['id_lang']]))
				$share_description[$language['id_lang']] = MyConf::get('RSPONSORSHIP_SHARE_DESCRIPTION', $language['id_lang'], $this->id_template);
			if (!isset($description_gc[$language['id_lang']]))
				$description_gc[$language['id_lang']] = MyConf::get('RSPONSORSHIP_VOUCHER_DETAILS', $language['id_lang'], $this->id_template);
			if (!isset($real_description_gc[$language['id_lang']]))
				$real_description_gc[$language['id_lang']] = MyConf::get('RSPONSORSHIP_REAL_DESC_GC', (int)$language['id_lang'], $this->id_template);
			if (!isset($account_txt[$language['id_lang']]))
				$account_txt[$language['id_lang']] = MyConf::get('RSPONSORSHIP_ACCOUNT_TXT', (int)$language['id_lang'], $this->id_template);
			if (!isset($order_txt[$language['id_lang']]))
				$order_txt[$language['id_lang']] = MyConf::get('RSPONSORSHIP_ORDER_TXT', (int)$language['id_lang'], $this->id_template);
			if (!isset($popup_txt[$language['id_lang']]))
				$popup_txt[$language['id_lang']] = MyConf::get('RSPONSORSHIP_POPUP_TXT', (int)$language['id_lang'], $this->id_template);
			if (!isset($rules_txt[$language['id_lang']]))
				$rules_txt[$language['id_lang']] = MyConf::get('RSPONSORSHIP_RULES_TXT', (int)$language['id_lang'], $this->id_template);
		}

		$real_code_gc = Tools::getValue('real_code_gc', MyConf::get('RSPONSORSHIP_REAL_CODE_GC', null, $this->id_template));

		$this->context->smarty->assign(array(
			'module' => $this->instance,
			'object' => $this,
			'currencies' => $currencies,
			'currency' => new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT')),
			'languages' => $languages,
			'current_language_id' => $this->context->language->id,
			'groups' => Group::getGroups((int)$this->context->language->id),
			'groups_off' => array(Configuration::get('PS_UNIDENTIFIED_GROUP'), Configuration::get('PS_GUEST_GROUP')),
			'allowed_groups' => Tools::getValue('rsponsorship_groups', explode(',', Configuration::get('RSPONSORSHIP_GROUPS'))),
			'child_groups' => Tools::getValue('rsponsorship_child_groups', explode(',', MyConf::get('RSPONSORSHIP_CHILD_GROUPS', null, $this->id_template))),
			'customer_group' => (int)Configuration::get('PS_CUSTOMER_GROUP'),
			'sponsorship_active' => (int)Tools::getValue('sponsorship_active', MyConf::get('RSPONSORSHIP_ACTIVE', null, $this->id_template)),
			'rsponsorship_use_voucher_field' => (int)Tools::getValue('rsponsorship_use_voucher_field', MyConf::get('RSPONSORSHIP_USE_VOUCHER_FIELD', null, $this->id_template)),
			'rsponsorship_multiple_sponsor' => (int)Tools::getValue('rsponsorship_multiple_sponsor', MyConf::get('RSPONSORSHIP_MULTIPLE_SPONSOR', null, $this->id_template)),
			'rsponsorship_anonymize' => (int)Tools::getValue('rsponsorship_anonymize', MyConf::get('RSPONSORSHIP_ANONYMIZE', null, $this->id_template)),
			'reward_registration' => (int)Tools::getValue('reward_registration', MyConf::get('RSPONSORSHIP_REWARD_REGISTRATION', null, $this->id_template)),
			'reward_order' => (int)Tools::getValue('reward_order', MyConf::get('RSPONSORSHIP_REWARD_ORDER', null, $this->id_template)),
			'discount_gc' => (int)Tools::getValue('discount_gc', MyConf::get('RSPONSORSHIP_DISCOUNT_GC', null, $this->id_template)),
			'sponsorship_product_share' => (int)Tools::getValue('sponsorship_product_share', MyConf::get('RSPONSORSHIP_PRODUCT_SHARE', null, $this->id_template)),
			'after_order' => (int)Tools::getValue('after_order', MyConf::get('RSPONSORSHIP_AFTER_ORDER', null, $this->id_template)),
			'popup' => (int)Tools::getValue('popup', MyConf::get('RSPONSORSHIP_POPUP', null, $this->id_template)),
			'popup_delay' => (int)Tools::getValue('popup_delay', MyConf::get('RSPONSORSHIP_POPUP_DELAY', null, $this->id_template)),
			'share_title' => $share_title,
			'share_description' => $share_description,
			'share_image_url' => Tools::getValue('share_image_url', MyConf::get('RSPONSORSHIP_SHARE_IMAGE_URL', null, $this->id_template)),
			'share_url' => $this->context->link->getPageLink('index', true, $this->context->language->id, 's='.Tools::passwdGen(6)),
			'nb_friends' => (int)Tools::getValue('nb_friends', MyConf::get('RSPONSORSHIP_NB_FRIENDS', null, $this->id_template)),
			'order_quantity_s' => (int)Tools::getValue('order_quantity_s', MyConf::get('RSPONSORSHIP_ORDER_QUANTITY_S', null, $this->id_template)),
			'sponsorship_redirect' => Tools::getValue('sponsorship_redirect', MyConf::get('RSPONSORSHIP_REDIRECT', null, $this->id_template)),
			'cms_list' => CMS::listCms($this->context->language->id),
			'rsponsorship_child_default_group' => Tools::getValue('rsponsorship_child_default_group', MyConf::get('RSPONSORSHIP_CHILD_DEFAULT_GROUP', null, $this->id_template)),
			'sponsorship_registr_multiple' => Tools::getValue('rsponsorship_registr_multiple', explode(',', MyConf::get('RSPONSORSHIP_REGISTR_MULTIPLE', null, $this->id_template))),
			'sponsorship_registr_repeat' => Tools::getValue('rsponsorship_registr_repeat', explode(',', MyConf::get('RSPONSORSHIP_REGISTR_REPEAT', null, $this->id_template))),
			'sponsorship_registr_value' => Tools::getValue('rsponsorship_registr_value', explode(',', MyConf::get('RSPONSORSHIP_REGISTR_VALUE', null, $this->id_template))),
			'rsponsorship_duration' => (int)Tools::getValue('rsponsorship_duration', MyConf::get('RSPONSORSHIP_DURATION', null, $this->id_template)),
			'reward_on_every_order' => (int)Tools::getValue('reward_on_every_order', MyConf::get('RSPONSORSHIP_ON_EVERY_ORDER', null, $this->id_template)),
			'rsponsorship_discounted_allowed' => (int)Tools::getValue('rsponsorship_discounted_allowed', MyConf::get('RSPONSORSHIP_DISCOUNTED_ALLOWED', null, $this->id_template)),
			'rsponsorship_tax' => (int)Tools::getValue('rsponsorship_tax', MyConf::get('RSPONSORSHIP_TAX', null, $this->id_template)),
			'unlock_shipping' => (int)Tools::getValue('unlock_shipping', MyConf::get('RSPONSORSHIP_UNLOCK_SHIPPING', null, $this->id_template)),
			'unlock_gc' => $unlock_gc,
			'configuration' => $this->_configuration,
			'unlimited_levels' => (int)Tools::getValue('unlimited_levels', MyConf::get('RSPONSORSHIP_UNLIMITED_LEVELS', null, $this->id_template)),
			'real_voucher_gc' => (int)Tools::getValue('real_voucher_gc', MyConf::get('RSPONSORSHIP_REAL_VOUCHER_GC', null, $this->id_template)),
			'voucher_prefix_gc' => Tools::getValue('voucher_prefix_gc', MyConf::get('RSPONSORSHIP_VOUCHER_PREFIX_GC', null, $this->id_template)),
			'voucher_duration_gc' => (int)Tools::getValue('voucher_duration_gc', MyConf::get('RSPONSORSHIP_VOUCHER_DURATION_GC', null, $this->id_template)),
			'description_gc' => $description_gc,
			'real_code_gc' => $real_code_gc,
			'cart_rule' => !empty($real_code_gc) ? new CartRule((int)CartRule::getIdByCode(Tools::getValue('real_code_gc', MyConf::get('RSPONSORSHIP_REAL_CODE_GC', null, $this->id_template)))) : null,
			'token' => Tools::getAdminToken('AdminCartRules'.(int)Tab::getIdFromClassName('AdminCartRules').(int)$this->context->employee->id),
			'real_description_gc' => $real_description_gc,
			'voucher_quantity_gc' => (int)Tools::getValue('voucher_quantity_gc', MyConf::get('RSPONSORSHIP_QUANTITY_GC', null, $this->id_template)),
			'freeshipping_gc' => (int)Tools::getValue('freeshipping_gc', MyConf::get('RSPONSORSHIP_FREESHIPPING_GC', null, $this->id_template)),
			'discount_type_gc' => (int)Tools::getValue('discount_type_gc', MyConf::get('RSPONSORSHIP_DISCOUNT_TYPE_GC', null, $this->id_template)),
			'rsponsorship_all_categories' => (int)Tools::getValue('rsponsorship_all_categories', MyConf::get('RSPONSORSHIP_ALL_CATEGORIES', null, $this->id_template)),
			'categories' => $this->getCategoriesTree(Tools::getValue('categoryBox', explode(',', MyConf::get('RSPONSORSHIP_CATEGORIES_GC', null, $this->id_template)))),
			'voucher_behavior_gc' => (int)Tools::getValue('voucher_behavior_gc', (int)MyConf::get('RSPONSORSHIP_VOUCHER_BEHAVIOR', null, $this->id_template)),
			'cumulative_voucher_gc' => (int)Tools::getValue('cumulative_voucher_gc', MyConf::get('RSPONSORSHIP_CUMUL_GC', null, $this->id_template)),
			'include_tax_gc' => (int)Tools::getValue('include_tax_gc', MyConf::get('RSPONSORSHIP_MINIMAL_TAX_GC', null, $this->id_template)),
			'discount_value_gc' => $discount_value_gc,
			'minimum_value_gc' => $minimum_value_gc,
			'mail_admin_registration' => (int)Tools::getValue('mail_admin_registration', Configuration::get('RSPONSORSHIP_MAIL_REGISTRATION')),
			'mail_admin_order' => (int)Tools::getValue('mail_admin_order', Configuration::get('RSPONSORSHIP_MAIL_ORDER')),
			'mail_sponsor_registration' => (int)Tools::getValue('mail_sponsor_registration', Configuration::get('RSPONSORSHIP_MAIL_REGISTRATION_S')),
			'mail_sponsor_order' => (int)Tools::getValue('mail_sponsor_order', Configuration::get('RSPONSORSHIP_MAIL_ORDER_S')),
			'mail_sponsor_validation' => (int)Tools::getValue('mail_sponsor_validation', Configuration::get('RSPONSORSHIP_MAIL_VALIDATION_S')),
			'mail_sponsor_cancel_product' => (int)Tools::getValue('mail_sponsor_cancel_product', Configuration::get('RSPONSORSHIP_MAIL_CANCELPROD_S')),
			'account_txt' => $account_txt,
			'order_txt' => $order_txt,
			'popup_txt' => $popup_txt,
			'rules_txt' => $rules_txt,
		));
		return $this->getTemplateForm($this->l('Sponsorship')).$this->instance->display($this->instance->path, 'views/templates/admin/admin-sponsorship.tpl');
	}

	private function _getStatistics($id_sponsor=null)
	{
		$this->context->smarty->assign(array(
			'module' => $this->instance,
			'object' => $this,
			'token' => Tools::getAdminToken('AdminCustomers'.(int)Tab::getIdFromClassName('AdminCustomers').(int)$this->context->employee->id),
			'stats' => RewardsSponsorshipModel::getAdminStatistics(),
			'id_sponsor' => $id_sponsor,
		));
		return $this->instance->display($this->instance->path, 'views/templates/admin/admin-sponsorship-statistics.tpl');
	}

	// Return the reward calculated from a price in a specific currency, and converted in the 2nd currency
	private function _getNbCreditsByPrice($price, $idCurrencyFrom, $idCurrencyTo = NULL, $extraParams = array())
	{
		if (!isset($idCurrencyTo))
			$idCurrencyTo = $idCurrencyFrom;

		// for a fixed reward, special offers are always taken in account
		if (Configuration::get('PS_CURRENCY_DEFAULT') != $idCurrencyFrom)
		{
			// convert from customer's currency to default currency
			$price = (float)Tools::convertPrice($price, Currency::getCurrency($idCurrencyFrom), false);
		}
		if ($price > 0) {
			if ((int)$extraParams['type'] == 1) {
				$credits = (float)number_format($extraParams['value'], 2, '.', '');
				// convert from customer's currency to default currency
				$credits = round(Tools::convertPrice($credits, Currency::getCurrency($idCurrencyFrom), false), 2);
			} else {
				$credits = (float)number_format($price, 2, '.', '') * (float)$extraParams['value'] / 100;
			}
			return round(Tools::convertPrice($credits, Currency::getCurrency($idCurrencyTo)), 2);
		} else {
			return 0;
		}
	}

	private function _checkSponsorship()
	{
		// check if a sponsorship link has been clicked, create the cookie if needed and redirect the customer
		if (Tools::getValue('s') && !(version_compare(_PS_VERSION_, '1.7', '>') && ($this->context->controller instanceof SearchController || $this->context->controller instanceof IqitSearchSearchiqitModuleFrontController))) {
			$sponsor = null;
			$id_template = 0;
			$sponsorship = new RewardsSponsorshipModel(RewardsSponsorshipModel::decodeSponsorshipMailLink(Tools::getValue('s')));
			if (Validate::isLoadedObject($sponsorship))
				$sponsor = new Customer($sponsorship->id_sponsor);
			else
				$sponsor = new Customer(RewardsSponsorshipModel::decodeSponsorshipLink(Tools::getValue('s')));
			if (Validate::isLoadedObject($sponsor) && RewardsSponsorshipModel::isCustomerAllowed($sponsor, true)) {
				$this->context->cookie->rewards_sponsor_id = $sponsor->id;
				$this->context->cookie->rewards_sponsor_channel = (Tools::getValue('c') && is_numeric(Tools::getValue('c'))) ? Tools::getValue('c') : 2;
				$this->context->cookie->rewards_sponsorship_id = Validate::isLoadedObject($sponsorship) ? $sponsorship->id : '';

				$id_template = (int)MyConf::getIdTemplate('sponsorship', $sponsor->id);
				if (MyConf::get('RSPONSORSHIP_REDIRECT', null, $id_template) != 'home' && $this->context->controller instanceof IndexController) {
					if (MyConf::get('RSPONSORSHIP_REDIRECT', null, $id_template) == 'form')
						Tools::redirect('index.php?controller=authentication&create_account=1&s='.Tools::getValue('s'));
					else {
						$link = $this->context->link->getCMSLink(MyConf::get('RSPONSORSHIP_REDIRECT', null, $id_template));
						$link .= strpos($link, '?') !== false ? '&s='.Tools::getValue('s') : '?s='.Tools::getValue('s');
						Tools::redirect($link);
					}
				}
			} else {
				// if the s parameter was wrong, we need to remove the optional saved sponsorship
				unset($this->context->cookie->rewards_sponsor_id);
				unset($this->context->cookie->rewards_sponsorship_id);
			}
		}

		// check if the sponsor in cookie is still authorized to sponsor, update sponsorship if needed (sponsorship after registration, or multiple sponsorship)
		if (!empty($this->context->cookie->rewards_sponsor_id)) {
			$sponsor = new Customer($this->context->cookie->rewards_sponsor_id);
			if (!RewardsSponsorshipModel::isCustomerAllowed($sponsor, true) || RewardsSponsorshipModel::isDescendant($this->context->customer->id, $sponsor->id)) {
				unset($this->context->cookie->rewards_sponsor_id);
				unset($this->context->cookie->rewards_sponsorship_id);
			} else if ($this->context->customer->isLogged()) {
				$id_template = (int)MyConf::getIdTemplate('sponsorship', $sponsor->id);
				$is_multiple_authorized = (int)MyConf::get('RSPONSORSHIP_MULTIPLE_SPONSOR', null, $id_template);
				$nb_orders = (int)Db::getInstance()->getValue('SELECT COUNT(`id_order`) AS nb_orders FROM `'._DB_PREFIX_.'orders` WHERE `id_customer`='.(int)$this->context->customer->id);
				$current_sponsor = RewardsSponsorshipModel::getSponsorId($this->context->customer->id);
				if ((!$current_sponsor && !$nb_orders) || ($current_sponsor != $sponsor->id && $is_multiple_authorized))
					$this->_createSponsorship($sponsor, $this->context->customer);
				else {
					unset($this->context->cookie->rewards_sponsor_id);
					unset($this->context->cookie->rewards_sponsorship_id);
				}
			}
		}

		// check if a sponsor code has been entered in the voucher field from the cart summary
		if (($this->context->controller instanceof OrderOpcController || $this->context->controller instanceof OrderController || $this->context->controller instanceof CartController) && $sponsor_code=Tools::getValue('discount_name')) {
			$sponsor = new Customer();
			if (Validate::isEmail($sponsor_code))
				$sponsor = $sponsor->getByEmail($sponsor_code);
			else
				$sponsor = new Customer(RewardsSponsorshipModel::decodeSponsorshipLink($sponsor_code));

			if (Validate::isLoadedObject($sponsor)) {
				$valid = false;
				$id_template = (int)MyConf::getIdTemplate('sponsorship', (int)$sponsor->id);
				if (MyConf::get('RSPONSORSHIP_USE_VOUCHER_FIELD', null, $id_template)) {
					if (RewardsSponsorshipModel::isCustomerAllowed($sponsor) && !RewardsSponsorshipModel::isDescendant($this->context->customer->id, $sponsor->id)) {
						$this->context->cookie->rewards_sponsor_channel = 2;
						if (!$this->context->customer->isLogged()) {
							$this->context->cookie->rewards_sponsor_id = $sponsor->id;
							$valid = true;
						} else {
							$is_multiple_authorized = (int)MyConf::get('RSPONSORSHIP_MULTIPLE_SPONSOR', null, $id_template);
							$nb_orders = (int)Db::getInstance()->getValue('SELECT COUNT(`id_order`) AS nb_orders FROM `'._DB_PREFIX_.'orders` WHERE `id_customer`='.(int)$this->context->customer->id);
							$current_sponsor = RewardsSponsorshipModel::getSponsorId($this->context->customer->id);
							if ((!$current_sponsor && !$nb_orders) || ($current_sponsor != $sponsor->id && $is_multiple_authorized)) {
								if ($this->_createSponsorship($sponsor, $this->context->customer))
									$valid = true;
							}
						}
					}
					if (version_compare(_PS_VERSION_, '1.7', '>=')) {
						if (!$valid) {
							$error_txt = $this->l('This sponsor is not valid');
							if ((int)$current_sponsor==$sponsor->id)
								$error_txt = $this->l('Your sponsorship has already been taken in account');
						} else {
							$error_txt = '[AIOR]'.$this->l('Your sponsorship has been taken in account.');
							if (MyConf::get('RSPONSORSHIP_DISCOUNT_GC', null, $id_template))
								$error_txt .= ' '.$this->l('The voucher will be applied to the cart after registration.');
						}

						$result = json_encode([
			                'hasError' => $this->context->customer->isLogged() && $valid ? false : true,
			                'errors' => [$error_txt],
			                'quantity' => null,
			            ]);
			            echo $result;
			            die();
			        } else
			        	$this->context->controller->errors = [$valid ? $this->l('Your sponsorship has been taken in account.') : $this->l('This sponsor is not valid')];
				}
			}
		}
	}

	public function hookDisplayHeader()
	{
		// check for the sponsor
		$this->_checkSponsorship();

		if (version_compare(_PS_VERSION_, '1.7', '<') && $this->context->controller instanceof AuthController && !empty($this->context->cookie->rewards_sponsorship_id)) {
			$sponsorship = new RewardsSponsorshipModel($this->context->cookie->rewards_sponsorship_id);
			if (Validate::isLoadedObject($sponsorship)) {
				// hack for display sponsorship information in form
				if (!Tools::isSubmit('submitCreate')) {
					$_POST['customer_firstname'] = $sponsorship->firstname;
					$_POST['firstname'] = $sponsorship->firstname;
					$_POST['customer_lastname'] = $sponsorship->lastname;
					$_POST['lastname'] = $sponsorship->lastname;
					$_POST['email'] = $sponsorship->email;
					$_POST['email_create'] = $sponsorship->email;
				}
				$_POST['sponsorship_invisible'] = '1';
			}
		}

		// add css and js for the sponsorship form and popup
		if (RewardsSponsorshipModel::isCustomerAllowed($this->context->customer, true)) {
			if (version_compare(_PS_VERSION_, '1.7', '>=')) {
				Media::addJsDef(array(
					'msg' => Tools::htmlentitiesUTF8($this->l('You must agree to the terms of service before continuing.')),
					'url_allinone_sponsorship' => $this->context->link->getModuleLink('allinone_rewards', 'sponsorship', array(), true)
				));
			}

			$this->context->controller->addjqueryPlugin('fancybox');
			$this->context->controller->addJS($this->instance->getPath().'js/clipboard/clipboard.min.js');
			if (version_compare(_PS_VERSION_, '1.5.5.0', '<'))
				$this->context->controller->addJS($this->instance->getPath().'js/sponsorship-before-1550.js');
			else
				$this->context->controller->addJS($this->instance->getPath().'js/sponsorship.js');
		}


		// add js to detect the sponsorship message displayed in the promo code block and change its color
		if (Configuration::get('RSPONSORSHIP_USE_VOUCHER_FIELD') && ($this->context->controller instanceof OrderOpcController || $this->context->controller instanceof OrderController || $this->context->controller instanceof CartController))
			$this->context->controller->addJS($this->instance->getPath().'js/cart-sponsorship.js');

		// add Facebook tags
		$id_template = (int)MyConf::getIdTemplate('sponsorship', (int)$this->context->cookie->rewards_sponsor_id);
		$ogimage = MyConf::get('RSPONSORSHIP_SHARE_IMAGE_URL', null, $id_template);
		$ogtitle = MyConf::get('RSPONSORSHIP_SHARE_TITLE', (int)$this->context->language->id, $id_template);
		$ogdescription = MyConf::get('RSPONSORSHIP_SHARE_DESCRIPTION', (int)$this->context->language->id, $id_template);
		if (Tools::getValue('s') && (int)$this->context->cookie->rewards_sponsor_id > 0 && (($this->context->controller instanceof IndexController && MyConf::get('RSPONSORSHIP_REDIRECT', null, $id_template) == 'home') || ($this->context->controller instanceof AuthController && MyConf::get('RSPONSORSHIP_REDIRECT', null, $id_template) == 'form') || ($this->context->controller instanceof CmsController && (int)MyConf::get('RSPONSORSHIP_REDIRECT', null, $id_template) == (int)Tools::getValue('id_cms')))) {
			$this->context->smarty->assign(array('ogurl' => Tools::getShopDomainSsl(true, true).'/'.ltrim($_SERVER['REQUEST_URI'], '/')));
			if ($ogimage)
				$this->context->smarty->assign(array('ogimage' => $ogimage));
			if ($ogtitle)
				$this->context->smarty->assign(array('ogtitle' => $ogtitle));
			if ($ogdescription)
				$this->context->smarty->assign(array('ogdescription' => $ogdescription));

			return $this->instance->display($this->instance->path, 'header-sponsorship.tpl');
		}
		return false;
	}

	// Open sponsorship popup
	public function hookDisplayFooter($params)
	{
		// if popup is activated and cookie time is over
		$id_template = (int)MyConf::getIdTemplate('sponsorship', $this->context->customer->id);
		$key = 'rewards_sponsor' . MyConf::get('RSPONSORSHIP_POPUP_KEY', null, $id_template);
		if (MyConf::get('RSPONSORSHIP_POPUP', null, $id_template) && (!$this->context->cookie->$key || ($this->context->cookie->$key + (MyConf::get('RSPONSORSHIP_POPUP_DELAY', null, $id_template)*86400)) < time())
			&& strpos($_SERVER['REQUEST_URI'], "/sponsorship.php") === false)
			return $this->_popup(true);
		return false;
	}

	public function hookDisplayCustomerAccount($params)
	{
		if (RewardsSponsorshipModel::isCustomerAllowed($this->context->customer)) {
			if (version_compare(_PS_VERSION_, '1.7', '>='))
				return $this->instance->display($this->instance->path, 'presta-1.7/customer-account-sponsorship.tpl');
			return $this->instance->display($this->instance->path, 'customer-account-sponsorship.tpl');
		}
	}

	public function hookDisplayMyAccountBlock($params)
	{
		if (RewardsSponsorshipModel::isCustomerAllowed($this->context->customer)) {
			if (version_compare(_PS_VERSION_, '1.7', '>='))
				return $this->instance->display($this->instance->path, 'presta-1.7/my-account-sponsorship.tpl');
			return $this->instance->display($this->instance->path, 'my-account-sponsorship.tpl');
		}
	}

	public function hookDisplayMyAccountBlockFooter($params)
	{
		return $this->hookDisplayMyAccountBlock($params);
	}

	// Add an additional input on bottom for the sponsor's email address
	public function hookDisplayCustomerAccountForm($params)
	{
    	if (empty($this->context->cookie->rewards_sponsor_id)) {
			if (version_compare(_PS_VERSION_, '1.7', '<')) {
	   			$this->context->controller->addJS($this->instance->getPath().'js/sponsorship-registration.js');
				return $this->instance->display($this->instance->path, 'authentication.tpl');
			}
		} else if (version_compare(_PS_VERSION_, '1.7', '>=') && $this->context->controller instanceof OrderController) {
			return $this->instance->display($this->instance->path, 'authentication-top.tpl');
		}
		return false;
	}

	// Inform the customer that he has been sponsored
    public function hookDisplayCustomerAccountFormTop($params)
    {
    	if (!empty($this->context->cookie->rewards_sponsor_id) && (version_compare(_PS_VERSION_, '1.7', '<') || !($this->context->controller instanceof OrderController)))
    		return $this->instance->display($this->instance->path, 'authentication-top.tpl');
    	return false;
    }

    // presta >= 1.7
	// Add an additional input on bottom for the sponsor's email address
	public function hookAdditionalCustomerFormFields($params)
    {
    	if (isset($this->context->customer) && !$this->context->customer->isLogged() && empty($this->context->cookie->rewards_sponsor_id)) {
   			$this->context->controller->addJS($this->instance->getPath().'js/sponsorship-registration.js');
   			Media::addJsDef(array('ps_version' => _PS_VERSION_, 'url_allinone_sponsorship' => $this->context->link->getModuleLink('allinone_rewards', 'sponsorship', array(), true)));
   			Media::addJsDefL('error_sponsor', $this->l('This sponsor does not exist or is not valid'));

	        $form = new FormField();
	        return array($form->setName('sponsorship')->setType('text')->setLabel($this->l('Code or E-mail address of your sponsor')));
	    }
	    return array();
    }

	// Create the sponsorship, and a discount for the customer
	public function hookActionCustomerAccountAdd($params)
	{
		$newCustomer = $params['newCustomer'];
		if (!Validate::isLoadedObject($newCustomer))
			return false;

		$sponsor = null;
		if (!empty($this->context->cookie->rewards_sponsor_id)) {
			// sponsor already in the cookie
			$sponsor = new Customer($this->context->cookie->rewards_sponsor_id);
		} else {
			// sponsor email entered on the form
			if (!Tools::getIsset('sponsorship'))
				return false;
			$sponsorship = trim(Tools::getValue('sponsorship'));
			$sponsor = new Customer();
			if (Validate::isEmail($sponsorship))
				$sponsor=$sponsor->getByEmail($sponsorship);
			else
				$sponsor = new Customer(RewardsSponsorshipModel::decodeSponsorshipLink($sponsorship));
		}
		return $this->_createSponsorship($sponsor, $newCustomer);
	}

	private function _createSponsorship($sponsor, $customer, $force=false, $voucher=true, $currency=0) {
		unset($this->context->cookie->rewards_sponsor_id);
		unset($this->context->cookie->rewards_sponsorship_id);

		if (Validate::isLoadedObject($sponsor) && RewardsSponsorshipModel::isCustomerAllowed($sponsor, true, $force) && $sponsor->email != $customer->email && !(Tools::strtolower($sponsor->firstname)==Tools::strtolower($customer->firstname) && Tools::strtolower($sponsor->lastname)==Tools::strtolower($customer->lastname)))
		{
			// if this customer has already been sponsored, mark the current sponsorship as deleted
			$already_sponsored = false;
			if ($id_sponsorship = RewardsSponsorshipModel::isSponsorised((int)$customer->id, true)) {
				$sponsorship = new RewardsSponsorshipModel((int)$id_sponsorship);
				$sponsorship->deleted = 1;
				$sponsorship->save();
				$already_sponsored = true;
			}

			$id_template = (int)MyConf::getIdTemplate('sponsorship', $sponsor->id);
			if ($id_sponsorship = RewardsSponsorshipModel::isMailSponsorised($sponsor->id, $customer->email)) {
				$sponsorship = new RewardsSponsorshipModel((int)$id_sponsorship);
				// guest account turning into real account
				// This should be improved, because both customer should be considerated as sponsored, so probably create a new sponsorship !
				if (!empty($sponsorship->id_customer))
					return false;
				$sponsorship->id_customer = $customer->id;
				$sponsorship->firstname = $customer->firstname;
				$sponsorship->lastname = $customer->lastname;
			} else {
				// if this customer has been sponsored by another sponsor, it is deleted
				RewardsSponsorshipModel::deleteSponsoredByOther($customer->email);
				$sponsorship = new RewardsSponsorshipModel();
				$sponsorship->id_sponsor = $sponsor->id;
				$sponsorship->email = $customer->email;
				$sponsorship->id_customer = $customer->id;
				$sponsorship->firstname = $customer->firstname;
				$sponsorship->lastname = $customer->lastname;
				if (!empty($this->context->cookie->rewards_sponsor_channel))
					$sponsorship->channel = $this->context->cookie->rewards_sponsor_channel;
				else
					$sponsorship->channel = 2;
			}
			if ((int)MyConf::get('RSPONSORSHIP_DURATION', null, $id_template))
				$sponsorship->date_end = date('Y-m-d H:i:s', time() + (int)MyConf::get('RSPONSORSHIP_DURATION', null, $id_template)*24*60*60);

			if ($sponsorship->save()) {
				if (MyConf::get('RSPONSORSHIP_CHILD_GROUPS', null, $id_template)) {
					$customer_groups_to_add = explode(',', MyConf::get('RSPONSORSHIP_CHILD_GROUPS', null, $id_template));
					if (is_array($customer_groups_to_add)) {
						$customer->addGroups($customer_groups_to_add);
						if ($customer_default_group = MyConf::get('RSPONSORSHIP_CHILD_DEFAULT_GROUP', null, $id_template)) {
							$customer->id_default_group = $customer_default_group;
							$customer->save();
						}
					}
				}

				// check if there's some reward to give for registration
				$sponsor_reward = 0;
				if (!$already_sponsored && MyConf::get('RSPONSORSHIP_REWARD_REGISTRATION', null, $id_template) && $friends = RewardsSponsorshipModel::getSponsorFriends((int)$sponsor->id, 'subscribed')) {
					$nbFriends = count($friends);
					$multiples = explode(',', MyConf::get('RSPONSORSHIP_REGISTR_MULTIPLE', null, $id_template));
					$repeats = explode(',', MyConf::get('RSPONSORSHIP_REGISTR_REPEAT', null, $id_template));
					$values = explode(',', MyConf::get('RSPONSORSHIP_REGISTR_VALUE', null, $id_template));
					foreach($multiples as $key => $value) {
						if (($nbFriends % $multiples[$key]) == 0 && ($nbFriends <= $repeats[$key] || $repeats[$key] == 0))
							$sponsor_reward += (float)$values[$key];
					}
					if ($sponsor_reward > 0) {
						$reward = new RewardsModel();
						$reward->plugin = $this->name;
						$reward->id_customer = (int)$sponsor->id;
						$reward->id_reward_state = RewardsStateModel::getValidationId();
						$reward->credits = (float)$sponsor_reward;
						if (Configuration::get('REWARDS_DURATION'))
							$reward->date_end = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d') + Configuration::get('REWARDS_DURATION'), date('Y')));
						if ($reward->save())
							RewardsSponsorshipModel::saveDetails($reward->id, (int)$sponsorship->id, 1);
					}
				}

				// send notifications
				if (!$already_sponsored && (Configuration::get('RSPONSORSHIP_MAIL_REGISTRATION') || Configuration::get('RSPONSORSHIP_MAIL_REGISTRATION_S'))) {
					$id_template_core = (int)MyConf::getIdTemplate('core', $sponsor->id);
					$lang = (int)Configuration::get('PS_LANG_DEFAULT');
					if (version_compare(_PS_VERSION_, '1.5.4.0', '>='))
						$lang = (int)$sponsor->id_lang;
					$rewardAmount = $this->instance->getRewardReadyForDisplay((float)$sponsor_reward, (int)Configuration::get('PS_CURRENCY_DEFAULT'), $lang, true, $id_template_core);
					$rewardAmountAdmin = $this->instance->getRewardReadyForDisplay((float)$sponsor_reward, (int)Configuration::get('PS_CURRENCY_DEFAULT'), (int)Configuration::get('PS_LANG_DEFAULT'), true, $id_template_core);
					$data = array(
						'{sponsored_firstname}' => $customer->firstname,
						'{sponsored_lastname}' => $customer->lastname,
						'{sponsored_email}' => $customer->email,
						'{sponsor_firstname}' => $sponsor->firstname,
						'{sponsor_lastname}' => $sponsor->lastname,
						'{sponsor_email}' => $sponsor->email);
					if (Configuration::get('RSPONSORSHIP_MAIL_REGISTRATION')) {
						$data['{sponsor_reward}'] = $rewardAmountAdmin;
						$this->instance->sendMail((int)Configuration::get('PS_LANG_DEFAULT'), 'sponsorship-registration-admin', $this->l('Sponsorship', (int)Configuration::get('PS_LANG_DEFAULT')), $data, Configuration::get('PS_SHOP_EMAIL'), NULL);
					}
					if (Configuration::get('RSPONSORSHIP_MAIL_REGISTRATION_S')) {
						if ($sponsorship->channel!=1 && MyConf::get('RSPONSORSHIP_ANONYMIZE', null, $id_template)) {
							$data['{sponsored_firstname}'] = Tools::substr($customer->firstname, 0, 1).'***';
							$data['{sponsored_lastname}'] = Tools::substr($customer->lastname, 0, 1).'***';
							$data['{sponsored_email}'] = Tools::substr($customer->email, 0, 1).'***';
						}
						$data['{sponsor_reward}'] = $rewardAmount;
						$this->instance->sendMail($lang, $sponsor_reward > 0 ? 'sponsorship-registration-reward' : 'sponsorship-registration', $this->l('Sponsorship', $lang), $data, $sponsor->email, $sponsor->firstname.' '.$sponsor->lastname);
					}
				}

				if (MyConf::get('RSPONSORSHIP_DISCOUNT_GC', null, $id_template) && $voucher) {
					// when called from back-end, currency is provided
					if ($currency == 0)
						$currency = (int)$this->context->currency->id;
					if ($sponsorship->registerDiscount($currency)) {
						$cartRule = new CartRule((int)$sponsorship->id_cart_rule);
						if (MyConf::get('RSPONSORSHIP_VOUCHER_PREFIX_GC', null, $id_template)!='' && Validate::isLoadedObject($cartRule)) {
							$data = array(
								'{firstname}' => $customer->firstname,
								'{lastname}' => $customer->lastname,
								'{nb_discount}' => $cartRule->quantity_per_user,
								'{voucher_num}' => $cartRule->code);
							if (MyConf::get('RSPONSORSHIP_REAL_VOUCHER_GC', null, $id_template))
								$data['{voucher_details}'] = $this->instance->getDiscountReadyForDisplay(null, null, null, null, MyConf::get('RSPONSORSHIP_REAL_DESC_GC', (int)$this->context->language->id, $id_template));
							else {
								if ((float)$cartRule->reduction_amount > 0 || (float)$cartRule->reduction_percent > 0) {
									if ((float)$cartRule->reduction_percent > 0)
										$data['{voucher_details}'] = $this->instance->getDiscountReadyForDisplay(1, $cartRule->free_shipping, $cartRule->reduction_percent);
									else
										$data['{voucher_details}'] = $this->instance->getDiscountReadyForDisplay(2, $cartRule->free_shipping, $cartRule->reduction_amount, $currency);
								} else
									$data['{voucher_details}'] = $this->l('Free shipping');
							}
							$this->instance->sendMail((int)$this->context->language->id, 'sponsorship-voucher', $this->l('Congratulations!'), $data, $customer->email, $customer->firstname.' '.$customer->lastname);
						}
					}
				}
				return true;
			}
		}
		return false;
	}

	// Create all sponsorship rewards for an order
	private function _createAllRewards($order, $customer)
	{
		// All sponsors who should get a reward
		$sponsorships = RewardsSponsorshipModel::getSponsorshipAscendants($customer->id);

		if (count($sponsorships) > 0) {
			// totals with and without discounted products
			$totals = RewardsModel::getOrderTotalsForReward($order);

			// loop on sponsor, starting from the nearest
			$sponsorsMailHtml = $sponsorsMailTxt = '';
			$bMail = false;
			$level = -1;
			foreach($sponsorships as $sponsorship) {
				// if a sponsorship is over, stop all rewards for the ascendants
				if ($sponsorship['date_end']!='0000-00-00 00:00:00' && $sponsorship['date_end'] <= date('Y-m-d H:i:s'))
					break;

				$level++;
				$id_template = (int)MyConf::getIdTemplate('sponsorship', $sponsorship['id_sponsor']);
				$this->_initConf($id_template);
				// maximum level for this template
				$limit = count($this->_configuration['reward_type']) - 1;
				// try to get settings for the level, if not found last will be used
				$indice = $level;
				if ($level > $limit && !$this->_configuration['unlimited'])
					continue;
				else if (!isset($this->_configuration['reward_type'][$level]))
					$indice = $limit;

				$reward = new RewardsModel();
				$reward->plugin = $this->name;
				$reward->id_customer = (int)$sponsorship['id_sponsor'];
				$reward->id_order = (int)$order->id;
				$reward->id_reward_state = RewardsStateModel::getDefaultId();

				// if reward is product per product
				if ((int)$this->_configuration['reward_type'][$indice] == 3) {
					$reward->credits = RewardsModel::getOrderRewardByProduct($order, MyConf::get('RSPONSORSHIP_DISCOUNTED_ALLOWED', null, $id_template), MyConf::get('RSPONSORSHIP_TAX', null, $id_template), 'sponsorship', $id_template, $indice+1);
				} else {
					$price = MyConf::get('RSPONSORSHIP_DISCOUNTED_ALLOWED', null, $id_template) ? $totals[MyConf::get('RSPONSORSHIP_TAX', null, $id_template) ? 'tax_incl' : 'tax_excl']['with_discounted'] : $totals[MyConf::get('RSPONSORSHIP_TAX', null, $id_template) ? 'tax_incl' : 'tax_excl']['without_discounted'];
					if ($price > 0) {
						$extraParams = array();
						$extraParams['type'] = (int)$this->_configuration['reward_type'][$indice];
						$extraParams['value'] = (float)($extraParams['type'] == 1 ? $this->_configuration['reward_value'][$indice][$order->id_currency] : $this->_configuration['reward_percentage'][$indice]);
						$reward->credits = (float)$this->_getNbCreditsByPrice($price, $order->id_currency, Configuration::get('PS_CURRENCY_DEFAULT'), $extraParams);
					}
				}
				// if sponsor's reward=0 (only special offers, voucher used, or % set to 0 in BO)
				if ($reward->credits == 0)
					continue;

				if ($reward->save()) {
					RewardsSponsorshipModel::saveDetails($reward->id, (int)$sponsorship['id_sponsorship'], $level+1);
					$bMail = true;

					// send customer's notifications
					if (Configuration::get('RSPONSORSHIP_MAIL_ORDER_S') || Configuration::get('RSPONSORSHIP_MAIL_ORDER')) {
						$sponsor = new Customer((int) $sponsorship['id_sponsor']);
						$id_template_core = (int)MyConf::getIdTemplate('core', $sponsor->id);
						$lang = (int)Configuration::get('PS_LANG_DEFAULT');
						if (version_compare(_PS_VERSION_, '1.5.4.0', '>='))
							$lang = (int)$sponsor->id_lang;
						$rewardAmount = $this->instance->getRewardReadyForDisplay((float)$reward->credits, (int)$order->id_currency, $lang, true, $id_template_core);
						$rewardAmountAdmin = $this->instance->getRewardReadyForDisplay((float)$reward->credits, (int)$order->id_currency, (int)Configuration::get('PS_LANG_DEFAULT'), true, $id_template_core);
						$data = array(
							'{sponsored_firstname}' => $sponsorship['channel']!=1 && MyConf::get('RSPONSORSHIP_ANONYMIZE', null, $id_template) ? Tools::substr($customer->firstname, 0, 1).'***' : $customer->firstname,
							'{sponsored_lastname}' => $sponsorship['channel']!=1 && MyConf::get('RSPONSORSHIP_ANONYMIZE', null, $id_template) ? Tools::substr($customer->lastname, 0, 1).'***' : $customer->lastname,
							'{sponsored_email}' => $sponsorship['channel']!=1 && MyConf::get('RSPONSORSHIP_ANONYMIZE', null, $id_template) ? Tools::substr($customer->email, 0, 1).'***' : $customer->email,
							'{sponsor_firstname}' => $sponsor->firstname,
							'{sponsor_lastname}' => $sponsor->lastname,
							'{sponsor_email}' => $sponsor->email,
							'{sponsor_reward}' => $rewardAmount,
							'{link_rewards}' => $this->context->link->getModuleLink('allinone_rewards', 'rewards', array(), true));
						$template = 'sponsorship-order';
						if ($level > 0)
							$template = 'sponsorship-order-levels';
						if (Configuration::get('RSPONSORSHIP_MAIL_ORDER_S'))
							$this->instance->sendMail($lang, $template, $this->l('Sponsorship', $lang), $data, $sponsor->email, $sponsor->firstname.' '.$sponsor->lastname);

						// text for the admin notification
						$sponsorsMailHtml .= $this->l('Level', (int)Configuration::get('PS_LANG_DEFAULT')).' '.($level+1).' : '. $sponsor->firstname.' '.$sponsor->lastname.' ('.$sponsor->email.') '.$this->l('will receive', (int)Configuration::get('PS_LANG_DEFAULT')).' '.$rewardAmountAdmin.'<br>';
						$sponsorsMailTxt .= $this->l('Level', (int)Configuration::get('PS_LANG_DEFAULT')).' '.($level+1).' : '. $sponsor->firstname.' '.$sponsor->lastname.' ('.$sponsor->email.') '.$this->l('will receive', (int)Configuration::get('PS_LANG_DEFAULT')).' '.$rewardAmountAdmin.'\r\n';
					}
				}
			}
			// admin notification
			if ($bMail && Configuration::get('RSPONSORSHIP_MAIL_ORDER')) {
				$data = array(
					'{sponsored_firstname}' => $customer->firstname,
					'{sponsored_lastname}' => $customer->lastname,
					'{sponsored_email}' => $customer->email,
					'{sponsors_html}' => $sponsorsMailHtml,
					'{sponsors_txt}' => $sponsorsMailTxt);
				$this->instance->sendMail((int)Configuration::get('PS_LANG_DEFAULT'), 'sponsorship-order-admin', $this->l('Sponsorship', (int)Configuration::get('PS_LANG_DEFAULT')), $data, Configuration::get('PS_SHOP_EMAIL'), NULL);
			}
		}
	}

	// give reward to sponsor in "Waiting for validation" state
	// send notification to sponsor to inform them a sponsored placed an order
	public function hookActionValidateOrder($params)
	{
		if (!Validate::isLoadedObject($customer = $params['customer']) || !Validate::isLoadedObject($order = $params['order']))
			die(Tools::displayError('Missing parameters for hookActionValidateOrder'));

		// check if the customer has been sponsored
		$sponsorship = new RewardsSponsorshipModel(RewardsSponsorshipModel::isSponsorised((int)$customer->id, true, true));
		if (!Validate::isLoadedObject($sponsorship))
			return false;

		// TODO : toute cette partie devrait être faite dans la boucle des parrains dans createAllRewards, car un parrain peut avoir été désactivé, ou bien avoir des conditions différentes
		// par exemple être récompensé sur toutes les commandes dans un template en étant niveau 2, alors que le niveau 1 est dans un autre template qui n'est récompensé qu'à la 1ère.
		// ou bien avoir des minimums différents dans les conditions d'attribution, ou de taxe, ou de frais de livraison, ou de discount...
		$id_template = (int)MyConf::getIdTemplate('sponsorship', $sponsorship->id_sponsor);

		// TODO : if order is splitted but if it's the first order, should be allowed for both orders even if rewards is only for the first order
		// if sponsorship reward is active (because of order creation from admin generating reward even when it was turned off), and sponsor is allowed to get a reward
		if (RewardsSponsorshipModel::isCustomerAllowed(new Customer($sponsorship->id_sponsor)) && MyConf::get('RSPONSORSHIP_REWARD_ORDER', null, $id_template)) {
			// if there's reward only on the first order and the sponsor has already beeen rewarded for this customer, do nothing
			if ((int)MyConf::get('RSPONSORSHIP_ON_EVERY_ORDER', null, $id_template) == 0 && RewardsSponsorshipModel::isAlreadyRewarded($sponsorship->id))
				return false;

			// Shipping included in minimum to unlock sponsor's reward ?
			$total_unlock = MyConf::get('RSPONSORSHIP_TAX', null, $id_template) ? (float)$order->total_paid_tax_incl : (float)$order->total_paid_tax_excl;
			$total_shipping = MyConf::get('RSPONSORSHIP_TAX', null, $id_template) ? (float)$order->total_shipping_tax_incl : (float)$order->total_shipping_tax_excl;

			if ((int)MyConf::get('RSPONSORSHIP_UNLOCK_SHIPPING', null, $id_template) == 0)
				$total_unlock = $total_unlock - $total_shipping;

			// Check if minimum is reached
			if ($total_unlock >= (float)MyConf::get('RSPONSORSHIP_UNLOCK_GC_' . $order->id_currency, null, $id_template)) {
				$this->_createAllRewards($order, $customer);
				return true;
			}
		}
		return false;
	}

	// modify all rewards for a given order
	private function _updateStatusAllRewards($order, $customer, $orderState)
	{
		// if no reward has been granted for this sponsorship
		$rewards=RewardsSponsorshipModel::getByOrderId($order->id);
		if (!$rewards)
			return;

		foreach($rewards as $reward) {
			$level = $reward['level_sponsorship'];

			$reward = new RewardsModel((int)$reward['id_reward']);
			if (!Validate::isLoadedObject($reward))
				continue;

			if ($reward->credits > 0 && $reward->id_reward_state != RewardsStateModel::getConvertId() && $reward->id_reward_state != RewardsStateModel::getWaitingPaymentId() && $reward->id_reward_state != RewardsStateModel::getPaidId()) {
				$oldState = $reward->id_reward_state;

				$sponsor = new Customer((int)$reward->id_customer);
				$lang = (int)Configuration::get('PS_LANG_DEFAULT');
				if (version_compare(_PS_VERSION_, '1.5.4.0', '>='))
					$lang = (int)$sponsor->id_lang;

				// if not already converted, validate or cancel the reward
				if (in_array($orderState->id, $this->rewardStateValidation->getValues())) {
					// if reward is locked during return period
					if (Configuration::get('REWARDS_WAIT_RETURN_PERIOD') && Configuration::get('PS_ORDER_RETURN') && (int)Configuration::get('PS_ORDER_RETURN_NB_DAYS') > 0) {
						$reward->id_reward_state = RewardsStateModel::getReturnPeriodId();
						if (Configuration::get('REWARDS_DURATION'))
							$reward->date_end = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d') + Configuration::get('PS_ORDER_RETURN_NB_DAYS') + Configuration::get('REWARDS_DURATION'), date('Y')));
						$template = 'sponsorship-return-period';
						$subject = $this->l('Reward validation', $lang);
					} else {
						$reward->id_reward_state = RewardsStateModel::getValidationId();
						if (Configuration::get('REWARDS_DURATION'))
							$reward->date_end = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d') + Configuration::get('REWARDS_DURATION'), date('Y')));
						$template = 'sponsorship-validation';
						$subject = $this->l('Reward validation', $lang);
					}
				} else {
					$reward->id_reward_state = RewardsStateModel::getCancelId();
					$template = 'sponsorship-cancellation';
					$subject = $this->l('Reward cancellation', $lang);
				}

				if ($oldState != $reward->id_reward_state) {
					$reward->save();

					// send customers's notifications
					if (Configuration::get('RSPONSORSHIP_MAIL_VALIDATION_S')) {
						$id_template_core = (int)MyConf::getIdTemplate('core', $sponsor->id);
						$data = array(
							'{sponsor_firstname}' => $sponsor->firstname,
							'{sponsor_lastname}' => $sponsor->lastname,
							'{sponsor_email}' => $sponsor->email,
							'{sponsor_reward}' => $this->instance->getRewardReadyForDisplay((float)$reward->credits, (int)$order->id_currency, $lang, true, $id_template_core),
							'{link_rewards}' => $this->context->link->getModuleLink('allinone_rewards', 'rewards', array(), true)
						);

						if ($level > 1)
							$template .= '-levels';
						else {
							$id_template = (int)MyConf::getIdTemplate('sponsorship', $sponsor->id);
							$sponsorship = new RewardsSponsorshipModel(RewardsSponsorshipModel::isSponsorised((int)$customer->id, true));
							$data['{sponsored_firstname}'] = Validate::isLoadedObject($sponsorship) && $sponsorship->channel!=1 && MyConf::get('RSPONSORSHIP_ANONYMIZE', null, $id_template) ? Tools::substr($customer->firstname, 0, 1).'***' : $customer->firstname;
							$data['{sponsored_lastname}'] = Validate::isLoadedObject($sponsorship) && $sponsorship->channel!=1 && MyConf::get('RSPONSORSHIP_ANONYMIZE', null, $id_template) ? Tools::substr($customer->lastname, 0, 1).'***' : $customer->lastname;
							$data['{sponsored_email}'] = Validate::isLoadedObject($sponsorship) && $sponsorship->channel!=1 && MyConf::get('RSPONSORSHIP_ANONYMIZE', null, $id_template) ? Tools::substr($customer->email, 0, 1).'***' : $customer->email;
						}

						if ($reward->id_reward_state == RewardsStateModel::getReturnPeriodId()) {
							$data['{reward_unlock_date}'] = version_compare(_PS_VERSION_, '8.0.0', '<') ? Tools::displayDate($reward->getUnlockDate(), null, true) : Tools::displayDate($reward->getUnlockDate(), true);
						}
						$this->instance->sendMail($lang, $template, $subject, $data, $sponsor->email, $sponsor->firstname.' '.$sponsor->lastname);
					}
				}
			}
		}
	}

	// Validate or cancel the sponsor's rewards
	// Send mail to notify about validation or cancellation of the reward
	public function hookActionOrderStatusUpdate($params)
	{
		$this->instanceDefaultStates();

		if (!Validate::isLoadedObject($orderState = $params['newOrderStatus']) || !Validate::isLoadedObject($order = new Order((int)$params['id_order'])) || !Validate::isLoadedObject($customer = new Customer((int)$order->id_customer)))
			return false;

		// check if a sponsorship is in progress
		$sponsorship = new RewardsSponsorshipModel(RewardsSponsorshipModel::isSponsorised((int)$customer->id, true));
		if (!Validate::isLoadedObject($sponsorship))
			return false;

		// if status change to validation status or cancellation status for the reward
		if ($orderState->id != $order->getCurrentState() && (in_array($orderState->id, $this->rewardStateValidation->getValues()) || in_array($orderState->id, $this->rewardStateCancel->getValues()))) {
			$this->_updateStatusAllRewards($order, $customer, $orderState);
			return true;
		}
		return false;
	}

	// calulate all rewards after an order detail has been modified
	private function _modifyOrderDetailAllRewards($order, $customer)
	{
		$rewards=RewardsSponsorshipModel::getByOrderId($order->id);
		if (!$rewards)
			return;

		// totals with and without discounted products
		$totals = RewardsModel::getOrderTotalsForReward($order);

		foreach($rewards as $reward) {
			$level = $reward['level_sponsorship'] - 1;

			$reward = new RewardsModel((int)$reward['id_reward']);
			if (!Validate::isLoadedObject($reward))
				continue;

			if ($reward->id_reward_state != RewardsStateModel::getConvertId() && $reward->id_reward_state != RewardsStateModel::getWaitingPaymentId() && $reward->id_reward_state != RewardsStateModel::getPaidId()) {
				$id_template = (int)MyConf::getIdTemplate('sponsorship', $reward->id_customer);
				$this->_initConf($id_template);

				$oldCredits = (float)$reward->credits;

				// maximum level for this template
				$limit = count($this->_configuration['reward_type']) - 1;
				// try to get settings for the level, if not found last will be used
				if ($level > $limit && !$this->_configuration['unlimited'])
					$reward->credits = 0;
				else {
					$indice = $level;
					if (!isset($this->_configuration['reward_type'][$level]))
						$indice = $limit;

					if ((int)$this->_configuration['reward_type'][$indice] == 3) {
						$reward->credits = (float)RewardsModel::getOrderRewardByProduct($order, MyConf::get('RSPONSORSHIP_DISCOUNTED_ALLOWED', null, $id_template), MyConf::get('RSPONSORSHIP_TAX', null, $id_template), 'sponsorship', $id_template, $indice+1);
					} else {
						$price = MyConf::get('RSPONSORSHIP_DISCOUNTED_ALLOWED', null, $id_template) ? $totals[MyConf::get('RSPONSORSHIP_TAX', null, $id_template) ? 'tax_incl' : 'tax_excl']['with_discounted'] : $totals[MyConf::get('RSPONSORSHIP_TAX', null, $id_template) ? 'tax_incl' : 'tax_excl']['without_discounted'];
						$extraParams = array();
						$extraParams['type'] = (int)$this->_configuration['reward_type'][$indice];
						$extraParams['value'] = (float)($extraParams['type'] == 1 ? $this->_configuration['reward_value'][$indice][$order->id_currency] : $this->_configuration['reward_percentage'][$indice]);
						$reward->credits = (float)$this->_getNbCreditsByPrice($price, $order->id_currency, Configuration::get('PS_CURRENCY_DEFAULT'), $extraParams);
					}
				}

				// test if something has changed, because return product doesn't change the price of the cart
				if ($oldCredits != $reward->credits) {
					if ($reward->credits == 0)
						$reward->id_reward_state = RewardsStateModel::getCancelId();
					$reward->save();

					// send notification
					if (Configuration::get('RSPONSORSHIP_MAIL_CANCELPROD_S')) {
						$sponsor = new Customer((int)$reward->id_customer);
						$id_template_core = (int)MyConf::getIdTemplate('core', $sponsor->id);
						$lang = (int)Configuration::get('PS_LANG_DEFAULT');
						if (version_compare(_PS_VERSION_, '1.5.4.0', '>='))
							$lang = (int)$sponsor->id_lang;

						$data = array(
							'{sponsor_firstname}' => $sponsor->firstname,
							'{sponsor_lastname}' => $sponsor->lastname,
							'{sponsor_email}' => $sponsor->email,
							'{old_sponsor_reward}' => $this->instance->getRewardReadyForDisplay((float)$oldCredits, (int)$order->id_currency, $lang, true, $id_template_core),
							'{new_sponsor_reward}' => $this->instance->getRewardReadyForDisplay((float)$reward->credits, (int)$order->id_currency, $lang, true, $id_template_core));
						$template = 'sponsorship-cancel-product';
						if ($level > 0)
							$template = 'sponsorship-cancel-product-levels';
						else {
							$sponsorship = new RewardsSponsorshipModel(RewardsSponsorshipModel::isSponsorised((int)$customer->id, true));
							$data['{sponsored_firstname}'] = Validate::isLoadedObject($sponsorship) && $sponsorship->channel!=1 && MyConf::get('RSPONSORSHIP_ANONYMIZE', null, $id_template) ? Tools::substr($customer->firstname, 0, 1).'***' : $customer->firstname;
							$data['{sponsored_lastname}'] = Validate::isLoadedObject($sponsorship) && $sponsorship->channel!=1 && MyConf::get('RSPONSORSHIP_ANONYMIZE', null, $id_template) ? Tools::substr($customer->lastname, 0, 1).'***' : $customer->lastname;
							$data['{sponsored_email}'] = Validate::isLoadedObject($sponsorship) && $sponsorship->channel!=1 && MyConf::get('RSPONSORSHIP_ANONYMIZE', null, $id_template) ? Tools::substr($customer->email, 0, 1).'***' : $customer->email;
						}

						$this->instance->sendMail($lang, $template, $this->l('Reward modification', $lang), $data, $sponsor->email, $sponsor->firstname.' '.$sponsor->lastname);
					}
				}
			}
		}
	}

	// Hook called when a product is returned or partially refunded
	public function hookActionProductCancel($params)
	{
		$params = array('object' => new OrderDetail($params['id_order_detail']));
		return $this->_modifyOrderDetail($params);
	}

	// Hook called when the order detail is modified
	public function hookActionObjectOrderDetailAddAfter($params)
	{
		return $this->_modifyOrderDetail($params);
	}

	// Hook called when the order detail is modified
	public function hookActionObjectOrderDetailDeleteAfter($params)
	{
		return $this->_modifyOrderDetail($params);
	}

	// Hook called when the order detail is modified
	public function hookActionObjectOrderDetailUpdateAfter($params)
	{
		return $this->_modifyOrderDetail($params);
	}

	// calculate reward when the order detail is modified
	private function _modifyOrderDetail($params)
	{
		if (!Validate::isLoadedObject($order_detail = $params['object'])
		|| !Validate::isLoadedObject($order = new Order((int)$order_detail->id_order))
		|| !Validate::isLoadedObject($customer = new Customer((int)$order->id_customer))) {
			return false;
		}

		// if the sponsorship exists
		$sponsorship = new RewardsSponsorshipModel(RewardsSponsorshipModel::isSponsorised((int)$customer->id, true));
		if (!Validate::isLoadedObject($sponsorship))
			return false;

		$this->_modifyOrderDetailAllRewards($order, $customer);

		return true;
	}

	// display the sponsorship form
	public function hookDisplayOrderConfirmation($params)
	{
		$id_template = (int)MyConf::getIdTemplate('sponsorship', $this->context->customer->id);
		if (MyConf::get('RSPONSORSHIP_AFTER_ORDER', null, $id_template))
			return $this->_popup();
		return false;
	}

	// open the sponsorship popup
	private function _popup($scheduled=false)
	{
		if (!($this->context->controller instanceof Allinone_rewardsSponsorshipModuleFrontController) && !$this->_popup && $this->context->customer->isLogged() && Validate::isLoadedObject($this->context->customer) && RewardsSponsorshipModel::isCustomerAllowed($this->context->customer, true)) {
			$id_template = (int)MyConf::getIdTemplate('sponsorship', $this->context->customer->id);
			$this->_popup = true;
			$key = 'rewards_sponsor' . MyConf::get('RSPONSORSHIP_POPUP_KEY', null, $id_template);
			$this->context->cookie->$key = time();
			$this->context->smarty->assign(array('scheduled' => $scheduled));
			if (version_compare(_PS_VERSION_, '1.7', '>='))
				return $this->instance->display($this->instance->path, 'presta-1.7/popup.tpl');
			return $this->instance->display($this->instance->path, 'popup.tpl');
		}
		return false;
	}

	// Display sponsorship information in the order page
	public function hookDisplayAdminOrder($params)
	{
		if (version_compare(_PS_VERSION_, '1.7.7.0', '<'))
			return $this->hookDisplayAdminOrderMainBottom($params);
	}

	public function hookDisplayAdminOrderMainBottom($params)
	{
		$smarty_values = array(
			'rewards' => RewardsSponsorshipModel::getAllSponsorshipRewardsByOrderId($params['id_order'])
		);
		$this->context->smarty->assign($smarty_values);
		if (version_compare(_PS_VERSION_, '1.7', '>='))
			return $this->instance->display($this->instance->path, 'presta-1.7/adminorders-sponsorship.tpl');
		return $this->instance->display($this->instance->path, 'adminorders-sponsorship.tpl');
	}

	// Display sponsorship information in the customer page
	public function hookDisplayAdminCustomers($params)
	{
		$customer = new Customer((int)$params['id_customer']);
		if (!Validate::isLoadedObject($customer))
			die(Tools::displayError('Incorrect object Customer.'));

		$msg = $this->postProcess($params);

		$stats = RewardsSponsorshipModel::getAdminStatistics((int)$customer->id);
		$customerStats = $stats['sponsors'][(int)$customer->id];
		$friends = $stats['sponsored'][(int)$customer->id];
		$code_sponsorship = RewardsSponsorshipModel::getSponsorshipCode($customer);
		$link_sponsorship = RewardsSponsorshipModel::getSponsorshipLink($customer);
		$rewards_sponsorship_code = new RewardsSponsorshipCodeModel((int)$customer->id);

		$sponsorship_template_id = (int)MyConf::getIdTemplate('sponsorship', $this->context->customer->id);
		$sponsorship_templates = RewardsTemplateModel::getList('sponsorship');

		if ($id_sponsorship = RewardsSponsorshipModel::isSponsorised((int)$customer->id, true)) {
			$sponsorship = new RewardsSponsorshipModel((int)$id_sponsorship);
			$sponsor = new Customer((int)$sponsorship->id_sponsor);
		}

		$smarty_values = array(
			'msg' => $msg,
			'sponsorship_templates' => $sponsorship_templates,
			'sponsorship_template_id' => $sponsorship_template_id,
			'sponsor' => isset($sponsor) ? $sponsor : null,
			'sponsorship_code' => $code_sponsorship,
			'sponsorship_custom_code' => Tools::getValue('sponsorship_custom_code', Validate::isLoadedObject($rewards_sponsorship_code) ? $rewards_sponsorship_code->code : ''),
			'sponsorship_link' => $link_sponsorship,
			'sponsorship_allowed' => RewardsSponsorshipModel::isCustomerAllowed($customer),
			'currencies' => $this->instance->getCurrencies(),
			'default_currency' => Configuration::get('PS_CURRENCY_DEFAULT'),
			'friends' => $friends,
			'stats' => $customerStats,
			'sponsor_url' =>$this->context->link->getAdminLink('AdminSponsor').'&ajax=1&id_customer='.$params['id_customer']
		);
		$this->context->smarty->assign($smarty_values);
		if (version_compare(_PS_VERSION_, '1.7', '>='))
			return $this->instance->display($this->instance->path, 'presta-1.7/admincustomer-sponsorship.tpl');
		return $this->instance->display($this->instance->path, 'admincustomer-sponsorship.tpl');
	}

	public function hookActionAdminControllerSetMedia($params)
	{
    	// add necessary javascript to customers back office
		if ($this->context->controller->controller_name == 'AdminCustomers' && version_compare(_PS_VERSION_, '1.6', '>=')) {
			$this->context->controller->addJS(_PS_JS_DIR_.'jquery/plugins/autocomplete/jquery.autocomplete.js');
			$this->context->controller->addCSS(_PS_JS_DIR_.'jquery/plugins/autocomplete/jquery.autocomplete.css');
		}
	}

	// set the POST values to prefill the field with the sponsored friend information
	// can't be done in hookDisplayHeader on presta > 1.7, because fields are already loaded
	// It has no effect on standart OPC on 1.7, but it has been implemented in custom onepagecheckoutps module
	public function hookActionFrontControllerSetMedia($params)
	{
		if (($this->context->controller instanceof AuthController || $this->context->controller instanceof OrderController) && !empty($this->context->cookie->rewards_sponsorship_id)) {
			$sponsorship = new RewardsSponsorshipModel($this->context->cookie->rewards_sponsorship_id);
			if (Validate::isLoadedObject($sponsorship)) {
				// hack for display sponsorship information in form
				if (!Tools::isSubmit('submitCreate')) {
					$_POST['customer_firstname'] = $sponsorship->firstname;
					$_POST['firstname'] = $sponsorship->firstname;
					$_POST['customer_lastname'] = $sponsorship->lastname;
					$_POST['lastname'] = $sponsorship->lastname;
					$_POST['email'] = $sponsorship->email;
					$_POST['email_create'] = $sponsorship->email;
				}
				$_POST['sponsorship_invisible'] = '1';
			}
		}
	}

	// Hook called on product page
	public function hookDisplayLeftColumnProduct($params)
	{
		if (version_compare(_PS_VERSION_, '1.7', '<'))
			return $this->_displayProductShareLink();
	}

	public function hookDisplayProductButtons($params)
	{
		if (version_compare(_PS_VERSION_, '1.7', '>=') && version_compare(_PS_VERSION_, '1.7.1.0', '<'))
			return $this->_displayProductShareLink();
	}

	public function hookDisplayProductAdditionalInfo($params)
	{
		if (version_compare(_PS_VERSION_, '1.7.1.0', '>='))
			return $this->_displayProductShareLink();
	}

	private function _displayProductShareLink() {
		if (!Tools::getValue('content_only') && Tools::getValue('action')!='quickview') {
			$id_template = (int)MyConf::getIdTemplate('sponsorship', $this->context->customer->id);
			if ((int)MyConf::get('RSPONSORSHIP_PRODUCT_SHARE', null, $id_template) && RewardsSponsorshipModel::isCustomerAllowed($this->context->customer, true)) {
				$link = RewardsSponsorshipModel::getSponsorshipProductLink(Tools::getValue('id_product'));
				$this->context->smarty->assign(array('sponsorship_link' => $link));
				if (version_compare(_PS_VERSION_, '1.7', '>='))
					return $this->instance->display($this->instance->path, 'presta-1.7/product-sponsorship.tpl');
				return $this->instance->display($this->instance->path, 'product-sponsorship.tpl');
			}
		}
	}

	public function hookActionObjectCustomerDeleteAfter($params)
	{
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_sponsorship` WHERE `id_sponsor` NOT IN (SELECT `id_customer` FROM `'._DB_PREFIX_.'customer`)');
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_sponsorship` WHERE `id_customer`!=0 AND `id_customer` NOT IN (SELECT `id_customer` FROM `'._DB_PREFIX_.'customer`)');
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_sponsorship_code` WHERE `id_sponsor` NOT IN (SELECT `id_customer` FROM `'._DB_PREFIX_.'customer`)');
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_sponsorship_detail` WHERE `id_reward` NOT IN (SELECT `id_reward` FROM `'._DB_PREFIX_.'rewards`) OR `id_sponsorship` NOT IN (SELECT `id_sponsorship` FROM `'._DB_PREFIX_.'rewards_sponsorship`)');
	}
}