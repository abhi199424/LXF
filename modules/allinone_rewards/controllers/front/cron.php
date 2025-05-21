<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

class Allinone_rewardsCronModuleFrontController extends ModuleFrontController
{
	public $content_only = true;
	public $display_header = false;
	public $display_footer = false;

	public function init()
	{
		$this->content_only = true;
		$this->display_header = false;
		$this->display_footer = false;
		parent::init();
	}

	public function display() {
		if (Tools::getValue('secure_key') && Configuration::get('REWARDS_USE_CRON')) {
			$secureKey = Configuration::getGlobalValue('REWARDS_CRON_SECURE_KEY');
			if (!empty($secureKey) AND $secureKey === Tools::getValue('secure_key')) {
				RewardsModel::checkRewardsStates();
				RewardsAccountModel::sendReminder();
				return true;
			}
		}
		die('invalid secure key or cron option disabled');
	}
}