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

class RewardsRegistrationPlugin extends RewardsGenericPlugin
{
	public $name = 'registration';

	public function install()
	{
		// hooks
		if (!$this->registerHook('actionCustomerAccountAdd'))
			return false;

		if (!Configuration::updateValue('RREGISTRATION_ACTIVE', 0) || !Configuration::updateValue('RREGISTRATION_REWARD_SPONSORED', 1) || !Configuration::updateValue('RREGISTRATION_MAIL', 1))
			return false;

		foreach ($this->instance->getCurrencies() as $currency)
			Configuration::updateValue('RREGISTRATION_REWARD_VALUE_'.(int)($currency['id_currency']), 1);

		return true;
	}

	public function uninstall()
	{
		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'configuration_lang`
			WHERE `id_configuration` IN (SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE `name` LIKE \'RREGISTRATION_%\')');

		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'configuration`
			WHERE `name` LIKE \'RREGISTRATION_%\'');

		return true;
	}

	public function isActive()
	{
		return Configuration::get('RREGISTRATION_ACTIVE');
	}

	public function isRewardsAccountVisible()
	{
		return $this->isActive();
	}

	public function getTitle()
	{
		return $this->l('Account creation');
	}

	public function getDetails($reward, $admin) {
		return  $this->l('Account creation');
	}

	protected function postProcess($params=null)
	{
		if (Tools::isSubmit('submitRegistrationReward')) {
			$this->_postValidation();
			if (!sizeof($this->_errors)) {
				Configuration::updateValue('RREGISTRATION_ACTIVE', (int)Tools::getValue('registration_active'));
				Configuration::updateValue('RREGISTRATION_REWARD_SPONSORED', (int)Tools::getValue('registration_reward_sponsored'));
				foreach ($this->instance->getCurrencies() as $currency)
					Configuration::updateValue('RREGISTRATION_REWARD_VALUE_'.$currency['id_currency'], (float)Tools::getValue('registration_reward_value_'.$currency['id_currency']));
				$this->instance->confirmation = $this->instance->displayConfirmation($this->l('Settings updated.'));
			} else
				$this->instance->errors = $this->instance->displayError(implode('<br />', $this->_errors));
		} else if (Tools::isSubmit('submitRegistrationNotifications')) {
			Configuration::updateValue('RREGISTRATION_MAIL', (int)Tools::getValue('registration_mail'));
			$this->instance->confirmation = $this->instance->displayConfirmation($this->l('Settings updated.'));
		}
	}

	private function _postValidation()
	{
		if (Tools::isSubmit('submitRegistrationReward')) {
			foreach ($this->instance->getCurrencies() as $currency) {
				if (!Tools::getValue('registration_reward_value_'.$currency['id_currency']) || !Validate::isUnsignedFloat(Tools::getValue('registration_reward_value_'.$currency['id_currency'])))
					$this->_errors[] = sprintf($this->l('The reward value is required/invalid for the currency %s'), $currency['name']);
			}
		}
	}

	public function displayForm()
	{
		if (Tools::getValue('stats'))
			return $this->_getStatistics();

		$this->postProcess();

		$registration_reward_values = array();
		$currencies = $this->instance->getCurrencies();
		foreach($currencies as $currency)
			$registration_reward_values[$currency['id_currency']] = (float)Tools::getValue('registration_reward_value_'.$currency['id_currency'], (float)Configuration::get('RREGISTRATION_REWARD_VALUE_'.$currency['id_currency']));

		$this->context->smarty->assign(array(
			'module' => $this->instance,
			'object' => $this,
			'currencies' => $currencies,
			'currency' => $this->context->currency,
			'registration_active' => (int)Tools::getValue('newsletter_active', Configuration::get('RREGISTRATION_ACTIVE')),
			'registration_reward_sponsored' => (int)Tools::getValue('registration_reward_sponsored', Configuration::get('RREGISTRATION_REWARD_SPONSORED')),
			'registration_mail' => (int)Tools::getValue('registration_mail', Configuration::get('RREGISTRATION_MAIL')),
			'registration_reward_values' => $registration_reward_values,
		));
		return $this->instance->display($this->instance->path, 'views/templates/admin/admin-registration.tpl');
	}

	public function hookActionCustomerAccountAdd($params)
	{
		$newCustomer = $params['newCustomer'];
		if (!Validate::isLoadedObject($newCustomer))
			return false;

		// check if the customer already got a sponsorship voucher, try catch in case sponsorship plugin is not installed and table doesn't exist
		if (!Configuration::get('RREGISTRATION_REWARD_SPONSORED')) {
			try {
				if (Db::getInstance()->getValue('SELECT 1 FROM `'._DB_PREFIX_.'rewards_sponsorship` WHERE id_cart_rule > 0 AND id_customer='.(int)$newCustomer->id))
					return false;
			} catch(Exception $e) {}
		}

		// TODO : envoi d'email à conditionner par une option
		$credits = (float)Configuration::get('RREGISTRATION_REWARD_VALUE_'.(int)$this->context->currency->id);

		$reward = new RewardsModel();
		$reward->plugin = $this->name;
		$reward->id_customer = (int)$newCustomer->id;
		$reward->id_reward_state = RewardsStateModel::getValidationId();
		$reward->credits = round(Tools::convertPrice($credits, $this->context->currency, false), 2);

		if (Configuration::get('REWARDS_DURATION'))
			$reward->date_end = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d') + Configuration::get('REWARDS_DURATION'), date('Y')));
		if ($reward->save()) {
			if (Configuration::get('RREGISTRATION_MAIL')) {
				$lang = (int)Configuration::get('PS_LANG_DEFAULT');
				if (version_compare(_PS_VERSION_, '1.5.4.0', '>='))
					$lang = (int)$newCustomer->id_lang;
				$reward_amount = $this->instance->getRewardReadyForDisplay($credits, (int)$this->context->currency->id, $lang, false);
				$data = array(
					'{customer_firstname}' => $newCustomer->firstname,
					'{customer_lastname}' => $newCustomer->lastname,
					'{customer_reward}' => $reward_amount,
					'{link_rewards}' => $this->context->link->getModuleLink('allinone_rewards', 'rewards', array(), true)
				);
				$this->instance->sendMail($lang, 'registration-validation', $this->l('You just got a new reward', $lang), $data, $newCustomer->email, $newCustomer->firstname.' '.$newCustomer->lastname);
			}
		}
	}

	private function _getStatistics()
	{
		$stats = array('nb_customers' => 0, 'total_rewards' => 0);
		$query = '
			SELECT c.id_customer, c.firstname, c.lastname, r.credits
			FROM `'._DB_PREFIX_.'rewards` r
			JOIN `'._DB_PREFIX_.'customer` AS c ON (c.id_customer=r.id_customer'.Shop::addSqlRestriction(false, 'c').')
			WHERE plugin=\'registration\'
			GROUP BY id_customer';
		$stats['customers'] = Db::getInstance()->executeS($query);
		foreach ($stats['customers'] as $row) {
			$stats['nb_customers']++;
			$stats['total_rewards'] += (float)$row['credits'];
		}

		$this->context->smarty->assign(array(
			'module' => $this->instance,
			'object' => $this,
			'token' => Tools::getAdminToken('AdminCustomers'.(int)Tab::getIdFromClassName('AdminCustomers').(int)$this->context->employee->id),
			'stats' => $stats,
		));
		return $this->instance->display($this->instance->path, 'views/templates/admin/admin-registration-statistics.tpl');
	}
}