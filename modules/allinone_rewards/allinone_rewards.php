<?php
/**
 * All-in-one Rewards Module
 *
 * @author    Yann BONNAILLIE - ByWEB
 * @copyright 2012-2025 Yann BONNAILLIE - ByWEB
 * @license https://opensource.org/licenses/AFL-3.0 Academic Free License version 3.0
 */

if (!defined('_PS_VERSION_')) { exit; }

require_once(_PS_MODULE_DIR_.'/allinone_rewards/api/ReviewGenericAPI.php');
require_once(_PS_MODULE_DIR_.'/allinone_rewards/plugins/RewardsCorePlugin.php');
require_once(_PS_MODULE_DIR_.'/allinone_rewards/plugins/RewardsLoyaltyPlugin.php');
require_once(_PS_MODULE_DIR_.'/allinone_rewards/plugins/RewardsSponsorshipPlugin.php');
require_once(_PS_MODULE_DIR_.'/allinone_rewards/plugins/RewardsRegistrationPlugin.php');
require_once(_PS_MODULE_DIR_.'/allinone_rewards/plugins/RewardsNewsletterPlugin.php');
require_once(_PS_MODULE_DIR_.'/allinone_rewards/plugins/RewardsReviewPlugin.php');
require_once(_PS_MODULE_DIR_.'/allinone_rewards/plugins/RewardsToolsPlugin.php');
require_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsStateModel.php');
require_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsModel.php');
require_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsSponsorshipModel.php');
require_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsSponsorshipCodeModel.php');
require_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsReviewModel.php');
require_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsPaymentModel.php');
require_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsAccountModel.php');
require_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsTemplateModel.php');
require_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsProductModel.php');
require_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsGiftProductModel.php');
require_once(_PS_MODULE_DIR_.'/allinone_rewards/models/RewardsGiftProductAttributeModel.php');

class allinone_rewards extends Module
{
	public $addons = true;
	public $confirmation = '';
	public $errors = '';
	public $path = __FILE__;
	public $core;
	public $loyalty;
	public $sponsorship;
	public $registration;
	public $newsletter;
	public $review;
	public $tools;
	public $plugins;
	private static $_categories;

	public function __construct()
	{
		$this->name = 'allinone_rewards';
		$this->tab = 'advertising_marketing';
		$this->version = '7.0.0';
		$this->author = 'Prestaplugins';
		$this->need_instance = 1;
		$this->ps_versions_compliancy = ['min' => '1.5.0.1', 'max' => _PS_VERSION_];
		$this->module_key = 'a5f535f18bd0a7a74d44b578250baca1';
		$this->controllers = array('rewards', 'sponsorship', 'gifts');

		// Plugins to install : loyalty, sponsorship, sendtoafriend...
		$this->core = new RewardsCorePlugin($this);
		$this->loyalty = new RewardsLoyaltyPlugin($this);
		$this->sponsorship = new RewardsSponsorshipPlugin($this);
		$this->registration = new RewardsRegistrationPlugin($this);
		$this->newsletter = new RewardsNewsletterPlugin($this);
		$this->review = new RewardsReviewPlugin($this);
		$this->tools = new RewardsToolsPlugin($this);
		$this->plugins = array($this->core, $this->loyalty, $this->sponsorship, $this->registration, $this->newsletter, $this->review, $this->tools);


		parent::__construct();

		$this->displayName = $this->l('All-in-one Rewards : loyalty, multi levels sponsorship, affiliation...');
		$this->description = $this->l('This module allows your customers to earn rewards while developing SEO and reputation of your shop: loyalty program, sponsorship program (multi-level, self-promotional),... In addition, the rewards are all grouped into a single account!');
		$this->confirmUninstall = $this->l('Do you really want to remove this module and all of its settings (customer\'s rewards and sponsorship won\'t be removed) ?');

		// add the warnings for each plugin
		foreach($this->plugins as $plugin)
			$plugin->checkWarning();
	}

