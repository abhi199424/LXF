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

class RewardsReviewPlugin extends RewardsGenericPlugin
{
	public $name = 'review';

	public function install()
	{
		if (!Configuration::updateValue('RREVIEW_ACTIVE', 0) || !Configuration::updateValue('RREVIEW_MAIL', 1) || !Configuration::updateValue('RREVIEW_PRODUCT', 0) || !Configuration::updateValue('RREVIEW_PRODUCT_MAX', 0) || !Configuration::updateValue('RREVIEW_PRODUCT_RATING', 0) || !Configuration::updateValue('RREVIEW_MAX_PER_PRODUCT', 0) ||
			!Configuration::updateValue('RREVIEW_SITE', 0) || !Configuration::updateValue('RREVIEW_SITE_MAX', 0) || !Configuration::updateValue('RREVIEW_SITE_RATING', 0) || !Configuration::updateGlobalValue('RREVIEW_CRON_SECURE_KEY', Tools::strtoupper(Tools::passwdGen(16))))
			return false;

		foreach ($this->instance->getCurrencies() as $currency) {
			Configuration::updateValue('RREVIEW_REWARD_PRODUCT_VALUE_'.(int)($currency['id_currency']), 1);
			Configuration::updateValue('RREVIEW_REWARD_SITE_VALUE_'.(int)($currency['id_currency']), 1);
		}

		Db::getInstance()->execute('
		CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'rewards_review` (
			`id_reward_review` INT UNSIGNED NOT NULL AUTO_INCREMENT,
			`id_reward` INT UNSIGNED NOT NULL,
			`id_review` VARCHAR(50) NOT NULL,
			`api` VARCHAR(50) NOT NULL,
			`type` VARCHAR(20) NOT NULL,
			`id_product` INT UNSIGNED DEFAULT NULL,
			`date_add` DATETIME NOT NULL,
			PRIMARY KEY (`id_reward_review`),
			UNIQUE KEY `index_unique_rewards_review_reward` (`id_reward`),
			UNIQUE KEY `index_unique_rewards_review_review_api_type` (`id_review`, `api`, `type`),
  			INDEX `index_rewards_review_review` (`id_review`),
			INDEX `index_rewards_review_api` (`api`),
			INDEX `index_rewards_review_type` (`type`)
		) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8 ;');

		return true;
	}

	public function uninstall()
	{
		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'configuration_lang`
			WHERE `id_configuration` IN (SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE `name` LIKE \'RREVIEW_%\')');

		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'configuration`
			WHERE `name` LIKE \'RREVIEW_%\'');

