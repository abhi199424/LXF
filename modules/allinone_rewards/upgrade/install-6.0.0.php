<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

function upgrade_module_6_0_0($object)
{
	$result = true;

	/* new plugins */
	$object->registration->install();
	$object->newsletter->install();

	/* new version */
	Configuration::updateGlobalValue('REWARDS_VERSION', $object->version);

	Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'rewards` SET plugin=\'free\', reason=\''.pSQL($object->l('Facebook - Like')).'\' WHERE plugin=\'facebook\'');
	try {
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'configuration_lang`	WHERE `id_configuration` IN (SELECT `id_configuration` FROM `'._DB_PREFIX_.'configuration` WHERE `name` LIKE \'RFACEBOOK_%\')');
		Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'configuration` WHERE `name` LIKE \'RFACEBOOK_%\'');
		@Db::getInstance()->execute('DROP TABLE `'._DB_PREFIX_.'rewards_facebook`');
	} catch (Exception $e) {}

	@unlink(dirname(__FILE__).'/../js/facebook.js');
	@unlink(dirname(__FILE__).'/../controllers/front/facebook.php');
	@unlink(dirname(__FILE__).'/../models/RewardsFacebookModel.php');
	@unlink(dirname(__FILE__).'/../plugins/RewardsFacebookPlugin.php');
	@unlink(dirname(__FILE__).'/../views/templates/hook/facebook_block.tpl');
	@unlink(dirname(__FILE__).'/../views/templates/hook/facebook_confirmation.tpl');
	@unlink(dirname(__FILE__).'/../views/templates/hook/facebook_like.tpl');
	@unlink(dirname(__FILE__).'/../views/templates/hook/facebook_shopping_cart.tpl');
	@unlink(dirname(__FILE__).'/../views/templates/hook/presta-1.7/facebook_block.tpl');
	@unlink(dirname(__FILE__).'/../views/templates/hook/presta-1.7/facebook_confirmation.tpl');
	@unlink(dirname(__FILE__).'/../views/templates/hook/presta-1.7/facebook_shopping_cart.tpl');

	/* clear cache */
	if (version_compare(_PS_VERSION_, '1.5.5.0', '>='))
		Tools::clearSmartyCache();

	return $result;
}