	public function checkUpdates() {
		if ($rewards_version=Configuration::get('REWARDS_VERSION')) {
			Configuration::deleteByName('REWARDS_VERSION');
			Configuration::updateGlobalValue('REWARDS_VERSION', $rewards_version);
		}
		if (Configuration::getGlobalValue('REWARDS_VERSION') && version_compare($this->version, Configuration::getGlobalValue('REWARDS_VERSION'), '>')) {
			$this->installed = true;
			Module::initUpgradeModule($this);
			self::$modules_cache[$this->name]['upgrade']['upgraded_from'] = Configuration::getGlobalValue('REWARDS_VERSION');
			Module::loadUpgradeVersionList($this->name, $this->version, Configuration::getGlobalValue('REWARDS_VERSION'));
			$this->runUpgradeModule();
			$this->confirmation = $this->displayConfirmation(sprintf($this->l('The module has been update to version %s'), $this->version));
		}
	}

	public function enable($force_all = false) {
		$this->_getXmlRss();
		return parent::enable($force_all);
	}

	public function enableDevice($device) {
		$this->_getXmlRss();
		return parent::enableDevice($device);
	}

	public function install() {
		if (Shop::isFeatureActive()) {
        	Shop::setContext(Shop::CONTEXT_ALL);
    	}

		if (!parent::install() || !$this->_installConf() || !$this->_installPlugins() || !$this->_installQuickAccess())
			return false;
		return true;
	}

	private function _installConf() {
		if (Shop::isFeatureActive()) {
        	Shop::setContext(Shop::CONTEXT_ALL);
    	}

		if (!Configuration::updateGlobalValue('REWARDS_VERSION', $this->version)
		|| !Configuration::updateGlobalValue('REWARDS_INITIAL_CONDITIONS', 0)
		|| !Configuration::updateGlobalValue('PS_CART_RULE_FEATURE_ACTIVE', 1))
			return false;
		return true;
	}

	public function uninstall() {
		if (!parent::uninstall() || !$this->_uninstallPlugins() || !$this->_uninstallQuickAccess())
			return false;
		// reload configuration cache
		Configuration::loadConfiguration();
		return true;
	}

	private function _installPlugins() {
		foreach($this->plugins as $plugin) {
			if (!$plugin->install()) {
				return false;
			}
		}
		return true;
	}

	private function _uninstallPlugins() {
		foreach($this->plugins as $plugin) {
			if (!$plugin->uninstall()) {
				return false;
			}
		}
		return true;
	}

	private function _installQuickAccess() {
		$qa = new QuickAccess();
		foreach (Language::getLanguages() as $language)
			$qa->name[(int)$language['id_lang']] = "All-in-one Rewards";
		$qa->link = "index.php?controller=AdminModules&configure=allinone_rewards&tab_module=&module_name=allinone_rewards";
		$qa->new_window = 0;
		$qa->save();
		return true;
	}

