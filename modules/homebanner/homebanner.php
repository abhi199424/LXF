<?php
/**
* 2007-2015 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author    PrestaShop SA <contact@prestashop.com>
*  @copyright 2007-2015 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

include(dirname(__FILE__).'/classes/HomeBannersection.php');
class Homebanner extends Module
{
    private $templateFile;
    private $templateFile2;
    protected $config_form = false;

    public $tab_array = array(
        array(
            'class_name' => 'HomeBanner',
            'name' => 'Home banner',
        ),
    );

    public function __construct()
    {
        $this->name = 'homebanner';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'DevAbhi';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Home page sections');
        $this->description = $this->l('home page content creator');

        $this->confirmUninstall = $this->l('Are you sure you want to uninstall my module?');

        $this->templateFile = 'module:homebanner/views/templates/hook/home_banner.tpl';


        Shop::addTableAssociation('homebanner', array('type' => 'shop'));
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        include(dirname(__FILE__).'/sql/install.php');
        $this->_clearCache('*');

        foreach ($this->tab_array as $tab) {
            $new_tab = new Tab();
            $new_tab->class_name = $tab['class_name'];
            $new_tab->id_parent = 38;
            $new_tab->active = 1;
            $new_tab->module = $this->name;
            $languages = Language::getLanguages();
            foreach ($languages as $language) {
                $new_tab->name[$language['id_lang']] = $tab['name'];
            }

            $new_tab->add();
        }

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayHomeBanner') &&
            $this->registerHook('actionShopDataDuplication') &&
            $this->registerHook('actionObjectHomeBannersectionDeleteAfter') &&
            $this->registerHook('actionObjectHomeBannersectionAddAfter') &&
            $this->registerHook('actionObjectHomeBannersectionUpdateAfter');
    }

    public function uninstall()
    {
        include(dirname(__FILE__).'/sql/uninstall.php');
        $this->_clearCache('*');
        
        $tabb = new Tab((int)Tab::getIdFromClassName('HomeBanner'));
 		$tabb->delete();

        $tabHome = new Tab((int)Tab::getIdFromClassName('HomeLink'));
 		$tabHome->delete();

        return parent::uninstall();
    }
    
    public function hookDisplayHomeBanner()
    {   
        $sql = 'SELECT p.*, pl.*
            FROM '._DB_PREFIX_.'homebanner p
            LEFT JOIN '._DB_PREFIX_.'homebanner_shop ps ON(p.id_homebanner = ps.id_homebanner)
            LEFT JOIN '._DB_PREFIX_.'homebanner_lang pl ON(p.id_homebanner = pl.id_homebanner AND ps.id_shop = pl.id_shop)
            WHERE ps.active = 1 AND pl.id_lang = '.$this->context->language->id.' AND ps.id_shop = '.$this->context->shop->id;

        $result = Db::getInstance()->executeS($sql);
        

        $this->smarty->assign([
                'home_banners' => $result,
                'banner_img_dir' => _PS_IMG_DIR_
            ]
        );
        return $this->fetch($this->templateFile);

    }
    
    public function hookActionObjectHomeBannersectionUpdateAfter()
	{
		$this->_clearCache('*');	
	}

	public function hookActionObjectHomeBannersectionAddAfter()
	{
		$this->_clearCache('*');
	}

	public function hookActionObjectHomeBannersectionDeleteAfter()
	{
		$this->_clearCache('*');
	}

    protected function _clearCache($template, $cacheId = null, $compileId = null)
	{
		$cache_id_1 = 'home_banner_mobile-'.$this->name;
        $cache_id_2 = 'home_banner_desk-'.$this->name;
		parent::_clearCache($this->templateFile,$cache_id_1);	
		parent::_clearCache($this->templateFile,$cache_id_2);

        $cache_id_1 = 'home_link_mobile-'.$this->name;
        $cache_id_2 = 'home_link_desk-'.$this->name;
		parent::_clearCache($this->templateFile2,$cache_id_1);	
		parent::_clearCache($this->templateFile2,$cache_id_2);
	}

    public function hookActionShopDataDuplication($params)
	{
		Db::getInstance()->execute('
			INSERT IGNORE INTO '._DB_PREFIX_.'homebanner_shop (id_homebanner, id_shop, active)
			SELECT id_homebanner, '.(int)$params['new_id_shop'].', active
			FROM '._DB_PREFIX_.'homebanner_shop
			WHERE id_shop = '.(int)$params['old_id_shop']
		);
	}

    public function hookHeader()
    {
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }
}
