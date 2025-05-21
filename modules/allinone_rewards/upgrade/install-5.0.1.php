<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

function upgrade_module_5_0_1($object)
{
	$result = true;

	/* new hooks */
	$object->registerHook('actionProductCancel');

	/* new configuration */
	if ($value=Configuration::get('REWARDS_INITIAL_CONDITIONS')) {
		Configuration::deleteByName('REWARDS_INITIAL_CONDITIONS');
		Configuration::updateGlobalValue('REWARDS_INITIAL_CONDITIONS', $value);
	}

	/* new version */
	Configuration::deleteByName('REWARDS_VERSION');
	Configuration::updateGlobalValue('REWARDS_VERSION', $object->version);

	/* clear cache */
	if (version_compare(_PS_VERSION_, '1.5.5.0', '>='))
		Tools::clearSmartyCache();

	return $result;
}