	private function _uninstallQuickAccess() {
		$qa = Db::getInstance()->getValue('
			SELECT id_quick_access FROM `'._DB_PREFIX_.'quick_access_lang`
			WHERE `name`=\'All-in-one Rewards\'');
		if ((int)$qa > 0) {
			Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'quick_access`
				WHERE `id_quick_access`='.(int)$qa);
			Db::getInstance()->execute('
				DELETE FROM `'._DB_PREFIX_.'quick_access_lang`
				WHERE `id_quick_access`='.(int)$qa);
		}
		return true;
	}

	public function getContent() {
		$this->checkUpdates();

		// ajax call to the tabs
		if (Tools::getValue('ajaxtab')) {
			foreach($this->plugins as $plugin) {
				if (Tools::getValue('plugin')==$plugin->name) {
					echo $plugin->displayForm();
					exit;
				}
			}
		}

		if (!Configuration::getGlobalValue('REWARDS_INITIAL_CONDITIONS') &&
			($result=$this->_checkRequiredConditions()) !== true) {
				return $result;
		}
		$this->_postProcess();

		$this->context->controller->addCSS($this->getPath() . 'css/jqueryui/flick/jquery-ui-1.8.16.custom.css', 'all');
		$this->context->controller->addCSS($this->getPath() . 'js/tablesorter/css/theme.ice.css', 'all');
		$this->context->controller->addCSS($this->getPath() . 'js/tablesorter/addons/pager/jquery.tablesorter.pager.css', 'all');
		$this->context->controller->addCSS($this->getPath() . 'js/multiselect/jquery.multiselect.css', 'all');
		$this->context->controller->addCSS($this->getPath() . 'css/admin.css', 'all');

		// categories tree
		if (version_compare(_PS_VERSION_, '1.6', '<')) {
			$this->context->controller->addJS(array(
				_PS_JS_DIR_.'jquery/plugins/treeview-categories/jquery.treeview-categories.js',
				_PS_JS_DIR_.'jquery/plugins/treeview-categories/jquery.treeview-categories.async.js',
				_PS_JS_DIR_.'jquery/plugins/treeview-categories/jquery.treeview-categories.edit.js',
				_PS_JS_DIR_.'admin-categories-tree.js',
			));

			$this->context->controller->addCSS(array(
				_PS_JS_DIR_.'jquery/plugins/treeview-categories/jquery.treeview-categories.css',
			));
		} else {
			$admin_webpath = str_ireplace(_PS_CORE_DIR_, '', _PS_ADMIN_DIR_);
			$admin_webpath = preg_replace('/^'.preg_quote(DIRECTORY_SEPARATOR, '/').'/', '', $admin_webpath);
			$bo_theme = ((Validate::isLoadedObject($this->context->employee) && $this->context->employee->bo_theme) ? $this->context->employee->bo_theme : 'default');
			if (!file_exists(_PS_BO_ALL_THEMES_DIR_.$bo_theme.DIRECTORY_SEPARATOR.'template'))
				$bo_theme = 'default';
			$this->context->controller->addJS(__PS_BASE_URI__.$admin_webpath.'/themes/'.$bo_theme.'/js/tree.js');
		}

		if (version_compare(_PS_VERSION_, '1.6.0.12', '>='))
			$this->context->controller->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
		else
			$this->context->controller->addJS(_PS_JS_DIR_.'tinymce.inc.js');
		$this->context->controller->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
		if (version_compare(_PS_VERSION_, '1.6', '>=')) {
			$this->context->controller->addJqueryPlugin('ui.tabs.min', _PS_JS_DIR_.'jquery/ui/');
			// si idtabs est déjà ajouté par un autre module, utiliser plutôt ceci (semble ne pas marcher sur 1.6.1.4, à vérifier)
			//$this->context->controller->addJqueryPlugin(array('idTabs'));
		} else
			$this->context->controller->addJS($this->getPath() . 'js/jquery-ui-1.8.16.custom.min.js');
		$this->context->controller->addJS($this->getPath() . 'js/admin.js');
		$this->context->controller->addJS($this->getPath() . 'js/tablesorter/jquery.tablesorter.min.js');
		$this->context->controller->addJS($this->getPath() . 'js/tablesorter/jquery.tablesorter.widgets.js');
		$this->context->controller->addJS($this->getPath() . 'js/tablesorter/addons/pager/jquery.tablesorter.pager.js');
		$this->context->controller->addJS($this->getPath() . 'js/multiselect/jquery.multiselect.js');

		$iso = Language::getIsoById((int)$this->context->language->id);
		$rss = $this->_getXmlRss();
		$is_registered = (int)Configuration::getGlobalValue('REWARDS_REGISTERED');
		$id_template = (int)Tools::getValue('rewards_core_template_id');
		$currencies = $this->getCurrencies();
		$virtual_values = array();
		foreach ($currencies as $currency)
			$virtual_values[(int)$currency['id_currency']] = (float)MyConf::get('REWARDS_VIRTUAL_VALUE_'.(int)$currency['id_currency'], $id_template);

		// doit être éxecuté avant pour récupérer les erreurs, la confirmation et faire la sauvegarde des plugins
		$content = array();
		foreach ($this->plugins as &$plugin)
			$plugin->content = $plugin->displayForm();

		if (!$is_registered)
			$this->errors = $this->displayError($this->l('You must register your module license, else you won\'t be able to use the module.'));

		$this->context->smarty->assign(array(
			'module' => $this,
			'is_registered' => $is_registered,
			'current_plugin' => Tools::getValue('plugin'),
			'current_subtab' => Tools::getValue('tabs-' . Tools::getValue('plugin')),
			'facebook_suffix' => $this->context->language->iso_code == 'fr' ? '_fr' : '_en',
			'doc_suffix' => file_exists(dirname(__FILE__).'/readme_'.$this->context->language->iso_code.'.pdf') ? $this->context->language->iso_code : 'en',
			'rss' => $rss,
			'virtual_values' => $virtual_values,
			'virtual_name' => MyConf::get('REWARDS_VIRTUAL_NAME', (int)$this->context->language->id, $id_template),
			'default_language_id' => (int)Configuration::get('PS_LANG_DEFAULT'),
			'isoTinyMCE' => file_exists(_PS_ROOT_DIR_.'/js/tiny_mce/langs/'.$iso.'.js') ? $iso : 'en',
			'ad' => dirname($_SERVER["PHP_SELF"]),
			'pathCSS' => _THEME_CSS_DIR_,
			'languages' => Language::getLanguages(),
			'version' => version_compare(_PS_VERSION_, '1.6', '<') ? '1.5' : '1.6',
		));
		return $this->confirmation.$this->errors.$this->display($this->path, 'views/templates/admin/admin-tabs.tpl');
	}

	private function _postProcess()	{
		if (Tools::isSubmit('submitInitialConditions') && !Configuration::getGlobalValue('REWARDS_INITIAL_CONDITIONS')) {
			// import existing loyalty
			if (Tools::getValue('loyalty_import'))
				RewardsModel::importFromLoyalty();
			// import existing sponsorship
			if (Tools::getValue('advancedreferralprogram_import'))
				RewardsSponsorshipModel::importFromReferralProgram(true);
			else if (Tools::getValue('referralprogram_import'))
				RewardsSponsorshipModel::importFromReferralProgram();

			// inactive old modules
			$modules = array('loyalty', 'advancedreferralprogram', 'referralprogram');
			foreach($modules as $tmpmod) {
				if (Module::isInstalled($tmpmod) && $mod=Module::getInstanceByName($tmpmod))
					$mod->disable();
			}
			Configuration::updateGlobalValue('REWARDS_INITIAL_CONDITIONS', 1);
			$this->confirmation = $this->displayConfirmation($this->l('The module has been initialized.'));
		} else if (Tools::isSubmit('submitRegistration') && Tools::getValue('rewards_registration')) {
			Configuration::updateGlobalValue('REWARDS_REGISTRATION', Tools::getValue('rewards_registration'));
		} else if (Tools::isSubmit('submitNewVersion') && Configuration::getGlobalValue('REWARDS_REGISTRATION')) {
			$data = array('{registration}' => Configuration::getGlobalValue('REWARDS_REGISTRATION'), '{module_version}' => $this->version, '{ps_version}' => _PS_VERSION_);
			$this->sendMail($this->context->language->id, 'asknewversion', $this->l('New version of the module'), $data, 'contact@prestaplugins.com', 'Prestaplugins');
		}
	}

	private function _checkRequiredConditions() {
		if (Tools::isSubmit('submitInitialConditions')) {
			$this->_postProcess();
			return true;
		}

		// Are rewards or sponsorships empty in database ?
		// Could contains datas, if not removed by the uninstall action
		// If not empty, skip that step.
		if (RewardsModel::isNotEmpty() || RewardsSponsorshipModel::isNotEmpty())
			return true;

		// Loyalty installed ?
		$nb_loyalty = 0;
		$loyalty = null;
		if (Module::isInstalled('loyalty') && (float)Configuration::get('PS_LOYALTY_POINT_VALUE') > 0) {
			$loyalty = Module::getInstanceByName('loyalty');
			$nb_loyalty=(int)Db::getInstance()->getValue('SELECT count(*) AS nb FROM `'._DB_PREFIX_.'loyalty`');
		}
		// Advancedreferralprogram or referralprogram installed ?
		$nb_referral = 0;
		$prefix = '';
		$referral = null;
		if (Module::isInstalled('advancedreferralprogram')) {
			$referral = Module::getInstanceByName('advancedreferralprogram');
			$nb_referral=(int)Db::getInstance()->getValue('SELECT count(*) AS nb FROM `'._DB_PREFIX_.'advreferralprogram`');
			$prefix = 'advanced';
		} else if (Module::isInstalled('referralprogram')) {
			$referral = Module::getInstanceByName('referralprogram');
			$nb_referral=(int)Db::getInstance()->getValue('SELECT count(*) AS nb FROM `'._DB_PREFIX_.'referralprogram`');
		}
		if (!$nb_loyalty && !$nb_referral && (!isset($loyalty) || !$loyalty->active) && (!isset($referral) || !$referral->active))
			return true;

		$this->context->smarty->assign(array(
			'module' => $this,
			'nb_loyalty' => $nb_loyalty,
			'loyalty' => $loyalty,
			'nb_referral' => $nb_referral,
			'referral' => $referral,
			'prefix' => $prefix
		));
		return $this->display($this->path, 'views/templates/admin/admin-init.tpl');
	}

	public function getCategories() {
		if (!self::$_categories)
			self::$_categories = Category::getCategories((int)$this->context->language->id, false);
		return self::$_categories;
	}

	// display news and check if a new version is available
	private function _getXmlRss() {
		$bError = false;
		$data = array('version' => $this->version, 'registration' => Configuration::getGlobalValue('REWARDS_REGISTRATION'), 'asknewversion' => Tools::isSubmit('submitNewVersion') ? 1 : 0, 'multistore' => Configuration::getGlobalValue('PS_MULTISHOP_FEATURE_ACTIVE'), 'email' => Configuration::get('PS_SHOP_EMAIL'));
		$request = http_build_query($data, '', '&');
		if (function_exists('curl_init') && $ch = @curl_init('https://www.prestaplugins.com/news/allinone_rewards.php')) {
			curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $request);
			curl_setopt($ch, CURLOPT_TIMEOUT, 50);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($ch, CURLOPT_REFERER, $_SERVER['HTTP_HOST']);
            $response = @curl_exec($ch);
            @curl_close($ch);
		} else if (ini_get('allow_url_fopen')) {
			if ($fp = @fsockopen('https://www.prestaplugins.com', 80, $errno, $errstr, 50)){
				fputs($fp, "GET /news/allinone_rewards.php HTTP/1.0\r\n");
				fputs($fp, "Host: www.prestaplugins.com\r\n");
				fputs($fp, "Referer: ".$_SERVER['HTTP_HOST']."\r\n");
				fputs($fp, "Connection: close\r\n");
				$response = '';
				while (!feof($fp))
					$response .= fgets($fp, 1024);
				fclose($fp);
			} else
				$bError = true;
		} else
			$bError = true;

		if (!$bError) {
			if (!empty($response)) {
				$doc = new DOMDocument('1.0', 'UTF-8');
				@$doc->loadXML($response);
				$version = $doc->getElementsByTagName('version')->item(0)->nodeValue;
				$registered = (int)$doc->getElementsByTagName('registered')->item(0)->nodeValue;
				$asknewversion = (int)$doc->getElementsByTagName('asknewversion')->item(0)->nodeValue;
				$newslist = $doc->getElementsByTagName('news');

				Configuration::updateGlobalValue('REWARDS_REGISTERED', $registered < 1 ? 0 : 1);
				if ($registered == -1)
					Configuration::updateGlobalValue('REWARDS_REGISTRATION', '');

				$i = 0;
				$suffix = ($this->context->language->iso_code == 'fr') ? '_fr' : '_en';
				$articles = array();
				foreach($newslist as $news) {
					$date = $news->getElementsByTagName('date')->item(0)->nodeValue;
					$article = array();
					$article['date'] = ($this->context->language->iso_code == 'fr') ? date('d/m/Y', strtotime($date)) : date('Y-m-d', strtotime($date));
					$article['title'] = $news->getElementsByTagName('title'.$suffix)->item(0)->nodeValue;
					$article['text'] = nl2br($news->getElementsByTagName('text'.$suffix)->item(0)->nodeValue);

					$article['new'] = false;
					if (empty($this->context->cookie->rewards_news) || $this->context->cookie->rewards_news <= $date)
						$article['new'] = true;
					$articles[] = $article;

					if ($i == 0) {
						$this->context->cookie->rewards_news = $date;
						$i++;
					}
				}

				$this->context->smarty->assign(array(
					'module' => $this,
					'xml_error' => $bError,
					'response' => $response,
					'registered' => $registered,
					'asknewversion' => $asknewversion,
					'registration' => Configuration::getGlobalValue('REWARDS_REGISTRATION'),
					'version' => $version,
					'articles' => $articles
				));
			}
		} else
			$this->context->smarty->assign('xml_error', true);

		return $this->display($this->path, 'views/templates/admin/admin-news.tpl');
	}

