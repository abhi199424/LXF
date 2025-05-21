<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

class Allinone_rewardsCronreviewModuleFrontController extends ModuleFrontController
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
		if (Tools::getValue('secure_key')) {
			$secureKey = Configuration::getGlobalValue('RREVIEW_CRON_SECURE_KEY');
			if (!empty($secureKey) AND $secureKey === Tools::getValue('secure_key')) {
				RewardsReviewModel::generateRewards();
				return true;
			}
		}
		die('invalid secure key');
	}
}