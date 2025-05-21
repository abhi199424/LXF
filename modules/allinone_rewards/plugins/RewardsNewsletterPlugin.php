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

class RewardsNewsletterPlugin extends RewardsGenericPlugin
{
	public $name = 'newsletter';

	public function install()
	{
		// hooks
		if (!$this->registerHook('actionCustomerAccountAdd') || !$this->registerHook('actionCustomerAccountUpdate') || !$this->registerHook('actionNewsletterRegistrationAfter'))
			return false;

		if (!Configuration::updateValue('RNEWSLETTER_ACTIVE', 0) || !Configuration::updateValue('RNEWSLETTER_MAIL', 1))
			return false;

		foreach ($this->instance->getCurrencies() as $currency)
			Configuration::updateValue('RNEWSLETTER_REWARD_VALUE_'.(int)($currency['id_currency']), 1);

		return true;
	}

	public function uninstall()
	{
		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'configuration_lang`
			WHERE `id_configuration` IN (SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE `name` LIKE \'RNEWSLETTER_%\')');

		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'configuration`
			WHERE `name` LIKE \'RNEWSLETTER_%\'');

		return true;
	}

	public function isActive()
	{
		return Configuration::get('RNEWSLETTER_ACTIVE');
	}

	public function isRewardsAccountVisible()
	{
		return $this->isActive();
	}

	public function getTitle()
	{
		return $this->l('Newsletter sign-up');
	}

	public function getDetails($reward, $admin) {
		return  $this->l('Newsletter sign-up');
	}

	protected function postProcess($params=null)
	{
		if (Tools::isSubmit('submitNewsletter')) {
			$this->_postValidation();
			if (!sizeof($this->_errors)) {
				Configuration::updateValue('RNEWSLETTER_ACTIVE', (int)Tools::getValue('newsletter_active'));
				foreach ($this->instance->getCurrencies() as $currency)
					Configuration::updateValue('RNEWSLETTER_REWARD_VALUE_'.$currency['id_currency'], (float)Tools::getValue('newsletter_reward_value_'.$currency['id_currency']));
				$this->instance->confirmation = $this->instance->displayConfirmation($this->l('Settings updated.'));
			} else
				$this->instance->errors = $this->instance->displayError(implode('<br />', $this->_errors));
		} else if (Tools::isSubmit('submitNewsletterNotifications')) {
			Configuration::updateValue('RNEWSLETTER_MAIL', (int)Tools::getValue('newsletter_mail'));
			$this->instance->confirmation = $this->instance->displayConfirmation($this->l('Settings updated.'));
		}
	}

	private function _postValidation()
	{
		if (Tools::isSubmit('submitNewsletter')) {
			foreach ($this->instance->getCurrencies() as $currency) {
				if (!Tools::getValue('newsletter_reward_value_'.$currency['id_currency']) || !Validate::isUnsignedFloat(Tools::getValue('newsletter_reward_value_'.$currency['id_currency'])))
					$this->_errors[] = sprintf($this->l('The reward value is required/invalid for the currency %s'), $currency['name']);
			}
		}
	}

	public function displayForm()
	{
		if (Tools::getValue('stats'))
			return $this->_getStatistics();

		$this->postProcess();

		$newsletter_reward_values = array();
		$currencies = $this->instance->getCurrencies();
		foreach($currencies as $currency)
			$newsletter_reward_values[$currency['id_currency']] = (float)Tools::getValue('newsletter_reward_value_'.$currency['id_currency'], (float)Configuration::get('RNEWSLETTER_REWARD_VALUE_'.$currency['id_currency']));

		$this->context->smarty->assign(array(
			'module' => $this->instance,
			'object' => $this,
			'currencies' => $currencies,
			'currency' => $this->context->currency,
			'newsletter_active' => (int)Tools::getValue('newsletter_active', Configuration::get('RNEWSLETTER_ACTIVE')),
			'newsletter_mail' => (int)Tools::getValue('newsletter_mail', Configuration::get('RNEWSLETTER_MAIL')),
			'newsletter_reward_values' => $newsletter_reward_values,
		));
		return $this->instance->display($this->instance->path, 'views/templates/admin/admin-newsletter.tpl');
	}

	public function hookActionCustomerAccountAdd($params)
	{
		$this->_generateReward($params['newCustomer']);
		return true;
	}

	public function hookActionCustomerAccountUpdate($params)
    {
    	$this->_generateReward($params['customer']);
        return true;
    }

    public function hookActionNewsletterRegistrationAfter($params)
	{
		$email = $params['email'];
		$action = $params['action'];
		$customer = new Customer();
		if (!$action && Validate::isEmail($email)) {
			$customer = $customer->getByEmail($email);
			$this->_generateReward($customer);
		}
		return true;
	}

    private function _generateReward($customer)
    {
    	// TODO : envoi d'email à conditionner par une option
    	if (Validate::isLoadedObject($customer) && $customer->newsletter) {
    		if (!Db::getInstance()->getValue('SELECT 1 FROM `'._DB_PREFIX_.'rewards` WHERE plugin=\''.pSQL($this->name).'\' AND id_customer='.(int)$customer->id)) {
    			$credits = (float)Configuration::get('RNEWSLETTER_REWARD_VALUE_'.(int)$this->context->currency->id);

				$reward = new RewardsModel();
				$reward->plugin = $this->name;
				$reward->id_customer = (int)$customer->id;
				$reward->id_reward_state = RewardsStateModel::getValidationId();
				$reward->credits = round(Tools::convertPrice($credits, $this->context->currency, false), 2);
				if (Configuration::get('REWARDS_DURATION'))
					$reward->date_end = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d') + Configuration::get('REWARDS_DURATION'), date('Y')));
				if ($reward->save()) {
					if (Configuration::get('RNEWSLETTER_MAIL')) {
						$lang = (int)Configuration::get('PS_LANG_DEFAULT');
						if (version_compare(_PS_VERSION_, '1.5.4.0', '>='))
							$lang = (int)$customer->id_lang;
						$reward_amount = $this->instance->getRewardReadyForDisplay($credits, (int)$this->context->currency->id, $lang, false);
						$data = array(
							'{customer_firstname}' => $customer->firstname,
							'{customer_lastname}' => $customer->lastname,
							'{customer_reward}' => $reward_amount,
							'{link_rewards}' => $this->context->link->getModuleLink('allinone_rewards', 'rewards', array(), true)
						);
						$this->instance->sendMail($lang, 'newsletter-validation', $this->l('You just got a new reward', $lang), $data, $customer->email, $customer->firstname.' '.$customer->lastname);
					}
				}
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
			WHERE plugin=\'newsletter\'
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
		return $this->instance->display($this->instance->path, 'views/templates/admin/admin-newsletter-statistics.tpl');
	}
}