<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

class Allinone_rewardsRewardsModuleFrontController extends ModuleFrontController
{
	public function init()
	{
		if (!$this->context->customer->isLogged())
			Tools::redirect('index.php?controller=authentication&back='.$this->context->link->getModuleLink('allinone_rewards', 'rewards'));
		parent::init();
	}

	public function setMedia()
	{
		parent::setMedia();
		$this->addJqueryPlugin(array('idTabs', 'fancybox'));
		$this->addJS($this->module->getPath().'js/rewards.js');
		return true;
	}

	public function getBreadcrumbLinks()
    {
        $breadcrumb = parent::getBreadcrumbLinks();
        $breadcrumb['links'][] = $this->addMyAccountToBreadcrumb();
        $breadcrumb['links'][] = [
            'title' => $this->l('My rewards account', 'rewards'),
            'url' => $this->context->link->getModuleLink('allinone_rewards', 'rewards'),
        ];

        return $breadcrumb;
    }

	public function initContent()
	{
		parent::initContent();
		$error = false;

		// nb de lignes par page
		$nbpagination = 10;

		$id_template = (int)MyConf::getIdTemplate('core', $this->context->customer->id);
		$payment_ratio = (float)MyConf::get('REWARDS_PAYMENT_RATIO', null, $id_template);

		// récupère le nombre de crédits convertibles
		$totals = RewardsModel::getAllTotalsByCustomer((int)$this->context->customer->id);
		$totalGlobal = isset($totals['total']) ? (float)$totals['total'] : 0;
		$totalConverted = isset($totals[RewardsStateModel::getConvertId()]) ? (float)$totals[RewardsStateModel::getConvertId()] : 0;
		$totalAvailable = isset($totals[RewardsStateModel::getValidationId()]) ? (float)$totals[RewardsStateModel::getValidationId()] : 0;
		$totalPending = (isset($totals[RewardsStateModel::getDefaultId()]) ? (float)$totals[RewardsStateModel::getDefaultId()] : 0) + (isset($totals[RewardsStateModel::getReturnPeriodId()]) ? $totals[RewardsStateModel::getReturnPeriodId()] : 0);
		$totalWaitingPayment = isset($totals[RewardsStateModel::getWaitingPaymentId()]) ? (float)$totals[RewardsStateModel::getWaitingPaymentId()] : 0;
		$totalPaid = isset($totals[RewardsStateModel::getPaidId()]) ? (float)$totals[RewardsStateModel::getPaidId()] : 0;
		$totalForPaymentDefaultCurrency = round($totalAvailable * $payment_ratio / 100, 2);

		$totalAvailableUserCurrency = RewardsModel::getCurrencyValue($totalAvailable, $this->context->currency->id);
		$voucherMininum = (float)MyConf::get('REWARDS_VOUCHER_MIN_VALUE_'.(int)$this->context->currency->id, null, $id_template) > 0 ? (float)MyConf::get('REWARDS_VOUCHER_MIN_VALUE_'.(int)$this->context->currency->id, null, $id_template) : 0;
		$paymentMininum = (float)MyConf::get('REWARDS_PAYMENT_MIN_VALUE_'.(int)$this->context->currency->id, null, $id_template) > 0 ? (float)MyConf::get('REWARDS_PAYMENT_MIN_VALUE_'.(int)$this->context->currency->id, null, $id_template) : 0;

		$giftAllowed = RewardsModel::isCustomerAllowedForGiftProduct();
		$voucherAllowed = RewardsModel::isCustomerAllowedForVoucher();
		$paymentAllowed = RewardsModel::isCustomerAllowedForPayment();

		$voucherType = (int)MyConf::get('REWARDS_VOUCHER_TYPE', null, $id_template);
		if ($voucherType==0 || $voucherType==1) {
			$voucherMaximum = (float)MyConf::get('REWARDS_VOUCHER_MAXIMUM', null, $id_template);
			$voucherMaximum = $voucherMaximum && $voucherMaximum < $totalAvailable ? $voucherMaximum : $totalAvailable;
			$voucherMaximumUserCurrency = RewardsModel::getCurrencyValue($voucherMaximum, $this->context->currency->id);
		} else {
			$list_values = explode(';', MyConf::get('REWARDS_VOUCHER_LIST_VALUES', null, $id_template));
			$voucher_list_values = array();
			foreach($list_values as $value) {
				if ($value <= $totalAvailable) {
					$voucher_list_values[] = array(
						'value' => $value,
						'label' => Tools::displayPrice(RewardsModel::getCurrencyValue($value, $this->context->currency->id), $this->context->currency->id),
						'virtual' => (int)MyConf::get('REWARDS_VIRTUAL', null, $id_template) ? $this->module->getRewardReadyForDisplay($value, (int)$this->context->currency->id) : ''
					);
				}
			}
		}


		/* transform credits into voucher if needed */
		if ($voucherAllowed && Tools::getValue('transform-credits') && $totalAvailable > 0 && $totalAvailableUserCurrency >= $voucherMininum)
		{
			if ($voucherType==0) {
				RewardsModel::createDiscount($voucherMaximum);
				Tools::redirect($this->context->link->getModuleLink('allinone_rewards', 'rewards', array(), true));
			} else {
				if ($voucherType==1) {
					$value_to_transform = (float)round(Tools::convertPrice((float)Tools::getValue('value-to-transform'), $this->context->currency, false), 2);
					if ($value_to_transform > $voucherMaximum || $value_to_transform <= 0 || (float)Tools::getValue('value-to-transform') > $voucherMaximumUserCurrency) {
						if (version_compare(_PS_VERSION_, '1.7', '>='))
							$this->errors[] = sprintf($this->l('The value your entered is not valid, it must be higher than 0 and maximum %s', 'rewards'), Tools::displayPrice(RewardsModel::getCurrencyValue($voucherMaximum, $this->context->currency->id), $this->context->currency->id));
						else
							$error = sprintf($this->module->l('The value your entered is not valid, it must be higher than 0 and maximum %s', 'rewards'), Tools::displayPrice(RewardsModel::getCurrencyValue($voucherMaximum, $this->context->currency->id), $this->context->currency->id));
					} else {
						RewardsModel::createDiscount($value_to_transform);
						Tools::redirect($this->context->link->getModuleLink('allinone_rewards', 'rewards', array(), true));
					}
				} else {
					if (in_array(Tools::getValue('value-to-transform'), $list_values)) {
						RewardsModel::createDiscount((float)Tools::getValue('value-to-transform'));
						Tools::redirect($this->context->link->getModuleLink('allinone_rewards', 'rewards', array(), true));
					} else {
						if (version_compare(_PS_VERSION_, '1.7', '>='))
							$this->errors[] = $this->l('That value is not allowed', 'rewards');
						else
							$error = $this->module->l('That value is not allowed', 'rewards');
					}
				}
			}
		}

		if ($paymentAllowed && Tools::isSubmit('submitPayment') && $totalAvailableUserCurrency >= $paymentMininum && $totalForPaymentDefaultCurrency > 0) {
			if (Tools::getValue('payment_details') && (!MyConf::get('REWARDS_PAYMENT_INVOICE', null, $id_template) || (isset($_FILES['payment_invoice']['name']) && !empty($_FILES['payment_invoice']['tmp_name'])))) {
				if (RewardsPaymentModel::askForPayment($totalAvailable, $payment_ratio, Tools::getValue('payment_details'), $_FILES['payment_invoice']))
					Tools::redirect($this->context->link->getModuleLink('allinone_rewards', 'rewards', array(), true));
				else {
					$this->context->smarty->assign('payment_error', 2);
					if (version_compare(_PS_VERSION_, '1.7', '>='))
						$this->errors[] = $this->l('An error occured during the treatment of your request', 'rewards');
				}
			} else {
				$this->context->smarty->assign('payment_error', 1);
				if (version_compare(_PS_VERSION_, '1.7', '>='))
					$this->errors[] = $this->l('Please fill all the required fields', 'rewards');
			}
		}

		$rewards_reminder = Tools::getValue('rewards_reminder', -1);
		$rewards_account = new RewardsAccountModel((int)$this->context->customer->id);
		if ($rewards_reminder != -1) {
			$rewards_account->remind_active = (int)$rewards_reminder;
			if (!Validate::isLoadedObject($rewards_account)) {
				$rewards_account->id_customer = (int)$this->context->customer->id;
				$rewards_account->save();
			} else {
				Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'rewards_account` SET remind_active='.(int)$rewards_reminder.' WHERE id_customer='.(int)$this->context->customer->id);
			}
		}

