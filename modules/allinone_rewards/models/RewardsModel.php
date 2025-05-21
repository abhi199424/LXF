<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

require_once(dirname(__FILE__).'/RewardsAccountModel.php');

class RewardsModel extends ObjectModel
{
	public $id_reward_state;
	public $id_customer;
	public $id_order;
	public $id_cart_rule;
	public $id_payment;
	public $credits;
	public $plugin;
	public $reason;
	public $date_end;
	public $date_add;
	public $date_upd;

	public static $definition = array(
		'table' => 'rewards',
		'primary' => 'id_reward',
		'fields' => array(
			'id_reward_state' =>	array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_customer' =>		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'id_order' =>			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_cart_rule' =>		array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'id_payment' =>			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),
			'credits' =>			array('type' => self::TYPE_FLOAT, 'validate' => 'isFloat', 'required' => true),
			'plugin' =>				array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'required' => true, 'size' => 20),
			'reason' =>				array('type' => self::TYPE_STRING, 'validate' => 'isGenericName', 'size' => 80),
			'date_end' =>			array('type' => self::TYPE_DATE, 'validate' => 'isDateFormat'),
			'date_add' =>			array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
			'date_upd' =>			array('type' => self::TYPE_DATE, 'validate' => 'isDate'),
		)
	);

	public function save($historize = true, $nullValues = false, $autodate = true)
	{
		if (parent::save($nullValues, $autodate)) {
			// create the account first time a reward is created for that customer
			$rewardsAccount = new RewardsAccountModel($this->id_customer);
			if (!Validate::isLoadedObject($rewardsAccount)) {
				$rewardsAccount->id_customer = $this->id_customer;
				$rewardsAccount->save();
			}

			if ($historize)
				$this->historize();
			return true;
		}
		return false;
	}

	public static function isNotEmpty() {
		Db::getInstance()->executeS('SELECT 1 FROM `'._DB_PREFIX_.'rewards`');
		return (bool)Db::getInstance()->NumRows();
	}

	public static function importFromLoyalty() {
		$pointValue = (float)Configuration::get('PS_LOYALTY_POINT_VALUE');
		if ($pointValue > 0) {
			Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'rewards` (id_reward, id_reward_state, id_customer, id_order, id_cart_rule, credits, plugin, date_add, date_upd)
				SELECT id_loyalty, id_loyalty_state, id_customer, id_order, id_cart_rule, points * ' . $pointValue. ', \'loyalty\', date_add, date_upd FROM `'._DB_PREFIX_.'loyalty` WHERE id_loyalty_state!=5');
			Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'rewards_history` (id_reward, id_reward_state, credits, date_add)
				SELECT id_loyalty, id_loyalty_state, points * ' . $pointValue. ', date_add FROM `'._DB_PREFIX_.'loyalty_history` WHERE id_loyalty_state!=5');
			$row = Db::getInstance()->getRow('SELECT IFNULL(MAX(id_reward),0)+1 AS nextid FROM `'._DB_PREFIX_.'rewards`');
			Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'rewards` AUTO_INCREMENT=' . $row['nextid']);
			Db::getInstance()->execute('INSERT IGNORE INTO `'._DB_PREFIX_.'rewards_account` (id_customer, date_last_remind, remind_active, date_add, date_upd) SELECT DISTINCT id_customer, NULL, 1, date_add, NOW() FROM `'._DB_PREFIX_.'rewards` GROUP BY id_customer ORDER BY date_add ASC');
		}
	}

	public static function getByOrderId($id_order)
	{
		if (!Validate::isUnsignedId($id_order))
			return false;

		return Db::getInstance()->executeS('
			SELECT r.id_reward
			FROM `'._DB_PREFIX_.'rewards` r
			WHERE r.plugin=\'loyalty\' AND r.id_order='.(int)$id_order.'
			ORDER BY id_cart_rule, id_payment');
	}

	// return reward product by product for an order, in the default currency
	// TODO : pourquoi s'embêter à calculer la quantité, ne peut-on connaitre directement le prix par produit ?
	// TODO : cela permettrait d'appliquer les réductions à la quantité par exemple, et de calculer le bon %
	// TODO : attention, si le produit donne une valeur fixe, il faut continuer de calculer par quantité
	public static function getOrderRewardByProduct($order, $discounted, $tax, $plugin, $id_template=NULL, $level=1)
	{
		if (!Validate::isLoadedObject($order))
			return false;

		$orderDetails = $order->getProductsDetail();
		$gifts = array();
		foreach ($order->getCartRules() as $rule) {
			$cart_rule = new CartRule($rule['id_cart_rule']);
			if ($cart_rule->gift_product)
				$gifts[$cart_rule->gift_product.'_'.$cart_rule->gift_product_attribute] = isset($gifts[$cart_rule->gift_product.'_'.$cart_rule->gift_product_attribute]) ? $gifts[$cart_rule->gift_product.'_'.$cart_rule->gift_product_attribute] + 1 : 1;
		}

		$total = 0;
		if (is_array($orderDetails)) {
			foreach($orderDetails as $detail) {
				// si le produit est en promo et que les promotions ne sont pas prises en compte
				if (!$discounted && ((float)$detail['reduction_amount'] != 0 || (float)$detail['reduction_percent'] != 0))
					continue;
				$quantity = $detail['product_quantity'] - $detail['product_quantity_return'] - $detail['product_quantity_refunded'] - (isset($gifts[$detail['product_id'].'_'.$detail['product_attribute_id']]) ? $gifts[$detail['product_id'].'_'.$detail['product_attribute_id']] : 0);
				$total += (float)RewardsProductModel::getProductReward((int)$detail['product_id'], $tax ? $detail['unit_price_tax_incl'] : $detail['unit_price_tax_excl'], $quantity, $order->id_currency, $plugin=='loyalty' ? (int)MyConf::getIdTemplate($plugin, (int)$order->id_customer) : $id_template, $plugin, $level);
			}
		}
		return round(Tools::convertPrice($total, $order->id_currency, false), 2);
	}

	// renvoie le prix total avec produits promo et sans produits promo d'une commande dans la devise du panier
	// TODO : tenir compte des prix dégressifs
	public static function getOrderTotalsForReward($order, $allowedCategories = NULL, $vouchersDeducted = true)
	{
		if (!Validate::isLoadedObject($order))
			return false;

		$orderDetails = $order->getProductsDetail();

		$gifts = array();
		$discount = 0;
		$discount_vat_excl = 0;
		$bshipping_deducted = 0;
		foreach ($order->getCartRules() as $rule) {
			$cart_rule = new CartRule($rule['id_cart_rule']);
			if ($cart_rule->gift_product)
				$gifts[$cart_rule->gift_product.'_'.$cart_rule->gift_product_attribute] = isset($gifts[$cart_rule->gift_product.'_'.$cart_rule->gift_product_attribute]) ? $gifts[$cart_rule->gift_product.'_'.$cart_rule->gift_product_attribute] + 1 : 1;
			if ((float)$cart_rule->reduction_percent != 0 || (float)$cart_rule->reduction_amount != 0) {
				$discount += (float)$rule['value'] - ((int)$cart_rule->free_shipping==1 && !$bshipping_deducted ? $order->total_shipping_tax_incl : 0);
				$discount_vat_excl += (float)$rule['value_tax_excl'] - ((int)$cart_rule->free_shipping==1 && !$bshipping_deducted ? $order->total_shipping_tax_excl : 0);
				if ((int)$cart_rule->free_shipping==1)
					$bshipping_deducted=1;
			}
		}

		$totals = array('tax_incl' => array('with_discounted' => 0, 'without_discounted' => 0), 'tax_excl' => array('with_discounted' => 0, 'without_discounted' => 0));
		if (is_array($orderDetails)) {
			foreach($orderDetails as $detail) {
				// si le produit n'est pas dans les catégories autorisées
				if (is_array($allowedCategories) && !Product::idIsOnCategoryId($detail['product_id'], $allowedCategories))
					continue;
				$quantity = $detail['product_quantity'] - $detail['product_quantity_return'] - $detail['product_quantity_refunded'] - (isset($gifts[$detail['product_id'].'_'.$detail['product_attribute_id']]) ? $gifts[$detail['product_id'].'_'.$detail['product_attribute_id']] : 0);
				$totals['tax_incl']['with_discounted'] += $quantity * $detail['unit_price_tax_incl'];
				$totals['tax_excl']['with_discounted'] += $quantity * $detail['unit_price_tax_excl'];
				// s'il n'y a pas eu de promo sur ce produit (prix dégressifs, prix forcés et prix de groupe ne sont pas des promos)
				if ((float)$detail['reduction_amount'] == 0 && (float)$detail['reduction_percent'] == 0) {
					$totals['tax_incl']['without_discounted'] += $quantity * $detail['unit_price_tax_incl'];
					$totals['tax_excl']['without_discounted'] += $quantity * $detail['unit_price_tax_excl'];
				}
			}
		}

		if ($vouchersDeducted) {
			$totals['tax_incl']['with_discounted'] = ($totals['tax_incl']['with_discounted'] - $discount) < 0 ? 0 : $totals['tax_incl']['with_discounted'] - $discount;
			$totals['tax_incl']['without_discounted'] = ($totals['tax_incl']['without_discounted'] - $discount) < 0 ? 0 : $totals['tax_incl']['without_discounted'] - $discount;
			$totals['tax_excl']['with_discounted'] = ($totals['tax_excl']['with_discounted'] - $discount_vat_excl) < 0 ? 0 : $totals['tax_excl']['with_discounted'] - $discount_vat_excl;
			$totals['tax_excl']['without_discounted'] = ($totals['tax_excl']['without_discounted'] - $discount_vat_excl) < 0 ? 0 : $totals['tax_excl']['without_discounted'] - $discount_vat_excl;
		}
		return $totals;
	}

	// indique si un produit bénéficie d'une réduction. Les prix dégressifs sont considérés comme des réductions, car de toute façon quand la commande est créée, la table order_detail indique une réduction
	public static function isDiscountedProduct($id_product, $id_product_attribute=0, $quantity_to_add=0)
	{
		$context = Context::getContext();
		$cart_quantity = !$context->cart ? 0 : Db::getInstance()->getValue('
			SELECT SUM(`quantity`)
			FROM `'._DB_PREFIX_.'cart_product`
			WHERE `id_product` = '.(int)$id_product.' AND `id_cart` = '.(int)$context->cart->id.' AND `id_product_attribute` = '.(int)$id_product_attribute
		);
		$quantity = $cart_quantity + $quantity_to_add;
		$ids = Address::getCountryAndState((int)$context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')});
		$id_country = (int)(is_array($ids) && $ids['id_country'] ? $ids['id_country'] : Configuration::get('PS_COUNTRY_DEFAULT'));

		$specific = SpecificPrice::getSpecificPrice((int)$id_product, $context->shop->id, (int)$context->currency->id, $id_country, $context->customer->id_default_group, $quantity, (int)$id_product_attribute, 0, 0, $quantity);
		if ($specific && $specific['reduction'] > 0)
			return true;
		return false;
	}

	public static function getCurrencyValue($credits, $idCurrencyTo)
	{
		return round(Tools::convertPrice($credits, Currency::getCurrency((int)$idCurrencyTo)), 2);
	}

	public static function getAllTotalsByCustomer($id_customer)
	{
		$rewards = array();
		$rewards['total'] = 0;
		$rewards[RewardsStateModel::getConvertId()] = 0;
		$rewards[RewardsStateModel::getValidationId()] = 0;
		$rewards[RewardsStateModel::getDefaultId()] = 0;
		$rewards[RewardsStateModel::getReturnPeriodId()] = 0;
		$rewards[RewardsStateModel::getWaitingPaymentId()] = 0;
		$rewards[RewardsStateModel::getPaidId()] = 0;
		$query = '
		SELECT id_reward_state, SUM(r.credits) AS credits
		FROM `'._DB_PREFIX_.'rewards` r
		WHERE r.id_customer = '.(int)$id_customer.'
		GROUP BY id_reward_state';
		$totals = Db::getInstance()->executeS($query);
		foreach($totals as $total) {
			$rewards[$total['id_reward_state']] = (float)$total['credits'];
			if ((int)$total['id_reward_state'] != RewardsStateModel::getCancelId())
				$rewards['total'] += $rewards[$total['id_reward_state']];
		}
		return $rewards;
	}

	public static function getNbRewards($id_customer)
	{
		return (int)Db::getInstance()->getValue('SELECT count(*) FROM `'._DB_PREFIX_.'rewards` WHERE id_customer = '.(int)$id_customer);
	}

	public static function getByIdCustomer($id_customer, $currency, $nb = 10, $page = 1)
	{
		$context = Context::getContext();

		$query = '
		SELECT r.*, o.reference AS order_reference, rsl.name AS state
		FROM `'._DB_PREFIX_.'rewards` r
		LEFT JOIN `'._DB_PREFIX_.'orders` o USING (id_order)
		LEFT JOIN `'._DB_PREFIX_.'rewards_state_lang` rsl ON (r.id_reward_state = rsl.id_reward_state AND rsl.id_lang = '.(int)$context->language->id.')
		WHERE r.id_customer = '.(int)($id_customer).'
		ORDER BY r.date_add DESC, r.id_reward DESC
		LIMIT '.(((int)$page - 1) * (int)$nb).', '.(int)$nb;

		$module = new allinone_rewards();
		if ($rewards = Db::getInstance()->executeS($query)) {
			foreach($rewards as $key => $reward) {
				$rewards[$key]['credits'] = $module->getRewardReadyForDisplay($reward['credits'], $currency);
				if ($reward['plugin'] != 'free') {
					$rewards[$key]['detail'] = html_entity_decode($module->{$reward['plugin']}->getDetails($reward, false));
				} else {
					$rewards[$key]['detail'] = html_entity_decode($reward['reason']);
				}
			}
			return $rewards;
		}
		return false;
	}

	public static function getAllByIdCustomer($id_customer)
	{
		$context = Context::getContext();

		$query = '
		SELECT r.*, o.reference AS order_reference, ROUND(o.total_paid, 2) AS total_without_shipping, o.id_currency, rsl.name AS state, cr.date_to AS date_cart_rule, cr.id_cart_rule, cr.code, cr.active, cr.quantity, o2.id_order AS order_cart_rule, o2.reference
		FROM `'._DB_PREFIX_.'rewards` r
		LEFT JOIN `'._DB_PREFIX_.'rewards_state_lang` rsl ON (r.id_reward_state = rsl.id_reward_state AND rsl.id_lang = '.(int)$context->language->id.')
		LEFT JOIN `'._DB_PREFIX_.'orders` o ON (r.id_order=o.id_order)
		LEFT JOIN `'._DB_PREFIX_.'cart_rule` cr ON (r.id_cart_rule=cr.id_cart_rule)
		LEFT JOIN `'._DB_PREFIX_.'order_cart_rule` ocr ON (r.id_cart_rule=ocr.id_cart_rule)
		LEFT JOIN `'._DB_PREFIX_.'orders` o2 ON (ocr.id_order=o2.id_order)
		WHERE r.id_customer = '.(int)$id_customer.'
		GROUP BY r.id_reward ORDER BY r.date_add DESC, r.id_reward DESC';

		if ($rewards = Db::getInstance()->executeS($query)) {
			$module = new allinone_rewards();
			foreach($rewards as $key => $reward) {
				if ($reward['plugin'] != 'free')
					$rewards[$key]['detail'] = html_entity_decode($module->{$reward['plugin']}->getDetails($reward, true));
				else
					$rewards[$key]['detail'] = html_entity_decode($reward['reason']);
			}
			return $rewards;
		}
		return false;
	}

	public static function createDiscount($credits)
	{
		$context = Context::getContext();
		$id_template = (int)MyConf::getIdTemplate('core', (int)$context->customer->id);

		/* Generate a discount code */
		$code = NULL;
		do $code = MyConf::get('REWARDS_VOUCHER_PREFIX', null, $id_template).Tools::passwdGen(6);
		while (CartRule::cartRuleExists($code));

		/* Voucher creation and affectation to the customer */
		$cartRule = new CartRule();
		$cartRule->id_customer = (int)$context->customer->id;
		$cartRule->date_from = date('Y-m-d H:i:s', time() - 1); /* remove 1s because of a strict comparison between dates in getCustomerCartRules */
		$cartRule->date_to = date('Y-m-d H:i:s', time() + (int)MyConf::get('REWARDS_VOUCHER_DURATION', null, $id_template)*24*60*60);
		$cartRule->description = MyConf::get('REWARDS_VOUCHER_DETAILS', (int)$context->language->id, $id_template);
		$cartRule->quantity = 1;
		$cartRule->quantity_per_user = 1;
		$cartRule->highlight = (int)MyConf::get('REWARDS_DISPLAY_CART', null, $id_template);
		$cartRule->partial_use = (int)MyConf::get('REWARDS_VOUCHER_BEHAVIOR', null, $id_template);
		$cartRule->code = $code;
		$cartRule->active = 1;
		$cartRule->reduction_amount = self::getCurrencyValue($credits, $context->currency->id);
		$cartRule->reduction_tax = (int)MyConf::get('REWARDS_VOUCHER_TAX', null, $id_template);
		$cartRule->reduction_currency = (int)$context->currency->id;
		if ((int)MyConf::get('REWARDS_VOUCHER_MINIMUM', null, $id_template) > 0) {
			$cartRule->minimum_amount = (int)MyConf::get('REWARDS_VOUCHER_MINIMUM', null, $id_template)==1 ? (float)MyConf::get('REWARDS_VOUCHER_MIN_ORDER_'.$context->currency->id, null, $id_template) : $cartRule->reduction_amount * (float)MyConf::get('REWARDS_VOUCHER_MINIMUM_MULTIPLE', null, $id_template);
			$cartRule->minimum_amount_tax = (int)MyConf::get('REWARDS_MINIMAL_TAX', null, $id_template);
			$cartRule->minimum_amount_currency = (int)$context->currency->id;
			$cartRule->minimum_amount_shipping = (int)MyConf::get('REWARDS_MINIMAL_SHIPPING', null, $id_template);
		}
		$cartRule->cart_rule_restriction = (int)(!(bool)MyConf::get('REWARDS_VOUCHER_CUMUL_S', null, $id_template));

		$languages = Language::getLanguages(true);
		$default_text = MyConf::get('REWARDS_VOUCHER_DETAILS', (int)Configuration::get('PS_LANG_DEFAULT'), $id_template);
		foreach ($languages as $language)
		{
			$text = MyConf::get('REWARDS_VOUCHER_DETAILS', (int)$language['id_lang'], $id_template);
			$cartRule->name[(int)($language['id_lang'])] = $text ? $text : $default_text;
		}
		$cartRule->add();

		// If the discount has no cart rule restriction, then it must be added to the white list of the other cart rules that have restrictions
		if ((int)MyConf::get('REWARDS_VOUCHER_CUMUL_S', null, $id_template))
		{
			Db::getInstance()->execute('
			INSERT INTO `'._DB_PREFIX_.'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`) (
				SELECT id_cart_rule, '.(int)$cartRule->id.' FROM `'._DB_PREFIX_.'cart_rule` WHERE cart_rule_restriction = 1 AND id_customer IN (0, '.(int)$context->customer->id.')
			)');
		}

		/* Modify the rewards which contirbuted to generated that discount */
		self::registerDiscount($cartRule, $credits);

		return $cartRule;
	}

	public static function purchaseProductFromRewards($product, $credits) {
		$context = Context::getContext();
		$id_template = (int)MyConf::getIdTemplate('core', (int)$context->customer->id);

		/* Generate a discount code */
		$code = NULL;
		do $code = MyConf::get('REWARDS_GIFT_PREFIX', null, $id_template).Tools::passwdGen(6);
		while (CartRule::cartRuleExists($code));

		$cartRule = new CartRule();
		$cartRule->id_customer = (int)$context->customer->id;
		$cartRule->date_from = date('Y-m-d H:i:s', time() - 1); /* remove 1s because of a strict comparison between dates in getCustomerCartRules */
		$cartRule->date_to = date('Y-m-d H:i:s', time() + (int)MyConf::get('REWARDS_GIFT_DURATION', null, $id_template)*24*60*60);
		$cartRule->quantity = 1;
		$cartRule->quantity_per_user = 1;
		$cartRule->highlight = 1;
		$cartRule->partial_use = 0;
		$cartRule->code = $code;
		$cartRule->reduction_tax = (int)MyConf::get('REWARDS_GIFT_TAX', null, $id_template);
		$cartRule->active = 1;
		$cartRule->gift_product = $product->id;
		$cartRule->gift_product_attribute = $product->id_product_attribute;
		$cartRule->minimum_amount = (float)MyConf::get('REWARDS_GIFT_MIN_ORDER_'.$context->currency->id, null, $id_template);
		$cartRule->minimum_amount_tax = (int)MyConf::get('REWARDS_GIFT_MINIMAL_TAX', null, $id_template);
		$cartRule->minimum_amount_currency = (int)$context->currency->id;
		$cartRule->minimum_amount_shipping = (int)MyConf::get('REWARDS_GIFT_MINIMAL_SHIPPING', null, $id_template);

		$languages = Language::getLanguages(true);
		foreach ($languages as $language)
		{
			if ($product->id_product_attribute)
				$cartRule->name[(int)$language['id_lang']] = Product::getProductName($product->id, $product->id_product_attribute, (int)$language['id_lang']);
			else
				$cartRule->name[(int)$language['id_lang']] = $product->name[(int)$language['id_lang']];
		}

		if ($cartRule->add()) {
			/* the cart rule must be added to the white list of the other cart rules that have restrictions */
			Db::getInstance()->execute('
				INSERT INTO `'._DB_PREFIX_.'cart_rule_combination` (`id_cart_rule_1`, `id_cart_rule_2`) (
					SELECT id_cart_rule, '.(int)$cartRule->id.' FROM `'._DB_PREFIX_.'cart_rule` WHERE cart_rule_restriction = 1 AND id_customer IN (0, '.(int)$context->customer->id.')
				)'
			);

			/* Modify the rewards which contirbuted to generated that discount */
			self::registerDiscount($cartRule, $credits);
			if (!Validate::isLoadedObject($context->cart)) {
				$context->cart->add();
				$context->cookie->id_cart = (int)$context->cart->id;
			}

			// on ajoute le produit au panier, sinon avec un panier vide cela échoue en 1.7
			Configuration::updateGlobalValue('REWARDS_EXIT_HOOKACTIONCARTSAVE', 1);
			$context->cart->updateQty(1, (int)Configuration::getGlobalValue('REWARDS_ID_DEFAULT_GIFT_PRODUCT'), 0);

			if ($context->cart->addCartRule($cartRule->id)) {
				$error = version_compare(_PS_VERSION_, '1.6.0.11', '>=') ? $cartRule->checkValidity($context, true, true, true) : $cartRule->checkValidity($context, true, true);
				if (!empty($error)) {
					$context->cart->removeCartRule($cartRule->id);
					return $error;
				}
			}
		} else
			return false;
		return true;
	}

	public static function registerDiscount($cartRule, $credits)
	{
		if (!Validate::isLoadedObject($cartRule))
			die(Tools::displayError('Incorrect object Discount.'));

		$total = 0;
		if ($items = Db::getInstance()->executeS('SELECT id_reward FROM `'._DB_PREFIX_.'rewards` r WHERE r.id_customer='.(int)$cartRule->id_customer.' AND r.id_reward_state='.(int)RewardsStateModel::getValidationId().' ORDER BY r.date_add ASC, r.id_reward ASC')) {
			foreach($items as $item)
			{
				$r = new RewardsModel((int)$item['id_reward']);
				$r->id_cart_rule = (int)$cartRule->id;
				$r->id_reward_state = (int)RewardsStateModel::getConvertId();
				$r->save();

				$total += (float)$r->credits;
				if ($total >= $credits)
					break;
			}

			// si transformation partielle de la dernière récompense, on modifie sa valeur et on génère une nouvelle récompense pour restituer la différence
			if ($total > $credits) {
				$r->credits = $r->credits - ($total - $credits);
				$r->save();

				$old_id = $r->id;
				$r->id = null;
				$r->id_cart_rule = 0;
				$r->id_reward_state = (int)RewardsStateModel::getValidationId();
				$r->credits = $total - $credits;
				if ($r->save(true, false, false)) {
					$module = new allinone_rewards();
					if ($r->plugin != 'free' && method_exists($module->{$r->plugin}, 'duplicateReward'))
						$module->{$r->plugin}->duplicateReward($old_id, $r->id);
				}
			}
		}
	}

	public static function registerPayment($payment, $credits)
	{
		$context = Context::getContext();

		if (!Validate::isLoadedObject($payment))
			die(Tools::displayError('Incorrect object RewardsPaymentModel.'));

		$total = 0;
		if ($items = Db::getInstance()->executeS('SELECT id_reward FROM `'._DB_PREFIX_.'rewards` r WHERE r.id_customer='.(int)$context->customer->id.' AND r.id_reward_state='.(int)RewardsStateModel::getValidationId().' ORDER BY r.date_add ASC, r.id_reward ASC')) {
			foreach($items as $item)
			{
				$r = new RewardsModel((int)$item['id_reward']);
				$r->id_payment = (int)$payment->id;
				$r->id_reward_state = (int)RewardsStateModel::getWaitingPaymentId();
				$r->save();

				$total += (float)$r->credits;
				if ($total >= $credits)
					break;
			}

			// si transformation partielle de la dernière récompense, on modifie sa valeur et on génère une nouvelle récompense pour restituer la différence
			if ($total > $credits) {
				$r->credits = $r->credits - ($total - $credits);
				$r->save();

				$old_id = $r->id;
				$r->id = null;
				$r->id_payment = 0;
				$r->id_reward_state = (int)RewardsStateModel::getValidationId();
				$r->credits = $total - $credits;
				if ($r->save(true, false, false)) {
					$module = new allinone_rewards();
					if (method_exists($module->{$r->plugin}, 'duplicateReward'))
						$module->{$r->plugin}->duplicateReward($old_id, $r->id);
				}
			}
		}
	}

	public static function acceptPayment($id_payment)
	{
		$query = 'SELECT * FROM `'._DB_PREFIX_.'rewards` r WHERE r.id_payment='.(int)$id_payment.' AND r.id_reward_state='.(int)RewardsStateModel::getWaitingPaymentId();
		$items = Db::getInstance()->executeS($query);
		foreach($items as $item)
		{
			$r = new RewardsModel((int)$item['id_reward']);
			$r->id_reward_state = (int)RewardsStateModel::getPaidId();
			$r->save();
		}
		return $items[0]['id_customer'];
	}

	// Convert rewards in ReturnPeriodId or ValidationId state if return date is over
	// Cancel rewards if validity has expired
	public static function checkRewardsStates() {
		$rewardStateValidation = new RewardsStateModel(RewardsStateModel::getValidationId());
		// rewards waiting for the end of the return period or rewards not validated automatically (expeditor_inet for example)
		// TODO : add the check of the date for rewards not validated automatically in case of return period activated
		// TODO : update only the rewards from the customer available in the current shop, because configuration could be different on another shop
		$query = '
		SELECT r.id_reward
		FROM `'._DB_PREFIX_.'rewards` r
		JOIN `'._DB_PREFIX_.'orders` o ON (o.id_order = r.id_order'.Shop::addSqlRestriction(false, 'o').')
		WHERE (
			(r.id_reward_state='.(int)RewardsStateModel::getDefaultId().' AND o.current_state IN ('.implode(',', $rewardStateValidation->getValues()).'))
			OR (r.id_reward_state = '.(int)RewardsStateModel::getReturnPeriodId().' AND o.current_state IN ('.implode(',', $rewardStateValidation->getValues()).'))
		)';

		// rewards which have been in return period since time > return period nb days
		if (Configuration::get('REWARDS_WAIT_RETURN_PERIOD') && Configuration::get('PS_ORDER_RETURN') && (int)Configuration::get('PS_ORDER_RETURN_NB_DAYS') > 0) {
			$query .= '
			AND (
				DATE_ADD(r.date_upd, INTERVAL '.(int)Configuration::get('PS_ORDER_RETURN_NB_DAYS').' DAY) < NOW()
				OR EXISTS (
					SELECT rh.id_reward
					FROM `'._DB_PREFIX_.'rewards_history` rh
					WHERE rh.id_reward = r.id_reward
					AND rh.id_reward_state = '.(int)RewardsStateModel::getReturnPeriodId().'
					AND DATE_ADD(rh.date_add, INTERVAL '.(int)Configuration::get('PS_ORDER_RETURN_NB_DAYS').' DAY) < NOW()
				)
			)';
		}

		$rows = Db::getInstance()->executeS($query);
		if (is_array($rows)) {
			foreach ($rows as $row)	{
				$reward = new RewardsModel((int)$row['id_reward']);
				$reward->id_reward_state = (int)RewardsStateModel::getValidationId();
				$reward->save();
			}
		}

		// rewards with expired validity
		$query = '
		SELECT r.id_reward
		FROM `'._DB_PREFIX_.'rewards` r
		JOIN `'._DB_PREFIX_.'customer` c ON (c.id_customer = r.id_customer'.Shop::addSqlRestriction(false, 'c').')
		WHERE r.id_reward_state = '.(int)RewardsStateModel::getValidationId().'
		AND date_end < NOW() AND date_end != \'0000-00-00 00:00:00\'';
		$rows = Db::getInstance()->executeS($query);
		if (is_array($rows)) {
			foreach ($rows as $row)	{
				$reward = new RewardsModel((int)$row['id_reward']);
				$reward->id_reward_state = (int)RewardsStateModel::getCancelId();
				$reward->save();
			}
		}
	}

	public function getUnlockDate() {
		$query = '
			SELECT DATE_ADD(date_add, INTERVAL '.(int)Configuration::get('PS_ORDER_RETURN_NB_DAYS').' DAY) AS unlock_date
			FROM `'._DB_PREFIX_.'rewards_history` rh
			WHERE rh.id_reward = '.(int)$this->id.'
			AND rh.id_reward_state = '.(int)RewardsStateModel::getReturnPeriodId().'
			ORDER BY date_add ASC';
		$result = Db::getInstance()->getRow($query);
		return $result['unlock_date'];
	}

	// Register all transaction in a specific history table
	private function historize()
	{
		Db::getInstance()->execute('
		INSERT INTO `'._DB_PREFIX_.'rewards_history` (`id_reward`, `id_reward_state`, `credits`, `date_add`)
		VALUES ('.(int)$this->id.', '.(int)$this->id_reward_state.', '.(float)$this->credits.', NOW())');
	}

	// check if customer is in a group which is allowed to purchase gift products, transform rewards into vouchers or ask for payment
	static public function isCustomerAllowedForGiftProduct()
	{
		return self::_isCustomerAllowed('REWARDS_GIFT');
	}

	static public function isCustomerAllowedForVoucher()
	{
		return self::_isCustomerAllowed('REWARDS_VOUCHER');
	}

	static public function isCustomerAllowedForPayment()
	{
		return self::_isCustomerAllowed('REWARDS_PAYMENT');
	}

	static private function _isCustomerAllowed($key) {
		$context = Context::getContext();
		if ($context->customer->isLogged()) {
			$id_template = (int)MyConf::getIdTemplate('core', $context->customer->id);

			// if the customer is linked to a template, then it overrides the groups setting
			if (MyConf::get($key, null, $id_template)) {
				$stats = $context->customer->getStats();
				if ((int)$stats['nb_orders'] >= (int)MyConf::get($key.'_NB_ORDERS', null, $id_template)) {
					if ($id_template)
						return true;
					$allowed_groups = explode(',', Configuration::get($key.'_GROUPS'));
					$customer_groups = $context->customer->getGroups();
					return sizeof(array_intersect($allowed_groups, $customer_groups)) > 0;
				}
			}
		}
		return false;
	}

	// get all statistics for BO
	static public function getAdminStatistics() {
		$result = array('total_rewards' => 0, 'nb_rewards' => 0, 'nb_customers' => 0, 'details_by_status' => array(),
				'details_by_plugin' => array('loyalty' => 0, 'sponsorship' => 0, 'registration' => 0, 'newsletter' => 0, 'free' => 0),
				'details_by_customer' => array(), 'total_cart_rules' => 0);

		// by id_reward_state
		// initialise chaque statut à 0 pour éviter les notices d'erreur sur presta 1.5
		$rows = Db::getInstance()->executeS('SELECT id_reward_state FROM `'._DB_PREFIX_.'rewards_state`');
		foreach ($rows as $row)
			$result['details_by_status'][$row['id_reward_state']] = 0;

		$query = '
			SELECT id_reward_state, COUNT(DISTINCT id_reward) AS nb_rewards, COUNT(DISTINCT c.id_customer) AS nb_customers, SUM(credits) AS credits
			FROM `'._DB_PREFIX_.'rewards` AS r
			JOIN `'._DB_PREFIX_.'customer` AS c ON (c.id_customer=r.id_customer'.Shop::addSqlRestriction(false, 'c').')
			WHERE
				r.id_order = 0 OR
				r.id_order IN (
					SELECT DISTINCT id_order FROM `'._DB_PREFIX_.'orders`
					WHERE 1'.Shop::addSqlRestriction(false).'
				)
			GROUP BY id_reward_state';
		$rows = Db::getInstance()->executeS($query);
		foreach ($rows as $row) {
			$result['details_by_status'][$row['id_reward_state']] = array(
				'total_rewards' => (float)$row['credits'],
				'nb_rewards' => (int)$row['nb_rewards'],
				'nb_customers' => (int)$row['nb_customers'],
			);
			if ($row['id_reward_state'] != RewardsStateModel::getCancelId()) {
				// global
				$result['total_rewards'] += (float)$row['credits'];
				$result['nb_rewards'] += (int)$row['nb_rewards'];
			}
		}

		// by plugin
		$query = '
			SELECT plugin, COUNT(DISTINCT id_reward) AS nb_rewards, COUNT(DISTINCT c.id_customer) AS nb_customers, SUM(credits) AS credits
			FROM `'._DB_PREFIX_.'rewards` AS r
			JOIN `'._DB_PREFIX_.'customer` AS c ON (c.id_customer=r.id_customer'.Shop::addSqlRestriction(false, 'c').')
			WHERE
				r.id_reward_state != '.RewardsStateModel::getCancelId().'
				AND (
					r.id_order = 0 OR
					r.id_order IN (
						SELECT DISTINCT id_order FROM `'._DB_PREFIX_.'orders`
						WHERE 1'.Shop::addSqlRestriction(false).'
					)
				)
			GROUP BY plugin';
		$rows = Db::getInstance()->executeS($query);
		foreach ($rows as $row) {
			// by plugin
			$result['details_by_plugin'][$row['plugin']] = array(
				'total_rewards' => (float)$row['credits'],
				'nb_rewards' => (int)$row['nb_rewards'],
				'nb_customers' => (int)$row['nb_customers'],
			);
		}

		$query = '
			SELECT id_reward_state, c.id_customer, c.firstname, c.lastname, COUNT(DISTINCT id_reward) AS nb_rewards, SUM(credits) AS credits, SUM(IF(ocr.id_cart_rule > 0, credits, 0)) AS total_cart_rules_used
			FROM `'._DB_PREFIX_.'rewards` AS r
			JOIN `'._DB_PREFIX_.'customer` AS c ON (c.id_customer=r.id_customer'.Shop::addSqlRestriction(false, 'c').')
            LEFT JOIN `'._DB_PREFIX_.'order_cart_rule` AS ocr ON (ocr.id_cart_rule=r.id_cart_rule AND ocr.id_cart_rule != 0)
			WHERE
				r.id_order = 0 OR
				r.id_order IN (
					SELECT DISTINCT id_order FROM `'._DB_PREFIX_.'orders`
					WHERE 1'.Shop::addSqlRestriction(false).'
				)
			GROUP BY id_reward_state, id_customer';
		$rows = Db::getInstance()->executeS($query);
		foreach ($rows as $row) {
			if (!isset($result['details_by_customer'][$row['id_customer']]['total_rewards'])) {
				$result['nb_customers']++;
				$result['details_by_customer'][$row['id_customer']]['total_rewards'] = 0;
				$result['details_by_customer'][$row['id_customer']]['nb_rewards'] = 0;
				$result['details_by_customer'][$row['id_customer']]['total_cart_rules_used'] = 0;
			}

			$result['details_by_customer'][$row['id_customer']]['firstname'] = $row['firstname'];
			$result['details_by_customer'][$row['id_customer']]['lastname'] = $row['lastname'];
			$result['details_by_customer'][$row['id_customer']][$row['id_reward_state']] = (float)$row['credits'];
			if ($row['id_reward_state'] != RewardsStateModel::getCancelId()) {
				$result['details_by_customer'][$row['id_customer']]['total_rewards'] += (float)$row['credits'];
				$result['details_by_customer'][$row['id_customer']]['nb_rewards'] += (int)$row['nb_rewards'];
				$result['details_by_customer'][$row['id_customer']]['total_cart_rules_used'] += (float)$row['total_cart_rules_used'];
				$result['total_cart_rules'] += (float)$row['total_cart_rules_used'];
			}
		}
		return $result;
	}

	static public function getCartRulesFromRewards($id_customer, $only_active=false) {
		$context = Context::getContext();
		$query = '
			SELECT cr.*, SUM(r.credits) AS credits, MIN(cr.date_add) AS date, IF(ocr.id_cart_rule > 0, ocr.id_order, 0) AS id_order, IF(ocr.id_cart_rule > 0, o.reference, \'\') AS reference
			FROM `'._DB_PREFIX_.'rewards` AS r '.
			($only_active ? 'JOIN' : 'LEFT JOIN').' `'._DB_PREFIX_.'cart_rule` AS cr ON (cr.id_cart_rule=r.id_cart_rule'.($only_active ? ' AND cr.active=1 AND cr.quantity=1 AND cr.date_to > NOW()' : '').')
			LEFT JOIN `'._DB_PREFIX_.'order_cart_rule` AS ocr ON (ocr.id_cart_rule=r.id_cart_rule)
			LEFT JOIN `'._DB_PREFIX_.'orders` AS o ON (o.id_order=ocr.id_order)
			WHERE r.`id_customer`='.(int)$id_customer.'
			AND r.`id_cart_rule` > 0
			GROUP BY r.id_cart_rule
			ORDER BY cr.date_add DESC';
		if ($rows = Db::getInstance()->executeS($query)) {
			$module = new allinone_rewards();
			foreach ($rows as &$row) {
				if ((int)$row['product_restriction']) {
					$product = new Product((int)$row['product_restriction']);
					$row['product'] = $product->name[(int)$context->language->id];
				} else if ((int)$row['gift_product']) {
					if ((int)$row['gift_product_attribute'])
						$row['product'] = Product::getProductName((int)$row['gift_product'], (int)$row['gift_product_attribute'], (int)$context->language->id);
					else {
						$product = new Product((int)$row['gift_product']);
						$row['product'] = $product->name[(int)$context->language->id];
					}
				}
				$row['virtual_credits'] = $module->getRewardReadyForDisplay($row['credits'], (int)$context->currency->id);
				$row['credits'] = Tools::displayPrice(self::getCurrencyValue($row['credits'], (int)$context->currency->id), (int)$context->currency->id);
				if (isset($row['minimum_amount']) && $row['minimum_amount'] > 0)
					$row['minimal'] = Tools::displayPrice(Tools::convertPriceFull($row['minimum_amount'], new Currency($row['minimum_amount_currency']), $context->currency), (int)$context->currency->id);
			}
			return $rows;
		}
		return false;
	}
}