<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

function upgrade_module_5_0_0($object)
{
	$result = true;

	try {
		// update for customers who had already that feature
		@Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'rewards_product` CHANGE `id_template` `id_template` INT(10) NOT NULL DEFAULT \'-1\'');
		@Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'rewards_product` SET `id_template`=-1 WHERE `id_template`=0');
	} catch (Exception $e) {}

	try {
		// new column for others
		@Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'rewards_product` ADD `id_template` INT NOT NULL DEFAULT \'-1\' AFTER `id_product`');
	} catch (Exception $e) {}

	try {
		// new column for others
		@Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'rewards_gift_product_attribute` ADD `price` DECIMAL(20, 6) UNSIGNED NOT NULL DEFAULT \'0\'');
		@Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'rewards_template` ADD `groups` TEXT NULL AFTER `name`');
	} catch (Exception $e) {}


	/* new hooks */
	if (version_compare(_PS_VERSION_, '1.7.7.0', '>='))
		$object->registerHook('displayAdminOrderMainBottom');

	/* new configuration */
	if ($registration=Configuration::get('REWARDS_REGISTRATION')) {
		Configuration::deleteByName('REWARDS_REGISTRATION');
		Configuration::updateGlobalValue('REWARDS_REGISTRATION', $registration);
	}
	if ($secure_key=Configuration::get('REWARDS_CRON_SECURE_KEY')) {
		Configuration::deleteByName('REWARDS_CRON_SECURE_KEY');
		Configuration::updateGlobalValue('REWARDS_CRON_SECURE_KEY', $secure_key);
	}

	Configuration::updateValue('REWARDS_VOUCHER_MINIMUM', 0);
	foreach($object->getCurrencies() as $currency) {
		if (Configuration::get('REWARDS_VOUCHER_MIN_VALUE_'.(int)$currency['id_currency']) > 0) {
			Configuration::updateValue('REWARDS_VOUCHER_MINIMUM', 1);
			break;
		}
	}
	Configuration::updateValue('REWARDS_VOUCHER_DISPLAY_CART', 1);
	Configuration::updateValue('REWARDS_VOUCHER_MINIMUM_MULTIPLE', 1);
	Configuration::updateValue('REWARDS_VOUCHER_TYPE', 0);
	Configuration::updateValue('REWARDS_VOUCHER_MAXIMUM', 0);
	Configuration::updateValue('RLOYALTY_DISPLAY_CART', 1);
	Configuration::updateValue('RLOYALTY_DISPLAY_PRODUCT', 1);
	Configuration::updateValue('RLOYALTY_DISPLAY_INVOICE', (int)Configuration::get('RLOYALTY_INVOICE'));
	Configuration::updateValue('RLOYALTY_DEDUCE_VOUCHERS', 1);
	Configuration::updateValue('RLOYALTY_MAX_REWARD', 0);
	Configuration::deleteByName('RLOYALTY_INVOICE');


	/* new version */
	Configuration::updateValue('REWARDS_VERSION', $object->version);

	/* clear cache */
	if (version_compare(_PS_VERSION_, '1.5.5.0', '>='))
		Tools::clearSmartyCache();

	return $result;
}