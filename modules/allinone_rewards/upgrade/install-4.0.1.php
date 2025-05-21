<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

function upgrade_module_4_0_1($object)
{
	$result = true;

	/* Fix bug caused by 4.0.0 */
	Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'rewards_account` SET remind_active=1');

	/* fichier inutile */
	@unlink(dirname(__FILE__).'/../views/templates/hook/header.tpl');
	@unlink(dirname(__FILE__).'/../views/templates/hook/facebook_header.tpl');

	/* new hooks */
	$object->registerHook('displayCustomerAccountFormTop');

	/* new version */
	Configuration::updateValue('REWARDS_VERSION', $object->version);

	/* clear cache */
	if (version_compare(_PS_VERSION_, '1.5.5.0', '>='))
		Tools::clearSmartyCache();

	return $result;
}