	/**
     * idem than Module::l but with $id_lang
     **/
    public function l2($string, $id_lang=null, $specific=false)
    {
        global $_MODULE, $_MODULES;

        if (!isset($id_lang))
        	$id_lang = Context::getContext()->language->id;

        $_MODULEStmp = $_MODULES;
        $_MODULES = array();

		$filesByPriority = array(
			// Translations in theme
			_PS_THEME_DIR_.'modules/'.$this->name.'/translations/'.Language::getIsoById((int)$id_lang).'.php',
			_PS_MODULE_DIR_.$this->name.'/translations/'.Language::getIsoById((int)$id_lang).'.php',
		);

		foreach ($filesByPriority as $file) {
			if (Tools::file_exists_cache($file) && include($file)) {
				$_MODULES = !empty($_MODULES) ? array_merge($_MODULES, $_MODULE) : $_MODULE;
			}
		}

		$source = Tools::strtolower($specific ? $specific : $this->name);
		$key = md5(str_replace('\'', '\\\'', $string));

		$ret = $string;
		$current_key = Tools::strtolower('<{'.$this->name.'}'._THEME_NAME_.'>'.$source).'_'.$key;
		$default_key = Tools::strtolower('<{'.$this->name.'}prestashop>'.$source).'_'.$key;
		if (isset($_MODULES[$current_key]))
			$ret = stripslashes($_MODULES[$current_key]);
		elseif (isset($_MODULES[$default_key]))
			$ret = stripslashes($_MODULES[$default_key]);

		$ret = str_replace('"', '&quot;', $ret);
        $_MODULES = $_MODULEStmp;
        return $ret;
    }

