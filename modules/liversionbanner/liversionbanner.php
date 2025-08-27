<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class Liversionbanner extends Module
{
    public function __construct()
    {
        $this->name = 'liversionbanner';
        $this->version = '1.0.0';
        $this->author = 'DevAbhi';
        $this->tab = 'front_office_features';
        $this->need_instance = 0;
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Liversion Banner');
        $this->description = $this->l('Displays banner based on category or product selection.');
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('displayLiveVersionBanner') &&
            Configuration::updateValue('LVB_TITLE', '') &&
            Configuration::updateValue('LVB_HTML', '') &&
            Configuration::updateValue('LVB_CATEGORY', '') &&
            Configuration::updateValue('LVB_PRODUCTS', '');
    }

    public function uninstall()
    {
        return parent::uninstall() &&
            Configuration::deleteByName('LVB_TITLE') &&
            Configuration::deleteByName('LVB_HTML') &&
            Configuration::deleteByName('LVB_CATEGORY') &&
            Configuration::deleteByName('LVB_PRODUCTS');
    }

    public function getContent()
    {
        $output = '';

        if (Tools::isSubmit('submitLVBConfig')) {
            $title = Tools::getValue('LVB_TITLE');
            $html = Tools::getValue('LVB_HTML');
            $categories = Tools::getValue('LVB_CATEGORY');
            $products = Tools::getValue('LVB_PRODUCTS');

            Configuration::updateValue('LVB_TITLE', $title);
            Configuration::updateValue('LVB_HTML', $html, true);
            Configuration::updateValue('LVB_CATEGORY', is_array($categories) ? implode(',', $categories) : '');
            Configuration::updateValue('LVB_PRODUCTS', $products);

            $output .= $this->displayConfirmation($this->l('Settings updated'));
        }

        return $output.$this->renderForm();
    }

    protected function renderForm()
    {
        $defaultLang = (int)Configuration::get('PS_LANG_DEFAULT');

        $fieldsForm[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Settings'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Title'),
                    'name' => 'LVB_TITLE',
                ),
                array(
                    'type' => 'textarea',
                    'autoload_rte' => true,
                    'label' => $this->l('HTML Content'),
                    'name' => 'LVB_HTML',
                ),
                array(
                    'type' => 'categories',
                    'label' => $this->l('Select Categories'),
                    'name' => 'LVB_CATEGORY',
                    'tree' => array(
                        'id' => 'categories-tree',
                        'use_checkbox' => true,
                        'selected_categories' => explode(',', Configuration::get('LVB_CATEGORY')),
                    ),
                ),
                array(
                    'type' => 'text',
                    'label' => $this->l('Product IDs (comma separated)'),
                    'name' => 'LVB_PRODUCTS',
                ),
            ),
            'submit' => array(
                'title' => $this->l('Save'),
            ),
        );

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $helper->module = $this;
        $helper->default_form_language = $defaultLang;
        $helper->allow_employee_form_lang = (int)Configuration::get('PS_BO_ALLOW_EMPLOYEE_FORM_LANG');
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitLVBConfig';
        $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
        $helper->token = Tools::getAdminTokenLite('AdminModules');

        $helper->fields_value['LVB_TITLE'] = Configuration::get('LVB_TITLE');
        $helper->fields_value['LVB_HTML'] = Configuration::get('LVB_HTML');
        $helper->fields_value['LVB_CATEGORY'] = explode(',', Configuration::get('LVB_CATEGORY'));
        $helper->fields_value['LVB_PRODUCTS'] = Configuration::get('LVB_PRODUCTS');

        return $helper->generateForm($fieldsForm);
    }

    public function hookDisplayLiveVersionBanner($params)
    {
        $selectedCategories = array_filter(explode(',', Configuration::get('LVB_CATEGORY')));
        $selectedProducts   = array_filter(explode(',', Configuration::get('LVB_PRODUCTS')));

        $passCategory = isset($params['id_category']) ? (int)$params['id_category'] : 0;
        $passProduct  = isset($params['id_product']) ? (int)$params['id_product'] : 0;

        $showBanner = false;

        // Show if either product matches OR category matches
        if ($passProduct && in_array($passProduct, $selectedProducts)) {
            $showBanner = true;
        } elseif ($passCategory && in_array($passCategory, $selectedCategories)) {
            $showBanner = true;
        }

        if ($showBanner) {
            $this->context->smarty->assign(array(
                'lvb_title' => Configuration::get('LVB_TITLE'),
                'lvb_html'  => Configuration::get('LVB_HTML'),
            ));

            return $this->display(__FILE__, 'views/templates/hook/liversionbanner.tpl');
        }

        return '';
    }
}
