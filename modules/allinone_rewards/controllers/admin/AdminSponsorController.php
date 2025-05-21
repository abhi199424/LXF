<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

class AdminSponsorController extends ModuleAdminController
{
	public function postProcess()
	{
		die(json_encode(RewardsSponsorshipModel::getAvailableSponsors(Tools::getValue('id_customer'), version_compare(_PS_VERSION_, '1.6', '>=') ? Tools::getValue('q') : Tools::getValue('term'))));
	}
}