		return true;
	}

	public function isActive()
	{
		$id_template=0;
		if (isset($this->context->customer))
			$id_template = (int)MyConf::getIdTemplate('review', $this->context->customer->id);
		return MyConf::get('RREVIEW_ACTIVE', null, $id_template);
	}

	public function isRewardsAccountVisible()
	{
		return $this->isActive();
	}

	public function getTitle()
	{
		return $this->l('Customer reviews');
	}

	public function getDetails($reward, $admin) {
		if ($row = RewardsReviewModel::getRewardDetails($reward['id_reward'])) {
			if ($row['id_product']) {
				$product = new Product((int)$row['id_product']);
				if ($reward['id_order'])
					return sprintf($this->l('Product review - %s (order #%s)'), isset($product->name[$this->context->language->id]) ? $product->name[$this->context->language->id] : (int)$row['id_product'], $admin ? '<a href="'.$this->context->link->getAdminLink('AdminOrders', true, [], ['id_order'=> $reward['id_order'], 'vieworder' => 1]).'" style="display: inline; width: auto">'.(empty($row['reference']) ? sprintf('%06d', $reward['id_order']) : $row['reference']).'</a>' : (empty($row['reference']) ? sprintf('%06d', $reward['id_order']) : $row['reference']));
				else
					return sprintf($this->l('Product review - %s'), isset($product->name[$this->context->language->id]) ? $product->name[$this->context->language->id] : (int)$row['id_product']);
			} else {
				if ($reward['id_order'])
					return sprintf($this->l('Site review - order #%s'), $admin ? '<a href="'.$this->context->link->getAdminLink('AdminOrders', true, [], ['id_order'=> $reward['id_order'], 'vieworder' => 1]).'" style="display: inline; width: auto">'.(empty($row['reference']) ? sprintf('%06d', $reward['id_order']) : $row['reference']).'</a>' : (empty($row['reference']) ? sprintf('%06d', $reward['id_order']) : $row['reference']));
				else
					return $this->l('Site review');
			}
		} else
			return '';
	}

	protected function postProcess($params=null)
	{
		// on initialise le template à chaque chargement
		$this->initTemplate();

		if (Tools::isSubmit('submitReview')) {
			$this->_postValidation();
			if (!sizeof($this->_errors)) {
				MyConf::updateValue('RREVIEW_ACTIVE', (int)Tools::getValue('review_active'), null, $this->id_template);
        		MyConf::updateValue('RREVIEW_PRODUCT', (int)Tools::getValue('review_product'), null, $this->id_template);
        		MyConf::updateValue('RREVIEW_PRODUCT_MAX', (int)Tools::getValue('review_product_max'), null, $this->id_template);
        		MyConf::updateValue('RREVIEW_PRODUCT_RATING', (int)Tools::getValue('review_product_rating'), null, $this->id_template);
        		MyConf::updateValue('RREVIEW_MAX_PER_PRODUCT', (int)Tools::getValue('review_max_per_product'), null, $this->id_template);
           		MyConf::updateValue('RREVIEW_SITE', (int)Tools::getValue('review_site'), null, $this->id_template);
        		MyConf::updateValue('RREVIEW_SITE_MAX', (int)Tools::getValue('review_site_max'), null, $this->id_template);
        		MyConf::updateValue('RREVIEW_SITE_RATING', (int)Tools::getValue('review_site_rating'), null, $this->id_template);
        		// si on change la date de début, on reset la LAST_CHECK de toutes les API
        		$types = ['product', 'site'];
        		$scandir = scandir(_PS_MODULE_DIR_.'/allinone_rewards/api');
        		foreach($types as $type) {
	        		if (Tools::getValue('review_'.$type.'_from') != MyConf::get('RREVIEW_'.strtoupper($type).'_FROM', null, $this->id_template)) {
						foreach($scandir as $api) {
							if ($api != '.' && $api != '..' && $api != 'index.php' && $api != 'ReviewGenericAPI.php') {
						  		require_once(_PS_MODULE_DIR_.'/allinone_rewards/api/'.$api);
						  		$classname = str_replace('.php', '', $api);
								$review_api = new $classname($this->instance);
								MyConf::updateValue('RREVIEW_'.$review_api->getCode().'_'.strtoupper($type).'_LAST_CHECK', '', null, $this->id_template);
							}
						}
			       		MyConf::updateValue('RREVIEW_'.strtoupper($type).'_FROM', Tools::getValue('review_'.$type.'_from'), null, $this->id_template);
	        		}
	        	}
        		if (empty($this->id_template))
					Configuration::updateValue('RREVIEW_API', Tools::getValue('review_api'));
				if (!Configuration::getGlobalValue('RREVIEW_CRON_SECURE_KEY'))
					Configuration::updateGlobalValue('RREVIEW_CRON_SECURE_KEY', Tools::strtoupper(Tools::passwdGen(16)));

				foreach ($this->instance->getCurrencies() as $currency) {
					MyConf::updateValue('RREVIEW_REWARD_PRODUCT_VALUE_'.$currency['id_currency'], (float)Tools::getValue('review_reward_product_value_'.$currency['id_currency']), null, $this->id_template);
					MyConf::updateValue('RREVIEW_REWARD_SITE_VALUE_'.$currency['id_currency'], (float)Tools::getValue('review_reward_site_value_'.$currency['id_currency']), null, $this->id_template);
				}

				$classname = Tools::getValue('review_api');
				if (!empty($classname)) {
					require_once(_PS_MODULE_DIR_.'/allinone_rewards/api/'.$classname.'.php');
					$api = new $classname($this->instance);
					$api->postProcess($this->id_template);
				}

				$this->instance->confirmation = $this->instance->displayConfirmation($this->l('Settings updated.'));
			} else
				$this->instance->errors = $this->instance->displayError(implode('<br />', $this->_errors));
		} else if (Tools::isSubmit('submitReviewNotifications')) {
			Configuration::updateValue('RREVIEW_MAIL', (int)Tools::getValue('review_mail'));
			$this->instance->confirmation = $this->instance->displayConfirmation($this->l('Settings updated.'));
		}
	}

	private function _postValidation()
	{
		if (Tools::isSubmit('submitReview')) {
			if ((int)Tools::getValue('review_active') && !Tools::getValue('review_product') && !Tools::getValue('review_site'))
				$this->_errors[] = $this->l('Please select at least 1 reward.');

			if ((int)Tools::getValue('review_product') && (empty(Tools::getValue('review_product_from')) || !Validate::isDate(Tools::getValue('review_product_from'))))
            	$this->_errors[] = $this->l('Starting date for products reviews is required/invalid.');

        	if ((int)Tools::getValue('review_site') && (empty(Tools::getValue('review_site_from')) || !Validate::isDate(Tools::getValue('review_site_from'))))
            	$this->_errors[] = $this->l('Starting date for site reviews is required/invalid.');

			if ((int)Tools::getValue('review_product') && (!is_numeric(Tools::getValue('review_product_max')) || Tools::getValue('review_product_max') < 0))
				$this->_errors[] = $this->l('The maximum number of rewarded product reviews per customer is required/invalid.');

			if ((int)Tools::getValue('review_product') && (!is_numeric(Tools::getValue('review_max_per_product')) || Tools::getValue('review_max_per_product') < 0))
				$this->_errors[] = $this->l('The maximum number of rewarded reviews per customer for each product is required/invalid.');

			if ((int)Tools::getValue('review_product') && is_numeric(Tools::getValue('review_product_max')) && is_numeric(Tools::getValue('review_max_per_product')) && (Tools::getValue('review_product_max') > 0 && (Tools::getValue('review_max_per_product') > Tools::getValue('review_product_max') || Tools::getValue('review_max_per_product')==0)))
				$this->_errors[] = $this->l('The maximum number of rewarded reviews per customer for each product can\'t be higher than the maximum number of rewarded product reviews');

			if ((int)Tools::getValue('review_site') && (!is_numeric(Tools::getValue('review_site_max')) || Tools::getValue('review_site_max') < 0))
				$this->_errors[] = $this->l('The maximum number of rewarded site reviews per customer is required/invalid.');

			foreach ($this->instance->getCurrencies() as $currency) {
				if ((int)Tools::getValue('review_product') && !Tools::getValue('review_reward_product_value_'.$currency['id_currency']) || !Validate::isUnsignedFloat(Tools::getValue('review_reward_product_value_'.$currency['id_currency'])))
					$this->_errors[] = sprintf($this->l('The reward value for product review is required/invalid for the currency %s'), $currency['name']);
				if ((int)Tools::getValue('review_site') && !Tools::getValue('review_reward_site_value_'.$currency['id_currency']) || !Validate::isUnsignedFloat(Tools::getValue('review_reward_site_value_'.$currency['id_currency'])))
					$this->_errors[] = sprintf($this->l('The reward value for site review is required/invalid for the currency %s'), $currency['name']);
			}

			$classname = Tools::getValue('review_api');
			if (!empty($classname)) {
				require_once(_PS_MODULE_DIR_.'/allinone_rewards/api/'.$classname.'.php');
				$api = new $classname($this->instance);
				$api->postValidation($this->_errors);
			}
		}
	}

	public function displayForm()
	{
		if (Tools::getValue('stats'))
			return $this->_getStatistics();

		$this->postProcess();

		$review_reward_product_values = array();
		$review_reward_site_values = array();
		$currencies = $this->instance->getCurrencies();
		foreach($currencies as $currency) {
			$review_reward_product_values[$currency['id_currency']] = (float)Tools::getValue('review_reward_product_value_'.$currency['id_currency'], (float)MyConf::get('RREVIEW_REWARD_PRODUCT_VALUE_'.$currency['id_currency'], null, $this->id_template));
			$review_reward_site_values[$currency['id_currency']] = (float)Tools::getValue('review_reward_site_value_'.$currency['id_currency'], (float)MyConf::get('RREVIEW_REWARD_SITE_VALUE_'.$currency['id_currency'], null, $this->id_template));
		}

		$scandir = scandir(_PS_MODULE_DIR_.'/allinone_rewards/api');
		$apis = [];
		$selected_api = null;
		foreach($scandir as $api) {
			if ($api != '.' && $api != '..' && $api != 'index.php' && $api != 'ReviewGenericAPI.php') {
		  		require_once(_PS_MODULE_DIR_.'/allinone_rewards/api/'.$api);
		  		$classname = str_replace('.php', '', $api);
				$review_api = new $classname($this->instance);
				$apis[] = $review_api;
				if (Tools::getValue('review_api', Configuration::get('RREVIEW_API'))==$classname)
					$selected_api = $review_api;
			}
		}

		$nb_product_reviews = false;
		$nb_site_reviews = false;
		$product_reviews = false;
		$site_reviews = false;
		if (isset($selected_api)) {
			if ((int)MyConf::get('RREVIEW_PRODUCT', null, $this->id_template) && false!==($product_reviews=RewardsReviewModel::getValidatedReviews($selected_api, 'product', $this->id_template)))
				$nb_product_reviews = count($product_reviews);
			if ((int)MyConf::get('RREVIEW_SITE', null, $this->id_template) && false!==($site_reviews=RewardsReviewModel::getValidatedReviews($selected_api, 'site', $this->id_template)))
				$nb_site_reviews = count($site_reviews);
		}

		$this->context->smarty->assign(array(
			'id_template' => $this->id_template,
			'module' => $this->instance,
			'object' => $this,
			'lang_iso' => $this->context->language->iso_code,
			'currencies' => $currencies,
			'currency' => $this->context->currency,
			'review_active' => (int)Tools::getValue('review_active', MyConf::get('RREVIEW_ACTIVE', null, $this->id_template)),
			'review_mail' => (int)Tools::getValue('review_mail', Configuration::get('RREVIEW_MAIL')),
			'review_reward_product_values' => $review_reward_product_values,
			'review_reward_site_values' => $review_reward_site_values,
			'review_apis' => $apis,
			'selected_api' => $selected_api,
			'nb_product_reviews' => $nb_product_reviews,
			'nb_site_reviews' => $nb_site_reviews,
			'product_reviews' => $product_reviews,
			'site_reviews' => $site_reviews,
            'review_product' => (int)Tools::getValue('review_product', MyConf::get('RREVIEW_PRODUCT', null, $this->id_template)),
            'review_product_from' => Tools::getValue('review_product_from', MyConf::get('RREVIEW_PRODUCT_FROM', null, $this->id_template)),
            'review_product_max' => (int)Tools::getValue('review_product_max', MyConf::get('RREVIEW_PRODUCT_MAX', null, $this->id_template)),
            'review_product_rating' => (int)Tools::getValue('review_product_rating', MyConf::get('RREVIEW_PRODUCT_RATING', null, $this->id_template)),
            'review_max_per_product' => (int)Tools::getValue('review_max_per_product', MyConf::get('RREVIEW_MAX_PER_PRODUCT', null, $this->id_template)),
            'review_site' => (int)Tools::getValue('review_site', MyConf::get('RREVIEW_SITE', null, $this->id_template)),
            'review_site_from' => Tools::getValue('review_site_from', MyConf::get('RREVIEW_SITE_FROM', null, $this->id_template)),
            'review_site_max' => (int)Tools::getValue('review_site_max', MyConf::get('RREVIEW_SITE_MAX', null, $this->id_template)),
            'review_site_rating' => (int)Tools::getValue('review_site_rating', MyConf::get('RREVIEW_SITE_RATING', null, $this->id_template)),
            'review_cron_link' => $this->context->link->getModuleLink('allinone_rewards', 'cronreview', array('secure_key' => Configuration::getGlobalValue('RREVIEW_CRON_SECURE_KEY')), true),
            'token_order' => Tools::getAdminToken('AdminOrders'.(int)Tab::getIdFromClassName('AdminOrders').(int)$this->context->employee->id),
            'token_customer' => Tools::getAdminToken('AdminCustomers'.(int)Tab::getIdFromClassName('AdminCustomers').(int)$this->context->employee->id),
		));
		return $this->getTemplateForm($this->l('Customer reviews')).$this->instance->display($this->instance->path, 'views/templates/admin/admin-review.tpl');
	}

    private function _getStatistics()
	{
		$stats = array('nb_customers' => 0, 'nb_products_reviews' => 0, 'nb_site_reviews' => 0, 'nb_total_reviews' => 0, 'total_products_rewards' => 0, 'total_site_rewards' => 0, 'total_rewards' => 0);
		$query = '
			SELECT c.id_customer, c.firstname, c.lastname, SUM(IF(rr.type=\'product\', 1, 0)) AS nb_products_reviews, SUM(IF(rr.type=\'site\', 1, 0)) AS nb_site_reviews, SUM(IF(rr.type=\'product\', r.credits, 0)) AS total_products_rewards, SUM(IF(rr.type=\'site\', r.credits, 0)) AS total_site_rewards
			FROM `'._DB_PREFIX_.'rewards` r
			JOIN `'._DB_PREFIX_.'rewards_review` rr USING (id_reward)
			JOIN `'._DB_PREFIX_.'customer` AS c ON (c.id_customer=r.id_customer'.Shop::addSqlRestriction(false, 'c').')
			WHERE plugin=\'review\'
			GROUP BY id_customer';
		$stats['customers'] = Db::getInstance()->executeS($query);
		foreach ($stats['customers'] as $row) {
			$stats['nb_customers']++;
			$stats['nb_products_reviews'] += $row['nb_products_reviews'];
			$stats['nb_site_reviews'] += $row['nb_site_reviews'];
			$stats['nb_total_reviews'] += $row['nb_products_reviews'] + $row['nb_site_reviews'];
			$stats['total_products_rewards'] += (float)$row['total_products_rewards'];
			$stats['total_site_rewards'] += (float)$row['total_site_rewards'];
			$stats['total_rewards'] += (float)$row['total_products_rewards'] + (float)$row['total_site_rewards'];
		}

		$this->context->smarty->assign(array(
			'module' => $this->instance,
			'object' => $this,
			'token' => Tools::getAdminToken('AdminCustomers'.(int)Tab::getIdFromClassName('AdminCustomers').(int)$this->context->employee->id),
			'stats' => $stats,
		));
		return $this->instance->display($this->instance->path, 'views/templates/admin/admin-review-statistics.tpl');
	}
}