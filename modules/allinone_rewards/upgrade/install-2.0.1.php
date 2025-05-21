<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

function upgrade_module_2_0_1($object)
{
	$result = true;

	/* correction du fichier RewardsModel.php en 2.0.1 */
	Db::getInstance()->execute('INSERT IGNORE INTO `'._DB_PREFIX_.'rewards_account` (id_customer, date_last_remind, date_add, date_upd) SELECT DISTINCT id_customer, NULL, date_add, NOW() FROM `'._DB_PREFIX_.'rewards` GROUP BY id_customer ORDER BY date_add ASC');

	/* new hook */
	$object->registerHook('actionObjectOrderDetailUpdateAfter');
	$object->registerHook('actionObjectOrderDetailDeleteAfter');
	$object->registerHook('displayPDFInvoice');

	/* New option */
	Configuration::updateValue('RLOYALTY_INVOICE', 0);

	/* new version */
	Configuration::updateValue('REWARDS_VERSION', $object->version);

	/* clear cache */
	if (version_compare(_PS_VERSION_, '1.5.5.0', '>='))
		Tools::clearSmartyCache();

	return $result;
}