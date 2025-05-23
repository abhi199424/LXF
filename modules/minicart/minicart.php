<?php
/**
* 2007-2022 PrestaShop
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
*  @copyright 2007-2022 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Adapter\Cart\CartPresenter;

class Minicart extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'minicart';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'DevAb';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Mini Cart');
        $this->description = $this->l('Showing a list of cart products');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    /**
     * Don't forget to create update methods if needed:
     * http://doc.prestashop.com/display/PS16/Enabling+the+Auto-Update
     */
    public function install()
    {
        return parent::install() &&
            $this->registerHook('header') &&
            $this->registerHook('displayMinicart');
    }

    public function uninstall()
    {
        return parent::uninstall();
    }

    public function hookHeader()
    {
        $this->context->controller->addJS($this->_path.'/views/js/front.js');
        $this->context->controller->addCSS($this->_path.'/views/css/front.css');
    }

    public function hookDisplayMinicart()
    {
        $data = (new CartPresenter())->present(Context::getContext()->cart);

        $product = null;
        foreach ($data['products'] as $p) {
            $product = $p;
        }
        $this->smarty->assign([
            'product' => $product,
            'cart' => $data,
            'cart_url' => $this->getCartSummaryURL(),
        ]);
        return $this->display(__FILE__, 'views/templates/hook/minicart.tpl');
    }

    public function hookDisplayMinicartInner()
    {
        $data = (new CartPresenter())->present(Context::getContext()->cart);

        $product = null;
        foreach ($data['products'] as $p) {
            $product = $p;
        }
        $this->smarty->assign([
            'product' => $product,
            'cart' => $data,
            'cart_url' => $this->getCartSummaryURL(),
        ]);
        return $this->display(__FILE__, 'views/templates/hook/minicartinner.tpl');
    }

    public function hookDisplayMinicartcount()
    {
        $data = (new CartPresenter())->present(Context::getContext()->cart);
        $count_data = $data['products_count'];
        return $count_data;
    }

    private function getCartSummaryURL()
    {
        return $this->context->link->getPageLink(
            'cart',
            null,
            $this->context->language->id,
            [
                'action' => 'show',
            ],
            false,
            null,
            true
        );
    }
}
