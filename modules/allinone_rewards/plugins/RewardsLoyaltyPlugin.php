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

class RewardsLoyaltyPlugin extends RewardsGenericPlugin
{
	public $name = 'loyalty';

	public function install()
	{
		// hooks
		if (!$this->registerHook('displayRightColumnProduct') || !$this->registerHook('displayProductButtons') || !$this->registerHook('displayProductAdditionalInfo') || !$this->registerHook('displayShoppingCartFooter')
		|| !$this->registerHook('actionValidateOrder') || !$this->registerHook('actionOrderStatusUpdate')
		|| !$this->registerHook('actionProductCancel') || !$this->registerHook('actionObjectOrderDetailAddAfter') || !$this->registerHook('actionObjectOrderDetailUpdateAfter') || !$this->registerHook('actionObjectOrderDetailDeleteAfter')
		|| !$this->registerHook('displayAdminOrder') || (version_compare(_PS_VERSION_, '1.7.7.0', '>=') && !$this->registerHook('displayAdminOrderMainBottom'))
		|| !$this->registerHook('displayPDFInvoice'))
			return false;

		$groups_config = '';
		$groups = Group::getGroups((int)(Configuration::get('PS_LANG_DEFAULT')));
		foreach ($groups as $group)
			$groups_config .= (int)$group['id_group'].',';
		$groups_config = rtrim($groups_config, ',');

		if (!Configuration::updateValue('RLOYALTY_TYPE', 0)
		|| !Configuration::updateValue('RLOYALTY_TAX', 1)
		|| !Configuration::updateValue('RLOYALTY_POINT_VALUE', 0.50)
		|| !Configuration::updateValue('RLOYALTY_POINT_RATE', 10)
		|| !Configuration::updateValue('RLOYALTY_PERCENTAGE', 5)
		|| !Configuration::updateValue('RLOYALTY_DEDUCE_VOUCHERS', 1)
		|| !Configuration::updateValue('RLOYALTY_DEFAULT_PRODUCT_REWARD', 0)
		|| !Configuration::updateValue('RLOYALTY_DEFAULT_PRODUCT_TYPE', 0)
		|| !Configuration::updateValue('RLOYALTY_MULTIPLIER', 1)
		|| !Configuration::updateValue('RLOYALTY_DISCOUNTED_ALLOWED', 1)
		|| !Configuration::updateValue('RLOYALTY_MAX_REWARD', 0)
		|| !Configuration::updateValue('RLOYALTY_ACTIVE', 0)
		|| !Configuration::updateValue('RLOYALTY_DISPLAY_PRODUCT', 1)
		|| !Configuration::updateValue('RLOYALTY_DISPLAY_CART', 1)
		|| !Configuration::updateValue('RLOYALTY_DISPLAY_INVOICE', 1)
		|| !Configuration::updateValue('RLOYALTY_MAIL_VALIDATION', 1)
		|| !Configuration::updateValue('RLOYALTY_MAIL_CANCELPROD', 1)
		|| !Configuration::updateValue('RLOYALTY_GROUPS', $groups_config)
		|| !Configuration::updateValue('RLOYALTY_ALL_CATEGORIES', 1)
		|| !Configuration::updateValue('RLOYALTY_CATEGORIES', ''))
			return false;

		// create an invisible tab so we can call an admin controller to manage the product rewards in the product page
		$tab = new Tab();
		$tab->active = 1;
		$tab->class_name = "AdminProductReward";
		$tab->name = array();
		foreach (Language::getLanguages(true) as $lang)
			$tab->name[$lang['id_lang']] = 'AllinoneRewards Product Reward';
		$tab->id_parent = -1;
		$tab->module = $this->instance->name;

		if (!$tab->add())
			return false;

		return true;
	}

	public function uninstall()
	{
		$id_tab = (int)Tab::getIdFromClassName('AdminProductReward');
		if ($id_tab) {
			$tab = new Tab($id_tab);
			$tab->delete();
		}

		//Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'rewards_product`;');
		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'configuration_lang`
			WHERE `id_configuration` IN (SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE `name` LIKE \'RLOYALTY_%\')');

		Db::getInstance()->execute('
			DELETE FROM `'._DB_PREFIX_.'configuration`
			WHERE `name` LIKE \'RLOYALTY_%\'');

		return true;
	}

	public function isActive()
	{
		$id_template=0;
		if (isset($this->context->customer))
			$id_template = (int)MyConf::getIdTemplate('loyalty', $this->context->customer->id);
		return MyConf::get('RLOYALTY_ACTIVE', null, $id_template);
	}

	public function isRewardsAccountVisible()
	{
		return $this->isActive() && $this->_isCustomerAllowed($this->context->customer);
	}

	public function getTitle()
	{
		return $this->l('Loyalty program');
	}

	public function getDetails($reward, $admin)
	{
		$reference = $reward['order_reference'] ? $reward['order_reference'] : sprintf('%06d', $reward['id_order']);
		if ($admin) {
			if (version_compare(_PS_VERSION_, '1.7', '>='))
				return sprintf($this->l('Loyalty - order #%s'), '<a href="'.$this->context->link->getAdminLink('AdminOrders', true, [], ['id_order'=> $reward['id_order'], 'vieworder' => 1]).'" style="display: inline; width: auto">'.$reference.'</a>');
			else
				return sprintf($this->l('Loyalty - order #%s'), '<a href="'.$this->context->link->getAdminLink('AdminOrders').'&vieworder=1&id_order='.$reward['id_order'].'" style="display: inline; width: auto">'.$reference.'</a>');
		} else
			return sprintf($this->l('Loyalty - order #%s'), $reference);
	}