	public function getL($key, $id_lang=null) {
		$translations = array(
		'awaiting_validation' => $this->l2('Awaiting validation', $id_lang), // $this->l('Awaiting validation')
		'available' => $this->l2('Available', $id_lang), // $this->l('Available')
		'cancelled' => $this->l2('Cancelled', $id_lang), // $this->l('Cancelled')
		'already_converted' => $this->l2('Already converted', $id_lang), // $this->l('Already converted')
		'unavailable_on_discounts' => $this->l2('Unavailable on discounts', $id_lang), // $this->l('Unavailable on discounts')
		'return_period' => $this->l2('Waiting for return period exceeded', $id_lang), // $this->l('Waiting for return period exceeded')
		'awaiting_payment' => $this->l2('Awaiting payment', $id_lang), // $this->l('Awaiting payment')
		'paid' => $this->l2('Paid', $id_lang), // $this->l('Paid')
		'invitation' => $this->l2('Invitation from your friend', $id_lang), // $this->l('Invitation from your friend')
		'reminder' => $this->l2('Don\'t forget your rewards', $id_lang), // $this->l('Don\'t forget your rewards')
		'expire' => $this->l2('Please note that %s will expire if you do not use them before %s', $id_lang)); // $this->l('Please note that %s will expire if you do not use them before %s')
		return (array_key_exists($key, $translations)) ? $translations[$key] : $key;
	}

