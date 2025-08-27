<?php
/**
 * package SP Filter Products
 *
 * @version 1.0.0
 * @author    MagenTech http://www.magentech.com
 * @copyright (c) 2018 YouTech Company. All Rights Reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Adapter\Category\CategoryProductSearchProvider;
use PrestaShop\PrestaShop\Adapter\Image\ImageRetriever;
use PrestaShop\PrestaShop\Adapter\Product\PriceFormatter;
use PrestaShop\PrestaShop\Core\Product\ProductListingPresenter;
use PrestaShop\PrestaShop\Adapter\Product\ProductColorsRetriever;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchContext;
use PrestaShop\PrestaShop\Core\Product\Search\ProductSearchQuery;
use PrestaShop\PrestaShop\Core\Product\Search\SortOrder;

include_once _PS_MODULE_DIR_.'spfilterproducts/SpFilterProductsClass.php';

class SpFilterProducts extends Module implements WidgetInterface
{
    private $_html = '';
    private $templateFile;
    private $defaultHook = [
        'displayHome',
        'displayTop',
        'displayLeftColumn',
        'displayRightColumn',
        'displayFooter',
		'displayFilterproducts1',
		'displayFilterproducts2',
		'displayFilterproducts3',
		'displayFilterproducts4',
		'displayFilterproducts5',
		'displayFilterproducts6',
		'displayFilterproducts7',
		'displayFilterproducts8',
		'displayFilterproducts9',
		'displayFilterproducts10'
    ];

    /**
     * SpFilterProducts constructor.
     */
    public function __construct()
    {
        $this->name = 'spfilterproducts';
        $this->author = 'MagenTech';
        $this->version = '1.0.0';
        $this->need_instance = 0;
				$this->context = Context::getContext();
        $this->ps_versions_compliancy = [
            'min' => '1.7.1.0',
            'max' => _PS_VERSION_,
        ];

        $this->bootstrap = true;

        parent::__construct();
        $this->displayName = $this->l('Sp Filter Products');
        $this->description = $this->l('Sp Filter Products Module');

        $this->templateFile = 'module:spfilterproducts/views/templates/hook/spfilterproducts.tpl';
		$this->templateFile1 = 'module:spfilterproducts/views/templates/hook/spbestsellproducts.tpl';
    }

    /**
     * @return bool
     */
    public function install()
    {
        if (parent::install() == false
            || !$this->registerHook('displayHeader')
            || !$this->registerHook('displayBackOfficeHeader')
            || !$this->registerHook('actionShopDataDuplication')
            || !$this->registerHook('addproduct')
            || !$this->registerHook('updateproduct')
            || !$this->registerHook('deleteproduct')
            || !$this->registerHook('categoryUpdate')
			|| !$this->registerHook('displayProductCountDown')
            || !$this->registerHook('actionAdminGroupsControllerSaveAfter')
        ) {
            return false;
        }
        foreach ($this->defaultHook as $hook) {
            if (!$this->registerHook ($hook))
                return false;
        }
        $this->createTables();
        $this->installFixtures();
        return true;
    }

    /**
     * @return bool
     */
    public function uninstall()
    {
        $this->_clearCache('*');

        if (parent::uninstall()) {
            $res = $this->deleteTables();
            return (bool)$res;
        }

        return false;
    }

    /**
     * Creates tables
     */
    protected function createTables()
    {
        $res =  (bool)Db::getInstance ()->execute ('DROP TABLE IF EXISTS `'._DB_PREFIX_.'spfilterproducts`')
            && Db::getInstance ()->execute ('
			CREATE TABLE '._DB_PREFIX_.'spfilterproducts (
				`id_spfilterproducts` int(10) unsigned NOT NULL AUTO_INCREMENT,
				`hook` int(10) unsigned,
				`params` text NOT NULL DEFAULT \'\' ,
				`active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
				`position` int(10) unsigned NOT NULL DEFAULT \'0\',
				PRIMARY KEY (`id_spfilterproducts`)) ENGINE=InnoDB default CHARSET=utf8');
        $res &= Db::getInstance ()->Execute ('DROP TABLE IF EXISTS `'._DB_PREFIX_.'spfilterproducts_shop`')
            && Db::getInstance ()->Execute ('
				CREATE TABLE '._DB_PREFIX_.'spfilterproducts_shop (
				`id_spfilterproducts` int(10) unsigned NOT NULL,
				`id_shop` int(10) unsigned NOT NULL,
				`active` tinyint(1) unsigned NOT NULL DEFAULT \'0\',
				 PRIMARY KEY (`id_spfilterproducts`,`id_shop`)) ENGINE=InnoDB default CHARSET=utf8');
        $res &= Db::getInstance ()->Execute ('DROP TABLE IF EXISTS `'._DB_PREFIX_.'spfilterproducts_lang`')
            && Db::getInstance ()->Execute ('CREATE TABLE '._DB_PREFIX_.'spfilterproducts_lang (
				`id_spfilterproducts` int(10) unsigned NOT NULL,
				`id_lang` int(10) unsigned NOT NULL,
				`title_module` varchar(255) NOT NULL DEFAULT \'\',
				PRIMARY KEY (`id_spfilterproducts`,`id_lang`)) ENGINE=InnoDB default CHARSET=utf8');
        return $res;
    }

    /**
     * @return bool
     */
    protected function deleteTables()
    {
        return Db::getInstance()->execute('
            DROP TABLE IF EXISTS `'._DB_PREFIX_.'spfilterproducts`, `'._DB_PREFIX_.'spfilterproducts_shop`, `'._DB_PREFIX_.'spfilterproducts_lang`;
        ');
    }

    /**
     * @return bool
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function installFixtures()
    {
		$lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $datas = [
			[
				'SPFP_CATIDS' => 'all',
				'SPFP_TITLE_MODULE' => 'Best Sellers',
				'SPFP_DISPLAY_TITLE' => 1,
				'SPFP_CLS_SFX' => 'style_lol',
				'SPFP_STATUS' => 1,
				'SPFP_HOOK_INTO' => Hook::getIdByName('displayFilterproducts1'),
				'SPFP_TYPE_SHOW' => 2,
				'SPFP_ROW' => 2,
				'SPFP_COLUMN1' => 4,
				'SPFP_COLUMN2' => 4,
				'SPFP_COLUMN3' => 3,
				'SPFP_COLUMN4' => 3,
				'SPFP_AUTOPLAY' => 0,
				'SPFP_COUNTDOWN_TIMER' => 0,
				'SPFP_SELECT_SOURCE' => 'lastest_products',
				'SPFP_FEATURED_RANDOMIZE' => 0,
				'SPFP_DATE_TO' => '',
				'SPFP_CATIDS' => 'all',
				'SPFP_ORDERBY' => 'id',
				'SPFP_DIRECTION' => 'DESC',
				'SPFP_LIMIT' => 16,
				'SPFP_CENTER' => 0,
				'SPFP_NAV' => 1,
				'SPFP_DOTS' => 0,
				'SPFP_MARGIN' => 10
            ],
			[
				'SPFP_CATIDS' => 'all',
				'SPFP_TITLE_MODULE' => 'Weekly Deals',
				'SPFP_DISPLAY_TITLE' => 1,
				'SPFP_CLS_SFX' => 'deals',
				'SPFP_STATUS' => 1,
				'SPFP_HOOK_INTO' => Hook::getIdByName('displayFilterproducts2'),
				'SPFP_TYPE_SHOW' => 2,
				'SPFP_ROW' => 1,
				'SPFP_COLUMN1' => 1,
				'SPFP_COLUMN2' => 1,
				'SPFP_COLUMN3' => 1,
				'SPFP_COLUMN4' => 1,
				'SPFP_AUTOPLAY' => 0,
				'SPFP_COUNTDOWN_TIMER' => 1,
				'SPFP_SELECT_SOURCE' => 'countdown_products',
				'SPFP_FEATURED_RANDOMIZE' => 0,
				'SPFP_DATE_TO' => '31-12-2021',
				'SPFP_CATIDS' => 'all',
				'SPFP_ORDERBY' => 'id',
				'SPFP_DIRECTION' => 'DESC',
				'SPFP_LIMIT' => 8,
				'SPFP_CENTER' => 0,
				'SPFP_NAV' => 1,
				'SPFP_DOTS' => 0,
				'SPFP_MARGIN' => 30
            ],
			[
				'SPFP_CATIDS' => 'all',
				'SPFP_TITLE_MODULE' => 'Featured Products',
				'SPFP_DISPLAY_TITLE' => 1,
				'SPFP_CLS_SFX' => 'special',
				'SPFP_STATUS' => 1,
				'SPFP_HOOK_INTO' => Hook::getIdByName('displayFilterproducts3'),
				'SPFP_TYPE_SHOW' => 2,
				'SPFP_ROW' => 1,
				'SPFP_COLUMN1' => 3,
				'SPFP_COLUMN2' => 3,
				'SPFP_COLUMN3' => 4,
				'SPFP_COLUMN4' => 2,
				'SPFP_AUTOPLAY' => 0,
				'SPFP_COUNTDOWN_TIMER' => 0,
				'SPFP_SELECT_SOURCE' => 'lastest_products',
				'SPFP_FEATURED_RANDOMIZE' => 0,
				'SPFP_DATE_TO' => '',
				'SPFP_CATIDS' => 'all',
				'SPFP_ORDERBY' => 'name',
				'SPFP_DIRECTION' => 'DESC',
				'SPFP_LIMIT' => 4,
				'SPFP_CENTER' => 1,
				'SPFP_NAV' => 0,
				'SPFP_DOTS' => 0,
				'SPFP_MARGIN' => 10
            ],
			[
				'SPFP_CATIDS' => 'all',
				'SPFP_TITLE_MODULE' => 'Most Viewed Products',
				'SPFP_DISPLAY_TITLE' => 1,
				'SPFP_CLS_SFX' => 'style_lol',
				'SPFP_STATUS' => 1,
				'SPFP_HOOK_INTO' => Hook::getIdByName('displayFilterproducts4'),
				'SPFP_TYPE_SHOW' => 2,
				'SPFP_ROW' => 1,
				'SPFP_COLUMN1' => 5,
				'SPFP_COLUMN2' => 5,
				'SPFP_COLUMN3' => 4,
				'SPFP_COLUMN4' => 3,
				'SPFP_AUTOPLAY' => 0,
				'SPFP_COUNTDOWN_TIMER' => 0,
				'SPFP_SELECT_SOURCE' => 'featured_products',
				'SPFP_FEATURED_RANDOMIZE' => 0,
				'SPFP_DATE_TO' => '',
				'SPFP_CATIDS' => 'all',
				'SPFP_ORDERBY' => 'id',
				'SPFP_DIRECTION' => 'DESC',
				'SPFP_LIMIT' => 8,
				'SPFP_CENTER' => 0,
				'SPFP_NAV' => 1,
				'SPFP_DOTS' => 0,
				'SPFP_MARGIN' => 10
            ],
			[
				'SPFP_CATIDS' => 'all',
				'SPFP_TITLE_MODULE' => 'New Arrivals',
				'SPFP_DISPLAY_TITLE' => 1,
				'SPFP_CLS_SFX' => 'style_lol_2',
				'SPFP_STATUS' => 1,
				'SPFP_HOOK_INTO' => Hook::getIdByName('displayFilterproducts5'),
				'SPFP_TYPE_SHOW' => 2,
				'SPFP_ROW' => 1,
				'SPFP_COLUMN1' => 6,
				'SPFP_COLUMN2' => 5,
				'SPFP_COLUMN3' => 4,
				'SPFP_COLUMN4' => 3,
				'SPFP_AUTOPLAY' => 0,
				'SPFP_COUNTDOWN_TIMER' => 0,
				'SPFP_SELECT_SOURCE' => 'lastest_products',
				'SPFP_FEATURED_RANDOMIZE' => 0,
				'SPFP_DATE_TO' => '',
				'SPFP_CATIDS' => 'all',
				'SPFP_ORDERBY' => 'id',
				'SPFP_DIRECTION' => 'DESC',
				'SPFP_LIMIT' => 16,
				'SPFP_CENTER' => 0,
				'SPFP_NAV' => 1,
				'SPFP_DOTS' => 0,
				'SPFP_MARGIN' => 10
            ],
			[
				'SPFP_CATIDS' => 'all',
				'SPFP_TITLE_MODULE' => 'Most View Products',
				'SPFP_DISPLAY_TITLE' => 1,
				'SPFP_CLS_SFX' => 'style_lol_2',
				'SPFP_STATUS' => 1,
				'SPFP_HOOK_INTO' => Hook::getIdByName('displayFilterproducts6'),
				'SPFP_TYPE_SHOW' => 2,
				'SPFP_ROW' => 1,
				'SPFP_COLUMN1' => 6,
				'SPFP_COLUMN2' => 5,
				'SPFP_COLUMN3' => 4,
				'SPFP_COLUMN4' => 3,
				'SPFP_AUTOPLAY' => 0,
				'SPFP_COUNTDOWN_TIMER' => 0,
				'SPFP_SELECT_SOURCE' => 'featured_products',
				'SPFP_FEATURED_RANDOMIZE' => 0,
				'SPFP_DATE_TO' => '',
				'SPFP_CATIDS' => 'all',
				'SPFP_ORDERBY' => 'id',
				'SPFP_DIRECTION' => 'DESC',
				'SPFP_LIMIT' => 16,
				'SPFP_CENTER' => 0,
				'SPFP_NAV' => 1,
				'SPFP_DOTS' => 0,
				'SPFP_MARGIN' => 10
            ],
			[
				'SPFP_CATIDS' => 'all',
				'SPFP_TITLE_MODULE' => 'Sale Products',
				'SPFP_DISPLAY_TITLE' => 1,
				'SPFP_CLS_SFX' => 'filter_left',
				'SPFP_STATUS' => 1,
				'SPFP_HOOK_INTO' => Hook::getIdByName('displayFilterproducts7'),
				'SPFP_TYPE_SHOW' => 2,
				'SPFP_ROW' => 2,
				'SPFP_COLUMN1' => 6,
				'SPFP_COLUMN2' => 4,
				'SPFP_COLUMN3' => 3,
				'SPFP_COLUMN4' => 2,
				'SPFP_AUTOPLAY' => 0,
				'SPFP_COUNTDOWN_TIMER' => 0,
				'SPFP_SELECT_SOURCE' => 'lastest_products',
				'SPFP_FEATURED_RANDOMIZE' => 0,
				'SPFP_DATE_TO' => '',
				'SPFP_CATIDS' => 'all',
				'SPFP_ORDERBY' => 'id',
				'SPFP_DIRECTION' => 'DESC',
				'SPFP_LIMIT' => 16,
				'SPFP_CENTER' => 0,
				'SPFP_NAV' => 1,
				'SPFP_DOTS' => 0,
				'SPFP_MARGIN' => 10
            ],
			[
				'SPFP_CATIDS' => 'all',
				'SPFP_TITLE_MODULE' => 'Weekly Deals New',
				'SPFP_DISPLAY_TITLE' => 1,
				'SPFP_CLS_SFX' => 'filter_left',
				'SPFP_STATUS' => 1,
				'SPFP_HOOK_INTO' => Hook::getIdByName('displayFilterproducts8'),
				'SPFP_TYPE_SHOW' => 2,
				'SPFP_ROW' => 2,
				'SPFP_COLUMN1' => 6,
				'SPFP_COLUMN2' => 4,
				'SPFP_COLUMN3' => 3,
				'SPFP_COLUMN4' => 2,
				'SPFP_AUTOPLAY' => 0,
				'SPFP_COUNTDOWN_TIMER' => 0,
				'SPFP_SELECT_SOURCE' => 'weekly_deals_new',
				'SPFP_FEATURED_RANDOMIZE' => 0,
				'SPFP_DATE_TO' => '',
				'SPFP_CATIDS' => 'all',
				'SPFP_ORDERBY' => 'id',
				'SPFP_DIRECTION' => 'DESC',
				'SPFP_LIMIT' => 16,
				'SPFP_CENTER' => 0,
				'SPFP_NAV' => 1,
				'SPFP_DOTS' => 0,
				'SPFP_MARGIN' => 10
            ],
			[
				'SPFP_CATIDS' => 'all',
				'SPFP_TITLE_MODULE' => 'Deals',
				'SPFP_DISPLAY_TITLE' => 1,
				'SPFP_CLS_SFX' => 'filter_left_9',
				'SPFP_STATUS' => 1,
				'SPFP_HOOK_INTO' => Hook::getIdByName('displayFilterproducts9'),
				'SPFP_TYPE_SHOW' => 2,
				'SPFP_ROW' => 2,
				'SPFP_COLUMN1' => 6,
				'SPFP_COLUMN2' => 4,
				'SPFP_COLUMN3' => 3,
				'SPFP_COLUMN4' => 2,
				'SPFP_AUTOPLAY' => 0,
				'SPFP_COUNTDOWN_TIMER' => 0,
				'SPFP_SELECT_SOURCE' => 'other_products',
				'SPFP_FEATURED_RANDOMIZE' => 0,
				'SPFP_DATE_TO' => '',
				'SPFP_CATIDS' => 'all',
				'SPFP_ORDERBY' => 'id',
				'SPFP_DIRECTION' => 'DESC',
				'SPFP_LIMIT' => 16,
				'SPFP_CENTER' => 0,
				'SPFP_NAV' => 1,
				'SPFP_DOTS' => 0,
				'SPFP_MARGIN' => 10
            ],
			[
				'SPFP_CATIDS' => 'all',
				'SPFP_TITLE_MODULE' => 'Deals d10',
				'SPFP_DISPLAY_TITLE' => 1,
				'SPFP_CLS_SFX' => 'filter_left_10',
				'SPFP_STATUS' => 1,
				'SPFP_HOOK_INTO' => Hook::getIdByName('displayFilterproducts10'),
				'SPFP_TYPE_SHOW' => 2,
				'SPFP_ROW' => 2,
				'SPFP_COLUMN1' => 6,
				'SPFP_COLUMN2' => 4,
				'SPFP_COLUMN3' => 3,
				'SPFP_COLUMN4' => 2,
				'SPFP_AUTOPLAY' => 0,
				'SPFP_COUNTDOWN_TIMER' => 0,
				'SPFP_SELECT_SOURCE' => 'other_products',
				'SPFP_FEATURED_RANDOMIZE' => 0,
				'SPFP_DATE_TO' => '',
				'SPFP_CATIDS' => 'all',
				'SPFP_ORDERBY' => 'id',
				'SPFP_DIRECTION' => 'DESC',
				'SPFP_LIMIT' => 16,
				'SPFP_CENTER' => 0,
				'SPFP_NAV' => 1,
				'SPFP_DOTS' => 0,
				'SPFP_MARGIN' => 10
            ],
        ];

        $return = true;
				$categories = Category::getSimpleCategories($lang->id);
        foreach ($datas as $i => $data)
        {
			if ($data['SPFP_CATIDS'] == 'all'){
				$catids = [];
				 foreach($categories as $cat){
					$catids[] = $cat['id_category'];
				}
				$catids = array_slice($catids, 0, 5);
				$catids = ( is_array ($catids) && !empty( $catids ) ) ? implode (',', $catids): ''; 
				$data['SPFP_CATIDS'] = $catids;
			}
            $spfilterproducts = new SpFilterProductsClass();
            $spfilterproducts->hook = $data['SPFP_HOOK_INTO'];
            $spfilterproducts->active = $data['SPFP_STATUS'];
            $spfilterproducts->position = $i+1;
            $spfilterproducts->params = serialize($data);
            foreach (Language::getLanguages(false) as $lang)
                $spfilterproducts->title_module[$lang['id_lang']] = $data['SPFP_TITLE_MODULE'];
            $return &= $spfilterproducts->add();
        }
        return $return;
    }

    /**
     * @return string
     */
    public function renderList()
    {
        $modules = SpFilterProductsClass::getGridModules($this->context->language->id, $this->context->shop->id);
        foreach ($modules as $key => $module) {
			$name_hook = $this->getHookTitle ($module['hook']);
			$modules[$key]['hook_name'] = $name_hook;
            $associated_shop_ids = SpFilterProductsClass::getAssociatedIdsShop((int)$module['id_spfilterproducts']);
            if ($associated_shop_ids && count($associated_shop_ids) > 1) {
                $modules[$key]['is_shared'] = true;
            } else {
                $modules[$key]['is_shared'] = false;
            }
        }

        $this->context->smarty->assign(
            array(
                'link' => $this->context->link,
                'modules' => $modules,
            )
        );

        return $this->display(__FILE__, 'views/templates/admin/grid_modules.tpl');
    }

    /**
     *
     */
    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('controller') == 'AdminModules' && Tools::getValue('configure') == $this->name)
        {
            $this->context->controller->addJquery();
            $this->context->controller->addJqueryUI('ui.sortable');
            $this->context->controller->addJS($this->_path.'views/js/admin/spfp.js');
			$this->context->controller->addCSS($this->_path.'views/css/admin/spfp.css');
        }
    }

    /**
     * @param $params
     */
    public function hookAddProduct($params)
    {
        $this->_clearCache('*');
    }

    /**
     * @param $params
     */
    public function hookUpdateProduct($params)
    {
        $this->_clearCache('*');
    }

    /**
     * @param $params
     */
    public function hookDeleteProduct($params)
    {
        $this->_clearCache('*');
    }

    /**
     * @param $params
     */
    public function hookCategoryUpdate($params)
    {
        $this->_clearCache('*');
    }

    /**
     * @param $params
     */
    public function hookActionAdminGroupsControllerSaveAfter($params)
    {
        $this->_clearCache('*');
    }

    /**
     * @param string $template
     * @param null $cache_id
     * @param null $compile_id
     * @return int|void
     */
    public function _clearCache($template, $cache_id = null, $compile_id = null)
    {
        parent::_clearCache($this->templateFile);
    }

    /**
     * @return bool
     */
	private function postValidation()
	{
		$errors = array();
		if (Tools::isSubmit ('saveItem') || Tools::isSubmit ('saveAndStay'))
		{
			if (!Validate::isInt(Tools::getValue('active')) || (Tools::getValue('active') != 0
					&& Tools::getValue('active') != 1))
				$errors[] = $this->l('Invalid module state.');

			if (!Validate::isInt(Tools::getValue('SPFP_LIMIT')) || floor (Tools::getValue('SPFP_LIMIT')) < 0)
				$errors[] = $this->l('Invalid Count Number.');

			if (Tools::isSubmit('id_spfilterproducts'))
			{
				if (!Validate::isInt(Tools::getValue('id_spfilterproducts'))
					&& !$this->moduleExists(Tools::getValue('id_spfilterproducts')))
					$errors[] = $this->l('Invalid module ID');
			}
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
			{
				if (Tools::strlen(Tools::getValue('SPFP_TITLE_MODULE_'.$language['id_lang'])) > 255)
					$errors[] = $this->l('The title is too long.');
			}
			$id_lang_default = (int)Configuration::get('PS_LANG_DEFAULT');
			if (Tools::strlen(Tools::getValue('SPFP_TITLE_MODULE_'.$id_lang_default)) == 0)
				$errors[] = $this->l('The title module is not set.');
			
		}
		elseif (Tools::isSubmit('id_spfilterproducts') && (!Validate::isInt(Tools::getValue('id_spfilterproducts'))
				|| !$this->moduleExists((int)Tools::getValue('id_spfilterproducts'))))
			$errors[] = $this->l('Invalid module ID');

		if (count($errors))
		{
			$this->_html .= $this->displayError(implode('<br />', $errors));

			return false;
		}
		return true;
	}
    /**
     * @return string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function getContent()
    {
		if (Tools::isSubmit ('saveItem') || Tools::isSubmit ('saveAndStay') )
		{
			if ($this->postValidation())
			{
				$this->_html .= $this->postProcess();	
				$this->_html .= $this->renderList();
				$this->_html .= $this->getFormConfig();
			}
			else
				$this->_html .= $this->getFormConfig();
		}elseif (Tools::isSubmit ('updateItemConfirmationRedirect')){
			$this->_html .= $this->displayConfirmation ($this->l('Module successfully updated!'));
			$this->_html .= $this->renderList();	
		}elseif (Tools::isSubmit ('updateItemConfirmation')){
			$this->_html .= $this->displayConfirmation ($this->l('Module successfully updated!'));
			$this->_html .= $this->getFormConfig();		
		}elseif (Tools::isSubmit ('statusConfirmation')){
			$this->_html = $this->displayConfirmation($this->l('Module successfully change status!'));
			$this->_html .= $this->renderList();
		}
		elseif (Tools::isSubmit ('duplicateItemConfirmation')){
			$this->_html = $this->displayConfirmation($this->l('Module successfully duplicated!'));
			$this->_html .= $this->renderList();
		}
		elseif (Tools::isSubmit ('deleteItemConfirmation')){
			$this->_html = $this->displayConfirmation($this->l('Module successfully deleted!'));
			$this->_html .= $this->renderList();
		}
		elseif (Tools::isSubmit ('saveItemConfirmation')){
			$this->_html = $this->displayConfirmation ($this->l('Module created successfully!'));
			$this->_html .= $this->renderList();
		}
		elseif (Tools::isSubmit ('addModule') || (Tools::isSubmit('editModule')
				&& $this->moduleExists((int)Tools::getValue('id_spfilterproducts'))) || Tools::isSubmit ('saveItem'))
		{
			if (Tools::isSubmit('addModule'))
				$mode = 'add';
			else
				$mode = 'edit';
			if ($mode == 'add')
			{
				if (Shop::getContext() != Shop::CONTEXT_GROUP && Shop::getContext() != Shop::CONTEXT_ALL)
					$this->_html .= $this->getFormConfig ();
				else
					$this->_html .= $this->getShopContextError(null, $mode);
			}
			else
			{
				$associated_shop_ids = SpFilterProductsClass::getAssociatedIdsShop((int)Tools::getValue('id_spfilterproducts'));
				$context_shop_id = (int)Shop::getContextShopID();
				if ($associated_shop_ids === false)
					$this->_html .= $this->getShopAssociationError((int)Tools::getValue('id_spfilterproducts'));
				else if (Shop::getContext() != Shop::CONTEXT_GROUP && Shop::getContext() != Shop::CONTEXT_ALL
					&& in_array($context_shop_id, $associated_shop_ids))
				{
					if (count($associated_shop_ids) > 1)
						$this->_html = $this->getSharedSlideWarning();
					$this->_html .= $this->getFormConfig();
				}
				else
				{
					$shops_name_list = array();
					foreach ($associated_shop_ids as $shop_id)
					{
						$associated_shop = new Shop((int)$shop_id);
						$shops_name_list[] = $associated_shop->name;
					}
					$this->_html .= $this->getShopContextError($shops_name_list, $mode);
				}
			}
		}
		else
		{
			if ($this->postValidation())
			{
				$this->_html .= $this->postProcess();
				$this->_html .= $this->renderList();
			}
			else
				$this->_html .= $this->getFormConfig();
		}
		return $this->_html;
	}
	
	

	private function postProcess()
	{		
        $currentIndex = AdminController::$currentIndex;
        $output = '';
        $errors = array();
        if(Tools::isSubmit ('duplicateModule') && Tools::getValue ('id_spfilterproducts')) {
            $spfilterproducts = new SpFilterProductsClass(Tools::getValue('id_spfilterproducts'));
            foreach (Language::getLanguages(false) as $lang)
                $spfilterproducts->title_module[(int)$lang['id_lang']] = $spfilterproducts->title_module[(int)$lang['id_lang']] . $this->l(' (Copy)');
            $spfilterproducts->duplicate();
            $this->_clearCache('*');
            Tools::redirectAdmin($currentIndex . '&configure=' . $this->name . '&token=' . Tools::getAdminTokenLite('AdminModules') . '&duplicateItemConfirmation');
        }elseif (Tools::isSubmit ('deleteModule') && Tools::getValue ('id_spfilterproducts'))
        {
            $spfilterproducts = new SpFilterProductsClass(Tools::getValue('id_spfilterproducts'));
            $spfilterproducts->delete ();
            $this->_clearCache('*');
            Tools::redirectAdmin ($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite ('AdminModules')
                .'&deleteItemConfirmation');
		}elseif (Tools::isSubmit ('statusModule') && Tools::getValue ('id_spfilterproducts'))
		{
			$spfilterproducts = new SpFilterProductsClass(Tools::getValue('id_spfilterproducts'));
			if ($spfilterproducts->active == 0)
				$spfilterproducts->active = 1;
			else
				$spfilterproducts->active = 0;
			$spfilterproducts->update();
			$this->_clearCache('*');
			Tools::redirectAdmin($currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite('AdminModules').'&statusConfirmation');
		}elseif (Tools::isSubmit ('saveItem') || Tools::isSubmit ('saveAndStay'))
		{
			if (Tools::getValue('id_spfilterproducts'))
			{
				$spfilterproducts = new SpFilterProductsClass((int)Tools::getValue ('id_spfilterproducts'));
				if (!Validate::isLoadedObject($spfilterproducts))
				{
					$this->_html = $this->displayError($this->l('Invalid module ID'));
					return false;
				}
			}
			else
				$spfilterproducts = new SpFilterProductsClass();
			$next_ps = $spfilterproducts->getHigherPosition() + 1;
			$spfilterproducts->position = (!empty($spfilterproducts->position)) ? (int)$spfilterproducts->position : $next_ps;
			$spfilterproducts->active = (int)Tools::getValue('SPFP_STATUS');
			$spfilterproducts->hook	= (int)Tools::getValue('SPFP_HOOK_INTO');
			$tmp_data = array();
			
			$tmp_data['SPFP_DISPLAY_TITLE'] = (int)Tools::getValue('SPFP_DISPLAY_TITLE');
			$tmp_data['SPFP_CLS_SFX'] = (string)Tools::getValue('SPFP_CLS_SFX');
			$tmp_data['SPFP_STATUS'] = (int)Tools::getValue('SPFP_STATUS');
			$tmp_data['SPFP_HOOK_INTO'] = (int)Tools::getValue('SPFP_HOOK_INTO');
			$tmp_data['SPFP_TYPE_SHOW'] = (int)Tools::getValue('SPFP_TYPE_SHOW');
			$tmp_data['SPFP_ROW'] = (int)Tools::getValue('SPFP_ROW');
			$tmp_data['SPFP_COLUMN1'] = (int)Tools::getValue('SPFP_COLUMN1');
			$tmp_data['SPFP_COLUMN2'] = (int)Tools::getValue('SPFP_COLUMN2');
			$tmp_data['SPFP_COLUMN3'] = (int)Tools::getValue('SPFP_COLUMN3');
			$tmp_data['SPFP_COLUMN4'] = (int)Tools::getValue('SPFP_COLUMN4');
			$tmp_data['SPFP_AUTOPLAY'] = (int)Tools::getValue('SPFP_AUTOPLAY');
			$tmp_data['SPFP_CENTER'] = (int)Tools::getValue('SPFP_CENTER');
			$tmp_data['SPFP_NAV'] = (int)Tools::getValue('SPFP_NAV');
			$tmp_data['SPFP_DOTS'] = (int)Tools::getValue('SPFP_DOTS');
			$tmp_data['SPFP_MARGIN'] = (int)Tools::getValue('SPFP_MARGIN');
			$tmp_data['SPFP_COUNTDOWN_TIMER'] = (int)Tools::getValue('SPFP_COUNTDOWN_TIMER');
			$tmp_data['SPFP_FEATURED_RANDOMIZE'] = (int)Tools::getValue('SPFP_FEATURED_RANDOMIZE');
			$tmp_data['SPFP_SELECT_SOURCE'] = (string)Tools::getValue('SPFP_SELECT_SOURCE');
			$tmp_data['SPFP_DATE_TO'] = (string)Tools::getValue('SPFP_DATE_TO');
			$catids = Tools::getValue('SPFP_CATIDS');
			$catids = ( is_array ($catids) && !empty( $catids ) ) ? implode (',', $catids): '';
			
			$tmp_data['SPFP_CATIDS'] = $catids;
			$tmp_data['SPFP_ORDERBY'] = (string)Tools::getValue('SPFP_ORDERBY');
			$tmp_data['SPFP_DIRECTION'] = (string)Tools::getValue('SPFP_DIRECTION');
			$tmp_data['SPFP_LIMIT'] = (string)Tools::getValue('SPFP_LIMIT');
			
			$languages = Language::getLanguages(false);
			foreach ($languages as $language)
				$spfilterproducts->title_module[$language['id_lang']] = Tools::getValue('SPFP_TITLE_MODULE_'.$language['id_lang']);
			$spfilterproducts->params = serialize($tmp_data);
			$get_id = Tools::getValue ('id_spfilterproducts');
			($get_id && $this->moduleExists($get_id) )? $spfilterproducts->update() : $spfilterproducts->add ();
			$this->_clearCache('*');
			if (Tools::isSubmit ('saveAndStay'))
			{
				
				$id_spfilterproducts = Tools::getValue ('id_spfilterproducts')?
					(int)Tools::getValue ('id_spfilterproducts'):(int)$spfilterproducts->getHigherModuleID ();

				Tools::redirectAdmin ($currentIndex.'&configure='
					.$this->name.'&token='.Tools::getAdminTokenLite ('AdminModules').'&editModule&id_spfilterproducts='
					.$id_spfilterproducts.'&updateItemConfirmation');
			}
			else
				Tools::redirectAdmin ($currentIndex.'&configure='
					.$this->name.'&token='.Tools::getAdminTokenLite ('AdminModules').'&updateItemConfirmationRedirect');
        }
        return $this->_html;

    }
	
	 protected function getMultiLanguageInfoMsg()
    {
        return '<p class="alert alert-warning">'.
                    $this->getTranslator()->trans('Since multiple languages are activated on your shop, please mind to upload your image for each one of them', array(), 'Modules.Filterproducts.Admin').
                '</p>';
    }

    protected function getWarningMultishopHtml()
    {
        if (Shop::getContext() == Shop::CONTEXT_GROUP || Shop::getContext() == Shop::CONTEXT_ALL) {
            return '<p class="alert alert-warning">' .
            $this->getTranslator()->trans('You cannot manage modules items from a "All Shops" or a "Group Shop" context, select directly the shop you want to edit', array(), 'Modules.Filterproducts.Admin') .
            '</p>';
        } else {
            return '';
        }
    }

    protected function getShopContextError($shop_contextualized_name, $mode)
    {
        if (is_array($shop_contextualized_name)) {
            $shop_contextualized_name = implode('<br/>', $shop_contextualized_name);
        }

        if ($mode == 'edit') {
            return '<p class="alert alert-danger">' .
            $this->trans('You can only edit this module from the shop(s) context: %s', array($shop_contextualized_name), 'Modules.Filterproducts.Admin') .
            '</p>';
        } else {
            return '<p class="alert alert-danger">' .
            $this->trans('You cannot add modules from a "All Shops" or a "Group Shop" context', array(), 'Modules.Filterproducts.Admin') .
            '</p>';
        }
    }

    protected function getShopAssociationError($id_slide)
    {
        return '<p class="alert alert-danger">'.
                        $this->trans('Unable to get slide shop association information (id_slide: %d)', array((int)$id_slide), 'Modules.Filterproducts.Admin') .
                '</p>';
    }


    protected function getCurrentShopInfoMsg()
    {
        $shop_info = null;

        if (Shop::isFeatureActive()) {
            if (Shop::getContext() == Shop::CONTEXT_SHOP) {
                $shop_info = $this->trans('The modifications will be applied to shop: %s', array($this->context->shop->name),'Modules.Filterproducts.Admin');
            } else if (Shop::getContext() == Shop::CONTEXT_GROUP) {
                $shop_info = $this->trans('The modifications will be applied to this group: %s', array(Shop::getContextShopGroup()->name), 'Modules.Filterproducts.Admin');
            } else {
                $shop_info = $this->trans('The modifications will be applied to all shops and shop groups', array(), 'Modules.Filterproducts.Admin');
            }

            return '<div class="alert alert-info">'.
                        $shop_info.
                    '</div>';
        } else {
            return '';
        }
    }

    protected function getSharedSlideWarning()
    {
        return '<p class="alert alert-warning">'.
                    $this->trans('This module is shared with other shops! All shops associated to this module will apply modifications made here', array(), 'Modules.Filterproducts.Admin').
                '</p>';
    }
	
	private function getFormConfig(){
		$template_vars = [
            'tabs' => $this->getConfigTabs(),
			'content' => $this->getConfigContent(),
            'active_tab' => 'general_setting',
        ];

        $this->context->smarty->assign($template_vars);
        return $this->display(__FILE__, 'views/templates/admin/tabs.tpl');
	}
	
	/**
     * @return array
     */
	private function getConfigTabs()
    {
        $tabs = [];
        $tabs[] = array(
            'id' => 'general_setting',
            'title' => $this->l('General Setting'),
			'data_tabs' => 'general_setting',
			'icon' => 'icon-cog',
        );
		
		$tabs[] = array(
            'id' => 'source_option',
            'title' => $this->l('Source Options'),
            'data_tabs' => 'source_option_1',
			'icon' => 'icon-user',
        );
        return $tabs;
    }
	
	    /**
     * @return string
     */
	protected function getConfigContent(){
		$field_values = $this->getConfigFieldsValues();
		$hooks = $this->getHookList ();
		$opt_column = [
			[
				'id_option' => 1,
				'name'      => 1
			],
			[
				'id_option' => 2,
				'name'      => 2
			],
			[
				'id_option' => 3,
				'name'      => 3
			],
			[
				'id_option' => 4,
				'name'      => 4
			],
			[
				'id_option' => 5,
				'name'      => 5
			],
			[
				'id_option' => 6,
				'name'      => 6
			]
		];
		
        $fields_form["general_setting"] = array(
            'form' => array(
                'input' => array(
					array(
                        'type' => 'text',
						'class' => ' fixed-width-xxl',
                        'label' => $this->l('Title Module'),
                        'name' => 'SPFP_TITLE_MODULE',
                        'required' => true,
						'lang' => true
                    ),
                    array(
                        'type' => 'switch',
                        'label' => $this->l('Display Title '),
                        'hint'  => $this->l('A suffix to be applied to the CSS class of the module. This allows for individual module styling.'),
                        'name' => 'SPFP_DISPLAY_TITLE',
                        'required' => false,
                        'is_bool' => true,
                        'values' => array(
                           array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->getTranslator()->trans('Yes', array(), 'Admin.Global'),
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->getTranslator()->trans('No', array(), 'Admin.Global'),
							),
                        )
                    ),
					array(
						'type'  => 'text',
						'label' => $this->l('Module Class Suffix'),
						'name'  => 'SPFP_CLS_SFX',
						'hint'  => $this->l('A suffix to be applied to the CSS class of the module.
						This allows for individual module styling.'),
						'class' => 'fixed-width-xl'
					),
					array(
						'type'   => 'switch',
						'label'  => $this->l('Status'),
						'name'   => 'SPFP_STATUS',
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
						'label'   => $this->l('Hook Into'),
						'name'    => 'SPFP_HOOK_INTO',
						'options' => array(
							'query' => $hooks,
							'id'    => 'key',
							'name'  => 'name'
						)
					),
					array(
						'type'    => 'select',
						'label'   => $this->l('Type Show'),
						'name'    => 'SPFP_TYPE_SHOW',
						'options' => array(
							'query' => array(
								array(
									'key' => 1,
									'name'      => $this->l('Simple')
								),
								array(
									'key' => 2,
									'name'      => $this->l('Carousel Slider 1')
								)
							),
							'id'    => 'key',
							'name'  => 'name'
						)
					),
					array(
						'type'    => 'select',
						'lang'    => true,
						'label'   => $this->l('# Row'),
						'form_group_class' => "spfp-slide ".($field_values['SPFP_TYPE_SHOW'] == 2 ? '': 'hide')."",
						'name'    => 'SPFP_ROW',
						'options' => array(
							'query' => $opt_column,
							'id'    => 'id_option',
							'name'  => 'name'
						)
					),
					array(
						'type'    => 'select',
						'lang'    => true,
						'label'   => $this->l('# Column'),
						'form_group_class' => "spfp-slide ".($field_values['SPFP_TYPE_SHOW'] == 2 ? '': 'hide')."",
						'name'    => 'SPFP_COLUMN1',
						'desc'    => $this->l('For devices have screen width from 1400px to greater.'),
						'options' => array(
							'query' => $opt_column,
							'id'    => 'id_option',
							'name'  => 'name'
						)
					),
					array(
						'type'    => 'select',
						'lang'    => true,
						'label'   => $this->l('# Column'),
						'form_group_class' => "spfp-slide ".($field_values['SPFP_TYPE_SHOW'] == 2  ? '': 'hide')."",
						'name'    => 'SPFP_COLUMN2',
						'desc'    => $this->l('For devices have screen width from 1200px up to 1399px.'),
						'options' => array(
							'query' => $opt_column,
							'id'    => 'id_option',
							'name'  => 'name'
						)
					),
					array(
						'type'    => 'select',
						'lang'    => true,
						'label'   => $this->l('# Column'),
						'form_group_class' => "spfp-slide ".($field_values['SPFP_TYPE_SHOW'] == 2 ? '': 'hide')."",
						'name'    => 'SPFP_COLUMN3',
						'desc'    => $this->l('For devices have screen width from 991px up to 1199px.'),
						'class'   => 'fixed-width-xl',
						'options' => array(
							'query' => $opt_column,
							'id'    => 'id_option',
							'name'  => 'name'
						)
					),
					array(
						'type'    => 'select',
						'lang'    => true,
						'label'   => $this->l('# Column'),
						'form_group_class' => "spfp-slide ".($field_values['SPFP_TYPE_SHOW'] == 2 ? '': 'hide')."",
						'name'    => 'SPFP_COLUMN4',
						'desc'    => $this->l('For devices have screen width from 768px up to 990px.'),
						'class'   => 'fixed-width-xl',
						'options' => array(
							'query' => $opt_column,
							'id'    => 'id_option',
							'name'  => 'name'
						)
					),
					array(
						'type'   => 'switch',
						'label'  => $this->l('Auto Play'),
						'form_group_class' => "spfp-slide ".($field_values['SPFP_TYPE_SHOW'] == 2 ? '': 'hide')."",
						'name'   => 'SPFP_AUTOPLAY',
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
						'label'  => $this->l('Center'),
						'form_group_class' => "spfp-slide ".($field_values['SPFP_TYPE_SHOW'] == 2 ? '': 'hide')."",
						'name'   => 'SPFP_CENTER',
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
						'label'  => $this->l('Display Nav'),
						'form_group_class' => "spfp-slide ".($field_values['SPFP_TYPE_SHOW'] == 2 ? '': 'hide')."",
						'name'   => 'SPFP_NAV',
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
						'label'  => $this->l('Display Dots'),
						'form_group_class' => "spfp-slide ".($field_values['SPFP_TYPE_SHOW'] == 2 ? '': 'hide')."",
						'name'   => 'SPFP_DOTS',
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
						'type'  => 'text',
						'label' => $this->l('Margin'),
						'name'  => 'SPFP_MARGIN',
						'form_group_class' => "spfp-slide ".($field_values['SPFP_TYPE_SHOW'] == 2 ? '': 'hide')."",
						'class' => 'fixed-width-xl'
					),
                ),
                'buttons' => array(
                    array(
						'title' => $this->l('Save and stay'),
						'name'  => 'saveAndStay',
						'type'  => 'submit',
						'class' => 'btn btn-default pull-right',
						'icon'  => 'process-icon-save'
					)
                ),
                'submit' => array(
					'title' => $this->getTranslator()->trans('Save', array(), 'Admin.Actions'),
				),
            ),
        );
		
		$fields_form['source_option'] = array(
            'form' => array(
                'input' => array(
					array(
                        'type' => 'switch',
                        'label' => $this->l('Display Countdown Timer'),
						'hint'  => $this->l('This option is available on Homepage 4 & Type Show: Special Products only.'),
                        'name' => 'SPFP_COUNTDOWN_TIMER',
                        'required' => false,
                        'is_bool' => true,
                        'values' => array(
							array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->getTranslator()->trans('Yes', array(), 'Admin.Global'),
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->getTranslator()->trans('No', array(), 'Admin.Global'),
							),
                        )
                    ),
					array(
						'type'    => 'select',
						'label'   => $this->l('Type Show'),
						'name'    => 'SPFP_SELECT_SOURCE',
						'options' => array(
							'query' => array(
								array(
									'key' => 'featured_products',
									'name'    => $this->l('Featured Products')
								),
								array(
									'key' => 'best_sellers',
									'name'    => $this->l('Best Sellers')
								),
								array(
									'key' => 'lastest_products',
									'name'    => $this->l('New Products')
								),
								array(
									'key' => 'special_products',
									'name'    => $this->l('Special Products')
								),
								array(
									'key' => 'viewed_products',
									'name'    => $this->l('Most Viewed')
								),
								array(
									'key' => 'other_products',
									'name'    => $this->l('Products in Category')
								),
								array(
									'key' => 'countdown_products',
									'name'    => $this->l('Countdown Products')
								),
								array(
									'key' => 'weekly_deals_new',
									'name'    => $this->l('Weekly Deals New')
								),
							),
							'id'    => 'key',
							'name'  => 'name'
						)
					),
					array(
						'type' => 'datetime',
						'label' => $this->l('Date To'),
						'form_group_class' => "spfp-countdown ".($field_values['SPFP_SELECT_SOURCE'] == 'countdown_products' ? '': 'hide')."",
						'name' => 'SPFP_DATE_TO',
                        'input_group_class'   => 'fixed-width-xl',
					),
					array(
						'type' => 'categories',
						'label' => $this->l('Select Categories'),
						'name' => 'SPFP_CATIDS',
						'tree' => array(
							'id' => 'id_category',
							'use_checkbox' => true,
							'use_search'  => true,
							'name' => 'catids',
							'selected_categories' => $this->getFormValuesCat(),
							'root_category'       => Context::getContext()->shop->getCategory(),
						)
					),
					array(
                        'type' => 'switch',
                        'label' => $this->l('Randomly Display'),
						'description' => $this->l(' Enable if you wish the products to be displayed randomly (default: no).'),
						'form_group_class' => "spfp-random ".($field_values['SPFP_SELECT_SOURCE'] == 'featured_products' ? '': 'hide')."",
                        'name' => 'SPFP_FEATURED_RANDOMIZE',
                        'required' => false,
                        'is_bool' => true,
                        'values' => array(
                           array(
								'id' => 'active_on',
								'value' => 1,
								'label' => $this->getTranslator()->trans('Yes', array(), 'Admin.Global'),
							),
							array(
								'id' => 'active_off',
								'value' => 0,
								'label' => $this->getTranslator()->trans('No', array(), 'Admin.Global'),
							),
                        )
                    ),
					array(
						'type'    => 'select',
						'lang'    => true,
						'label'   => $this->l('Product Field to Order By'),
						'name'    => 'SPFP_ORDERBY',
						'form_group_class' => "spfp-in-category ".($field_values['SPFP_SELECT_SOURCE'] == 'other_products' ? '': 'hide')."",
						'hint'    => $this->l('Choose the position for showing button.'),
						'class'   => 'fixed-width-xl',
						'options' => array(
							'query' => array(
								array(
									'id_option' => 'name',
									'name'      => $this->l('Name')
								),
								array(
									'id_option' => 'id_product',
									'name'      => $this->l('ID')
								),
								array(
									'id_option' => 'date_add',
									'name'      => $this->l('Date Add')
								),
								array(
									'id_option' => 'price',
									'name'      => $this->l('Price')
								),
								array(
									'id_option' => 'rand',
									'name'      => $this->l('Random')
								),


							),
							'id'    => 'id_option',
							'name'  => 'name'
						)
					),
					array(
						'type'    => 'select',
						'lang'    => true,
						'label'   => $this->l('Ordering Direction'),
						'name'    => 'SPFP_DIRECTION',
						'form_group_class' => "spfp-in-category ".($field_values['SPFP_SELECT_SOURCE'] == 'other_products' ? '': 'hide')."",
						'hint'    => $this->l('Select the direction you would like Products.'),
						'class'   => 'fixed-width-xl',
						'options' => array(
							'query' => array(
								array(
									'id_option' => 'DESC',
									'name'      => $this->l('Descending')
								),
								array(
									'id_option' => 'ASC',
									'name'      => $this->l('Ascending')
								),
							),
							'id'    => 'id_option',
							'name'  => 'name'
						)
					),
					array(
                        'type' => 'text',
						'class' => ' fixed-width-xxl',
                        'label' => $this->l('Product Limitation'),
                        'name' => 'SPFP_LIMIT',
                        'required' => true,
                    ),
                ),
                'buttons' => array(
                    array(
						'title' => $this->l('Save and stay'),
						'name'  => 'saveAndStay',
						'type'  => 'submit',
						'class' => 'btn btn-default pull-right',
						'icon'  => 'process-icon-save'
					)
                ),
                'submit' => array(
					'title' => $this->getTranslator()->trans('Save', array(), 'Admin.Actions'),
				),
            ),
        );
		
		if (Tools::isSubmit('id_spfilterproducts') && $this->moduleExists((int)Tools::getValue('id_spfilterproducts'))) {
            $fields_form["general_setting"]['form']['input'][] = array('type' => 'hidden', 'name' => 'id_spfilterproducts');
        }
		
        $helper = new HelperForm();
        $helper->show_toolbar = false;
		$helper->table = $this->table;
		$lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $helper->allow_employee_form_lang = Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') ? Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG') : 0;
        $helper->id = (int) Tools::getValue('id_carrier');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'saveItem';
		$helper->show_cancel_button = true;
		$helper->back_url = AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite ('AdminModules');
		$helper->toolbar_btn = array(
			'save'          => array(
				'desc' => $this->l('Save'),
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.'&token='.Tools::getAdminTokenLite ('AdminModules')
			),
			'back'          => array(
				'href' => AdminController::$currentIndex.'&configure='.$this->name.'&token='.Tools::getAdminTokenLite ('AdminModules'),
				'desc' => $this->l('Back to list')
			),
			'save-and-stay' => array(
				'title' => $this->l('Save then add another value'),
				'name'  => 'submitAdd'.$this->table.'AndStay',
				'type'  => 'submit',
				'class' => 'btn btn-default pull-right',
				'icon'  => 'process-icon-save'
			)
		);
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) . '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $field_values,
			'languages' => $this->context->controller->getLanguages(),
            'id_language' => $this->context->language->id,
        );
		
        return $helper->generateForm($fields_form);
	}
	
	public function getHookList()
	{
		$hooks = array();
		foreach ($this->defaultHook as $key => $hook)
		{
			$id_hook = Hook::getIdByName ($hook);
			$name_hook = $this->getHookTitle ($id_hook);
			$hooks[$key]['key'] = $id_hook;
			$hooks[$key]['name'] = $name_hook;
		}
		return $hooks;
	}
	
	private function getHookTitle($id_hook, $name = false)
	{
		if (!$result = Db::getInstance ()->getRow ('
			SELECT `name`,`title` FROM `'._DB_PREFIX_.'hook` WHERE `id_hook` = '.( $id_hook )))
			return false;
		return (( $result['title'] != '' && $name ) ? $result['title'] : $result['name']);
	}

    /**
     * @return string
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */

    public function getConfigFieldsValues()
    {
        $fields = array();
        if (Tools::isSubmit('id_spfilterproducts') && $this->moduleExists((int)Tools::getValue('id_spfilterproducts'))) {
            $spfilterproducts = new SpFilterProductsClass((int)Tools::getValue('id_spfilterproducts'));
            $fields['id_spfilterproducts'] = (int)Tools::getValue('id_spfilterproducts', $spfilterproducts->id);
			$params = unserialize($spfilterproducts->params);
        } else {
            $spfilterproducts = new SpFilterProductsClass();
			$params = [];
        }
        $fields['SPFP_DISPLAY_TITLE'] = (int)Tools::getValue ('SPFP_DISPLAY_TITLE', isset( $params['SPFP_DISPLAY_TITLE'] ) ? $params['SPFP_DISPLAY_TITLE'] : 1);
		$fields['SPFP_CLS_SFX'] 	  = (string)Tools::getValue ('SPFP_CLS_SFX', isset( $params['SPFP_CLS_SFX'] ) ? $params['SPFP_CLS_SFX'] : '');
		$fields['SPFP_STATUS'] 		  = (int)Tools::getValue ('SPFP_STATUS',  $spfilterproducts->active);
		$fields['SPFP_HOOK_INTO'] 	  = (int)Tools::getValue ('SPFP_HOOK_INTO', isset( $params['SPFP_HOOK_INTO'] ) ? $params['SPFP_HOOK_INTO'] : 1);
		$fields['SPFP_TYPE_SHOW'] 	  = (int)Tools::getValue ('SPFP_TYPE_SHOW', isset( $params['SPFP_TYPE_SHOW'] ) ? $params['SPFP_TYPE_SHOW'] : 2);
		$fields['SPFP_ROW']       	  = (int)Tools::getValue ('SPFP_ROW', isset( $params['SPFP_ROW'] ) ? $params['SPFP_ROW'] : 1);
		$fields['SPFP_COLUMN1']       = (int)Tools::getValue ('SPFP_COLUMN1', isset( $params['SPFP_COLUMN1'] ) ? $params['SPFP_COLUMN1'] : 4);
		$fields['SPFP_COLUMN2']       = (int)Tools::getValue ('SPFP_COLUMN2', isset( $params['SPFP_COLUMN2'] ) ? $params['SPFP_COLUMN2'] : 3);
		$fields['SPFP_COLUMN3']       = (int)Tools::getValue ('SPFP_COLUMN3', isset( $params['SPFP_COLUMN3'] ) ? $params['SPFP_COLUMN3'] : 2);
		$fields['SPFP_COLUMN4']       = (int)Tools::getValue ('SPFP_COLUMN4', isset( $params['SPFP_COLUMN4'] ) ? $params['SPFP_COLUMN4'] : 1);
		$fields['SPFP_AUTOPLAY']      = (int)Tools::getValue ('SPFP_AUTOPLAY', isset( $params['SPFP_AUTOPLAY'] ) ? $params['SPFP_AUTOPLAY'] : 1);
		$fields['SPFP_CENTER']      = (int)Tools::getValue ('SPFP_CENTER', isset( $params['SPFP_CENTER'] ) ? $params['SPFP_CENTER'] : 0);
		$fields['SPFP_NAV']      = (int)Tools::getValue ('SPFP_NAV', isset( $params['SPFP_NAV'] ) ? $params['SPFP_NAV'] : 1);
		$fields['SPFP_DOTS']      = (int)Tools::getValue ('SPFP_DOTS', isset( $params['SPFP_DOTS'] ) ? $params['SPFP_DOTS'] : 1);
		$fields['SPFP_MARGIN']      = (int)Tools::getValue ('SPFP_MARGIN', isset( $params['SPFP_MARGIN'] ) ? $params['SPFP_MARGIN'] : 30);
		$fields['SPFP_COUNTDOWN_TIMER'] = (int)Tools::getValue ('SPFP_COUNTDOWN_TIMER', isset( $params['SPFP_COUNTDOWN_TIMER'] ) ? $params['SPFP_COUNTDOWN_TIMER'] : 0);
		$fields['SPFP_FEATURED_RANDOMIZE'] = (int)Tools::getValue ('SPFP_FEATURED_RANDOMIZE', isset( $params['SPFP_FEATURED_RANDOMIZE'] ) ? $params['SPFP_FEATURED_RANDOMIZE'] : 0);
		$fields['SPFP_SELECT_SOURCE'] = (string)Tools::getValue ('SPFP_SELECT_SOURCE', isset( $params['SPFP_SELECT_SOURCE'] ) ? $params['SPFP_SELECT_SOURCE'] : 'featured_products');
		$fields['SPFP_DATE_TO']       = (string)Tools::getValue ('SPFP_DATE_TO', isset( $params['SPFP_DATE_TO'] ) ? $params['SPFP_DATE_TO'] : '');
		$catids = $this->getFormValuesCat();
		$catids = is_array($catids) ? $catids : (!empty($catids)  ? explode(',', $catids) : '');
		$fields['SPFP_CATIDS[]']      = $catids;
		$fields['SPFP_ORDERBY']       = (string)Tools::getValue ('SPFP_ORDERBY', isset( $params['SPFP_ORDERBY'] ) ? $params['SPFP_ORDERBY'] : 'name');
		$fields['SPFP_DIRECTION']     = (string)Tools::getValue ('SPFP_DIRECTION', isset( $params['SPFP_DIRECTION'] ) ? $params['SPFP_DIRECTION'] : 'DESC');
		$fields['SPFP_LIMIT']         = (int)Tools::getValue ('SPFP_LIMIT', isset( $params['SPFP_LIMIT'] ) ? $params['SPFP_LIMIT'] : 6);
        $languages = Language::getLanguages(false);
        foreach ($languages as $lang) {
            $fields['SPFP_TITLE_MODULE'][$lang['id_lang']] = Tools::getValue('SPFP_TITLE_MODULE_'.(int)$lang['id_lang'], $spfilterproducts->title_module[$lang['id_lang']]);
        }

        return $fields;
    }
	
	private function getFormValuesCat()
	{
		$lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
		$id_spfilterproducts = Tools::getValue ('id_spfilterproducts');
		if (Tools::isSubmit ('id_spfilterproducts') && $id_spfilterproducts)
		{
			$spfilterproducts = new SpFilterProductsClass((int)$id_spfilterproducts);
			$params = unserialize($spfilterproducts->params);
		}else
		{
			$params = array();
		}
		$catids = [];
		if (isset($params['SPFP_CATIDS'])){
			if ($params['SPFP_CATIDS'] == 'all'){
				foreach(Category::getSimpleCategories($lang->id) as $cat){
					$catids[] = $cat['id_category'];
				}
				$catids = array_slice($catids, 0, 5);
			}else{
				if (!empty($params['SPFP_CATIDS']) && isset($params['SPFP_CATIDS'])){
					$catids = explode(',',$params['SPFP_CATIDS']);
				}
			}
		}
		return $catids;
	}
	
	public function moduleExists($id_spfilterproducts)
    {
        $req = 'SELECT hs.`id_spfilterproducts`
                FROM `'._DB_PREFIX_.'spfilterproducts` hs
                WHERE hs.`id_spfilterproducts` = '.(int)$id_spfilterproducts;
        $row = Db::getInstance(_PS_USE_SQL_SLAVE_)->getRow($req);
        return ($row);
    }

   
	
	private function getProducts($params){
		$type = $params['SPFP_SELECT_SOURCE'];
		switch($type){
			case 'featured_products':
				$products = $this->getFeaturedProducts($params);
			break;
			case 'best_sellers':
				$products = $this->getBestSellers($params);
			break;
			case 'lastest_products':
				$products = $this->getNewProducts($params);
			break;	
			case 'special_products':
				$products = $this->getSpecialProducts($params);
			break;	
			case 'viewed_products':
				$products = $this->getViewedProducts($params);
			break;	
			case 'other_products':
				$products = $this->getProductsInCategories($params);
			break;
			case 'countdown_products':
				$products = $this->getProductsCountDown($params);
			break;	
			case 'weekly_deals_new':
				$products = $this->getWeeklyDealsNew($params);
			break;	
			
		}
		if (empty($products))
			return [];
	//	var_dump($products);die;
		$assembler = new ProductAssembler($this->context);

        $presenterFactory = new ProductPresenterFactory($this->context);
        $presentationSettings = $presenterFactory->getPresentationSettings();
        $presenter = new ProductListingPresenter(
            new ImageRetriever(
                $this->context->link
            ),
            $this->context->link,
            new PriceFormatter(),
            new ProductColorsRetriever(),
            $this->context->getTranslator()
        );

        $products_for_template = array();

        if (is_array($products)) {
            foreach ($products as $rawProduct) {
                $products_for_template[] = $presenter->present(
                    $presentationSettings,
                    $assembler->assembleProduct($rawProduct),
                    $this->context->language
                );
            }
        }

        return $products_for_template;
	}
	
	private function getProductsCountDown($params){
		
		$context = Context::getContext();
		$now = date('Y-m-d H:i:s');
		$date_to = strtotime($params['SPFP_DATE_TO']);
		if ($date_to == false)
			return [];
		$date_to = date("Y-m-d H:i:s",$date_to);
		//$catids = $params['SPFP_CATIDS'];
		$idLang = $context->language->id;
		$limit = $params['SPFP_LIMIT'];
		$sql = 'SELECT p.`id_product`, product_shop.*, sp.`id_specific_price`, sp.`from`, sp.`to`
			FROM '._DB_PREFIX_.'specific_price sp
			LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = sp.`id_product`
			'.Shop::addSqlAssociation('product', 'p').
			'WHERE product_shop.`id_shop` = '.(int) $context->shop->id.'
			 AND product_shop.`active` = 1
			 AND sp.`from` <= \''.pSQL($now).'\' AND
			 `to` != \'0000-00-00 00:00:00\' AND `to` <= \''.pSQL($date_to).'\'
			'.(!empty($catids) ? ' AND product_shop.`id_category_default` IN  ('. $catids.')' : '');
		 $sql .= 'GROUP BY p.`id_product`
				  ORDER BY product_shop.`price` DESC
				  LIMIT '.(int)$limit;		
				  
        $products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);				  
		$list = [];
		if (is_array($products) && count($products)) {
			foreach ($products as $product) {
				$_product = get_object_vars(new Product((int)$product['id_product'], true, $idLang));
				$_product['id_product'] = $_product['id'];
				$coverImage = Product::getCover($_product['id_product']);
				$_product['id_image'] = $coverImage['id_image'];
				$list[] =  Product::getProductProperties( $idLang, $_product);;
			}
		}		
		return $list;
	}
	
	 public function getProductsInCategories($params) {
		if (empty($params['SPFP_CATIDS']))
			return []; 
		$context = Context::getContext();;
		$catids = $params['SPFP_CATIDS'];
		$idLang = $context->language->id;
        $p = 1;
        $n = $params['SPFP_LIMIT'];
        $orderyBy = $params['SPFP_ORDERBY'];
        $orderWay = $params['SPFP_DIRECTION'];
        $getTotal = false;
        $active = true;
        $random = $params['SPFP_ORDERBY'] == 'rand' ? true : false;
        $randomNumberProducts = $params['SPFP_LIMIT'];

        $front = in_array($context->controller->controller_type, array('front', 'modulefront'));
        $idSupplier = (int) Tools::getValue('id_supplier');

        /** Return only the number of products */
        if ($getTotal) {
            $sql = 'SELECT COUNT(cp.`id_product`) AS total
					FROM `'._DB_PREFIX_.'product` p
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
					WHERE cp.`id_category` = '.(int) $this->id.
                ($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
                ($active ? ' AND product_shop.`active` = 1' : '').
                ($idSupplier ? 'AND p.id_supplier = '.(int) $idSupplier : '');
            return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }

        if ($p < 1) {
            $p = 1;
        }

        /** Tools::strtolower is a fix for all modules which are now using lowercase values for 'orderBy' parameter */
        $orderyBy  = Validate::isOrderBy($orderyBy)   ? Tools::strtolower($orderyBy)  : 'position';
        $orderWay = Validate::isOrderWay($orderWay) ? Tools::strtoupper($orderWay) : 'ASC';

        $orderByPrefix = false;
        if ($orderyBy == 'id_product' || $orderyBy == 'date_add' || $orderyBy == 'date_upd') {
            $orderByPrefix = 'p';
        } elseif ($orderyBy == 'name') {
            $orderByPrefix = 'pl';
        } elseif ($orderyBy == 'manufacturer' || $orderyBy == 'manufacturer_name') {
            $orderByPrefix = 'm';
            $orderyBy = 'name';
        } elseif ($orderyBy == 'position') {
            $orderByPrefix = 'cp';
        }

        if ($orderyBy == 'price') {
            $orderyBy = 'orderprice';
        }

        $nbDaysNewProduct = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        if (!Validate::isUnsignedInt($nbDaysNewProduct)) {
            $nbDaysNewProduct = 20;
        }

        $sql = 'SELECT DISTINCT  p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity'.(Combination::isFeatureActive() ? ', IFNULL(product_attribute_shop.id_product_attribute, 0) AS id_product_attribute,
					product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity' : '').', pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, image_shop.`id_image` id_image,
					il.`legend` as legend, m.`name` AS manufacturer_name, cl.`name` AS category_default,
					DATEDIFF(product_shop.`date_add`, DATE_SUB("'.date('Y-m-d').' 00:00:00",
					INTERVAL '.(int) $nbDaysNewProduct.' DAY)) > 0 AS new, product_shop.price AS orderprice
				FROM `'._DB_PREFIX_.'category_product` cp
				LEFT JOIN `'._DB_PREFIX_.'product` p
					ON p.`id_product` = cp.`id_product`
				'.Shop::addSqlAssociation('product', 'p').
                (Combination::isFeatureActive() ? ' LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
				ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int) $context->shop->id.')':'').'
				'.Product::sqlStock('p', 0).'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON (product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = '.(int) $idLang.Shop::addSqlRestrictionOnLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int) $idLang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.')
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il
					ON (image_shop.`id_image` = il.`id_image`
					AND il.`id_lang` = '.(int) $idLang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE product_shop.`id_shop` = '.(int) $context->shop->id.'
					AND cp.`id_category` IN  ('. $catids.')'
                    .($active ? ' AND product_shop.`active` = 1' : '')
                    .($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
                    .($idSupplier ? ' AND p.id_supplier = '.(int)$idSupplier : '');
        if ($random === true) {
            $sql .= ' ORDER BY RAND() LIMIT '.(int) $randomNumberProducts;
        } else {
            $sql .= ' ORDER BY '.(!empty($orderByPrefix) ? $orderByPrefix.'.' : '').'`'.bqSQL($orderyBy).'` '.pSQL($orderWay).'
			LIMIT '.(((int) $p - 1) * (int) $n).','.(int) $n;
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);

        if (!$result) {
            return array();
        }

        if ($orderyBy == 'orderprice') {
            Tools::orderbyPrice($result, $orderWay);
        }

        // Modify SQL result
        return Product::getProductsProperties($idLang, $result);
    }

	
	private function getViewedProducts($params){
		
		$now = date('Y-m-d H:i:s');
		$date_to = $now;
		$date_from = strtotime($date_to."- 1 months");
		$date_from = date("Y-m-d",$date_from).' 00:00:00';
		$limit = $params['SPFP_LIMIT'];
		//$catids = $params['SPFP_CATIDS'];
		$sql = '
			SELECT DISTINCT p.`id_product`, pa.`id_object`, pv.`counter`
			FROM `'._DB_PREFIX_.'page_viewed` pv
			LEFT JOIN `'._DB_PREFIX_.'page` pa ON pv.`id_page` = pa.`id_page`
			LEFT JOIN `'._DB_PREFIX_.'product` p ON p.`id_product` = pa.`id_object`
			'.Shop::addSqlAssociation('product', 'p').'
			LEFT JOIN `'._DB_PREFIX_.'page_type` pt ON pt.`id_page_type` = pa.`id_page_type`
			WHERE pt.`name` = \'product\'
			'.Shop::addSqlRestriction(false, 'pv'). (!empty($catids) ? ' AND p.`id_category_default` IN  ('. $catids.')' : '').'
			ORDER BY pv.`counter` DESC
			LIMIT '.(int)$limit;	
		$products = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);
		$list = [];
		if (is_array($products) && count($products)) {
			foreach ($products as $product) {
				$_product = get_object_vars(new Product((int)$product['id_object'], true, $this->context->language->id));
				$_product['id_product'] = $_product['id'];
				$coverImage = Product::getCover($_product['id_product']);
				$_product['id_image'] = $coverImage['id_image'];
				$list[] =  Product::getProductProperties( $this->context->language->id, $_product);;
			}
		}		
		return $list;
	}
	
	private  function _getProductIdByDate($beginning, $ending, Context $context = null, $with_combination = false)
    {
        if (!$context) {
            $context = Context::getContext();
        }

        $id_address = $context->cart->{Configuration::get('PS_TAX_ADDRESS_TYPE')};
        $ids = Address::getCountryAndState($id_address);
        $id_country = $ids['id_country'] ? (int)$ids['id_country'] : (int) Configuration::get('PS_COUNTRY_DEFAULT');

        return SpecificPrice::getProductIdByDate(
            $context->shop->id,
            $context->currency->id,
            $id_country,
            $context->customer->id_default_group,
            $beginning,
            $ending,
            0,
            $with_combination
        );
    }
	
	private function getSpecialProducts($params) {
		$context = Context::getContext();;
		//$catids = $params['SPFP_CATIDS'];
		$id_lang = $context->language->id;
        $page_number = 0;
        $nb_products =  $params['SPFP_LIMIT'];
        $count = false;
        $order_by = null;
        $order_way = null;
        $beginning = false;
        $ending = false;
        if (!Validate::isBool($count)) {
            die(Tools::displayError());
        }
		
		if (!$context) {
            $context = Context::getContext();
        }
       
        if ($page_number < 1) {
            $page_number = 1;
        }
        if ($nb_products < 1) {
            $nb_products = 10;
        }
        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'price';
        }
        if (empty($order_way)) {
            $order_way = 'DESC';
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'product_shop';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        }
        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }
        $current_date = date('Y-m-d H:i:00');
        $ids_product = $this->_getProductIdByDate((!$beginning ? $current_date : $beginning), (!$ending ? $current_date : $ending), $context);

        $tab_id_product = array();
        foreach ($ids_product as $product) {
            if (is_array($product)) {
                $tab_id_product[] = (int)$product['id_product'];
            } else {
                $tab_id_product[] = (int)$product;
            }
        }

        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }

        $sql_groups = '';
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql_groups = ' AND EXISTS(SELECT 1 FROM `'._DB_PREFIX_.'category_product` cp
				JOIN `'._DB_PREFIX_.'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1').')
				WHERE cp.`id_product` = p.`id_product`)';
        }

        if ($count) {
            return Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue('
			SELECT COUNT(DISTINCT p.`id_product`)
			FROM `'._DB_PREFIX_.'product` p
			'.Shop::addSqlAssociation('product', 'p').'
			WHERE product_shop.`active` = 1
			AND product_shop.`show_price` = 1
			'.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
			'.((!$beginning && !$ending) ? 'AND p.`id_product` IN('.((is_array($tab_id_product) && count($tab_id_product)) ? implode(', ', $tab_id_product) : 0).')' : '').'
			'.$sql_groups);
        }

        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by = pSQL($order_by[0]).'.`'.pSQL($order_by[1]).'`';
        }

        $sql = '
		SELECT DISTINCT
			p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`available_now`, pl.`available_later`,
			IFNULL(product_attribute_shop.id_product_attribute, 0) id_product_attribute,
			pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`,
			pl.`name`, image_shop.`id_image` id_image, il.`legend`, m.`name` AS manufacturer_name,
			DATEDIFF(
				p.`date_add`,
				DATE_SUB(
					"'.date('Y-m-d').' 00:00:00",
					INTERVAL '.(Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20).' DAY
				)
			) > 0 AS new
		FROM `'._DB_PREFIX_.'product` p
		'.Shop::addSqlAssociation('product', 'p').'
		LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
			ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id.')
		'.Product::sqlStock('p', 0, false, $context->shop).'
		LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (
			p.`id_product` = pl.`id_product`
			AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl').'
		)
		LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
			ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.')
		LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang.')
		LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
		WHERE product_shop.`active` = 1'.
		(!empty($catids) ? ' AND product_shop.`id_category_default` IN  ('. $catids.')' : '').'
		AND product_shop.`show_price` = 1
		'.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
		'.((!$beginning && !$ending) ? ' AND p.`id_product` IN ('.((is_array($tab_id_product) && count($tab_id_product)) ? implode(', ', $tab_id_product) : 0).')' : '').'
		'.$sql_groups.'
		ORDER BY '.(isset($order_by_prefix) ? pSQL($order_by_prefix).'.' : '').pSQL($order_by).' '.pSQL($order_way).'
		LIMIT '.(int)(($page_number-1) * $nb_products).', '.(int)$nb_products;
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (!$result) {
            return false;
        }

        if ($order_by == 'price') {
            Tools::orderbyPrice($result, $order_way);
        }

        return Product::getProductsProperties($id_lang, $result);
    }
	
	private  function getNewProducts($params)
    {
		$context = $this->context;
		$pageNumber = 1;
        $nbProducts = $params['SPFP_LIMIT'];
		//$catids = $params['SPFP_CATIDS'];
		$id_lang = $context->language->id;
		$page_number = 0;
		$nb_products = $params['SPFP_LIMIT'];
		$count = false;
		$order_by = null; 
		$order_way = null;
        $now = date('Y-m-d') . ' 00:00:00';

        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }

        if ($page_number < 1) {
            $page_number = 1;
        }
        if ($nb_products < 1) {
            $nb_products = 10;
        }
        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'date_add';
        }
        if (empty($order_way)) {
            $order_way = 'DESC';
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'product_shop';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        }
        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }

        $sql_groups = '';
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql_groups = ' AND EXISTS(SELECT 1 FROM `'._DB_PREFIX_.'category_product` cp
				JOIN `'._DB_PREFIX_.'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= '.(int)Configuration::get('PS_UNIDENTIFIED_GROUP')).')
				WHERE cp.`id_product` = p.`id_product`)';
        }

        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by = $order_by[1];
        }

        $nb_days_new_product = (int) Configuration::get('PS_NB_DAYS_NEW_PRODUCT');

        if ($count) {
            $sql = 'SELECT COUNT(p.`id_product`) AS nb
					FROM `'._DB_PREFIX_.'product` p
					'.Shop::addSqlAssociation('product', 'p').'
					WHERE product_shop.`active` = 1
					AND product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.$nb_days_new_product.' DAY')).'"
					'.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
					'.$sql_groups;
            return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }
        $sql = new DbQuery();
        $sql->select(
            ' p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
			pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`, image_shop.`id_image` id_image, il.`legend`, m.`name` AS manufacturer_name,
			(DATEDIFF(product_shop.`date_add`,
				DATE_SUB(
					"'.$now.'",
					INTERVAL '.$nb_days_new_product.' DAY
				)
			) > 0) as new'
        );

        $sql->from('product', 'p');
        $sql->join(Shop::addSqlAssociation('product', 'p'));
        $sql->leftJoin('product_lang', 'pl', '
			p.`id_product` = pl.`id_product`
			AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl')
        );
        $sql->leftJoin('image_shop', 'image_shop', 'image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id);
        $sql->leftJoin('image_lang', 'il', 'image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang);
        $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');

        $sql->where('product_shop.`active` = 1 '.(!empty($catids) ? ' AND p.`id_category_default` IN  ('. $catids.')' : ''));
        if ($front) {
            $sql->where('product_shop.`visibility` IN ("both", "catalog")');
        }
        $sql->where('product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.$nb_days_new_product.' DAY')).'"');
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql->where('EXISTS(SELECT 1 FROM `'._DB_PREFIX_.'category_product` cp
				JOIN `'._DB_PREFIX_.'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1').')
				WHERE cp.`id_product` = p.`id_product`)');
        }

        $sql->orderBy((isset($order_by_prefix) ? pSQL($order_by_prefix).'.' : '').'`'.pSQL($order_by).'` '.pSQL($order_way));
        $sql->limit($nb_products, (int)(($page_number-1) * $nb_products));

        if (Combination::isFeatureActive()) {
            $sql->select('product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute');
            $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', 'p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id);
        }
        $sql->join(Product::sqlStock('p', 0));
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (!$result) {
            return false;
        }

        if ($order_by == 'price') {
            Tools::orderbyPrice($result, $order_way);
        }
        $products_ids = array();
        foreach ($result as $row) {
            $products_ids[] = $row['id_product'];
        }
        // Thus you can avoid one query per product, because there will be only one query for all the products of the cart
        Product::cacheFrontFeatures($products_ids, $id_lang);
        return Product::getProductsProperties((int)$id_lang, $result);
    }
	
	
	private function getBestSellers($params){
		$context = $this->context;
		$idLang = $context->language->id;
		$pageNumber = 1;
        $nbProducts = $params['SPFP_LIMIT'];
		//$catids = $params['SPFP_CATIDS'];
        $orderBy = 'sales';
		$orderWay =  'DESC';
        $finalOrderBy = $orderBy;
        $orderTable = '';
        $invalidOrderBy = !Validate::isOrderBy($orderBy);
        if ($invalidOrderBy || is_null($orderBy)) {
            $orderBy = 'quantity';
            $orderTable = 'ps';
        }

        if ($orderBy == 'date_add' || $orderBy == 'date_upd') {
            $orderTable = 'product_shop';
        }

        $invalidOrderWay = !Validate::isOrderWay($orderWay);
        if ($invalidOrderWay || is_null($orderWay) || $orderBy == 'sales') {
            $orderWay = 'DESC';
        }

        $interval = Validate::isUnsignedInt(Configuration::get('PS_NB_DAYS_NEW_PRODUCT')) ? Configuration::get('PS_NB_DAYS_NEW_PRODUCT') : 20;
        $sql = 'SELECT  p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity,
					'.(Combination::isFeatureActive()?'product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity,IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute,':'').'
					pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
					pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`,
					m.`name` AS manufacturer_name, p.`id_manufacturer` as id_manufacturer,
					image_shop.`id_image` id_image, il.`legend`,
					ps.`quantity` AS sales, t.`rate`, pl.`meta_keywords`, pl.`meta_title`, pl.`meta_description`,
					DATEDIFF(p.`date_add`, DATE_SUB("'.date('Y-m-d').' 00:00:00",
					INTERVAL '.(int) $interval.' DAY)) > 0 AS new'

              .' FROM `'._DB_PREFIX_.'product_sale` ps
				LEFT JOIN `'._DB_PREFIX_.'product` p ON ps.`id_product` = p.`id_product`
				'.Shop::addSqlAssociation('product', 'p', false);
        if (Combination::isFeatureActive()) {
            $sql .= ' LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
							ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id.')';
        }
        $sql .=    ' 
				
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int) $idLang.Shop::addSqlRestrictionOnLang('pl').'
				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int) $context->shop->id.')
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il ON (image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int) $idLang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
				LEFT JOIN `'._DB_PREFIX_.'tax_rule` tr ON (product_shop.`id_tax_rules_group` = tr.`id_tax_rules_group`)
					AND tr.`id_country` = '.(int) $context->country->id.'
					AND tr.`id_state` = 0
				LEFT JOIN `'._DB_PREFIX_.'tax` t ON (t.`id_tax` = tr.`id_tax`)
				'.Product::sqlStock('p', 0);

        $sql .= '
				WHERE product_shop.`active` = 1
					AND product_shop.`visibility` != \'none\'';
		$sql .= (!empty($catids) ? ' AND p.`id_category_default` IN  ('. $catids.')' : '');
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql .= ' AND EXISTS(SELECT 1 FROM `'._DB_PREFIX_.'category_product` cp
					JOIN `'._DB_PREFIX_.'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1').')
					WHERE cp.`id_product` = p.`id_product`)';
        }

        if ($finalOrderBy != 'price') {

            $sql .= '
					ORDER BY '.(!empty($orderTable) ? '`'.pSQL($orderTable).'`.' : '').'`'.pSQL($orderBy).'` '.pSQL($orderWay).'
					LIMIT '.(int) (($pageNumber-1) * $nbProducts).', '.(int) $nbProducts;
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if ($finalOrderBy == 'price') {
            Tools::orderbyPrice($result, $orderWay);
            $result = array_slice($result, (int) (($pageNumber-1) * $nbProducts), (int) $nbProducts);
        }
        if (!$result) {
            return false;
        }

        return Product::getProductsProperties($idLang, $result);
    }
	
	//webinsect custome code for weekly deals new start
	private function getWeeklyDealsNew($params){
		$context = $this->context;
		$pageNumber = 1;
        $nbProducts = $params['SPFP_LIMIT'];
		//$catids = $params['SPFP_CATIDS'];
		$id_lang = $context->language->id;
		$page_number = 0;
		$nb_products = $params['SPFP_LIMIT'];
		$count = false;
		$order_by = null; 
		$order_way = null;
        $now = date('Y-m-d') . ' 00:00:00';
		
		$sqlGetShop = "SELECT `other_shop_id` FROM `"._DB_PREFIX_."shop_store` WHERE `presta_shop_id` = '".(int)$context->shop->id."'"; 
		$other_shop_id = Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sqlGetShop);
		
		if($other_shop_id == 30){
			$sqlGetArtnr = "SELECT artnr FROM `tb_articles_fuman_details` WHERE sonderkz = 1 AND store_id = '".$other_shop_id."'"; 
			$sqlGetArtnrs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sqlGetArtnr);
		} else {
			$sqlGetArtnr = "SELECT artnr FROM `tb_articles_fuman_details` WHERE sonderkz = 18 AND store_id = '".$other_shop_id."'"; 
			$sqlGetArtnrs = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sqlGetArtnr);
		}
		
		$getArtnr = '';
		foreach($sqlGetArtnrs as $sqlGetArtnrOne){
			$getArtnr .= "'".$sqlGetArtnrOne['artnr']."', ";
		}
		$getArtnrResult = substr(trim($getArtnr), 0, -1);
        $front = true;
        if (!in_array($context->controller->controller_type, array('front', 'modulefront'))) {
            $front = false;
        }

        if ($page_number < 1) {
            $page_number = 1;
        }
        if ($nb_products < 1) {
            $nb_products = 10;
        }
        if (empty($order_by) || $order_by == 'position') {
            $order_by = 'date_add';
        }
        if (empty($order_way)) {
            $order_way = 'DESC';
        }
        if ($order_by == 'id_product' || $order_by == 'price' || $order_by == 'date_add' || $order_by == 'date_upd') {
            $order_by_prefix = 'product_shop';
        } elseif ($order_by == 'name') {
            $order_by_prefix = 'pl';
        }
        if (!Validate::isOrderBy($order_by) || !Validate::isOrderWay($order_way)) {
            die(Tools::displayError());
        }

        $sql_groups = '';
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
            $sql_groups = ' AND EXISTS(SELECT 1 FROM `'._DB_PREFIX_.'category_product` cp
				JOIN `'._DB_PREFIX_.'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= '.(int)Configuration::get('PS_UNIDENTIFIED_GROUP')).')
				WHERE cp.`id_product` = p.`id_product`)';
        }

        if (strpos($order_by, '.') > 0) {
            $order_by = explode('.', $order_by);
            $order_by_prefix = $order_by[0];
            $order_by = $order_by[1];
        }

        $nb_days_new_product = (int) Configuration::get('PS_NB_DAYS_NEW_PRODUCT');

        if ($count) {
            $sql = 'SELECT COUNT(p.`id_product`) AS nb
					FROM `'._DB_PREFIX_.'product` p
					'.Shop::addSqlAssociation('product', 'p').'
					WHERE product_shop.`active` = 1
					AND product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.$nb_days_new_product.' DAY')).'"
					'.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').'
					'.$sql_groups;
            return (int)Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }
        $sql = new DbQuery();
        $sql->select(
            ' p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) as quantity, pl.`description`, pl.`description_short`, pl.`link_rewrite`, pl.`meta_description`,
			pl.`meta_keywords`, pl.`meta_title`, pl.`name`, pl.`available_now`, pl.`available_later`, image_shop.`id_image` id_image, il.`legend`, m.`name` AS manufacturer_name,
			(DATEDIFF(product_shop.`date_add`,
				DATE_SUB(
					"'.$now.'",
					INTERVAL '.$nb_days_new_product.' DAY
				)
			) > 0) as new'
        );

        $sql->from('product', 'p');
        $sql->join(Shop::addSqlAssociation('product', 'p'));
        $sql->leftJoin('product_lang', 'pl', '
			p.`id_product` = pl.`id_product`
			AND pl.`id_lang` = '.(int)$id_lang.Shop::addSqlRestrictionOnLang('pl')
        );
        $sql->leftJoin('image_shop', 'image_shop', 'image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id);
        $sql->leftJoin('image_lang', 'il', 'image_shop.`id_image` = il.`id_image` AND il.`id_lang` = '.(int)$id_lang);
        $sql->leftJoin('manufacturer', 'm', 'm.`id_manufacturer` = p.`id_manufacturer`');

        //$sql->where('product_shop.`active` = 1 '.(!empty($catids) ? ' AND p.`id_category_default` IN  ('. $catids.')' : ''));
        if ($front) {
            //$sql->where('product_shop.`visibility` IN ("both", "catalog")');
        }
        //$sql->where('product_shop.`date_add` > "'.date('Y-m-d', strtotime('-'.$nb_days_new_product.' DAY')).'"');
        if (Group::isFeatureActive()) {
            $groups = FrontController::getCurrentCustomerGroups();
           // $sql->where('EXISTS(SELECT 1 FROM `'._DB_PREFIX_.'category_product` cp
			//	JOIN `'._DB_PREFIX_.'category_group` cg ON (cp.id_category = cg.id_category AND cg.`id_group` '.(count($groups) ? 'IN ('.implode(',', $groups).')' : '= 1').')
			//	WHERE cp.`id_product` = p.`id_product`)'); 
        }
		//$sql->where('p.reference IN ('.implode(',',$sqlGetArtnrs['artnr']).')');
		$sql->where('p.reference IN ('.$getArtnrResult.')');
        $sql->orderBy((isset($order_by_prefix) ? pSQL($order_by_prefix).'.' : '').'`'.pSQL($order_by).'` '.pSQL($order_way));
        $sql->limit($nb_products, (int)(($page_number-1) * $nb_products));

        if (Combination::isFeatureActive()) {
            $sql->select('product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity, IFNULL(product_attribute_shop.id_product_attribute,0) id_product_attribute');
            $sql->leftJoin('product_attribute_shop', 'product_attribute_shop', 'p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int)$context->shop->id);
        }
        $sql->join(Product::sqlStock('p', 0));
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql);

        if (!$result) {
            return false;
        }

        if ($order_by == 'price') {
            Tools::orderbyPrice($result, $order_way);
        }
        $products_ids = array();
        foreach ($result as $row) {
            $products_ids[] = $row['id_product'];
        }
        // Thus you can avoid one query per product, because there will be only one query for all the products of the cart
        Product::cacheFrontFeatures($products_ids, $id_lang);
        return Product::getProductsProperties((int)$id_lang, $result);
    }
	//webinsect custome code for weekly deals new start
	
	private function getFeaturedProducts($params){
		
        $context = $this->context;
        $idLang = $context->language->id;
        $getTotal = false;
        $p = 1;
        $n = $params['SPFP_LIMIT'];
        $orderyBy = 'position';
        $orderWay = null;
        $random = $params['SPFP_FEATURED_RANDOMIZE'] ? true : false;
        $randomNumberProducts = $params['SPFP_LIMIT'];

        $front = in_array($context->controller->controller_type, array('front', 'modulefront'));
        $idSupplier = (int) Tools::getValue('id_supplier');
		$catids = $params['SPFP_CATIDS'];
        /** Return only the number of products */
        if ($getTotal) {
            $sql = 'SELECT COUNT(cp.`id_product`) AS total
					FROM `'._DB_PREFIX_.'product` p
					'.Shop::addSqlAssociation('product', 'p').'
					LEFT JOIN `'._DB_PREFIX_.'category_product` cp ON p.`id_product` = cp.`id_product`
					WHERE product_shop.`active` = 1'.
					(!empty($catids) ? ' AND cp.`id_category` IN  ('. $catids.')' : '').
					($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '').
					($idSupplier ? 'AND p.id_supplier = '.(int) $idSupplier : '');

            return (int) Db::getInstance(_PS_USE_SQL_SLAVE_)->getValue($sql);
        }

        if ($p < 1) {
            $p = 1;
        }

        /** Tools::strtolower is a fix for all modules which are now using lowercase values for 'orderBy' parameter */
        $orderyBy  = Validate::isOrderBy($orderyBy)   ? Tools::strtolower($orderyBy)  : 'position';
        $orderWay = Validate::isOrderWay($orderWay) ? Tools::strtoupper($orderWay) : 'ASC';

        $orderByPrefix = false;
        if ($orderyBy == 'id_product' || $orderyBy == 'date_add' || $orderyBy == 'date_upd') {
            $orderByPrefix = 'p';
        } elseif ($orderyBy == 'name') {
            $orderByPrefix = 'pl';
        } elseif ($orderyBy == 'manufacturer' || $orderyBy == 'manufacturer_name') {
            $orderByPrefix = 'm';
            $orderyBy = 'name';
        } elseif ($orderyBy == 'position') {
            $orderByPrefix = 'cp';
        }

        if ($orderyBy == 'price') {
            $orderyBy = 'orderprice';
        }

        $nbDaysNewProduct = Configuration::get('PS_NB_DAYS_NEW_PRODUCT');
        if (!Validate::isUnsignedInt($nbDaysNewProduct)) {
            $nbDaysNewProduct = 20;
        }

        $sql = 'SELECT DISTINCT p.*, product_shop.*, stock.out_of_stock, IFNULL(stock.quantity, 0) AS quantity'.(Combination::isFeatureActive() ? ', IFNULL(product_attribute_shop.id_product_attribute, 0) AS id_product_attribute,
					product_attribute_shop.minimal_quantity AS product_attribute_minimal_quantity' : '').', pl.`description`, pl.`description_short`, pl.`available_now`,
					pl.`available_later`, pl.`link_rewrite`, pl.`meta_description`, pl.`meta_keywords`, pl.`meta_title`, pl.`name`, image_shop.`id_image` id_image,
					il.`legend` as legend, m.`name` AS manufacturer_name, cl.`name` AS category_default,
					DATEDIFF(product_shop.`date_add`, DATE_SUB("'.date('Y-m-d').' 00:00:00",
					INTERVAL '.(int) $nbDaysNewProduct.' DAY)) > 0 AS new, product_shop.price AS orderprice
				FROM `'._DB_PREFIX_.'category_product` cp
				LEFT JOIN `'._DB_PREFIX_.'product` p
					ON p.`id_product` = cp.`id_product`
				'.Shop::addSqlAssociation('product', 'p').
            (Combination::isFeatureActive() ? ' LEFT JOIN `'._DB_PREFIX_.'product_attribute_shop` product_attribute_shop
				ON (p.`id_product` = product_attribute_shop.`id_product` AND product_attribute_shop.`default_on` = 1 AND product_attribute_shop.id_shop='.(int) $context->shop->id.')':'').'
				'.Product::sqlStock('p', 0).'
				LEFT JOIN `'._DB_PREFIX_.'category_lang` cl
					ON (product_shop.`id_category_default` = cl.`id_category`
					AND cl.`id_lang` = '.(int) $idLang.Shop::addSqlRestrictionOnLang('cl').')
				LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
					ON (p.`id_product` = pl.`id_product`
					AND pl.`id_lang` = '.(int) $idLang.Shop::addSqlRestrictionOnLang('pl').')
				LEFT JOIN `'._DB_PREFIX_.'image_shop` image_shop
					ON (image_shop.`id_product` = p.`id_product` AND image_shop.cover=1 AND image_shop.id_shop='.(int)$context->shop->id.')
				LEFT JOIN `'._DB_PREFIX_.'image_lang` il
					ON (image_shop.`id_image` = il.`id_image`
					AND il.`id_lang` = '.(int) $idLang.')
				LEFT JOIN `'._DB_PREFIX_.'manufacturer` m
					ON m.`id_manufacturer` = p.`id_manufacturer`
				WHERE  product_shop.`active` = 1 AND product_shop.`id_shop` = '.(int) $context->shop->id.''
				//.(!empty($catids) ? ' AND product_shop.`id_category_default` IN  ('. $catids.')' : '') // original query
				.(!empty($catids) ? ' AND cp.`id_category` IN  ('. $catids.')' : '') // webinsect custom query 
				.($front ? ' AND product_shop.`visibility` IN ("both", "catalog")' : '')
				.($idSupplier ? ' AND p.id_supplier = '.(int)$idSupplier : '');

        if ($random === true) {
            $sql .= ' ORDER BY RAND() LIMIT '.(int) $randomNumberProducts;
        } else {
            $sql .= ' ORDER BY '.(!empty($orderByPrefix) ? $orderByPrefix.'.' : '').'`'.bqSQL($orderyBy).'` '.pSQL($orderWay).'
			LIMIT '.(((int) $p - 1) * (int) $n).','.(int) $n;
        }
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS($sql, true, false);

        if (!$result) {
            return array();
        }

        if ($orderyBy == 'orderprice') {
            Tools::orderbyPrice($result, $orderWay);
        }

        return Product::getProductsProperties($idLang, $result);
	}
	
	public function renderWidget($hookName = null, array $configuration = [])
    {
		//$this->_clearCache('*');

		$cache_key = $this->name.'-products';
        $cache_id = $this->getCacheId($cache_key);

		$variables = $this->getWidgetVariables($hookName, $configuration);
		if (empty($variables)) {
			return false;
		}
		$this->smarty->assign($variables);

		
		if($hookName == 'displayFilterproducts7'){
			return $this->fetch($this->templateFile1);
		} else{
			return $this->fetch($this->templateFile);
		}
		
    }

    public function getWidgetVariables($hookName = null, array $configuration = [])
    {
		
		if($hookName == 'displayFilterproducts1'){
			$link_name = Context::getContext()->link->getPageLink('best-sales');
		} elseif($hookName == 'displayFilterproducts5'){
			$link_name = Context::getContext()->link->getPageLink('new-products');
		} else{
			$link_name = '';
		}
		
		
		$list = $this->getItemInHook($hookName);
        if (!empty($list)) {
            return array(
                'list' => $list,
				'id_lang' => $this->context->language->id,
				'base_dir'            => _PS_BASE_URL_.__PS_BASE_URI__,
				'allLinkURL' => $link_name,
            );
        }
        return false;
    }
	
	private function getItemInHook($hookName)
	{
		
		$list = [];
		$this->context = Context::getContext();
		$id_shop = $this->context->shop->id;
		$id_hook = Hook::getIdByName ($hookName);

		
		if ($id_hook)
		{
			$results = Db::getInstance ()->ExecuteS ('
			SELECT b.`id_spfilterproducts`
			FROM `'._DB_PREFIX_.'spfilterproducts` b
			LEFT JOIN `'._DB_PREFIX_.'spfilterproducts_shop` bs ON (b.`id_spfilterproducts` = bs.`id_spfilterproducts`)
			WHERE bs.`active` = 1 AND (bs.`id_shop` = '.$id_shop.') AND b.`hook` = '.( $id_hook ).'
			ORDER BY b.`position`');
			$language_site = '';
			foreach (Language::getLanguages(false) as $lang)
			{
				if ($lang['id_lang'] == $this->context->language->id)
				{
					if ($lang['is_rtl'] == 1)
						$language_site = 'true';
					else
						$language_site = 'false';
				}
			}
			foreach ($results as $row)
			{
				$data = new SpFilterProductsClass($row['id_spfilterproducts']);
				$tmp = [];
				$tmp['params'] = unserialize($data->params);
				$tmp['params']['SPFP_TITLE_MODULE'] = $data->title_module;
				$tmp['params']['SPFP_IDMODULE'] = $row['id_spfilterproducts'];
				$tmp['products'] = $this->getProducts ($tmp['params']);
				$tmp['language_site'] = $language_site;
				$list[] = $tmp;
			}
		}
		if (empty( $list ))
			return;
		return $list;
	}
	
	public function hookdisplayProductCountDown($param){
		$product = $param['product'];

		if (isset($param['countdown_style'])){
			$style = $param['countdown_style'];
		}else{
			$style = 1;
		}
		
		$showPriceToDate = false;
		$days_percent = 0;
		$specific_prices = isset($product['specific_prices']) ? $product['specific_prices'] : false;
		if ($specific_prices  && strtotime ($specific_prices['to']) != false)
		{
			$current = date ('Y-m-d H:i:s');
			$start_date = strtotime ($specific_prices['from']) != false ? date ('Y-m-d H:i:s', strtotime ($specific_prices['from'])) : '0000-00-00 00:00:00';
			$date_end = date ('Y-m-d H:i:s', strtotime ($specific_prices['to']));
			$days_total = (strtotime($date_end) - strtotime($start_date))/86400; /* 60*60*24 */
			$days_left = (strtotime($date_end) - strtotime($current))/86400;
			$days_percent = ($days_left/$days_total)*100;
			
			if (strtotime ($date_end) >= strtotime ($current) && strtotime ($start_date) <= strtotime ($date_end)){
				$product['specialPriceToDate'] = $date_end;
				$showPriceToDate = true;
			}
			$this->smarty->assign(array('specialPriceToDate' => $product['specialPriceToDate'],
										'days_percent' => $days_percent,
										'days_left' => (int)$days_left,
										'showPriceToDate' => $showPriceToDate,
										
			));
				return $this->fetch('module:spfilterproducts/views/templates/hook/extends_countdown.tpl');
				
			
		}
		return;
	}
	
	public function hookActionShopDataDuplication($params)
	{
		Db::getInstance ()->execute ('
		INSERT IGNORE INTO `'._DB_PREFIX_.'spfilterproducts_shop` (`id_spfilterproducts`, `id_shop`)
		SELECT `id_spfilterproducts`, '.(int)$params['new_id_shop'].'
		FROM `'._DB_PREFIX_.'spfilterproducts_shop`
		WHERE `id_shop` = '.(int)$params['old_id_shop']);
	}
	
	public function hookdisplayHeader($params)
    {
		if (!defined('COUNTDOWN_TIMER')){
			$this->context->controller->registerJavascript('modules-countdown', 'modules/'.$this->name.'/views/js/front/jquery.countdown.js', ['position' => 'bottom', 'priority' => 150]);
			define('COUNTDOWN_TIMER',1);
		}
		$this->context->controller->registerStylesheet('modules-spfilterproducts1', 'modules/'.$this->name.'/views/css/front/owl.carousel.css', ['position' => 'top', 'priority' => 150]);
		$this->context->controller->registerStylesheet('modules-spfilterproducts', 'modules/'.$this->name.'/views/css/front/spfilterproducts.css', ['media' => 'all', 'priority' => 150]);
		
        $this->context->controller->registerJavascript('modules-spfilterproducts2', 'modules/'.$this->name.'/views/js/front/spfilterproducts.js', ['position' => 'bottom', 'priority' => 150]);
		$this->context->controller->registerJavascript('modules-spfilterproducts', 'modules/'.$this->name.'/views/js/front/owl.carousel.js', ['position' => 'bottom', 'priority' => 150]);
    }

}
