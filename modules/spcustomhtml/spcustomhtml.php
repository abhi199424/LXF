<?php
/**
 * package SP Custom Html
 *
 * @version 1.0.1
 * @author    MagenTech http://www.magentech.com
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

if (!defined ('_PS_VERSION_'))
	exit;
include_once ( dirname (__FILE__).'/SpCustomHtmlClass.php' );
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class SpCustomHtml extends Module implements WidgetInterface
{
	protected $error = false;
	private $html;
	private $templateFile;
	private $default_hook = array( 
		'displayCustomhtmlMobile1',
		'displayCustomhtmlMobile2',
		'displayID1Customhtml',
		'displayID1Customhtml2',
		'displayID1Customhtml3',
		'displayID1Customhtml4',
		'displayID1Customhtml5',
		'displayID1Customhtml6',
		'displayID1Customhtml7',
		'displayID1Customhtml8',
		'displayID1Customhtml9',
		'displayID2Customhtml',
		'displayID2Customhtml2',
		'displayID2Customhtml3',
		'displayID2Customhtml4',
		'displayID2Customhtml5',
		'displayID2Customhtml6',
		'displayID3Customhtml',
		'displayID3Customhtml2',
		'displayID3Customhtml3',
		'displayID4Customhtml1',
		'displayID5Customhtml1',
		'displayLeftColumn'
		);

	public function __construct()
	{
		$this->name = 'spcustomhtml';
		$this->tab = 'front_office_features';
		$this->version = '1.1.0';
		$this->author = 'MagenTech';
		$this->secure_key = Tools::encrypt ($this->name);
		$this->bootstrap = true;
		parent::__construct ();
		$this->displayName = $this->l('SP Custom Html');
		$this->description = $this->l('This Module allows you to create your own HTML Module using a WYSIWYG editor.');
		$this->confirmUninstall = $this->l('Are you sure?');
		$this->ps_versions_compliancy = array('min' => '1.7.0.0', 'max' => _PS_VERSION_);
		$this->templateFile  = 'module:spcustomhtml/views/templates/hook/default.tpl';
	}

	public function install()
	{
		if (parent::install () == false || !$this->registerHook ('header') || !$this->registerHook ('actionShopDataDuplication'))
			return false;
		foreach ($this->default_hook as $hook)
		{
			if (!$this->registerHook ($hook))
				return false;
		}
		$spcustomhtml = Db::getInstance ()->Execute ('DROP TABLE IF EXISTS `'._DB_PREFIX_.'spcustomhtml`')
			&& Db::getInstance ()->Execute ('CREATE TABLE `'._DB_PREFIX_.'spcustomhtml` (`id_spcustomhtml` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`hook` int(10) unsigned, 
			`params` text NOT NULL DEFAULT \'\' ,
			`active` tinyint(1) NOT NULL DEFAULT \'1\',
			`ordering` int(10) unsigned NOT NULL,
			PRIMARY KEY (`id_spcustomhtml`)) ENGINE=InnoDB default CHARSET=utf8');
		$spcustomhtml_shop = Db::getInstance ()->Execute ('DROP TABLE IF EXISTS `'._DB_PREFIX_.'spcustomhtml_shop`')
			&& Db::getInstance ()->Execute ('CREATE TABLE `'._DB_PREFIX_.'spcustomhtml_shop` (`id_spcustomhtml` int(10) unsigned NOT NULL,
			`id_shop` int(10) unsigned NOT NULL, 
			`active` tinyint(1) NOT NULL DEFAULT \'1\',
			PRIMARY KEY (`id_spcustomhtml`,`id_shop`)) ENGINE=InnoDB default CHARSET=utf8');
		$spcustomhtml_lang = Db::getInstance ()->Execute ('DROP TABLE IF EXISTS `'._DB_PREFIX_.'spcustomhtml_lang`')
			&& Db::getInstance ()->Execute ('CREATE TABLE '._DB_PREFIX_.'spcustomhtml_lang (`id_spcustomhtml` int(10) unsigned NOT NULL,
			`id_lang` int(10) unsigned NOT NULL,
			`title_module` varchar(255) NOT NULL DEFAULT \'\',
			`content` text,
			PRIMARY KEY (`id_spcustomhtml`,`id_lang`)) ENGINE=InnoDB default CHARSET=utf8');
		if (!$spcustomhtml || !$spcustomhtml_shop || !$spcustomhtml_lang)
			return false;

		$this->installFixtures();

		return true;
	}

	public function uninstall()
	{
		if (parent::uninstall () == false)
			return false;
		if (!Db::getInstance ()->Execute ('DROP TABLE IF EXISTS `'._DB_PREFIX_.'spcustomhtml`')
			|| !Db::getInstance ()->Execute ('DROP TABLE IF EXISTS `'._DB_PREFIX_.'spcustomhtml_shop`')
			|| !Db::getInstance ()->Execute ('DROP TABLE IF EXISTS `'._DB_PREFIX_.'spcustomhtml_lang`'))
			return false;
		$this->clearCacheItemForHook ();
		return true;
	}
	public function installFixtures()
	{
		$ps_root_dir=str_replace("\\","/",__PS_BASE_URI__);
		$datas = array(			
			array(
				'active' => 1,
				'id_spcustomhtml' => 1,
				'hook' => Hook::getIdByName('displayID1Customhtml'),
				'title_module' => 'Phone Header',
				'content' => '
								<span><em class="fa fa-truck"></em> Free Shipping on Orders $100,000</span>
							',
				'moduleclass_sfx' => 'free-shipping clearfix',
				'display_title_module' => 0
			),
			
			array(
				'active' => 1,
				'id_spcustomhtml' => 2,
				'hook' => Hook::getIdByName('displayID1Customhtml2'),
				'title_module' => 'Banner 1',
				'content' => 	'
									<h5>+1-214-472-222</h5>
									<p class="add"><span>Email: </span>Marketing@MagenTech.Com</p>
								',
				'moduleclass_sfx' => 'contact-html clearfix',
				'display_title_module' =>  0
			),
			
			array(
				'active' => 1,
				'id_spcustomhtml' => 3,
				'hook' => Hook::getIdByName('displayID1Customhtml3'),
				'title_module' => 'Banner 1',
				'content' => 	'
									<div class="clearfix item-1">
										<a class="banner" href="#"><img src="'.$ps_root_dir.'themes/sp_gameshop/assets/img/cms/banner-01.png" alt="Static Image"></a>
									</div>
									<div class="clearfix item-2">
										<a class="banner" href="#"><img src="'.$ps_root_dir.'themes/sp_gameshop/assets/img/cms/banner-02.png" alt="Static Image"></a>
									</div>
								',
				'moduleclass_sfx' => 'banner-1 clearfix',
				'display_title_module' =>  0
			),
			
			array(
				'active' => 1,
				'id_spcustomhtml' => 4,
				'hook' => Hook::getIdByName('displayID1Customhtml4'),
				'title_module' => 'Banner 2',
				'content' => 	'
									<div class="col-md-4 col-sm-4 col-xs-4 item-1">
										<a class="banner" href="#"><img src="'.$ps_root_dir.'themes/sp_gameshop/assets/img/cms/banner-03.png" alt="Static Image"></a>
									</div>
									<div class="col-md-4 col-sm-4 col-xs-4 item-2">
										<a class="banner" href="#"><img src="'.$ps_root_dir.'themes/sp_gameshop/assets/img/cms/banner-04.png" alt="Static Image"></a>
									</div>
									<div class="col-md-4 col-sm-4 col-xs-4 item-3">
										<a class="banner" href="#"><img src="'.$ps_root_dir.'themes/sp_gameshop/assets/img/cms/banner-05.png" alt="Static Image"></a>
									</div>
								',
				'moduleclass_sfx' => 'banner-2 clearfix',
				'display_title_module' =>  0
			),

			array(
				'active' => 1,
				'id_spcustomhtml' => 5,
				'hook' => Hook::getIdByName('displayID1Customhtml5'),
				'title_module' => 'Banner',
				'content' => 	'
									<a class="banner" href="#">
										<img src="'.$ps_root_dir.'themes/sp_gameshop/assets/img/cms/banner-06.png" alt="left Image" />
									</a>
								',
				'moduleclass_sfx' => 'banner-3 hidden-md-down block',
				'display_title_module' =>  0
			),
			
			array(
				'active' => 1,
				'id_spcustomhtml' => 6,
				'hook' => Hook::getIdByName('displayID1Customhtml6'),
				'title_module' => 'Bonus Menu',
				'content' => 	'
									<div class="item item-1">
										<div class="icon"></div>
										<div class="text">
											<a class="name" href="#">Worldwide Delivery</a>
											<p>Free Delivery with $50</p>
										</div>
									</div>
									<div class="item item-2">
										<div class="icon"></div>
										<div class="text">
											<a class="name" href="#">Online Support</a>
											<p>24/7 Support</p>
										</div>
									</div>
									<div class="item item-3">
										<div class="icon"></div>
										<div class="text">
											<a class="name" href="#">Payment Method</a>
											<p>100% Payment Secured</p>
										</div>
									</div>
									<div class="item item-4">
										<div class="icon"></div>
										<div class="text">
											<a class="name" href="#">Money Guarentee</a>
											<p>30 Days Money Back</p>
										</div>
									</div>
								',
				'moduleclass_sfx' => 'bonus-menu',
				'display_title_module' =>  0
			),
			
			array(
				'active' => 1,
				'id_spcustomhtml' => 7,
				'hook' => Hook::getIdByName('displayID1Customhtml7'),
				'title_module' => 'Banner',
				'content' => 	'
									<a class="banner" href="#">
										<img src="'.$ps_root_dir.'themes/sp_gameshop/assets/img/cms/banner-07.png" alt="left Image" />
									</a>
								',
				'moduleclass_sfx' => 'banner-4 block',
				'display_title_module' =>  0
			),
			
			array(
				'active' => 1,
				'id_spcustomhtml' => 8,
				'hook' => Hook::getIdByName('displayID1Customhtml8'),
				'title_module' => 'About Us',
				'content' => '
								<p>
									Amet nisl purus in mollis nunc sed id. Commodo nulla facilisi nullam vehicula ipsum a arcu cursus. Volutpat a tincidunt vita semper pellentesque diam volutpat.
								</p>
								<ul>
										<li>
											<a href="#">Home</a>
										</li>
										<li>
											<a href="#">Blog</a>
										</li>
										<li>
											<a href="#">Team</a>
										</li>
										<li>
											<a href="#">Support</a>
										</li>
										<li>
											<a href="#">Game</a>
										</li>
										<li>
											<a href="#">Our Story</a>
										</li>
										<li>
											<a href="#">Community</a>
										</li>
										<li>
											<a href="#">Contact Us</a>
										</li>
								</ul>
							',
				'moduleclass_sfx' => 'about-us-html',
				'display_title_module' => 1
			),
			
			array(
				'active' => 1,
				'id_spcustomhtml' => 9,
				'hook' => Hook::getIdByName('displayID1Customhtml9'),
				'title_module' => 'Download App',
				'content' => '
								<div class="app-store">
									<a class="app-1" href="#">google store</a> 
									<a class="app-2" href="#">apple store</a>
								</div>
							',
				'moduleclass_sfx' => 'download-app',
				'display_title_module' => 1
			),
			
			array(
				'active' => 1,
				'id_spcustomhtml' => 10,
				'hook' => Hook::getIdByName('displayID2Customhtml'),
				'title_module' => 'Contact HTML 2',
				'content' => 	'
									<span><em class="fa fa-envelope"></em> example@support.com</span>
									<span class="add"><em class="fa fa-phone"></em> +18 437.1473.74</span>
								',
				'moduleclass_sfx' => 'contact-html-2 clearfix',
				'display_title_module' =>  0
			),

			array(
				'active' => 1,
				'id_spcustomhtml' => 11,
				'hook' => Hook::getIdByName('displayID2Customhtml2'),
				'title_module' => 'Banner 2-1',
				'content' => 	'
									<div class="col-md-4 col-sm-6 col-xs-12 item-1">
										<a class="banner" href="#"><img src="'.$ps_root_dir.'themes/sp_gameshop/assets/img/cms/banner-2-1.jpg" alt="Static Image"></a>
									</div>
									<div class="col-md-4 col-sm-6 col-xs-12 item-2">
										<a class="banner" href="#"><img src="'.$ps_root_dir.'themes/sp_gameshop/assets/img/cms/banner-2-2.jpg" alt="Static Image"></a>
									</div>
									<div class="col-md-4 col-sm-6 col-xs-12 item-3">
										<a class="banner" href="#"><img src="'.$ps_root_dir.'themes/sp_gameshop/assets/img/cms/banner-2-3.jpg" alt="Static Image"></a>
									</div>
								',
				'moduleclass_sfx' => 'banner-2-1 clearfix',
				'display_title_module' =>  0
			),
			
			array(
				'active' => 1,
				'id_spcustomhtml' => 12,
				'hook' => Hook::getIdByName('displayID2Customhtml3'),
				'title_module' => 'Categories Items',
				'content' => 	'
									<div class="item item-1">
											<div class="icon">
												<em class="fa fa-phone"></em>
											</div>
											<h5>
												<a href="#">Laptop & Access...</a>
											</h5>
									</div>
									<div class="item item-2">
											<div class="icon">
												<em class="fa fa-phone"></em>
											</div>
											<h5>
												<a href="#">Tower Computer</a>
											</h5>
									</div>
									<div class="item item-3">
											<div class="icon">
												<em class="fa fa-phone"></em>
											</div>
											<h5>
												<a href="#">Computer Comp...</a>
											</h5>
									</div>
									<div class="item item-4">
											<div class="icon">
												<em class="fa fa-phone"></em>
											</div>
											<h5>
												<a href="#">Gaming Gear</a>
											</h5>
									</div>
									<div class="item item-5">
											<div class="icon">
												<em class="fa fa-phone"></em>
											</div>
											<h5>
												<a href="#">Monitor</a>
											</h5>
									</div>
									<div class="item item-6">
											<div class="icon">
												<em class="fa fa-phone"></em>
											</div>
											<h5>
												<a href="#">Computer Access...</a>
											</h5>
									</div>
									<div class="item item-7">
											<div class="icon">
												<em class="fa fa-phone"></em>
											</div>
											<h5>
												<a href="#">Speaker</a>
											</h5>
									</div>
									<div class="item item-8">
											<div class="icon">
												<em class="fa fa-phone"></em>
											</div>
											<h5>
												<a href="#">Console</a>
											</h5>
									</div>
									<div class="item item-9">
											<div class="icon">
												<em class="fa fa-phone"></em>
											</div>
											<h5>
												<a href="#">Accessories</a>
											</h5>
									</div>
									<div class="item item-10">
											<div class="icon">
												<em class="fa fa-phone"></em>
											</div>
											<h5>
												<a href="#">Cooling</a>
											</h5>
									</div>
								',
				'moduleclass_sfx' => 'categories-items clearfix',
				'display_title_module' =>  0
			),
			
			array(
				'active' => 1,
				'id_spcustomhtml' => 11,
				'hook' => Hook::getIdByName('displayID2Customhtml4'),
				'title_module' => 'Banner 2-1',
				'content' => 	'
									<div class="col-md-6 col-sm-6 col-xs-12 item-1">
										<a class="banner" href="#"><img src="'.$ps_root_dir.'themes/sp_gameshop/assets/img/cms/banner-2-4.jpg" alt="Static Image"></a>
									</div>
									<div class="col-md-6 col-sm-6 col-xs-12 item-2">
										<a class="banner" href="#"><img src="'.$ps_root_dir.'themes/sp_gameshop/assets/img/cms/banner-2-5.jpg" alt="Static Image"></a>
									</div>
								',
				'moduleclass_sfx' => 'banner-2-2 clearfix',
				'display_title_module' =>  0
			),
						
			array(
				'active' => 1,
				'id_spcustomhtml' => 11,
				'hook' => Hook::getIdByName('displayID2Customhtml5'),
				'title_module' => 'Banner 2-1',
				'content' => 	'
									<div class="col-md-4 col-sm-6 col-xs-12 item-1">
										<a class="banner" href="#"><img src="'.$ps_root_dir.'themes/sp_gameshop/assets/img/cms/banner-2-6.jpg" alt="Static Image"></a>
									</div>
									<div class="col-md-4 col-sm-6 col-xs-12 item-2">
										<a class="banner" href="#"><img src="'.$ps_root_dir.'themes/sp_gameshop/assets/img/cms/banner-2-7.jpg" alt="Static Image"></a>
									</div>
									<div class="col-md-4 col-sm-6 col-xs-12 item-2">
										<a class="banner" href="#"><img src="'.$ps_root_dir.'themes/sp_gameshop/assets/img/cms/banner-2-8.jpg" alt="Static Image"></a>
									</div>
								',
				'moduleclass_sfx' => 'banner-2-3 clearfix',
				'display_title_module' =>  0
			),
			
			array(
				'active' => 1,
				'id_spcustomhtml' => 14,
				'hook' => Hook::getIdByName('displayID4Customhtml1'),
				'title_module' => 'Banner 1',
				'content' => 	'
									<div class="clearfix item-1">
										<a class="banner" href="#"><img src="'.$ps_root_dir.'themes/sp_gameshop/assets/img/cms/banner-01.png" alt="Static Image"></a>
									</div>
									<div class="clearfix item-2">
										<a class="banner" href="#"><img src="'.$ps_root_dir.'themes/sp_gameshop/assets/img/cms/banner-02.png" alt="Static Image"></a>
									</div>
								',
				'moduleclass_sfx' => 'banner-1 clearfix',
				'display_title_module' =>  0
			),
			
			array(
				'active' => 1,
				'id_spcustomhtml' => 15,
				'hook' => Hook::getIdByName('displayID5Customhtml1'),
				'title_module' => 'Banner 1',
				'content' => 	'
									<div class="clearfix item-1">
										<a class="banner" href="#"><img src="'.$ps_root_dir.'themes/sp_gameshop/assets/img/cms/banner-01.png" alt="Static Image"></a>
									</div>
									<div class="clearfix item-2">
										<a class="banner" href="#"><img src="'.$ps_root_dir.'themes/sp_gameshop/assets/img/cms/banner-02.png" alt="Static Image"></a>
									</div>
								',
				'moduleclass_sfx' => 'banner-1 clearfix',
				'display_title_module' =>  0
			),
		);
		$return = true;
		$temp = array();
		foreach ($datas as $i => $data)
		{
			$customs = new SpCustomHtmlClass();
			$temp['content'] = $data['content'];
			$temp['title_module'] = $data['title_module'];
			$customs->hook = $data['hook'];
			$customs->active = $data['active'];
			$customs->ordering = $i;
			unset($data['content']);
			unset($data['title_module']);
			$customs->params = serialize($data);
			foreach (Language::getLanguages(false) as $lang)
			{
			 $customs->content[$lang['id_lang']] = $temp['content'];
			 $customs->title_module[$lang['id_lang']] = $temp['title_module'];
			}
			$return &= $customs->add();
		}
		return $return;
	}

	public function getContent()
	{
		if (Tools::isSubmit ('saveItem') || Tools::isSubmit ('saveAndStay'))
		{
			if ($this->postValidation())
			{
				$this->html .= $this->postProcess();
				$this->html .= $this->initForm();
			}
			else
				$this->html .= $this->initForm();
		}
		elseif (Tools::isSubmit ('addItem') || (Tools::isSubmit('editItem')
				&& $this->moduleExists((int)Tools::getValue('id_spcustomhtml'))) || Tools::isSubmit ('saveItem'))
		{
			if (Tools::isSubmit('addItem'))
				$mode = 'add';
			else
				$mode = 'edit';
			if ($mode == 'add')
			{
				if (Shop::getContext() != Shop::CONTEXT_GROUP && Shop::getContext() != Shop::CONTEXT_ALL)
					$this->html .= $this->initForm ();
				else
					$this->html .= $this->getShopContextError(null, $mode);
			}
			else
			{
				$associated_shop_ids = SpCustomHtmlClass::getAssociatedIdsShop((int)Tools::getValue('id_spcustomhtml'));
				$context_shop_id = (int)Shop::getContextShopID();

				if ($associated_shop_ids === false)
					$this->html .= $this->getShopAssociationError((int)Tools::getValue('id_spcustomhtml'));
				else if (Shop::getContext() != Shop::CONTEXT_GROUP && Shop::getContext() != Shop::CONTEXT_ALL
					&& in_array($context_shop_id, $associated_shop_ids))
				{
					if (count($associated_shop_ids) > 1)
						$this->html = $this->getSharedSlideWarning();
					$this->html .= $this->initForm();
				}
				else
				{
					$shops_name_list = array();
					foreach ($associated_shop_ids as $shop_id)
					{
						$associated_shop = new Shop((int)$shop_id);
						$shops_name_list[] = $associated_shop->name;
					}
					$this->html .= $this->getShopContextError($shops_name_list, $mode);
				}
			}
		}
		else
		{
			if ($this->postValidation())
			{
				$this->html .= $this->postProcess();
				$this->html .= $this->displayForm ();
			}
			else
				$this->html .= $this->displayForm ();
		}
		return $this->html;
	}
	private function postValidation()
	{	$errors = array();
		if (Tools::isSubmit ('saveItem') || Tools::isSubmit ('saveAndStay'))
		{
			if (!Validate::isInt(Tools::getValue('active')) || (Tools::getValue('active') != 0
					&& Tools::getValue('active') != 1))
				$errors[] = $this->l('Invalid slide state.');
			if (!Validate::isInt(Tools::getValue('position')) || (Tools::getValue('position') < 0))
				$errors[] = $this->l('Invalid slide position.');
			if (Tools::isSubmit('id_spcustomhtml'))
			{
				if (!Validate::isInt(Tools::getValue('id_spcustomhtml'))
					&& !$this->moduleExists(Tools::getValue('id_spcustomhtml')))
					$errors[] = $this->l('Invalid module ID');
			}
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
			{
				if (Tools::strlen(Tools::getValue('title_module_'.$language['id_lang'])) > 255)
					$errors[] = $this->l('The title is too long.');
				/*if (Tools::strlen(Tools::getValue('content_'.$language['id_lang'])) > 4000)
					$errors[] = $this->l('The content is too long.');*/
			}
			$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
			if (Tools::strlen(Tools::getValue('title_module_'.$id_lang_default)) == 0)
				$errors[] = $this->l('The title module is not set.');
			if (Tools::strlen(Tools::getValue('content_'.$id_lang_default)) == 0)
				$errors[] = $this->l('The content is not set.');
			if (Tools::strlen(Tools::getValue('moduleclass_sfx')) > 255)
				$errors[] = $this->l('The Module Class Suffix  is too long.');
		}elseif (Tools::isSubmit('id_spcustomhtml')
			&& (!Validate::isInt(Tools::getValue('id_spcustomhtml'))
				|| !$this->moduleExists((int)Tools::getValue('id_spcustomhtml'))))
			$errors[] = $this->l('Invalid module ID');
		if (count($errors))
		{
			$this->html .= $this->displayError(implode('<br />', $errors));
			return false;
		}
		return true;
	}

	private function postProcess()
	{
		$currentIndex = AdminController::$currentIndex;
		if (Tools::isSubmit ('saveItem') || Tools::isSubmit ('saveAndStay'))
		{
			if (Tools::getValue('id_spcustomhtml'))
			{
				$customhtml = new SpCustomHtmlClass((int)Tools::getValue ('id_spcustomhtml'));
				if (!Validate::isLoadedObject($customhtml))
				{
					$this->html .= $this->displayError($this->l('Invalid slide ID'));
					return false;
				}
			}
			else
				$customhtml = new SpCustomHtmlClass();
			$next_ps = $this->getNextPosition();
			$customhtml->ordering = (!empty($customhtml->ordering)) ? (int)$customhtml->ordering : $next_ps;
			$customhtml->active = (Tools::getValue('active')) ? (int)Tools::getValue('active') : 0;
			$customhtml->hook	= (int)Tools::getValue('hook');
			$tmp_data = array();
			$id_spcustomhtml = (int)Tools::getValue ('id_spcustomhtml');
			$id_spcustomhtml = $id_spcustomhtml ? $id_spcustomhtml : (int)$customhtml->getHigherModuleID();
			$tmp_data['id_spcustomhtml'] = $id_spcustomhtml;

			$tmp_data['active'] = (int)Tools::getValue ('active', 1);
			$tmp_data['moduleclass_sfx'] = Tools::getValue ('moduleclass_sfx');
			$tmp_data['display_title_module'] = Tools::getValue ('display_title_module');
			$tmp_data['hook '] = Tools::getValue('hook');
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
			{
				$customhtml->title_module[$language['id_lang']] = Tools::getValue('title_module_'.$language['id_lang']);
				$customhtml->content[(int)$language['id_lang']] = Tools::getValue ('content_'.$language['id_lang']);
			}
			$customhtml->params = serialize($tmp_data);
			(Tools::getValue ('id_spcustomhtml')
		&& $this->moduleExists((int)Tools::getValue ('id_spcustomhtml')) )? $customhtml->update() : $customhtml->add ();
			$this->clearCacheItemForHook ();
			if (Tools::isSubmit ('saveAndStay'))
			{
				$tool_id_spcustomhtml = Tools::getValue ('id_spcustomhtml');
				$higher_module = $customhtml->getHigherModuleID();
				$id_spcustomhtml = $tool_id_spcustomhtml?(int)$tool_id_spcustomhtml:(int)$higher_module;
				Tools::redirectAdmin ($currentIndex.'&configure='
				.$this->name.'&token='.Tools::getAdminTokenLite ('AdminModules').'&editItem&id_spcustomhtml='
					.$id_spcustomhtml.'&updateItemConfirmation');
			}
			else
				Tools::redirectAdmin ($currentIndex.'&configure='.$this->name
					.'&token='.Tools::getAdminTokenLite ('AdminModules').'&saveItemConfirmation');
		}
		elseif (Tools::isSubmit('changeStatusItem') && Tools::getValue ('id_spcustomhtml'))
		{
			$customhtml = new SpCustomHtmlClass((int)Tools::getValue ('id_spcustomhtml'));
			if ($customhtml->active == 0)
				$customhtml->active = 1;
			else
				$customhtml->active = 0;
			//$customhtml->updateStatus (Tools::getValue ('active'));
			$customhtml->update();
			$this->clearCacheItemForHook ();
			Tools::redirectAdmin ($currentIndex.'&configure='.$this->name
				.'&token='.Tools::getAdminTokenLite ('AdminModules'));
		}
		elseif (Tools::isSubmit ('deleteItem') && Tools::getValue ('id_spcustomhtml'))
		{
			$customhtml = new SpCustomHtmlClass((int)Tools::getValue ('id_spcustomhtml'));
			$customhtml->delete ();
			$this->clearCacheItemForHook ();
			Tools::redirectAdmin ($currentIndex.'&configure='.$this->name.'&token='
				.Tools::getAdminTokenLite ('AdminModules').'&deleteItemConfirmation');
		}
		elseif (Tools::isSubmit ('duplicateItem') && Tools::getValue ('id_spcustomhtml'))
		{
			$customhtml = new SpCustomHtmlClass(Tools::getValue ('id_spcustomhtml'));
			foreach (Language::getLanguages (false) as $lang)
				$customhtml->title_module[(int)$lang['id_lang']] = $customhtml->title_module[(int)$lang['id_lang']]
					.$this->l(' (Copy)');
			$customhtml->duplicate();
			$this->clearCacheItemForHook ();
			Tools::redirectAdmin ($currentIndex.'&configure='.$this->name.'&token='
				.Tools::getAdminTokenLite ('AdminModules').'&duplicateItemConfirmation');
		}
		elseif (Tools::isSubmit ('saveItemConfirmation'))
			$this->html = $this->displayConfirmation ($this->l('Module successfully updated!'));
		elseif (Tools::isSubmit ('deleteItemConfirmation'))
			$this->html = $this->displayConfirmation ($this->l('Module successfully deleted!'));
		elseif (Tools::isSubmit ('duplicateItemConfirmation'))
			$this->html = $this->displayConfirmation ($this->l('Module successfully duplicated!'));
		elseif (Tools::isSubmit ('updateItemConfirmation'))
			$this->html = $this->displayConfirmation ($this->l('Module successfully updated!'));
	}

	private function clearCacheItemForHook()
	{
		$this->_clearCache ('default.tpl');
	}
	public function moduleExists($id_spcustomhtml)
	{
		$req = 'SELECT cs.`id_spcustomhtml` 
				FROM `'._DB_PREFIX_.'spcustomhtml` cs
				WHERE cs.`id_spcustomhtml` = '.(int)$id_spcustomhtml;
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);

		return ($row);
	}
	public function getNextPosition()
	{
		$row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow('
			SELECT MAX(cs.`ordering`) AS `next_position`
			FROM `'._DB_PREFIX_.'spcustomhtml` cs, `'._DB_PREFIX_.'spcustomhtml_shop` css
			WHERE css.`id_spcustomhtml` = cs.`id_spcustomhtml` AND css.`id_shop` = '.(int)$this->context->shop->id
		);

		return (++$row['next_position']);
	}

	private function getGridItems()
	{
		$this->context = Context::getContext ();
		$id_lang = $this->context->language->id;
		$id_shop = $this->context->shop->id;
		$sql = 'SELECT b.`id_spcustomhtml`,  b.`hook`, b.`ordering`, bs.`active`, bl.`title_module`, bl.`content`
			FROM `'._DB_PREFIX_.'spcustomhtml` b
			LEFT JOIN `'._DB_PREFIX_.'spcustomhtml_shop` bs ON (b.`id_spcustomhtml` = bs.`id_spcustomhtml` )
			LEFT JOIN `'._DB_PREFIX_.'spcustomhtml_lang` bl ON (b.`id_spcustomhtml` = bl.`id_spcustomhtml`)
			WHERE bs.`id_shop` = '.(int)$id_shop.' 
			AND bl.`id_lang` = '.(int)$id_lang.'
			ORDER BY b.`ordering`';
		return Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql );
	}

	private function getHookTitle($id_hook, $name = false)
	{
		if (!$result = Db::getInstance ()->getRow ('
			SELECT `name`,`title`
			FROM `'._DB_PREFIX_.'hook`
			WHERE `id_hook` = '.( $id_hook )))
			return false;
		return ( ( $result['title'] != '' && $name )?$result['title']:$result['name'] );
	}

	private function displayForm()
	{
		$currentIndex = AdminController::$currentIndex;
		$modules = array();
		$this->html .= $this->headerHTML ();
		if (Shop::getContext() == Shop::CONTEXT_GROUP || Shop::getContext() == Shop::CONTEXT_ALL)
			$this->html .= $this->getWarningMultishopHtml();
		else if (Shop::getContext() != Shop::CONTEXT_GROUP && Shop::getContext() != Shop::CONTEXT_ALL)
		{
			$modules = $this->getGridItems ();
			if (!empty($modules))
			{
				foreach ($modules as $key => $mod)
				{
					$associated_shop_ids = SpCustomHtmlClass::getAssociatedIdsShop((int)$mod['id_spcustomhtml']);
					if ($associated_shop_ids && count($associated_shop_ids) > 1)
						$modules[$key]['is_shared'] = true;
					else
						$modules[$key]['is_shared'] = false;
				}
			}
		}
		$this->html .= '
	 	<div class="panel">
			<div class="panel-heading">
			'.$this->l('Module Manager').'
			<span class="panel-heading-action">
					<a class="list-toolbar-btn" href="'.$currentIndex.'&configure='.$this->name
			.'&token='.Tools::getAdminTokenLite ('AdminModules').'&addItem">
			<span data-toggle="tooltip" class="label-tooltip" data-original-title="'
			.$this->l('Add new module').'" data-html="true"><i class="process-icon-new "></i></span></a>
			</span>
			</div>
			<table width="100%" class="table" cellspacing="0" cellpadding="0">
			<thead>
			<tr class="nodrag nodrop">
				<th>'.$this->l('ID').'</th>
				<th>'.$this->l('Ordering').'</th>
				<th class=" left">'.$this->l('Title').'</th>
				<th class=" left">'.$this->l('Hook into').'</th>
				<th class=" left">'.$this->l('Status').'</th>
				<th class=" right"><span class="title_box text-right">'.$this->l('Actions').'</span></th>
			</tr>
			</thead>
			<tbody id="gird_items">';
		if (!empty($modules))
		{
			static $irow;
			foreach ($modules as $customhtml)
			{
				$this->html .= '
				<tr id="item_'.$customhtml['id_spcustomhtml'].'" class=" '.( $irow ++ % 2?' ':'' ).'">
					<td class=" 	" onclick="document.location = \''.$currentIndex.'&configure='.$this->name.'&token='
					.Tools::getAdminTokenLite ('AdminModules').'&editItem&id_spcustomhtml='
					.$customhtml['id_spcustomhtml'].'\'">'
					.$customhtml['id_spcustomhtml'].'</td>
					<td class=" dragHandle"><div class="dragGroup"><div class="positions">'.$customhtml['ordering']
					.'</div></div></td>
					<td class="  " onclick="document.location = \''.$currentIndex.'&configure='.$this->name.'&token='
					.Tools::getAdminTokenLite ('AdminModules')
					.'&editItem&id_spcustomhtml='.$customhtml['id_spcustomhtml'].'\'">'.$customhtml['title_module']
					.' '.($customhtml['is_shared'] ? '<span class="label color_field"
		style="background-color:#108510;color:white;margin-top:5px;">'.$this->l('Shared').'</span>' : '').'</td>
					<td class="  " onclick="document.location = \''.$currentIndex.'&configure='.$this->name
					.'&token='.Tools::getAdminTokenLite ('AdminModules').'&editItem&id_spcustomhtml='
					.$customhtml['id_spcustomhtml'].'\'">'
					.( Validate::isInt ($customhtml['hook'])?$this->getHookTitle ($customhtml['hook']):'' ).'</td>
					<td class="  "> <a href="'.$currentIndex.'&configure='.$this->name.'&token='
					.Tools::getAdminTokenLite ('AdminModules')
					.'&changeStatusItem&id_spcustomhtml='.$customhtml['id_spcustomhtml'].'&status='
					.$customhtml['active'].'&hook='.$customhtml['hook'].'">'.( $customhtml['active']?'
					<i class="icon-check"></i>':'<i class="icon-remove"></i>' ).'</a> </td>
					<td class="text-right">
						<div class="btn-group-action">
							<div class="btn-group pull-right">
								<a class="btn btn-default" href="'.$currentIndex.'&configure='.$this->name.'&token='
		.Tools::getAdminTokenLite ('AdminModules').'&editItem&id_spcustomhtml='.$customhtml['id_spcustomhtml'].'">
									<i class="icon-pencil"></i> Edit
								</a> 
								<button data-toggle="dropdown" class="btn btn-default dropdown-toggle">
									<span class="caret"></span>&nbsp;
								</button>
								<ul class="dropdown-menu">
									<li>
							<a onclick="return confirm(\''
					.$this->l('Are you sure want duplicate this item?')
					.'\');"  title="'.$this->l('Duplicate').'" href="'.$currentIndex.'&configure='
					.$this->name.'&token='
					.Tools::getAdminTokenLite ('AdminModules').'&duplicateItem&id_spcustomhtml='
					.$customhtml['id_spcustomhtml'].'">
											<i class="icon-copy"></i> '.$this->l('Duplicate').'
										</a>								
									</li>
									<li class="divider"></li>
									<li>
										<a title ="'.$this->l('Delete').'" onclick="return confirm(\''
					.$this->l('Are you sure?').'\');" href="'.$currentIndex
					.'&configure='.$this->name.'&token='
					.Tools::getAdminTokenLite ('AdminModules').'&deleteItem&id_spcustomhtml='
					.$customhtml['id_spcustomhtml'].'">
											<i class="icon-trash"></i> '.$this->l('Delete').'
										</a>
									</li>
								</ul>
							</div>
						</div>
					</td>
				</tr>';
			}
		}
		else
		{
			$this->html .= '<td colspan="5" class="list-empty">
								<div class="list-empty-msg">
									<i class="icon-warning-sign list-empty-icon"></i>
									'.$this->l('No records found').'
								</div>
							</td>';
		}
		$this->html .= '
			</tbody>
			</table>
		</div>';
	}

	public function getHookList()
	{
		$hooks = array();
		foreach ($this->default_hook as $key => $hook)
		{
			$id_hook = Hook::getIdByName ($hook);
			$name_hook = $this->getHookTitle ($id_hook);
			$hooks[$key]['key'] = $id_hook;
			$hooks[$key]['name'] = $name_hook;
		}
		return $hooks;
	}

	public function initForm()
	{
		$default_lang = (int)Configuration::get ('PS_LANG_DEFAULT');
		$hooks = $this->getHookList ();
		$this->fields_form[0]['form'] = array(
			'tinymce' => true,
			'legend'  => array(
				'title' => $this->l('General Options'),
				'icon'  => 'icon-cogs'
			),
			'input'   => array(
				array(
					'type'     => 'text',
					'label'    => $this->l('Title'),
					'lang'     => true,
					'name'     => 'title_module',
					'class'    => 'fixed-width-xl',
					'hint'     => $this->l('Title Of Module')
				),
				array(
					'type'  => 'text',
					'label' => $this->l('Module Class Suffix'),
					'name'  => 'moduleclass_sfx',
					'hint'  => $this->l('A suffix to be applied to the CSS class of the module.
					This allows for individual module styling.'),
					'class' => 'fixed-width-xl'
				),
				array(
					'type'   => 'switch',
					'label'  => $this->l('Display Title'),
					'name'   => 'display_title_module',
					'hint'   => $this->l('Display Title Of Module'),
					'values' => array(
						array(
							'id'    => 'active_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id'    => 'active_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				),
				array(
					'type'   => 'switch',
					'label'  => $this->l('Status'),
					'name'   => 'active',
					'hint'   => $this->l('Status Of Module'),
					'values' => array(
						array(
							'id'    => 'active_on',
							'value' => 1,
							'label' => $this->l('Enabled')
						),
						array(
							'id'    => 'active_off',
							'value' => 0,
							'label' => $this->l('Disabled')
						)
					)
				),
				array(
					'type'    => 'select',
					'label'   => $this->l('Hook into'),
					'name'    => 'hook',
					'hint'    => $this->l('Select Hook for Module'),
					'options' => array(
						'query' => $hooks,
						'id'    => 'key',
						'name'  => 'name'
					)
				),
				array(
					'type'         => 'textarea',
					'label'        => $this->l('Content'),
					'name'         => 'content',
					'hint'         => $this->l('Show Content Of Module'),
					'lang'         => true,
					'autoload_rte' => true,
					'cols'         => 40,
					'rows'         => 10
				)
			),
			'submit'  => array(
				'title' => $this->l('Save')
			),
			'buttons' => array(
				array(
					'title' => $this->l('Save and stay'),
					'name'  => 'saveAndStay',
					'type'  => 'submit',
					'class' => 'btn btn-default pull-right',
					'icon'  => 'process-icon-save'
				)
			)
		);
		$helper = new HelperForm();
		$helper->module = $this;
		$helper->name_controller = 'spcustomhtml';
		$helper->identifier = $this->identifier;
		$helper->token = Tools::getAdminTokenLite ('AdminModules');
		$helper->show_cancel_button = true;
		$helper->back_url = AdminController::$currentIndex.'&configure='.$this->name.'&token='
			.Tools::getAdminTokenLite ('AdminModules');
		foreach (Language::getLanguages (false) as $lang)
			$helper->languages[] = array(
				'id_lang'    => $lang['id_lang'],
				'iso_code'   => $lang['iso_code'],
				'name'       => $lang['name'],
				'is_default' => ( $default_lang == $lang['id_lang']?1:0 )
			);
		$helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
		$helper->default_form_language = $default_lang;
		$helper->allow_employee_form_lang = $default_lang;
		$helper->toolbar_scroll = true;
		$helper->title = $this->displayName;
		$helper->submit_action = 'saveItem';
		$helper->toolbar_btn = array(
			'save' => array(
				'desc' => $this->l('Save'),
				'href' => AdminController::$currentIndex.'&configure='.$this->name
					.'&save'.$this->name.'&token='.Tools::getAdminTokenLite ('AdminModules')
			),
			'back' => array(
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='
					.Tools::getAdminTokenLite ('AdminModules'),
				'desc' => $this->l('Back to list') )
		);
		$id_spcustomhtml = (int)Tools::getValue ('id_spcustomhtml');

		if (Tools::isSubmit ('id_spcustomhtml') && $id_spcustomhtml)
		{
			$customhtml = new SpCustomHtmlClass((int)$id_spcustomhtml);
			$params = unserialize($customhtml->params);
			$this->fields_form[0]['form']['input'][] = array(
				'type' => 'hidden',
				'name' => 'id_spcustomhtml' );
		$helper->fields_value['id_spcustomhtml'] = Tools::getValue ('id_spcustomhtml', $customhtml->id_spcustomhtml);
		}
		else
		{
			$customhtml = new SpCustomHtmlClass();
			$params = array();
		}
		foreach (Language::getLanguages (false) as $lang)
		{
			$helper->fields_value['title_module'][(int)$lang['id_lang']] = Tools::getValue ('title_module_'
				.(int)$lang['id_lang'],
				$customhtml->title_module[(int)$lang['id_lang']]);
			$helper->fields_value['content'][(int)$lang['id_lang']] = Tools::getValue ('content_'.(int)$lang['id_lang'],
				$customhtml->content[(int)$lang['id_lang']]);
		}
		$helper->fields_value['hook'] = Tools::getValue ('hook', $customhtml->hook);
		$helper->fields_value['active'] = (int)Tools::getValue('active', $customhtml->active);
		$display_title_module = isset( $params['display_title_module'] ) ? $params['display_title_module'] : 1;
		$helper->fields_value['display_title_module'] = Tools::getValue ('display_title_module', $display_title_module);
		$helper->fields_value['moduleclass_sfx'] = Tools::getValue ('moduleclass_sfx',
			isset($params['moduleclass_sfx']) ? $params['moduleclass_sfx'] : '' );
		$this->html .= $helper->generateForm ($this->fields_form);
	}

	private function getItemInHook($hook_name)
	{
		
		$list = array();
		$this->context = Context::getContext ();
		$id_shop = $this->context->shop->id;
		$id_lang = $this->context->language->id;
		$id_hook = Hook::getIdByName ($hook_name);
		if ($id_hook)
		{
			$sql = 'SELECT * FROM `'._DB_PREFIX_.'spcustomhtml` b
			LEFT JOIN `'._DB_PREFIX_.'spcustomhtml_shop` bs ON (b.`id_spcustomhtml` = bs.`id_spcustomhtml`)
			LEFT JOIN `'._DB_PREFIX_.'spcustomhtml_lang` bl ON (b.`id_spcustomhtml` = bl.`id_spcustomhtml`)
			WHERE bs.`active` = 1 AND (bs.`id_shop` = '.$id_shop.')
			AND (bl.`id_lang` = '.$id_lang.')
			AND b.`hook` = '.( $id_hook ).' ORDER BY b.`ordering`';
			$results = Db::getInstance ()->ExecuteS ($sql);
			foreach ($results as &$row)
			{
				$row['params'] = unserialize($row['params']);
			}
		}
		return $results;
	}

	public function hookHeader()
	{
		//$this->context->controller->addCSS ($this->_path.'views/css/style.css', 'all');
	}

	public function renderWidget($hookName = null, array $configuration = [])
    {

			$variables = $this->getWidgetVariables($hookName, $configuration);
			$this->smarty->assign($variables);
        return $this->fetch($this->templateFile);
    }
	
	
	 public function getWidgetVariables($hookName = null, array $configuration = [])
    {
		
		$list = $this->getItemInHook ($hookName);
		if (!empty($list)){
			return array(
				'htmllist' => $list,
				'hookName' => $hookName
			);
		}else{
			return array(
				'htmllist' => '',
				'hookName' => $hookName
			);
		}
    }


	public function headerHTML()
	{
		
		if (Tools::getValue ('controller') != 'AdminModules' && Tools::getValue ('configure') != $this->name)
			return;
		$this->context->controller->addJqueryUI ('ui.sortable');
		$html = '<script type="text/javascript">
			$(function() {
				var $gird_items = $("#gird_items");
				$gird_items.sortable({
					opacity: 0.6,
					cursor: "move",
					handle: ".dragGroup",
					update: function() {
						var order = $(this).sortable("serialize") + "&action=updateSlidesPosition";
							$.ajax({
								type: "POST",
								dataType: "json",
								data:order,
								url:"'._PS_BASE_URL_.__PS_BASE_URI__.'modules/'.$this->name.'/ajax_'.$this->name
			.'.php?secure_key='.$this->secure_key.'",
								success: function (msg){
									if (msg.error)
									{
										showErrorMessage(msg.error);
										return;
									}
									$(".positions", $gird_items).each(function(i){
										$(this).text(i);
									});
									showSuccessMessage(msg.success);
								}
							});
						
						}
					});
					$(".dragGroup",$gird_items).hover(function() {
						$(this).css("cursor","move");
					},
					function() {
						$(this).css("cursor","auto");
				    });
			});
		</script>
		';
		$html .= '<style type="text/css">#gird_items .ui-sortable-helper{display:table!important;}</style>';
		return $html;
	}
	private function getWarningMultishopHtml()
	{
		if (Shop::getContext() == Shop::CONTEXT_GROUP || Shop::getContext() == Shop::CONTEXT_ALL)
			return '<p class="alert alert-warning">'.
						$this->l('You cannot manage modules items from a "All Shops" or a "Group Shop" context,
						select directly the shop you want to edit').
					'</p>';
		else
			return '';
	}

	private function getShopContextError($shop_contextualized_name, $mode)
	{
		if (is_array($shop_contextualized_name))
			$shop_contextualized_name = implode('<br/>', $shop_contextualized_name);

		if ($mode == 'edit')
			return '<p class="alert alert-danger">'.
							sprintf($this->l('You can only edit this module from the shop(s) context: %s'),
								$shop_contextualized_name).
					'</p>';
		else
			return '<p class="alert alert-danger">'.
							sprintf($this->l('You cannot add modules from a "All Shops" or a "Group Shop" context')).
					'</p>';
	}

	private function getShopAssociationError($id_customhtml)
	{
		return '<p class="alert alert-danger">'.
			sprintf($this->l('Unable to get module shop association information (id_module: %d)'), (int)$id_customhtml).
				'</p>';
	}


	private function getCurrentShopInfoMsg()
	{
		$shop_info = null;

		if (Shop::isFeatureActive())
		{
			if (Shop::getContext() == Shop::CONTEXT_SHOP)
			$shop_info = sprintf($this->l('The modifications will be applied to shop: %s'), $this->context->shop->name);
			else if (Shop::getContext() == Shop::CONTEXT_GROUP)
				$shop_info = sprintf($this->l('The modifications will be applied to this group: %s'),
					Shop::getContextShopGroup()->name);
			else
				$shop_info = $this->l('The modifications will be applied to all shops and shop groups');

			return '<div class="alert alert-info">'.
						$shop_info.
					'</div>';
		}
		else
			return '';
	}
	private function getSharedSlideWarning()
	{
		return '<p class="alert alert-warning">'.
					$this->l('This module is shared with other shops!
					All shops associated to this module will apply modifications made here').
				'</p>';
	}

	public function hookActionShopDataDuplication($params)
	{
		Db::getInstance ()->execute ('
		INSERT IGNORE INTO `'._DB_PREFIX_.'spcustomhtml_shop` (`id_spcustomhtml`, `id_shop`)
		SELECT `id_spcustomhtml`, '.(int)$params['new_id_shop'].'
		FROM `'._DB_PREFIX_.'spcustomhtml_shop`
		WHERE `id_shop` = '.(int)$params['old_id_shop']);
	}
}