	public function sendMail($id_lang, $template, $subject, $data, $mail, $name, $attachment=null) {
		if (Configuration::get('REWARDS_MAILS_IGNORED')) {
			$ignore_list = explode(',', Configuration::get('REWARDS_MAILS_IGNORED'));
			if (is_array($ignore_list)) {
				$ignore_list = array_map('trim', $ignore_list);
				foreach($ignore_list as $ignore) {
					if (strpos($mail, $ignore) !== false)
						return false;
				}
			}
		}

		if (version_compare(_PS_VERSION_, '1.7', '>='))
			$template = '17-'.$template;
		else if (version_compare(_PS_VERSION_, '1.6', '>='))
			$template = '16-'.$template;
		$iso = Language::getIsoById((int)$id_lang);
		if (file_exists(dirname(__FILE__).'/mails/'.$iso.'/'.$template.'.txt') && file_exists(dirname(__FILE__).'/mails/'.$iso.'/'.$template.'.html'))
			return Mail::Send((int)$id_lang, $template, $subject, $data, $mail, $name, Configuration::get('PS_SHOP_EMAIL'), Configuration::get('PS_SHOP_NAME'), $attachment, NULL, dirname(__FILE__).'/mails/');
		else if (file_exists(dirname(__FILE__).'/mails/en/'.$template.'.txt') && file_exists(dirname(__FILE__).'/mails/en/'.$template.'.html')) {
			$id_lang = Language::getIdByIso('en');
			if ($id_lang)
				return Mail::Send((int)$id_lang, $template, $subject, $data, $mail, $name, Configuration::get('PS_SHOP_EMAIL'), Configuration::get('PS_SHOP_NAME'), $attachment, NULL, dirname(__FILE__).'/mails/');
		}
		return false;
	}