	protected function postProcess($params=null)
	{
		// on initialise le template à chaque chargement
		$this->initTemplate();

		if (Tools::isSubmit('submitLoyalty')) {
			$this->_postValidation();
			if (!sizeof($this->_errors)) {
				if (empty($this->id_template)) {
					Configuration::updateValue('RLOYALTY_GROUPS', implode(",", Tools::getValue('rloyalty_groups')));
				}
				MyConf::updateValue('RLOYALTY_ACTIVE', (int)Tools::getValue('rloyalty_active'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_DISPLAY_PRODUCT', (int)Tools::getValue('rloyalty_display_product'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_DISPLAY_CART', (int)Tools::getValue('rloyalty_display_cart'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_DISPLAY_INVOICE', (int)Tools::getValue('rloyalty_display_invoice'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_TYPE', (int)Tools::getValue('rloyalty_type'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_TAX', (int)Tools::getValue('rloyalty_tax'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_POINT_VALUE', (float)Tools::getValue('rloyalty_point_value'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_POINT_RATE', (float)Tools::getValue('rloyalty_point_rate'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_PERCENTAGE', (float)Tools::getValue('rloyalty_percentage'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_DEDUCE_VOUCHERS', (int)Tools::getValue('rloyalty_deduce_vouchers'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_DEFAULT_PRODUCT_REWARD', (float)Tools::getValue('rloyalty_default_product_reward'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_DEFAULT_PRODUCT_TYPE', (int)Tools::getValue('rloyalty_default_product_type'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_MULTIPLIER', (float)Tools::getValue('rloyalty_multiplier'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_DISCOUNTED_ALLOWED', (int)Tools::getValue('rloyalty_discounted_allowed'), null, $this->id_template);
				MyConf::updateValue('RLOYALTY_MAX_REWARD', (float)Tools::getValue('rloyalty_max_reward'), null, $this->id_template);
				if (!Tools::getValue('rloyalty_type') || (int)Tools::getValue('rloyalty_type') == 1) {
					MyConf::updateValue('RLOYALTY_ALL_CATEGORIES', (int)Tools::getValue('rloyalty_all_categories'), null, $this->id_template);
					MyConf::updateValue('RLOYALTY_CATEGORIES', Tools::getValue('categoryBox') ? implode(',', Tools::getValue('categoryBox')) : '', null, $this->id_template);
				}
				$this->instance->confirmation = $this->instance->displayConfirmation($this->l('Settings updated.'));
			} else
				$this->instance->errors = $this->instance->displayError(implode('<br />', $this->_errors));
		} else if (Tools::isSubmit('submitLoyaltyNotifications')) {
			Configuration::updateValue('RLOYALTY_MAIL_VALIDATION', (int)Tools::getValue('rloyalty_mail_validation'));
			Configuration::updateValue('RLOYALTY_MAIL_CANCELPROD', (int)Tools::getValue('rloyalty_mail_cancel_product'));
			$this->instance->confirmation = $this->instance->displayConfirmation($this->l('Settings updated.'));
		}
	}

	private function _postValidation()
	{
		if (empty($this->id_template)) {
			if (!is_array(Tools::getValue('rloyalty_groups')))
				$this->_errors[] = $this->l('Please select at least 1 customer group allowed to get loyalty rewards');
		}
		if ((int)Tools::getValue('rloyalty_type')==0 && (!is_numeric(Tools::getValue('rloyalty_point_rate')) || Tools::getValue('rloyalty_point_rate') <= 0))
			$this->_errors[] = $this->l('The ratio is required/invalid.');
		if ((int)Tools::getValue('rloyalty_type')==0 && (!is_numeric(Tools::getValue('rloyalty_point_value')) || Tools::getValue('rloyalty_point_value') <= 0))
			$this->_errors[] = $this->l('The value is required/invalid.');
		if ((int)Tools::getValue('rloyalty_type')==1 && (!is_numeric(Tools::getValue('rloyalty_percentage')) || Tools::getValue('rloyalty_percentage') <= 0))
			$this->_errors[] = $this->l('The percentage is required/invalid.');
		if ((int)Tools::getValue('rloyalty_type')==2 && (!is_numeric(Tools::getValue('rloyalty_default_product_reward')) || Tools::getValue('rloyalty_default_product_reward') < 0))
			$this->_errors[] = $this->l('The default reward is invalid.');
		if ((int)Tools::getValue('rloyalty_type')==2 && (!is_numeric(Tools::getValue('rloyalty_multiplier')) || Tools::getValue('rloyalty_multiplier') <= 0))
			$this->_errors[] = $this->l('The coefficient multiplier is required/invalid.');
		if (Tools::getValue('rloyalty_max_reward') && !Validate::isUnsignedFloat(Tools::getValue('rloyalty_max_reward')))
			$this->_errors[] = $this->l('The maximum reward is invalid.');
		if ((!Tools::getValue('rloyalty_type') || (int)Tools::getValue('rloyalty_type')==1) && !Tools::getValue('rloyalty_all_categories') && (!is_array(Tools::getValue('categoryBox')) || !sizeof(Tools::getValue('categoryBox'))))
			$this->_errors[] = $this->l('You must choose at least one category of products');
	}

	public function displayForm()
	{
		if (Tools::getValue('stats'))
			return $this->_getStatistics();

		$this->postProcess();

		$this->context->smarty->assign(array(
			'module' => $this->instance,
			'object' => $this,
			'rloyalty_active' => (int)Tools::getValue('rloyalty_active', MyConf::get('RLOYALTY_ACTIVE', null, $this->id_template)),
			'currency' => new Currency((int)Configuration::get('PS_CURRENCY_DEFAULT')),
			'groups' => Group::getGroups((int)$this->context->language->id),
			'allowed_groups' => Tools::getValue('rloyalty_groups', explode(',', Configuration::get('RLOYALTY_GROUPS'))),
			'categories' => $this->getCategoriesTree(Tools::getValue('categoryBox', explode(',', MyConf::get('RLOYALTY_CATEGORIES', null, $this->id_template)))),
			'rloyalty_display_product' => (int)Tools::getValue('rloyalty_display_product', MyConf::get('RLOYALTY_DISPLAY_PRODUCT', null, $this->id_template)),
			'rloyalty_display_cart' => (int)Tools::getValue('rloyalty_display_cart', MyConf::get('RLOYALTY_DISPLAY_CART', null, $this->id_template)),
			'rloyalty_display_invoice' => (int)Tools::getValue('rloyalty_display_invoice', MyConf::get('RLOYALTY_DISPLAY_INVOICE', null, $this->id_template)),
			'rloyalty_type' => (int)Tools::getValue('rloyalty_type', MyConf::get('RLOYALTY_TYPE', null, $this->id_template)),
			'rloyalty_deduce_vouchers' => (int)Tools::getValue('rloyalty_deduce_vouchers', MyConf::get('RLOYALTY_DEDUCE_VOUCHERS', null, $this->id_template)),
			'rloyalty_point_rate' => (float)Tools::getValue('rloyalty_point_rate', (float)MyConf::get('RLOYALTY_POINT_RATE', null, $this->id_template)),
			'rloyalty_point_value' => (float)Tools::getValue('rloyalty_point_value', (float)MyConf::get('RLOYALTY_POINT_VALUE', null, $this->id_template)),
			'rloyalty_percentage' => (float)Tools::getValue('rloyalty_percentage', (float)MyConf::get('RLOYALTY_PERCENTAGE', null, $this->id_template)),
			'rloyalty_default_product_reward' => (float)Tools::getValue('rloyalty_default_product_reward', (float)MyConf::get('RLOYALTY_DEFAULT_PRODUCT_REWARD', null, $this->id_template)),
			'rloyalty_default_product_type' => (int)Tools::getValue('rloyalty_default_product_type', (int)MyConf::get('RLOYALTY_DEFAULT_PRODUCT_TYPE', null, $this->id_template)),
			'rloyalty_multiplier' => (float)Tools::getValue('rloyalty_multiplier', (float)MyConf::get('RLOYALTY_MULTIPLIER', null, $this->id_template)),
			'rloyalty_all_categories' => (int)Tools::getValue('rloyalty_all_categories', MyConf::get('RLOYALTY_ALL_CATEGORIES', null, $this->id_template)),
			'rloyalty_tax' => (int)Tools::getValue('rloyalty_tax', MyConf::get('RLOYALTY_TAX', null, $this->id_template)),
			'rloyalty_discount_allowed' => (int)MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, $this->id_template),
			'rloyalty_max_reward' => (float)Tools::getValue('rloyalty_max_reward', (float)MyConf::get('RLOYALTY_MAX_REWARD', null, $this->id_template)),
			'rloyalty_mail_validation' => (int)Tools::getValue('rloyalty_mail_validation', Configuration::get('RLOYALTY_MAIL_VALIDATION')),
			'rloyalty_mail_cancel_product' => (int)Tools::getValue('rloyalty_mail_cancel_product', Configuration::get('RLOYALTY_MAIL_CANCELPROD')),
		));
		return $this->getTemplateForm($this->l('Loyalty')).$this->instance->display($this->instance->path, 'views/templates/admin/admin-loyalty.tpl');
	}

	private function _getStatistics()
	{
		$this->instanceDefaultStates();

		$stats = array('total_rewards_valid' => 0, 'total_rewards_invalid' => 0, 'nb_orders' => 0, 'nb_customers' => 0, 'credits' => 0, 'customers' => array());
		$query = '
			SELECT c.id_customer, c.firstname, c.lastname, COUNT(DISTINCT r.id_order) AS nb_orders, SUM(IF(id_reward_state IN ('.RewardsStateModel::getValidationId().','.RewardsStateModel::getConvertId().','.RewardsStateModel::getWaitingPaymentId().','.RewardsStateModel::getPaidId().'), credits, 0)) AS credits_valid, SUM(IF(id_reward_state IN ('.RewardsStateModel::getDefaultId().','.RewardsStateModel::getReturnPeriodId().'), credits, 0)) AS credits_invalid
			FROM `'._DB_PREFIX_.'rewards` r
			JOIN `'._DB_PREFIX_.'customer` AS c ON (c.id_customer=r.id_customer'.Shop::addSqlRestriction(false, 'c').')
			WHERE plugin=\'loyalty\'
			GROUP BY id_customer';
		$rows = Db::getInstance()->executeS($query);
		foreach ($rows as $row) {
			$stats['customers'][$row['id_customer']] = $row;
			$stats['nb_orders'] += (int)$row['nb_orders'];
			$stats['nb_customers']++;
			$stats['total_rewards_valid'] += (float)$row['credits_valid'];
			$stats['total_rewards_invalid'] += (float)$row['credits_invalid'];
		}

		$this->context->smarty->assign(array(
			'module' => $this->instance,
			'object' => $this,
			'token' => Tools::getAdminToken('AdminCustomers'.(int)Tab::getIdFromClassName('AdminCustomers').(int)$this->context->employee->id),
			'stats' => $stats,
		));
		return $this->instance->display($this->instance->path, 'views/templates/admin/admin-loyalty-statistics.tpl');
	}

	// check if customer is in a group which is allowed to get loyalty rewards
	// if bCheckDefault is true, then return true if the default group is checked (to know if we display the rewards for people not logged in)
	private function _isCustomerAllowed($customer, $bCheckDefault=false)
	{
		$allowed_groups = explode(',', Configuration::get('RLOYALTY_GROUPS'));
		if (Validate::isLoadedObject($customer)) {
			// if the customer is linked to a template, then it overrides the groups setting
			if ((int)MyConf::getIdTemplate('loyalty', $customer->id))
				return true;
			$customer_groups = $customer->getGroups();
			return sizeof(array_intersect($allowed_groups, $customer_groups)) > 0;
		} else if ($bCheckDefault && in_array(Configuration::get('PS_UNIDENTIFIED_GROUP'), $allowed_groups)) {
			return true;
		}
	}

	// convert the string into an array of object(array) which have id_category as key
	private function _getAllowedCategories()
	{
		$id_template=0;
		if (isset($this->context->customer))
			$id_template = (int)MyConf::getIdTemplate('loyalty', $this->context->customer->id);
		if (MyConf::get('RLOYALTY_ALL_CATEGORIES', null, $id_template))
			return NULL;
		else {
			$allowed_categories = array();
			$categories = explode(',', MyConf::get('RLOYALTY_CATEGORIES', null, $id_template));
			foreach($categories as $category) {
				$allowed_categories[] = array('id_category' => $category);
			}
			return $allowed_categories;
		}
	}

	// check if the product is in a category which is allowed to give loyalty rewards
	// or if a reward is defined on that product
	private function _isProductAllowed($id_product)
	{
		$product = new Product((int)$id_product);
		if (!Validate::isLoadedObject($product) || !$product->active || !$product->available_for_order)
			return false;

		$id_template = (int)MyConf::getIdTemplate('loyalty', $this->context->customer->id);
		if ((int)MyConf::get('RLOYALTY_TYPE', null, $id_template) == 0 || (int)MyConf::get('RLOYALTY_TYPE', null, $id_template) == 1) {
			if (MyConf::get('RLOYALTY_ALL_CATEGORIES', null, $id_template))
				return true;
			return Product::idIsOnCategoryId($id_product, $this->_getAllowedCategories());
		} else
			return RewardsProductModel::isProductRewarded($id_product, $id_template, 'loyalty');
	}

	// return the total of the cart for the reward calculation, in the cart currency
	// TODO : tenir compte des prix dégressifs
	private function _getCartTotalForReward($newProduct=NULL, $quantity=0)
	{
		$benefits = false;
		$total = 0;
		$cartProducts = array();
		$cart_currency = $this->context->currency;
		$cart = $this->context->cart;
		$id_template = (int)MyConf::getIdTemplate('loyalty', $this->context->customer->id);
		$allowedCategories = $this->_getAllowedCategories();

		if (Validate::isLoadedObject($cart)) {
			$cartProducts = $cart->getProducts();
			$cart_currency = new Currency((int)$cart->id_currency);
		}

		if (isset($newProduct) && !empty($newProduct->id)) {
			$found = false;
			foreach ($cartProducts as $key => $product) {
				if ($newProduct->id == $product['id_product'] && $newProduct->id_product_attribute == $product['id_product_attribute']) {
					$found = true;
					$cartProducts[$key]['cart_quantity'] += $quantity;
				}
			}
			if (!$found) {
				$cartProductsNew = array();
				$cartProductsNew['id_product'] = (int)$newProduct->id;
				$cartProductsNew['id_product_attribute'] = $newProduct->id_product_attribute ? (int)$newProduct->id_product_attribute : (int)$newProduct->getIdProductAttributeMostExpensive();
				$cartProductsNew['price'] = number_format($newProduct->getPrice(false, $cartProductsNew['id_product_attribute']), 2, '.', '');
				if (MyConf::get('RLOYALTY_TAX', null, $id_template))
					$cartProductsNew['price_wt'] = number_format($newProduct->getPrice(true, $cartProductsNew['id_product_attribute']), 2, '.', '');
				$cartProductsNew['cart_quantity'] = $quantity;
				if ($benefits) {
					$product_attribute = $newProduct->getAttributeCombinationsById($cartProductsNew['id_product_attribute'], (int)(Configuration::get('PS_LANG_DEFAULT')));
					$cartProductsNew['wholesale_price'] = isset($product_attribute[0]['wholesale_price']) && (float)($product_attribute[0]['wholesale_price']) > 0 ? (float) $product_attribute[0]['wholesale_price'] : (float)$newProduct->wholesale_price;
				}
				$cartProducts[] = $cartProductsNew;
			}
		}

		$gifts = array();
		if (Validate::isLoadedObject($cart)) {
			foreach ($cart->getCartRules(CartRule::FILTER_ACTION_GIFT) as $rule) {
				$cart_rule = new CartRule($rule['id_cart_rule']);
				$gifts[$cart_rule->gift_product.'_'.$cart_rule->gift_product_attribute] = isset($gifts[$cart_rule->gift_product.'_'.$cart_rule->gift_product_attribute]) ? $gifts[$cart_rule->gift_product.'_'.$cart_rule->gift_product_attribute] + 1 : 1;
			}
		}

		foreach ($cartProducts as $product) {
			if ((!MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, $id_template) && RewardsModel::isDiscountedProduct($product['id_product'], (int)$product['id_product_attribute'], $quantity)) || (is_array($allowedCategories) && !Product::idIsOnCategoryId($product['id_product'], $allowedCategories))) {
				if (is_object($newProduct) && $product['id_product'] == $newProduct->id && $product['id_product_attribute'] == $newProduct->id_product_attribute)
					$this->context->smarty->assign('no_pts_discounted', 1);
				continue;
			}

			$quantity = (int)$product['cart_quantity'] - (isset($gifts[$product['id_product'].'_'.$product['id_product_attribute']]) ? $gifts[$product['id_product'].'_'.$product['id_product_attribute']] : 0);
			if ($benefits)
				$total += ($product['price'] - ((float)$product['wholesale_price'] * (float)$cart_currency->conversion_rate)) * $quantity;
			else
				$total += (!MyConf::get('RLOYALTY_TAX', null, $id_template) ? $product['price'] : $product['price_wt']) * $quantity;
		}

		if (Validate::isLoadedObject($cart) && MyConf::get('RLOYALTY_DEDUCE_VOUCHERS', null, $id_template)) {
			foreach ($cart->getCartRules(CartRule::FILTER_ACTION_REDUCTION) as $cart_rule)
				$total -= $benefits || !MyConf::get('RLOYALTY_TAX', null, $id_template) ? $cart_rule['value_tax_exc'] : $cart_rule['value_real'];
		}
		if ($total < 0)
			$total = 0;

		return $total;
	}

	// return loyalty reward product by product for a cart, in the cart currency
	// TODO : tenir compte des prix dégressifs
	private function _getCartRewardByProduct($cart, $newProduct=NULL, $quantity=0)
	{
		$total = 0;
		$cartProducts = array();
		$cart_currency = $this->context->currency;
		$id_template = (int)MyConf::getIdTemplate('loyalty', (int)$this->context->customer->id);

		if (Validate::isLoadedObject($cart)) {
			$cartProducts = $cart->getProducts();
			$cart_currency = new Currency((int)$cart->id_currency);
		}

		if (isset($newProduct) && !empty($newProduct->id)) {
			$found = false;
			foreach ($cartProducts as $key => $product) {
				if ($newProduct->id == $product['id_product'] && $newProduct->id_product_attribute == $product['id_product_attribute']) {
					$found = true;
					$cartProducts[$key]['cart_quantity'] += $quantity;
				}
			}
			if (!$found) {
				$cartProductsNew = array();
				$cartProductsNew['id_product'] = (int)$newProduct->id;
				$cartProductsNew['id_product_attribute'] = $newProduct->id_product_attribute ? (int)$newProduct->id_product_attribute : (int)$newProduct->getIdProductAttributeMostExpensive();
				$cartProductsNew['price'] = number_format($newProduct->getPrice(false, $cartProductsNew['id_product_attribute']), 2, '.', '');
				if (MyConf::get('RLOYALTY_TAX', null, $id_template))
					$cartProductsNew['price_wt'] = number_format($newProduct->getPrice(true, $cartProductsNew['id_product_attribute']), 2, '.', '');
				$cartProductsNew['cart_quantity'] = $quantity;
				$cartProducts[] = $cartProductsNew;
			}
		}

		$gifts = array();
		if (Validate::isLoadedObject($cart)) {
			foreach ($cart->getCartRules(CartRule::FILTER_ACTION_GIFT) as $rule) {
				$cart_rule = new CartRule($rule['id_cart_rule']);
				$gifts[$cart_rule->gift_product.'_'.$cart_rule->gift_product_attribute] = isset($gifts[$cart_rule->gift_product.'_'.$cart_rule->gift_product_attribute]) ? $gifts[$cart_rule->gift_product.'_'.$cart_rule->gift_product_attribute] + 1 : 1;
			}
		}

		foreach ($cartProducts as $product) {
			if ((!MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, $id_template) && RewardsModel::isDiscountedProduct($product['id_product'], (int)$product['id_product_attribute'], $quantity))) {
				if (is_object($newProduct) && $product['id_product'] == $newProduct->id && $product['id_product_attribute'] == $newProduct->id_product_attribute)
					$this->context->smarty->assign('no_pts_discounted', 1);
				continue;
			}

			$quantity = (int)$product['cart_quantity'] - (isset($gifts[$product['id_product'].'_'.$product['id_product_attribute']]) ? $gifts[$product['id_product'].'_'.$product['id_product_attribute']] : 0);
			$price = !MyConf::get('RLOYALTY_TAX', null, $id_template) ? $product['price'] : $product['price_wt'];
			$total += (float)RewardsProductModel::getProductReward((int)$product['id_product'], $price, $quantity, $cart_currency->id, $id_template, 'loyalty');
		}

		if ($total < 0)
			$total = 0;

		if (MyConf::get('RLOYALTY_MAX_REWARD', null, $id_template) && $total > (float)MyConf::get('RLOYALTY_MAX_REWARD', null, $id_template))
			$total = (float)MyConf::get('RLOYALTY_MAX_REWARD', null, $id_template);

		return $total;
	}

	// Return the reward calculated from a price in a specific currency, and converted in the 2nd currency
	private function _getNbCreditsByPrice($id_customer, $price, $idCurrencyFrom, $idCurrencyTo = NULL, $extraParams = array())
	{
		$id_template = (int)MyConf::getIdTemplate('loyalty', $id_customer);
		if (!isset($idCurrencyTo))
			$idCurrencyTo = $idCurrencyFrom;

		if (Configuration::get('PS_CURRENCY_DEFAULT') != $idCurrencyFrom) {
			// converti de la devise du client vers la devise par défaut
			$price = Tools::convertPrice($price, Currency::getCurrency($idCurrencyFrom), false);
		}
		/* Prevent division by zero */
		$credits = 0;
		if ((int)MyConf::get('RLOYALTY_TYPE', null, $id_template) == 0) {
			$credits = floor(number_format($price, 2, '.', '') / (float)MyConf::get('RLOYALTY_POINT_RATE', null, $id_template)) * (float)MyConf::get('RLOYALTY_POINT_VALUE', null, $id_template);
		} else if ((int)MyConf::get('RLOYALTY_TYPE', null, $id_template) == 1) {
			$credits = number_format($price, 2, '.', '') * (float)MyConf::get('RLOYALTY_PERCENTAGE', null, $id_template) / 100;
		}

		if (MyConf::get('RLOYALTY_MAX_REWARD', null, $id_template) && $credits > (float)MyConf::get('RLOYALTY_MAX_REWARD', null, $id_template))
			$credits = (float)MyConf::get('RLOYALTY_MAX_REWARD', null, $id_template);
		return round(Tools::convertPrice($credits, Currency::getCurrency($idCurrencyTo)), 2);
	}

	// add the js used by the module
	public function hookDisplayHeader()
	{
		$id_template = (int)MyConf::getIdTemplate('loyalty', $this->context->customer->id);
		if (!Tools::getValue('content_only') && Tools::getValue('action')!='quickview' && $this->context->controller instanceof ProductController) {
			if (MyConf::get('RLOYALTY_DISPLAY_PRODUCT', null, $id_template) && $this->_isCustomerAllowed($this->context->customer, true) && $this->_isProductAllowed((int)Tools::getValue('id_product'))) {
				$this->context->controller->addJS($this->instance->getPath().'js/product.js');
				$this->context->controller->addJS($this->instance->getPath().'js/loyalty.js');
			}
		}

		if (MyConf::get('RLOYALTY_DISPLAY_CART', null, $id_template) && version_compare(_PS_VERSION_, '1.7', '>=') && $this->_isCustomerAllowed($this->context->customer, true) && $this->context->controller instanceof CartController)
			$this->context->controller->addJS($this->instance->getPath().'js/cart-loyalty.js');

		return false;
	}

	// Hook called on product page before 1.7
	public function hookDisplayRightColumnProduct($params)
	{
		return $this->displayRewardOnProductPage(false);
	}
	// Hook called on product page before 1.7.1
	public function hookDisplayProductButtons($params)
	{
		return $this->displayRewardOnProductPage(false);
	}
	// Hook called on product page since 1.7.1
	public function hookDisplayProductAdditionalInfo($params)
	{
		return $this->displayRewardOnProductPage(false);
	}

	// called on product page to display the reward for the selected combination
	public function displayRewardOnProductPage($ajax=true) {
		if (!Tools::getValue('content_only') && Tools::getValue('action')!='quickview') {
			$id_product = (int)Tools::getValue('id_product');

			$id_template = (int)MyConf::getIdTemplate('loyalty', $this->context->customer->id);
			if (MyConf::get('RLOYALTY_DISPLAY_PRODUCT', null, $id_template) && $this->_isCustomerAllowed($this->context->customer, true) && $this->_isProductAllowed($id_product)) {
				$id_product_attribute = (int)Tools::getValue('id_product_attribute');
		        if (!$id_product_attribute) {
		        	$groups = Tools::getValue('group');
					if (!empty($groups)) {
						try {
				            $id_product_attribute = (int)Product::getIdProductAttributeByIdAttributes($id_product, $groups, true);
				        } catch (Exception $e) {}
				    }
				}
				$quantity = (int)Tools::getValue('quantity');
				if (!$quantity)
					$quantity = (int)Tools::getValue('quantity_wanted', 1);

				$rewards_on_total = (int)MyConf::get('RLOYALTY_TYPE', null, $id_template) == 2 ? false : true;
				$product = new Product((int)$id_product);
				$product->id_product_attribute = $id_product_attribute;
				$product->loadStockData();

				// if the product is out of stock and can't be ordered, display nothing
				$available_quantity = Product::getQuantity($product->id, $product->id_product_attribute, $product->cache_is_pack);
				if ($available_quantity==0 && !$product->isAvailableWhenOutOfStock((int)$product->out_of_stock)) {
					$this->context->smarty->assign(array(
						'ajax_loyalty' => $ajax,
						'display' => false,
					));
					if (version_compare(_PS_VERSION_, '1.7', '>='))
						return $this->instance->display($this->instance->path, 'presta-1.7/product.tpl');
					return $this->instance->display($this->instance->path, 'product.tpl');
				} else if ($available_quantity < $quantity && !$product->isAvailableWhenOutOfStock((int)$product->out_of_stock))
					$quantity = $available_quantity;

				$total_before = 0;
				if (Validate::isLoadedObject($this->context->cart)) {
					if ($rewards_on_total) {
						$total_before = $this->_getCartTotalForReward();
						$total_after = $this->_getCartTotalForReward($product, $quantity);
						$credits_before = (float)$this->_getNbCreditsByPrice($this->context->customer->id, $total_before, $this->context->currency->id);
						$credits_after = (float)($this->_getNbCreditsByPrice($this->context->customer->id, $total_after, $this->context->currency->id));
					} else {
						$credits_before = $this->_getCartRewardByProduct($this->context->cart);
						$credits_after = $this->_getCartRewardByProduct($this->context->cart, $product, $quantity);
					}
					$credits = (float)($credits_after - $credits_before);
				} else {
					if (!(int)(MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, $id_template)) && RewardsModel::isDiscountedProduct($product->id, $product->id_product_attribute, $quantity)) {
						$credits = $credits_before = $credits_after = 0;
						$this->context->smarty->assign('no_pts_discounted', 1);
					} else {
						$credits_before = 0;
						if ($rewards_on_total) {
							$total_after = $this->_getCartTotalForReward($product, $quantity);
							$credits_after = (float)($this->_getNbCreditsByPrice($this->context->customer->id, $total_after, $this->context->currency->id));
						} else
							$credits_after = $this->_getCartRewardByProduct(null, $product, $quantity);
						$credits = $credits_after;
					}
				}

				// si pas de crédit, pas un produit discount, et pas en mode tranche ou en mode tranche mais que le minimum est déjà atteint, on affiche rien si en ajax, et sinon on masque le div
				// avant la version 1.7.1.0 on pilote tout par ajax, donc on démarre sans affichage pour ne pas voir un bloc apparaitre puis disparaitre.
				// depuis la version 1.7.1.0, prestashop rafraichit le bloc product_additional_info automatiquement, donc on démarre avec la fidélité déjà affichée
				// on garde le traitement dans product.js par sécurité (exemple une route modifiée qui ne contiendrait pas l'id_product_attribute) ou si certains ont utilisé un autre hook qui n'est pas mis à jour automatiquement par presta
				$display = version_compare(_PS_VERSION_, '1.7.1.0', '>=') || $ajax ? true : false;
				if ($credits == 0 && ((int)MyConf::get('RLOYALTY_TYPE', null, $id_template) != 0 || $total_before > Tools::convertPrice(MyConf::get('RLOYALTY_POINT_RATE', null, $id_template))) && !$this->context->smarty->getTemplateVars('no_pts_discounted')) {
					$display = false;
				}

				$this->context->smarty->assign(array(
					'ajax_loyalty' => $ajax,
					'display' => $display,
					'display_credits' => ((float)$credits > 0) ? true : false,
					'credits' => $this->instance->getRewardReadyForDisplay((float)$credits, (int)$this->context->currency->id, null, false),
					'total_credits' => $this->instance->getRewardReadyForDisplay((float)$credits_after, (int)$this->context->currency->id, null, false),
					'minimum' => Tools::displayPrice(round(Tools::convertPrice(MyConf::get('RLOYALTY_POINT_RATE', null, $id_template), (int)$this->context->currency->id), 2), (int)$this->context->currency->id)
				));
				if (version_compare(_PS_VERSION_, '1.7', '>='))
					return $this->instance->display($this->instance->path, 'presta-1.7/product.tpl');
				return $this->instance->display($this->instance->path, 'product.tpl');
			}
		}
		return false;
	}

	public function displayRewardOnCartPage()
	{
		$result = $this->hookDisplayShoppingCartFooter();
		if ($result !== false)
			return $result;
		return '';
	}

	public function hookDisplayShoppingCartFooter()
	{
		$id_template = (int)MyConf::getIdTemplate('loyalty', $this->context->customer->id);
		if (MyConf::get('RLOYALTY_DISPLAY_CART', null, $id_template) && $this->_isCustomerAllowed($this->context->customer, true)) {
			if (Validate::isLoadedObject($this->context->cart)) {
				if ((int)MyConf::get('RLOYALTY_TYPE', null, $id_template) != 2) {
					$total = $this->_getCartTotalForReward();
					$total = RewardsModel::getCurrencyValue($total, Configuration::get('PS_CURRENCY_DEFAULT'));
					$credits = $this->_getNbCreditsByPrice($this->context->customer->id, $total, $this->context->currency->id);
				} else {
					$credits = $this->_getCartRewardByProduct($this->context->cart);
					//$credits = RewardsModel::getCurrencyValue($credits, Configuration::get('PS_CURRENCY_DEFAULT'));
				}

				$this->context->smarty->assign(array(
					'display_credits' => ((float)$credits > 0) ? true : false,
					'credits' => $this->instance->getRewardReadyForDisplay((float)$credits, (int)$this->context->currency->id, null, false),
				));
			} else
				$this->context->smarty->assign('display_credits', false);
			$this->context->smarty->assign('minimum', (int)MyConf::get('RLOYALTY_TYPE', null, $id_template)==0 ? Tools::displayPrice(round(Tools::convertPrice(MyConf::get('RLOYALTY_POINT_RATE', null, $id_template), (int)$this->context->currency->id), 2), (int)$this->context->currency->id) : 0);

			if (version_compare(_PS_VERSION_, '1.7', '>='))
				return $this->instance->display($this->instance->path, 'presta-1.7/shopping-cart.tpl');
			return $this->instance->display($this->instance->path, 'shopping-cart.tpl');
		}
		return false;
	}

	public function hookActionValidateOrder($params)
	{
		if (!Validate::isLoadedObject($params['order']))
			die(Tools::displayError('Missing parameters'));

		$id_template = (int)MyConf::getIdTemplate('loyalty', (int)$params['order']->id_customer);
		// check if the loyalty reward is active, because order creation from admin was creating rewards even when the loyalty reward was desactivated
		if (MyConf::get('RLOYALTY_ACTIVE', null, $id_template) && $this->_isCustomerAllowed(new Customer((int)$params['order']->id_customer))) {
			if ((int)MyConf::get('RLOYALTY_TYPE', null, $id_template) != 2) {
				$totals = RewardsModel::getOrderTotalsForReward($params['order'], $this->_getAllowedCategories(), (int)MyConf::get('RLOYALTY_DEDUCE_VOUCHERS', null, $id_template));
				$credits = (float)$this->_getNbCreditsByPrice((int)$params['order']->id_customer, MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, $id_template) ? $totals[MyConf::get('RLOYALTY_TAX', null, $id_template) ? 'tax_incl' : 'tax_excl']['with_discounted'] : $totals[MyConf::get('RLOYALTY_TAX', null, $id_template) ? 'tax_incl' : 'tax_excl']['without_discounted'], $params['order']->id_currency, Configuration::get('PS_CURRENCY_DEFAULT'));
			} else {
				// Ajouter une option permettant de déduire le % que représente les bons de réduction de la récompense théorique.
				// Exemple, si les bons représentent 20% du total produit, alors les récompenses sont minorés de 20%.
				// Produit en croix :
				// Pour X€ de produit => Récompense = Y€
				// Pour (X€-réduction) => Récompense = (X€-réduction) * Y / X
				$credits = (float)RewardsModel::getOrderRewardByProduct($params['order'], MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, $id_template), MyConf::get('RLOYALTY_TAX', null, $id_template), 'loyalty');
				if (MyConf::get('RLOYALTY_MAX_REWARD', null, $id_template) && $credits > (float)MyConf::get('RLOYALTY_MAX_REWARD', null, $id_template))
					$credits = (float)MyConf::get('RLOYALTY_MAX_REWARD', null, $id_template);
			}

			$reward = new RewardsModel();
			$reward->id_customer = (int)$params['order']->id_customer;
			$reward->id_order = (int)$params['order']->id;
			$reward->credits = $credits;
			$reward->plugin = $this->name;
			$reward->id_reward_state = RewardsStateModel::getDefaultId();
			if ($reward->credits > 0)
				$reward->save();
			return true;
		}
		return false;
	}

	public function hookActionOrderStatusUpdate($params)
	{
		$this->instanceDefaultStates();

		if (!Validate::isLoadedObject($orderState = $params['newOrderStatus']) || !Validate::isLoadedObject($order = new Order((int)$params['id_order'])) || !Validate::isLoadedObject($customer = new Customer((int)$order->id_customer)))
			return false;

		// if state become validated or cancelled
		if ($orderState->id != $order->getCurrentState() && (in_array($orderState->id, $this->rewardStateValidation->getValues()) || in_array($orderState->id, $this->rewardStateCancel->getValues())))	{
			// if no reward has been granted for this order
			if (!($rewards = RewardsModel::getByOrderId($order->id)))
				return false;

			foreach($rewards as $reward) {
				$reward = new RewardsModel((int)$reward['id_reward']);
				if (!Validate::isLoadedObject($reward))
					continue;

				if ($reward->credits > 0 && $reward->id_reward_state != RewardsStateModel::getConvertId() && $reward->id_reward_state != RewardsStateModel::getWaitingPaymentId() && $reward->id_reward_state != RewardsStateModel::getPaidId()) {
					$oldState = $reward->id_reward_state;

					// if not already converted, then cancel or validate the reward
					if (in_array($orderState->id, $this->rewardStateValidation->getValues())) {
						// if reward is locked during return period
						if (Configuration::get('REWARDS_WAIT_RETURN_PERIOD') && Configuration::get('PS_ORDER_RETURN') && (int)Configuration::get('PS_ORDER_RETURN_NB_DAYS') > 0) {
							$reward->id_reward_state = RewardsStateModel::getReturnPeriodId();
							if (Configuration::get('REWARDS_DURATION'))
								$reward->date_end = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d') + Configuration::get('PS_ORDER_RETURN_NB_DAYS') + Configuration::get('REWARDS_DURATION'), date('Y')));
							$template = 'loyalty-return-period';
							$subject = $this->l('Reward validation', (int)$order->id_lang);
						} else {
							$reward->id_reward_state = RewardsStateModel::getValidationId();
							if (Configuration::get('REWARDS_DURATION'))
								$reward->date_end = date('Y-m-d H:i:s', mktime(date('H'), date('i'), date('s'), date('m'), date('d') + Configuration::get('REWARDS_DURATION'), date('Y')));
							$template = 'loyalty-validation';
							$subject = $this->l('Reward validation', (int)$order->id_lang);
						}
					} else {
						$reward->id_reward_state = RewardsStateModel::getCancelId();
						$template = 'loyalty-cancellation';
						$subject = $this->l('Reward cancellation', (int)$order->id_lang);
					}

					if ($oldState != $reward->id_reward_state) {
						$reward->save();

						// send notification
						if (Configuration::get('RLOYALTY_MAIL_VALIDATION')) {
							$id_template_core = (int)MyConf::getIdTemplate('core', $customer->id);

							// TODO : indiquer aux guests qu'ils doivent convertir leur compte en compte réel pour pouvoir utiliser leurs récompenses

							$data = array(
								'{customer_firstname}' => $customer->firstname,
								'{customer_lastname}' => $customer->lastname,
								'{order}' => $order->reference,
								'{link_rewards}' => $this->context->link->getModuleLink('allinone_rewards', 'rewards', array(), true),
								'{customer_reward}' => $this->instance->getRewardReadyForDisplay((float)$reward->credits, (int)$order->id_currency, (int)$order->id_lang, true, $id_template_core));
							if ($reward->id_reward_state == RewardsStateModel::getReturnPeriodId()) {
								$data['{reward_unlock_date}'] = version_compare(_PS_VERSION_, '8.0.0', '<') ? Tools::displayDate($reward->getUnlockDate(), null, true) : Tools::displayDate($reward->getUnlockDate(), true);
							}
							$this->instance->sendMail((int)$order->id_lang, $template, $subject, $data, $customer->email, $customer->firstname.' '.$customer->lastname);
						}
					}
				}
			}
		}
		return true;
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

	// Hook called in tab AdminOrders when a product is cancelled
	private function _modifyOrderDetail($params)
	{
		// il faut appeler une méthode qui boucle sur orderDetail car le panier original n'est pas modifié
		// par les 2 hooks précédents

		if (!Validate::isLoadedObject($order_detail = $params['object'])
		|| !Validate::isLoadedObject($order = new Order((int)$order_detail->id_order))
		|| !Validate::isLoadedObject($customer = new Customer((int)$order->id_customer)))
			return false;

		// check if a reward has been granted for this order
		if ($rewards = RewardsModel::getByOrderId($order->id)) {
			// if the reward has already been partially used, do nothing
			if (count($rewards) > 1)
				return false;
			else {
				$reward = new RewardsModel((int)$rewards[0]['id_reward']);
				if (!Validate::isLoadedObject($reward) || $reward->id_reward_state == RewardsStateModel::getConvertId() || $reward->id_reward_state == RewardsStateModel::getWaitingPaymentId() || $reward->id_reward_state == RewardsStateModel::getPaidId())
					return false;
			}
		} else
			return false;

		$id_template = (int)MyConf::getIdTemplate('loyalty', $order->id_customer);
		$oldCredits = (float)$reward->credits;

		if ((int)MyConf::get('RLOYALTY_TYPE', null, $id_template) != 2) {
			$totals = RewardsModel::getOrderTotalsForReward($order, $this->_getAllowedCategories(), (int)MyConf::get('RLOYALTY_DEDUCE_VOUCHERS', null, $id_template));
			$reward->credits = (float)$this->_getNbCreditsByPrice((int)$order->id_customer, MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, $id_template) ? $totals[MyConf::get('RLOYALTY_TAX', null, $id_template) ? 'tax_incl' : 'tax_excl']['with_discounted'] : $totals[MyConf::get('RLOYALTY_TAX', null, $id_template) ? 'tax_incl' : 'tax_excl']['without_discounted'], $order->id_currency, Configuration::get('PS_CURRENCY_DEFAULT'));
		} else {
			$reward->credits = (float)RewardsModel::getOrderRewardByProduct($order, MyConf::get('RLOYALTY_DISCOUNTED_ALLOWED', null, $id_template), MyConf::get('RLOYALTY_TAX', null, $id_template), 'loyalty');
			if (MyConf::get('RLOYALTY_MAX_REWARD', null, $id_template) && $reward->credits > (float)MyConf::get('RLOYALTY_MAX_REWARD', null, $id_template))
				$reward->credits = (float)MyConf::get('RLOYALTY_MAX_REWARD', null, $id_template);
		}

		// test if there was an update, because product return doesn't change the cart price
		if ($oldCredits != $reward->credits) {
			if ($reward->credits == 0)
				$reward->id_reward_state = RewardsStateModel::getCancelId();
			$reward->save();

			// send notifications
			if (Configuration::get('RLOYALTY_MAIL_CANCELPROD')) {
				$id_template_core = (int)MyConf::getIdTemplate('core', $customer->id);

				// TODO : indiquer aux guests qu'ils doivent convertir leur compte en compte réel pour pouvoir utiliser leurs récompenses

				$data = array(
					'{customer_firstname}' => $customer->firstname,
					'{customer_lastname}' => $customer->lastname,
					'{order}' => $order->reference,
					'{old_customer_reward}' => $this->instance->getRewardReadyForDisplay($oldCredits, (int)$order->id_currency, (int)$order->id_lang, true, $id_template_core),
					'{new_customer_reward}' => $this->instance->getRewardReadyForDisplay($reward->credits, (int)$order->id_currency, (int)$order->id_lang, true, $id_template_core));
				$this->instance->sendMail((int)$order->id_lang, 'loyalty-cancel-product', $this->l('Reward modification', (int)$order->id_lang), $data, $customer->email, $customer->firstname.' '.$customer->lastname);
			}
		}
		return true;
	}

	// Hook called in tab AdminOrder
	public function hookDisplayAdminOrder($params)
	{
		if (version_compare(_PS_VERSION_, '1.7.7.0', '<'))
			return $this->hookDisplayAdminOrderMainBottom($params);
	}

	public function hookDisplayAdminOrderMainBottom($params)
	{
		if ($rewards_id = RewardsModel::getByOrderId($params['id_order'])) {
			$rewards = array();
			$rewards_states = array();
			foreach($rewards_id as $reward_id) {
				$reward = new RewardsModel((int)$reward_id['id_reward']);
				$reward_state = new RewardsStateModel($reward->id_reward_state);
				$rewards[] = $reward;
				$rewards_states[$reward->id] = $reward_state->name[$this->context->language->id];
			}

			$smarty_values = array(
				'rewards' => $rewards,
				'rewards_states' => $rewards_states
			);
			$this->context->smarty->assign($smarty_values);
			if (version_compare(_PS_VERSION_, '1.7', '>='))
				return $this->instance->display($this->instance->path, 'presta-1.7/adminorders.tpl');
			return $this->instance->display($this->instance->path, 'adminorders.tpl');
		}
	}

	public function hookDisplayPDFInvoice($params)
	{
		if (!Validate::isLoadedObject($orderInvoice = $params['object']) || !Validate::isLoadedObject($order = new Order((int)$orderInvoice->id_order)) || !Validate::isLoadedObject($customer = new Customer((int)$order->id_customer)))
			return false; // an order and invoice with deleted customer can exist

		$id_template = (int)MyConf::getIdTemplate('loyalty', $customer->id);
		// check if a reward has been granted for this order
		if (MyConf::get('RLOYALTY_DISPLAY_INVOICE', null, $id_template) && $rewards = RewardsModel::getByOrderId($order->id)) {
			$id_template_core = (int)MyConf::getIdTemplate('core', $customer->id);
			$credits = 0;
			foreach($rewards as $reward) {
				$reward = new RewardsModel((int)$reward['id_reward']);
				$credits += (float) $reward->credits;
			}
			return '<br>'.sprintf($this->l('%s were added to your rewards account thanks to this order.'), $this->instance->getRewardReadyForDisplay((float)$credits, (int)$order->id_currency, (int)$order->id_lang, true, $id_template_core)).'<br>';
		}
		return false;
	}
}