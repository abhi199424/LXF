<?php
/**
* 2007-2025 PrestaShop
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
*  @copyright 2007-2025 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;

class Listcontent extends Module implements WidgetInterface
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'listcontent';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'DevAbhi';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('List Content');
        $this->description = $this->l('option to include HTML in custom product page');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {

        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayBackOfficeHeader') && 
            $this->installDB() && 
            $this->installTab();
    }

    private function installDB()
    {
        return Db::getInstance()->execute('
            CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'custom_listcontent` (
                `id_custom_list` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(255) NOT NULL,
                `id_categories` TEXT,
                `product_ids` TEXT,
                `html_content` TEXT
            ) ENGINE='._MYSQL_ENGINE_.' DEFAULT CHARSET=utf8;
        ');
    }

    private function uninstallDB()
    {
        return Db::getInstance()->execute('DROP TABLE IF EXISTS `'._DB_PREFIX_.'custom_listcontent`');
    }

    private function installTab()
    {
        $tab = new Tab();
        $tab->active = 1;
        $tab->class_name = 'AdminCustomListContent';
        $tab->name = [];
        foreach (Language::getLanguages(true) as $lang)
            $tab->name[$lang['id_lang']] = 'Shipping Infos';
        $tab->id_parent = (int) Tab::getIdFromClassName('IMPROVE');
        $tab->module = $this->name;
        return $tab->add();
    }

    private function uninstallTab()
    {
        $id_tab = (int) Tab::getIdFromClassName('AdminCustomListContent');
        if ($id_tab) {
            $tab = new Tab($id_tab);
            return $tab->delete();
        }
        return true;
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->uninstallDB() && $this->uninstallTab();
    }

    public function hookDisplayBackOfficeHeader()
    {
        if (Tools::getValue('configure') == $this->name) {
            $this->context->controller->addJS($this->_path.'views/js/back.js');
            $this->context->controller->addCSS($this->_path.'views/css/back.css');
        }
    }

    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function renderWidget($hookName, array $configuration)
    {
        $variables = $this->getWidgetVariables($hookName, $configuration);

        $this->context->smarty->assign($variables);

        return $this->display(__FILE__, 'views/templates/hook/displayCustomListContent.tpl');
    }

    public function getWidgetVariables($hookName, array $configuration)
    {
        $id_product = isset($configuration['id_product']) ? (int)$configuration['id_product'] : (int)Tools::getValue('id_product');
        $id_category = isset($configuration['id_category']) ? (int)$configuration['id_category'] : (int)Tools::getValue('id_category');
        $selected_html = '';

        $sql = 'SELECT * FROM `'._DB_PREFIX_.'custom_listcontent`';
        $lists = Db::getInstance()->executeS($sql);

        // 1. Try matching by category first
        foreach ($lists as $list) {
            $cat_ids = array_map('intval', array_map('trim', explode(',', $list['id_category'])));
            if (in_array($id_category, $cat_ids)) {
                $selected_html = $list['html_content'];
                break;
            }
        }

        // 2. Fallback: match by product
        if (empty($selected_html)) {
            foreach ($lists as $list) {
                $prod_ids = array_map('intval', array_map('trim', explode(',', $list['product_ids'])));
                if (in_array($id_product, $prod_ids)) {
                    $selected_html = $list['html_content'];
                    break;
                }
            }
        }

        return [
            'custom_html' => $selected_html,
        ];
    }
}
