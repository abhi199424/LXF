<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

function upgrade_module_1_8_2($object)
{
	$result = true;

	/* drop bad index */
	try {
		@Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'rewards_facebook` DROP INDEX `index_unique_facebook_customer`');
	} catch (Exception $e) {}

	/* shorten the key */
	foreach (Currency::getCurrencies() as $currency) {
		Configuration::updateValue('RSPONSORSHIP_VOUCHER_VALUE_GC_'.(int)($currency['id_currency']), (float)Configuration::get('RSPONSORSHIP_DISCOUNT_VALUE_GC_'.(int)($currency['id_currency'])));
		Configuration::deleteByName('RSPONSORSHIP_DISCOUNT_VALUE_GC_'.(int)($currency['id_currency']));
	}

	/* new version */
	Configuration::updateValue('REWARDS_VERSION', $object->version);

	return $result;
}