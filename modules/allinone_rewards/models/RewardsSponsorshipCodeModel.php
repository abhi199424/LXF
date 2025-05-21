<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

class RewardsSponsorshipCodeModel extends ObjectModel
{
	public $id_sponsor;
	public $code;

	public static $definition = array(
		'table' => 'rewards_sponsorship_code',
		'primary' => 'id_sponsor',
		'fields' => array(
			'id_sponsor' =>			array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId', 'required' => true),
			'code' =>				array('type' => self::TYPE_STRING, 'validate' => 'isString', 'required' => true, 'size' => 20)
		),
	);

	public static function getIdSponsorByCode($code)
	{
		return Db::getInstance()->getValue("SELECT `id_sponsor` FROM `"._DB_PREFIX_."rewards_sponsorship_code` WHERE code='".pSQL($code)."'");
	}
}