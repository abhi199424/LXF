<?php
/**
* 2007-2020 PrestaShop
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
*  @copyright 2007-2020 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}
use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
class Utilisationreco extends Module implements WidgetInterface
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'utilisationreco';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'Abhiz';
        $this->need_instance = 0;

        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Utilisation Recommandation');
        $this->description = $this->l('Custom technical HTML for product section');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('backOfficeHeader') &&
            $this->registerHook('displayAdminProductsExtra') &&
            $this->registerHook('actionProductUpdate');
    }

    public function hookBackOfficeHeader()
    {
        $this->context->controller->addJS($this->_path.'views/js/back.js');
        $this->context->controller->addCSS($this->_path.'views/css/back.css');
    }

    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookDisplayAdminProductsExtra($params)
    {
        $arrayHtmlData = $this->getManualTechData($params['id_product']);
        $id_preset = Configuration::get('UMT_PRODUCT_'.$params['id_product'].'_PRESET');

        $this->context->smarty->assign('UMT_html', $arrayHtmlData);
        $this->context->smarty->assign('id_preset', $id_preset);
        
        return $this->display(__FILE__, 'views/templates/admin/configure.tpl');
    }

    public function hookActionProductUpdate($params) 
    {
        $jsonData = json_encode(Tools::getValue('UMT_DATA_TECHNIQUE'), JSON_HEX_QUOT | JSON_HEX_TAG);
        if ($jsonData != 'false') {
            Configuration::updateValue(
                'UMT_DATA_TECHNIQUE-'.$params['id_product'],
                $jsonData,
                true
            );
        }
    }

    public function getManualTechData($idProduct)
    {        
        $arrayHtmlData = [];
        $jsonData = Configuration::get('UMT_DATA_TECHNIQUE-'.$idProduct);
        return json_decode($jsonData, true);
    }

    public function renderWidget($hookName, array $configuration) 
    {
        $this->smarty->assign($this->getWidgetVariables($hookName, $configuration));
        
        return $this->fetch('module:'.$this->name.'/views/templates/hook/pmtdatadisplay.tpl');
    }
 
    public function getWidgetVariables($hookName, array $configuration)
    {
        $id_product = $configuration['id_product'] ?? null;
        $arrayHtmlData = $this->getManualTechData($id_product);

        return [
            'UMT_html' => $arrayHtmlData,
        ];
    }
}