	public function getDiscountReadyForDisplay($type, $freeshipping, $value, $id_currency=null, $text=null) {
		if (!is_null($text)) {
			return $text;
		} else {
			$discount = '';
			if ((float)$value > 0) {
				if ((int)$type == 1)
					$discount = (float)$value.chr(37);
				elseif ((int)$type == 2) {
					// when sponsorship generated from back-end, id_currency is provided
					if (is_null($id_currency))
						$id_currency = $this->context->currency;
					$discount = Tools::displayPrice((float)$value, $id_currency);
				}
				$discount .= ' '.$this->l('discount on your order');
			}
			if ((int)$freeshipping == 1)
				$discount = (empty($discount) ? '' : $discount.' + ').$this->l('Free shipping');
			return $discount;
		}
	}

	// TODO : Il vaudrait mieux passer systématiquement l'id_lang, pour éviter les bugs de changement de langue sur le front qui ne sont pris en compte qu'au refresh suivant
	public function getRewardReadyForDisplay($reward, $id_currency, $id_lang=NULL, $convert=true, $id_template=NULL) {
		$context = Context::getContext();
		$currency = Currency::getCurrency((int)$id_currency);

		if (isset($context->customer)) {
			// si on a pas passé d'id_template, on va le chercher
			if (is_null($id_template))
				$id_template = (int)MyConf::getIdTemplate('core', $context->customer->id);
			if (is_null($id_lang) && version_compare(_PS_VERSION_, '1.5.4.0', '>=') && !empty($context->customer->id_lang))
				$id_lang = $context->customer->id_lang;
		}

		if (is_null($id_template))
			$id_template = 0;
		if (is_null($id_lang))
			$id_lang = $context->language->id;

		if (MyConf::get('REWARDS_VIRTUAL', null, $id_template)) {
			$reward = round($reward*(float)MyConf::get('REWARDS_VIRTUAL_VALUE_'.$currency['id_currency'], null, $id_template), 2);
			// on ajoute les décimales que si ce n'est pas un entier
			if ($reward != (int)$reward)
				$reward = number_format($reward, 2, '.', '');
			return $reward.' '.MyConf::get('REWARDS_VIRTUAL_NAME', $id_lang, $id_template);
		} else {
			if ($convert)
				return Tools::displayPrice(round(Tools::convertPrice((float)$reward, $currency), 2), (int)$currency['id_currency']);
			else
				return Tools::displayPrice(round((float)$reward, 2), (int)$currency['id_currency']);
		}
	}

