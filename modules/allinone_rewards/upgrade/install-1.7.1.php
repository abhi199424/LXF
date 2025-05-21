<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

function upgrade_module_1_7_1($object)
{
	$result = true;

	/* Fix bug on rewards created by previous version */
	$sql = 'UPDATE `'._DB_PREFIX_.'rewards` r SET credits=(SELECT credits FROM `'._DB_PREFIX_.'rewards_history` rh WHERE rh.id_reward=r.id_reward AND rh.id_reward_state=1 AND rh.credits > 0 ORDER BY id_reward_history LIMIT 1) WHERE credits=0 AND r.id_reward_state NOT IN (3, 4) AND EXISTS(SELECT 1 FROM `'._DB_PREFIX_.'rewards_history` rh WHERE rh.id_reward=r.id_reward)';
	if (!Db::getInstance()->execute($sql))
		$result = false;

	/* new version */
	Configuration::updateValue('REWARDS_VERSION', $object->version);

	return $result;
}