<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

class Allinone_rewardsRulesModuleFrontController extends ModuleFrontController
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

	public function initContent()
	{
		// allow to not add the javascript at the end causing JS issue (presta 1.6)
		$this->controller_type = 'modulefront';
		parent::initContent();

		$this->context->smarty->assign(array('sback' => Tools::getValue('sback'), 'rules' => MyConf::get('RSPONSORSHIP_RULES_TXT', $this->context->language->id, (int)MyConf::getIdTemplate('sponsorship', $this->context->customer->id))));
		if (version_compare(_PS_VERSION_, '1.7', '<'))
			$this->setTemplate('rules.tpl');
		else
			$this->setTemplate('module:allinone_rewards/views/templates/front/presta-1.7/rules.tpl');
	}

	// allow to not add the javascript at the end causing JS issue (presta 1.6)
	public function display() {
		$html = $this->context->smarty->fetch($this->template);
        echo trim($html);
        return true;
	}
}