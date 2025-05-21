<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

function upgrade_module_4_1_5($object)
{
	$result = true;

	/* fichier inutile */
	@unlink(dirname(__FILE__).'/../views/templates/hook/presta-1.7/authentication.tpl');

	/* new version */
	Configuration::updateValue('REWARDS_VERSION', $object->version);

	/* clear cache */
	if (version_compare(_PS_VERSION_, '1.5.5.0', '>='))
		Tools::clearSmartyCache();

	return $result;
}