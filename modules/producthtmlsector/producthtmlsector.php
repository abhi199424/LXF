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

class Producthtmlsector extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'producthtmlsector';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'DevAbhi';
        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Product HTML Section');
        $this->description = $this->l('module display custom HTML content');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('actionProductUpdate') &&
            $this->registerHook('displayAdminProductsExtra') &&
            $this->registerHook('displayProductExtraContent') &&
            $this->createDatabaseTable();
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->deleteDatabaseTable();
    }

    private function createDatabaseTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS `" . _DB_PREFIX_ . "customproductwidget` (
            `id_widget` INT AUTO_INCREMENT PRIMARY KEY,
            `id_product` INT NOT NULL,
            `custom_html` TEXT NOT NULL,
            INDEX (`id_product`)
        ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";
        
        return Db::getInstance()->execute($sql);
    }

    private function deleteDatabaseTable()
    {
        return Db::getInstance()->execute("DROP TABLE IF EXISTS `" . _DB_PREFIX_ . "customproductwidget`");
    }

    public function hookDisplayProductExtraContent($params)
    {
        $id_product = (int)$params['product']->id_product;
        $html = $this->getProductHtmlContent($id_product);

        if (empty($html)) {
            return '';
        }

        $this->context->smarty->assign('custom_html', json_decode($html));
        return $this->display(__FILE__, 'views/templates/hook/displayProductWidget.tpl');
    }

    private function getProductHtmlContent($id_product)
    {
        $sql = "SELECT custom_html FROM `" . _DB_PREFIX_ . "customproductwidget` WHERE id_product = " . (int)$id_product;
        return Db::getInstance()->getValue($sql);
    }

    public function hookActionProductUpdate($params)
    {
        $id_product = (int)$params['id_product'];
        $custom_html = Tools::getValue('custom_html');

        $jsonData = json_encode($custom_html, JSON_HEX_QUOT | JSON_HEX_TAG);
        Db::getInstance()->execute("
            INSERT INTO `" . _DB_PREFIX_ . "customproductwidget` (id_product, custom_html) 
            VALUES (" . (int)$id_product . ", '" . pSQL($jsonData) . "') 
            ON DUPLICATE KEY UPDATE custom_html = '" . pSQL($jsonData) . "'
        ");
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        $id_product = (int)$params['id_product'];
        $html_content = $this->getProductHtmlContent($id_product);

        $this->context->smarty->assign('custom_html', json_decode($html_content));
        return $this->display(__FILE__, 'views/templates/admin/product_widget.tpl');
    }
}
