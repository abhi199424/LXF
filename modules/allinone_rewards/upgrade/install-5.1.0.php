<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

function upgrade_module_5_1_0($object)
{
	$result = true;

	try {
		// new column for others
		@Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'rewards_sponsorship` ADD `deleted` TINYINT NOT NULL DEFAULT \'0\' AFTER `id_cart_rule`');
		@Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'rewards_sponsorship` DROP INDEX `index_unique_sponsorship_email`, ADD INDEX `index_sponsorship_email` (`email`) USING BTREE');
		@Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'rewards_sponsorship` DROP INDEX `index_id_customer`, ADD INDEX `index_id_customer_deleted` (`id_customer`, `deleted`) USING BTREE');
	} catch (Exception $e) {}

	Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_sponsorship` WHERE `id_sponsor` NOT IN (SELECT `id_customer` FROM `'._DB_PREFIX_.'customer`)');
	Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_sponsorship` WHERE `id_customer`!=0 AND `id_customer` NOT IN (SELECT `id_customer` FROM `'._DB_PREFIX_.'customer`)');
	Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'rewards_sponsorship_code` WHERE `id_sponsor` NOT IN (SELECT `id_customer` FROM `'._DB_PREFIX_.'customer`)');

	/* new hooks */
	$object->registerHook('actionObjectAttributeDeleteAfter');

	/* new option */
	Configuration::updateValue('RSPONSORSHIP_USE_VOUCHER_FIELD', 1);
	Configuration::updateValue('RSPONSORSHIP_MULTIPLE_SPONSOR', 0);
	Configuration::updateValue('RSPONSORSHIP_ANONYMIZE', 1);

	/* new version */
	Configuration::updateGlobalValue('REWARDS_VERSION', $object->version);

	/* clear cache */
	if (version_compare(_PS_VERSION_, '1.5.5.0', '>='))
		Tools::clearSmartyCache();

	return $result;
}