	public function getPath() {
		return $this->_path;
	}

	public function getCurrentPage($plugin=NULL, $ajax=false) {
		$token = Tools::getAdminToken('AdminModules'.(int)Tab::getIdFromClassName('AdminModules').(int)$this->context->employee->id);
		$url = "index.php?controller=AdminModules&configure=allinone_rewards&tab_module=&module_name=allinone_rewards&token=".$token;
		return $url.($ajax ? '&ajaxtab=1' : '').(isset($plugin) ? '&plugin='.$plugin : '');
	}

	public function getCurrencies() {
		if (version_compare(_PS_VERSION_, '1.6.0.7', '>='))
			$currencies = Currency::getCurrencies(false, true, true);
		else if (version_compare(_PS_VERSION_, '1.6.0.2', '>=')) {
			$temp = Currency::getCurrencies();
			$old = null;
			foreach($temp as $currency) {
				if ($currency['id_currency'] != $old) {
					$old = $currency['id_currency'];
					$currencies[] = $currency;
				}
			}
		} else
			$currencies = Currency::getCurrencies();
		return $currencies;
	}


	/*********/
	/* HOOKS */
	/*********/
	public function __call($method, $arguments) {
		return $this->_genericHook($method, isset($arguments[0]) ? $arguments[0] : null);
	}

	private function _genericHook($method, $arguments=NULL) {
		if (Configuration::getGlobalValue('REWARDS_REGISTERED')) {
			$result = '';
			$temp = NULL;
			foreach($this->plugins as $plugin) {
				// verify isActive only for FrontController, admin hooks are always executed
				// hookActionCustomerAccountAdd need to be executed for sponsorship in case the default template is inactive and another template is active, because isActive return false (customer already logged).
				if (($this->context->controller instanceof AdminController || $plugin->isActive() || ($plugin->name=='sponsorship' && strtolower($method)=='hookactioncustomeraccountadd')) && method_exists($plugin, $method)) {
					$temp = $plugin->$method($arguments);
					// hookadditionalcustomerformfields ne renvoit pas un string
					if (strtolower($method)=='hookadditionalcustomerformfields')
						return $temp;
					else if ($temp !== false && $temp !== true)
						$result .= $temp;
				}
			}
			if (!empty($result))
				return $result;
			// cas où le parrainage est inactif, il faut absolument renvoyer un tableau vide
			else if (strtolower($method)=='hookadditionalcustomerformfields')
				return array();
		}
		return false;
	}
}