		if (Tools::getValue('discount')) {
			$cart_rule = new CartRule(CartRule::getIdByCode(Tools::getValue('discount')));
			if (Validate::isLoadedObject($cart_rule)) {
				if (!Validate::isLoadedObject($this->context->cart)) {
					$this->context->cart->add();
					$this->context->cookie->id_cart = (int)$this->context->cart->id;
				}
				// In earlier version of prestashop 1.5, addCartRule return an exception if the cart rule is already in the cart
				// TODO : override checkValidity to test everything except the number of products > 0 or always add the free product before testing. Problem : hookActionCartSave in RewardsCorePlugin remove the product before we can test it, because the cart rule is not yet in the cart.
				try {
					// on ajoute le produit fictif au panier, sinon avec un panier vide cela échoue en 1.7
					if ($cart_rule->gift_product && MyConf::get('REWARDS_GIFT', null, $id_template)) {
						Configuration::updateGlobalValue('REWARDS_EXIT_HOOKACTIONCARTSAVE', 1);
						$this->context->cart->updateQty(1, (int)Configuration::getGlobalValue('REWARDS_ID_DEFAULT_GIFT_PRODUCT'), 0);
					}

					$error = $cart_rule->checkValidity($this->context);
					if (empty($error)) {
						if ($this->context->cart->addCartRule($cart_rule->id)) {
							if (version_compare(_PS_VERSION_, '1.7', '>='))
								Tools::redirect($this->context->link->getPageLink('cart', null, $this->context->language->id, array('action' => 'show')));
							else
								Tools::redirect($this->context->link->getPageLink(Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order', true));
						} else {
							if (version_compare(_PS_VERSION_, '1.7', '>='))
								$this->errors[] = $this->l('This voucher can\'t be added to your cart', 'rewards');
							else
								$error = $this->module->l('This voucher can\'t be added to your cart', 'rewards');
						}
					} else {
						if (version_compare(_PS_VERSION_, '1.7', '>='))
							$this->errors[] = $error;
					}
				} catch (Exception $e) {
					if (version_compare(_PS_VERSION_, '1.7', '>='))
						$this->errors[] = $this->l('This voucher can\'t be added to your cart', 'rewards');
					else
						$error = $this->module->l('This voucher can\'t be added to your cart', 'rewards');
				}
				$this->context->cart->save();
			}
		}

		$link = $this->context->link->getModuleLink('allinone_rewards', 'rewards', array(), true);
		$rewards = (int)RewardsModel::getNbRewards((int)$this->context->customer->id);
		$displayrewards = RewardsModel::getByIdCustomer((int)$this->context->customer->id, $this->context->currency->id, $nbpagination, (int)Tools::getValue('page') > 0 ? (int)Tools::getValue('page') : 1);

		$this->context->smarty->assign(array(
			'return_days' => (Configuration::get('REWARDS_WAIT_RETURN_PERIOD') && Configuration::get('PS_ORDER_RETURN') && (int)Configuration::get('PS_ORDER_RETURN_NB_DAYS') > 0) ? (int)Configuration::get('PS_ORDER_RETURN_NB_DAYS') : 0,
			'rewards' => $rewards,
			'cart_rules' => RewardsModel::getCartRulesFromRewards((int)$this->context->customer->id),
			'cart_rules_available' => RewardsModel::getCartRulesFromRewards((int)$this->context->customer->id, true),
			'order_process' => Configuration::get('PS_ORDER_PROCESS_TYPE') ? 'order-opc' : 'order',
			'rewards_virtual' => (int)MyConf::get('REWARDS_VIRTUAL', null, $id_template),
			'show_link' => $giftAllowed && (int)MyConf::get('REWARDS_GIFT_SHOW_LINK', null, $id_template),
			'activeTab' => Tools::getValue('transform-credits') || Tools::isSubmit('submitPayment') || Tools::getValue('rewards_reminder') ? '' : (Tools::getValue('page') ? 'history' : ''),
			'displayrewards' => $displayrewards,
			'page_link' => $link,
			'totalGlobal' => $this->module->getRewardReadyForDisplay($totalGlobal, (int)$this->context->currency->id),
			'totalConverted' => $this->module->getRewardReadyForDisplay($totalConverted, (int)$this->context->currency->id),
			'totalAvailable' => $this->module->getRewardReadyForDisplay($totalAvailable, (int)$this->context->currency->id),
			'totalAvailableCurrency' => Tools::displayPrice($totalAvailableUserCurrency, $this->context->currency),
			'totalPending' => $this->module->getRewardReadyForDisplay($totalPending, (int)$this->context->currency->id),
			'totalWaitingPayment' => $this->module->getRewardReadyForDisplay($totalWaitingPayment, (int)$this->context->currency->id),
			'totalPaid' => $this->module->getRewardReadyForDisplay($totalPaid, (int)$this->context->currency->id),
			'convertColumns' => ($voucherAllowed || $totalConverted > 0) ? true : false,
			'paymentColumns' => ($paymentAllowed || $totalPaid > 0 || $totalWaitingPayment > 0) ? true : false,
			'totalForPaymentDefaultCurrency' => Tools::displayPrice($totalForPaymentDefaultCurrency, (int)Configuration::get('PS_CURRENCY_DEFAULT')),
			'voucherMinimum' => $this->module->getRewardReadyForDisplay($voucherMininum, (int)$this->context->currency->id),
			'voucher_minimum_allowed' => $voucherAllowed && $voucherMininum > 0 ? true : false,
			'voucher_button_allowed' => $voucherAllowed && $totalAvailableUserCurrency >= $voucherMininum && $totalAvailableUserCurrency > 0,
			'voucher_type' => $voucherType,
			'voucher_maximum_currency' => $voucherType==0 || $voucherType==1 ? Tools::displayPrice($voucherMaximumUserCurrency, $this->context->currency) : 0,
			'voucher_list_values' => $voucherType==2 ? $voucher_list_values : null,
			'paymentMinimum' => $this->module->getRewardReadyForDisplay($paymentMininum, (int)$this->context->currency->id),
			'payment_minimum_allowed' => $paymentAllowed && $paymentMininum > 0 ? true : false,
			'payment_button_allowed' => $paymentAllowed && $totalAvailableUserCurrency >= $paymentMininum && $totalForPaymentDefaultCurrency > 0,
			'payment_txt' => MyConf::get('REWARDS_PAYMENT_TXT', (int)$this->context->language->id, $id_template),
			'general_txt' => MyConf::get('REWARDS_GENERAL_TXT', (int)$this->context->language->id, $id_template),
			'rewards_reminder' => Validate::isLoadedObject($rewards_account) ? (int)$rewards_account->remind_active : 1,
			'rewards_reminder_allowed' => (int)Configuration::get('REWARDS_REMINDER'),
			'payment_details' => Tools::getValue('payment_details'),
			'payment_invoice' => (int)MyConf::get('REWARDS_PAYMENT_INVOICE', null, $id_template),
			'pagination' => $rewards && (int)Tools::getValue('page') > 0 && (int)Tools::getValue('page') <= ceil($rewards / $nbpagination) ? (int)Tools::getValue('page') : 1,
			'max_page' => $rewards ? ceil($rewards / $nbpagination) : 0,
			'error' => $error
		));

		if (version_compare(_PS_VERSION_, '1.7', '<'))
			$this->setTemplate('rewards.tpl');
		else
			$this->setTemplate('module:allinone_rewards/views/templates/front/presta-1.7/rewards.tpl');
	}
}