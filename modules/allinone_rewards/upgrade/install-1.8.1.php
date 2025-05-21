<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

function upgrade_module_1_8_1($object)
{
	$result = true;

	/* forgotten column in the installation script */
	try {
		@Db::getInstance()->execute('ALTER TABLE `' . _DB_PREFIX_ . 'rewards` ADD `id_payment` INT( 10 ) UNSIGNED NULL DEFAULT NULL AFTER `id_cart_rule`');
	} catch (Exception $e) {}

	/* new version */
	Configuration::updateValue('REWARDS_VERSION', $object->version);

	return $result;
}