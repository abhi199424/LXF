<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

function upgrade_module_6_1_0($object)
{
	$result = true;

    /* new version */
    Configuration::updateGlobalValue('REWARDS_VERSION', $object->version);

    try {
        @Db::getInstance()->execute('ALTER TABLE `'._DB_PREFIX_.'rewards` ADD INDEX `index_rewards_plugin` (`plugin`)');
    } catch (Exception $e) {}


    @unlink(dirname(__FILE__).'/../FacebookLocales.xml');

    /* clear cache */
    if (version_compare(_PS_VERSION_, '1.5.5.0', '>='))
        Tools::clearSmartyCache();

	return $result;
}