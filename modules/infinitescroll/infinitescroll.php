<?php
/**
 * Infinite scroll premium
 *
 * @author    Studio Kiwik
 * @copyright Studio Kiwik 2014-2017
 * @license   http://licences.studio-kiwik.fr/infinitescroll
 */

class InfiniteScroll extends Module
{
    public $bootstrap = true;

    public $a_supported_pages = array();
    
    public function __construct()
    {
        $this->name = 'infinitescroll';
        $this->tab = 'front_office_features';
        $this->version = '1.1.17';
        $this->author = 'Studio Kiwik';
        $this->module_key = '9841824c9bda52a0dea53022553a78c6';
        $this->need_instance = 0;
        parent::__construct();
        $this->displayName = $this->l('Infinite Scroll Premium');
        $this->description =
        $this->l('Enhance your customer\'s navigation by getting rid of pagination in favor of infinite scroll.');
        $this->secure_key = Tools::encrypt($this->name);
        $this->ps_versions_compliancy = array(
            'min' => '1.5.1.0',
            'max' => _PS_VERSION_ . '.99',
        );

        $this->a_supported_pages = array(
            'category', 'search', 'best-sales', 'new-products',
            'prices-drop', 'manufacturer', 'supplier', 'advancedsearch4', 'kiwik_filters', 'kwkwishlist'
        );
    }
    public function install()
    {
        $result = true;
        // Activate every available pages
        foreach ($this->a_supported_pages as $s_page_name) {
            $result &= Configuration::updateValue('INFSCRL_ACTIVE_' . Tools::strtoupper($s_page_name), 1);
        }
        return $result && parent::install()
        && $this->registerHook('header')
        && $this->registerHook('displayHeader')
        && $this->registerHook('displayTop')
        && $this->registerHook('top')
        && Configuration::updateValue('INFSCRL_SANDBOX_MODE', 1)
        && Configuration::updateValue('INFSCRL_SANDBOX_IP', $_SERVER['REMOTE_ADDR'])
        && Configuration::updateValue(
            'INFSCRL_CENTRAL_SELECTOR',
            Tools::version_compare(_PS_VERSION_, '1.7', '<') ? '#center_column' : '#content-wrapper'
        )
        && Configuration::updateValue(
            'INFSCRL_SELECTOR',
            Tools::version_compare(_PS_VERSION_, '1.7', '<') ? '.product_list' : (Tools::version_compare(_PS_VERSION_, '1.7.7.0', '<') ? '#products' : '.products')
        )
        && Configuration::updateValue(
            'INFSCRL_ITEM_SELECTOR',
            Tools::version_compare(_PS_VERSION_, '1.7', '<') ? 'li.ajax_block_product' : (Tools::version_compare(_PS_VERSION_, '1.7.7.0', '<') ? 'article' : 'div[itemprop="itemListElement"]')
        )
        && Configuration::updateValue(
            'INFSCRL_PAGINATION_SELECTOR',
            Tools::version_compare(_PS_VERSION_, '1.7', '<') ?
            '.bottom-pagination-content, .top-pagination-content' :
            '.pagination'
        )
        && Configuration::updateValue(
            'INFSCRL_DEFAULT_PAGE_PARAMETER',
            Tools::version_compare(_PS_VERSION_, '1.7', '<') ? 'p' : 'page'
        )
        && Configuration::updateValue('INFSCRL_HIDE_BUTTON', 0)
        && Configuration::updateValue('INFSCRL_BORDER', '#F5F5F5')
        && Configuration::updateValue('INFSCRL_BG', '#D3D3D3')
        && Configuration::updateValue('INFSCRL_POLICE', '#858585')
        && Configuration::updateValue('INFSCRL_STOP_BOTTOM', 0)
        && Configuration::updateValue('INFSCRL_STOP_BOTTOM_PAGE', 2)
        && Configuration::updateValue('INFSCRL_STOP_BOTTOM_FREQ', 0)
        && (Tools::version_compare(_PS_VERSION_, '1.6', '<') ? true : Configuration::updateValue('INFSCRL_JS_SCRIPT_PROCESS_PRODUCTS', ''))//vilaine 1.5 je ne t'aime pas
        && Configuration::updateValue('INFSCRL_JS_SCRIPT_AFTER', '');
    }

    public function hookDisplayTop($params)
    {
        if ($this->canAccess() && Configuration::get('INFSCRL_SANDBOX_MODE')) {
            return $this->display(__FILE__, 'hookDisplayTop.tpl');
        }
    }

    public function canAccess()
    {
        $aIps = explode(',', Configuration::get('INFSCRL_SANDBOX_IP'));
        $aIps = array_map("trim", $aIps);
        return !Configuration::get('INFSCRL_SANDBOX_MODE') || in_array($_SERVER['REMOTE_ADDR'], $aIps);
    }

    public function getPathImage($img)
    {
        return $this->local_path . 'views/img/' . $img;
    }

    public function thumbImage()
    {
        return (Configuration::get('INFSCRL_IMG')
            && file_exists($this->getPathImage(Configuration::get('INFSCRL_IMG')))) ?
        $this->_path . 'views/img/' . Configuration::get('INFSCRL_IMG') :
        $this->_path . 'views/img/loader.gif';
    }

    public function postImage()
    {
        $output = '';
        $load_img = Tools::getValue('INFSCRL_IMG');
        if (isset($_FILES['INFSCRL_LOADER_IMAGE']) && is_uploaded_file($_FILES['INFSCRL_LOADER_IMAGE']['tmp_name'])) {
            if ($error = ImageManager::validateUpload(
                $_FILES['INFSCRL_LOADER_IMAGE'],
                (Configuration::get('PS_ATTACHMENT_MAXIMUM_SIZE') * 1024 * 1024)
            )
            ) {
                $output .= $this->displayError($error);
            }

            $upload_path = $this->local_path . 'views/img/';
            $pathinfo = pathinfo($_FILES['INFSCRL_LOADER_IMAGE']['name']);
            do {
                $uniqid = sha1(microtime());
            } while (file_exists($upload_path . $uniqid . '.' . $pathinfo['extension']));

            if (!copy(
                $_FILES['INFSCRL_LOADER_IMAGE']['tmp_name'],
                $upload_path . $uniqid . '.' . $pathinfo['extension']
            )) {
                $output .= $this->displayError($this->l('File copy failed'));
            }

            @unlink($_FILES['file']['tmp_name']);

            if ($load_img && file_exists($upload_path . $load_img)) {
                unlink($upload_path . $load_img);
            }

            $load_img = $uniqid . '.' . $pathinfo['extension'];

            Configuration::updateValue('INFSCRL_IMG', $load_img);
        }
        return $output;
    }

    public function hookDisplayHeader($params)
    {
        //handle sandbox
        if (!$this->canAccess()) {
            return;
        }
        //if we passed the sandbox then we should display the module
        try {
            $this->context->controller->addCSS(($this->_path) . 'views/css/infinitescroll.css', 'all');
            if (!empty($this->context->controller->page_name)) {
                $page_name = $this->context->controller->page_name;
            } elseif (!empty($this->context->controller->php_self)) {
                $page_name = $this->context->controller->php_self;
            } elseif (preg_match(
                '#^'.preg_quote($this->context->shop->physical_uri, '#').'module/([a-zA-Z0-9_-]+?)/(.*)$#',
                $_SERVER['REQUEST_URI'],
                $m
            )) {
                $page_name = 'module-'.$m[1].'-'.str_replace(array('.php', '/'), array('', '-'), $m[2]);
            } else {
                $page_name = Dispatcher::getInstance()->getController();
                $page_name = (preg_match('/^[0-9]/', $page_name) ? 'page_'.$page_name : $page_name);
            }

            //cas special AS4
            //CB ajout derniere occurence pour compat AS5
            $as4_pages = array('module-pm_advancedsearch4-searchresults', 'module-pm_advancedsearch4-seo', 'module-pm_advancedsearch-searchresults');
            if (in_array($page_name, $as4_pages)) {
                $page_name = 'advancedsearch4';
            }
            //cas jolisearch
            if ($page_name == 'module-ambjolisearch-jolisearch') {
                $page_name = 'search';
            }
            //cas kiwik filters
            if ($page_name == 'module-kiwik_filters-default') {
                $page_name = 'kiwik_filters';
            }

            //cas kwkwishlist
            if ($page_name == 'module-kwkwishlist-wishlist') {
                $page_name = 'kwkwishlist';    
            }

            if (Configuration::get('INFSCRL_ACTIVE_' . Tools::strtoupper($page_name))
                && ($page_name != "manufacturer" || Tools::getValue('id_manufacturer') != false)
                && ($page_name != "supplier" || Tools::getValue('id_supplier') != false)) {
                //ScrollHandlerFactory::create($sType)->setSmarty($this->smarty)->hookHeader();
                //$this->context->controller->addJS($this->_path . 'views/js/infinitescroll.js');
                $this->context->controller->addJS($this->_path . 'views/js/infinitescroll_plugin.js');

                 //@fix 17/09/2015 pour ajouter le # au début si absent
                $config_colors = array(
                    'border_color' => Configuration::get('INFSCRL_BORDER'),
                    'bg_color' => Configuration::get('INFSCRL_BG'),
                    'font_color' => Configuration::get('INFSCRL_POLICE')
                );
                foreach ($config_colors as $key => $value) {
                    if (strpos($value, '#') === false) {
                        $config_colors[$key] = '#'.$value;
                    }
                    //@fix 13/01/2015 pour le retirer si il est en double, triple, ou autre
                    $config_colors[$key] = preg_replace('|\#+|', '#', $value);
                }

                $this->smarty->assign(array(
                    'infinitescroll_selector' => Configuration::get('INFSCRL_SELECTOR'),
                    'infinitescroll_button' => Configuration::get('INFSCRL_HIDE_BUTTON'),
                    'infinitescroll_border' => $config_colors['border_color'],
                    'infinitescroll_background' => $config_colors['bg_color'],
                    'infinitescroll_police' => $config_colors['font_color'],
                    'infinitescroll_stop_bottom' => Configuration::get('INFSCRL_STOP_BOTTOM'),
                    'infinitescroll_stop_bottom_page' => Configuration::get('INFSCRL_STOP_BOTTOM_PAGE'),
                    'infinitescroll_stop_bottom_freq' => Configuration::get('INFSCRL_STOP_BOTTOM_FREQ'),
                    'infinitescroll_item_selector' => Configuration::get('INFSCRL_ITEM_SELECTOR'),
                    'infinitescroll_image' => $this->thumbImage(),
                    'infinitescroll_central_selector' => Configuration::get('INFSCRL_CENTRAL_SELECTOR'),
                    'infinitescroll_pagination_selector' => Configuration::get('INFSCRL_PAGINATION_SELECTOR'),
                    'current_page' => Tools::getValue(Configuration::get('INFSCRL_DEFAULT_PAGE_PARAMETER'), 1),
                    'infinitescroll_default_page_parameter' => Configuration::get('INFSCRL_DEFAULT_PAGE_PARAMETER'),
                    'infinitescroll_text_label_bottom_page' => $this->l('We have reached the bottom end of this page'),
                    'infinitescroll_text_label_totop' => $this->l('Go back to top'),
                    'infinitescroll_text_error' =>
                    $this->l('It looks like something wrong happened and we can not display further products'),
                    'infinitescroll_text_loadmore' => $this->l('Load more products'),
                    'infinitescroll_version' => $this->version,
                    'is_blocklayered_loaded' => (int) Module::isEnabled('blocklayered')
                        || (int) Module::isEnabled('blocklayered_mod') || (int) Module::isEnabled('ps_facetedsearch'),
                    'js_script_after' => Configuration::get('INFSCRL_JS_SCRIPT_AFTER'),
                    'js_script_process' => Configuration::get('INFSCRL_JS_SCRIPT_PROCESS_PRODUCTS'),
                    'debug_mode' => Configuration::get('INFSCRL_SANDBOX_MODE'),
                    'instant_search' => Configuration::get('PS_INSTANT_SEARCH'),
                    'ps_version' => _PS_VERSION_,
                    'shop_base_uri' => Context::getContext()->shop->getBaseURL(true),
                ));

                return $this->display(__FILE__, 'views/templates/hook/hookHeader.tpl');
            }
        } catch (Exception $e) {
            return null;
        }
    }
    public function getFooter()
    {
        return $this->getTranslatedAdminTemplate('footer');
    }

    protected function getTranslatedAdminTemplate($template, $default_lang_iso_code = 'en')
    {
        $template = Tools::strtolower($template);
        if (isset($this->bootstrap) && $this->bootstrap && version_compare('1.6', _PS_VERSION_, '<')) {
            $bootstrap_ext = '.bootstrap';
        } else {
            $bootstrap_ext = '.no_bootstrap';
        }

        $iso_codes = array_filter(
            array(
                Tools::strtolower(Context::getContext()->language->iso_code),
                Tools::strtolower($default_lang_iso_code),
            )
        );

        foreach ($iso_codes as $lang_iso_code) {
            $path = '/views/templates/admin/' . $template . '_' . $lang_iso_code . $bootstrap_ext . '.tpl';

            if (file_exists(dirname(__FILE__) . $path)) {
                $this->smarty->assign(array(
                    'module' => $this,
                    'module_path' => Context::getContext()->shop->getBaseURL(true) . '/modules/' . $this->name,
                ));

                return $this->display(__FILE__, $path);
            }
        }
        return '';
    }

    private function displayInfos()
    {
        if (Configuration::get('INFSCRL_SANDBOX_MODE') == '1') {
            return $this->_html = '<div class="alert alert-warning">' .
            $this->l('Be careful, you are on sandbox mode') . '</div>';
        }
    }

    public function getContent()
    {
        $output = '';
        if (Tools::isSubmit('submitInfiniteScroll')) {
            $output = $this->postImage();
            Configuration::updateValue(
                'INFSCRL_SANDBOX_MODE',
                Tools::getValue('INFSCRL_SANDBOX_MODE', Configuration::get('INFSCRL_SANDBOX_MODE'))
            );
            Configuration::updateValue(
                'INFSCRL_SANDBOX_IP',
                Tools::getValue('INFSCRL_SANDBOX_IP', Configuration::get('INFSCRL_SANDBOX_IP'))
            );
            Configuration::updateValue(
                'INFSCRL_LOADER_IMAGE',
                Tools::getValue('INFSCRL_LOADER_IMAGE', Configuration::get('INFSCRL_LOADER_IMAGE'))
            );
            Configuration::updateValue(
                'INFSCRL_HIDE_BUTTON',
                Tools::getValue('INFSCRL_HIDE_BUTTON', Configuration::get('INFSCRL_HIDE_BUTTON'))
            );
            Configuration::updateValue(
                'INFSCRL_CENTRAL_SELECTOR',
                Tools::getValue('INFSCRL_CENTRAL_SELECTOR', Configuration::get('INFSCRL_CENTRAL_SELECTOR'))
            );
            Configuration::updateValue(
                'INFSCRL_SELECTOR',
                Tools::getValue('INFSCRL_SELECTOR', Configuration::get('INFSCRL_SELECTOR'))
            );
            Configuration::updateValue(
                'INFSCRL_ITEM_SELECTOR',
                Tools::getValue('INFSCRL_ITEM_SELECTOR', Configuration::get('INFSCRL_ITEM_SELECTOR'))
            );
            Configuration::updateValue(
                'INFSCRL_PAGINATION_SELECTOR',
                Tools::getValue('INFSCRL_PAGINATION_SELECTOR', Configuration::get('INFSCRL_PAGINATION_SELECTOR'))
            );
            Configuration::updateValue(
                'INFSCRL_BG',
                Tools::getValue('INFSCRL_BG', Configuration::get('INFSCRL_BG'))
            );
            Configuration::updateValue(
                'INFSCRL_POLICE',
                Tools::getValue('INFSCRL_POLICE', Configuration::get('INFSCRL_POLICE'))
            );
            Configuration::updateValue(
                'INFSCRL_STOP_BOTTOM',
                Tools::getValue('INFSCRL_STOP_BOTTOM', Configuration::get('INFSCRL_STOP_BOTTOM'))
            );
            Configuration::updateValue(
                'INFSCRL_STOP_BOTTOM_PAGE',
                (int) Tools::getValue('INFSCRL_STOP_BOTTOM_PAGE', Configuration::get('INFSCRL_STOP_BOTTOM_PAGE'))
            );
            Configuration::updateValue(
                'INFSCRL_STOP_BOTTOM_FREQ',
                (int) Tools::getValue('INFSCRL_STOP_BOTTOM_FREQ', Configuration::get('INFSCRL_STOP_BOTTOM_FREQ'))
            );
            Configuration::updateValue(
                'INFSCRL_BORDER',
                Tools::getValue('INFSCRL_BORDER', Configuration::get('INFSCRL_BORDER'))
            );
            Configuration::updateValue(
                'INFSCRL_JS_SCRIPT_AFTER',
                Tools::getValue('INFSCRL_JS_SCRIPT_AFTER', Configuration::get('INFSCRL_JS_SCRIPT_AFTER')),
                true
            );

            Configuration::updateValue(
                'INFSCRL_JS_SCRIPT_PROCESS_PRODUCTS',
                Tools::getValue(
                    'INFSCRL_JS_SCRIPT_PROCESS_PRODUCTS',
                    Configuration::get('INFSCRL_JS_SCRIPT_PROCESS_PRODUCTS')
                ),
                true
            );

            foreach ($this->a_supported_pages as $s_page_name) {
                $s_key = 'INFSCRL_ACTIVE_' . Tools::strtoupper($s_page_name);
                Configuration::updateValue($s_key, Tools::getValue($s_key, Configuration::get($s_key)));
            }
            $output .= $this->displayConfirmation($this->l('Your settings have been updated.'));
        }
        return $output . $this->displayInfos() . $this->renderForm() . $this->renderAdvancedForm() . $this->getFooter();
    }

    public function getConfigFieldsValues()
    {
        $values = array(
            'INFSCRL_SANDBOX_MODE' => Tools::getValue(
                'INFSCRL_SANDBOX_MODE',
                Configuration::get('INFSCRL_SANDBOX_MODE')
            ),
            'INFSCRL_SANDBOX_IP' => Tools::getValue(
                'INFSCRL_SANDBOX_IP',
                Configuration::get('INFSCRL_SANDBOX_IP')
            ),
            'INFSCRL_LOADER_IMAGE' => Tools::getValue(
                'INFSCRL_LOADER_IMAGE',
                Configuration::get('INFSCRL_LOADER_IMAGE')
            ),
            'INFSCRL_HIDE_BUTTON' => Tools::getValue(
                'INFSCRL_HIDE_BUTTON',
                Configuration::get('INFSCRL_HIDE_BUTTON')
            ),
            'INFSCRL_CENTRAL_SELECTOR' => Tools::getValue(
                'INFSCRL_CENTRAL_SELECTOR',
                Configuration::get('INFSCRL_CENTRAL_SELECTOR')
            ),
            'INFSCRL_SELECTOR' => Tools::getValue(
                'INFSCRL_SELECTOR',
                Configuration::get('INFSCRL_SELECTOR')
            ),
            'INFSCRL_ITEM_SELECTOR' => Tools::getValue(
                'INFSCRL_ITEM_SELECTOR',
                Configuration::get('INFSCRL_ITEM_SELECTOR')
            ),
            'INFSCRL_PAGINATION_SELECTOR' => Tools::getValue(
                'INFSCRL_PAGINATION_SELECTOR',
                Configuration::get('INFSCRL_PAGINATION_SELECTOR')
            ),
            'INFSCRL_BG' => Tools::getValue(
                'INFSCRL_BG',
                Configuration::get('INFSCRL_BG')
            ),
            'INFSCRL_POLICE' => Tools::getValue(
                'INFSCRL_POLICE',
                Configuration::get('INFSCRL_POLICE')
            ),
            'INFSCRL_BORDER' => Tools::getValue(
                'INFSCRL_BORDER',
                Configuration::get('INFSCRL_BORDER')
            ),
            'INFSCRL_IMG' => Tools::getValue(
                'INFSCRL_IMG',
                Configuration::get('INFSCRL_IMG')
            ),
            'INFSCRL_STOP_BOTTOM' => Tools::getValue(
                'INFSCRL_STOP_BOTTOM',
                Configuration::get('INFSCRL_STOP_BOTTOM')
            ),
            'INFSCRL_STOP_BOTTOM_PAGE' => Tools::getValue(
                'INFSCRL_STOP_BOTTOM_PAGE',
                Configuration::get('INFSCRL_STOP_BOTTOM_PAGE')
            ),
            'INFSCRL_STOP_BOTTOM_FREQ' => Tools::getValue(
                'INFSCRL_STOP_BOTTOM_FREQ',
                Configuration::get('INFSCRL_STOP_BOTTOM_FREQ')
            ),
            'INFSCRL_JS_SCRIPT_AFTER' => Tools::getValue(
                'INFSCRL_JS_SCRIPT_AFTER',
                Configuration::get('INFSCRL_JS_SCRIPT_AFTER')
            ),
            'INFSCRL_JS_SCRIPT_PROCESS_PRODUCTS' => Tools::getValue(
                'INFSCRL_JS_SCRIPT_PROCESS_PRODUCTS',
                Configuration::get('INFSCRL_JS_SCRIPT_PROCESS_PRODUCTS')
            ),
        );
        foreach ($this->a_supported_pages as $s_page_name) {
            $s_key = 'INFSCRL_ACTIVE_' . Tools::strtoupper($s_page_name);
            $values[$s_key] = Tools::getValue($s_key, Configuration::get($s_key));
        }
        return $values;
    }
    public function renderAdvancedForm()
    {
        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Advanced Settings'),
                    'icon' => 'icon-warning',
                ),
                'input' => array(
                    array(
                        'type' => 'text',
                        'label' => $this->l('The central content of the page'),
                        'name' => 'INFSCRL_CENTRAL_SELECTOR',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('The product list selector'),
                        'name' => 'INFSCRL_SELECTOR',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('The single product selector'),
                        'name' => 'INFSCRL_ITEM_SELECTOR',
                    ),
                    array(
                        'type' => 'text',
                        'label' => $this->l('The pagination block selector'),
                        'name' => 'INFSCRL_PAGINATION_SELECTOR',
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Background of the message box'),
                        'name' => 'INFSCRL_BG',
                        'value' => '#D3D3D3',
                        'data-hex' => 'true',
                        'size' => 10,
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Police of the message box'),
                        'name' => 'INFSCRL_POLICE',
                        'value' => '#858585',
                        'data-hex' => 'true',
                        'size' => 10,
                    ),
                    array(
                        'type' => 'color',
                        'label' => $this->l('Border of the message box'),
                        'name' => 'INFSCRL_BORDER',
                        'value' => '#F5F5F5',
                        'data-hex' => 'true',
                        'size' => 10,
                    ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('JS callback script after ajax'),
                        'name' => 'INFSCRL_JS_SCRIPT_AFTER',
                        'desc' => $this->l('javascript executed after ajax is displayed'),
                        ),
                    array(
                        'type' => 'textarea',
                        'label' => $this->l('JS callback script on product processing'),
                        'name' => 'INFSCRL_JS_SCRIPT_PROCESS_PRODUCTS',
                        'desc' => $this->l('javascript executed on the result products'),
                        ),
                ),
                'submit' => array(
                    'title' => $this->l('Save'),
                ),

            ),
        );

        if (Tools::version_compare(_PS_VERSION_, '1.6', '<')) {
            $fields_form = $this->fixFieldsFor15($fields_form);
        }

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $this->fields_form = array();
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitInfiniteScroll';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) .
        '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;

        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'id_language' => $this->context->language->id,
        );
        return $helper->generateForm(array(
            $fields_form,
        ));
    }
    public function renderForm()
    {
       
        // Each pages can be manually selected
        $aInputs = array(
            array(
                'type' => 'switch',
                'label' => $this->l('Enable sandbox mode'),
                'name' => 'INFSCRL_SANDBOX_MODE',
                'desc' => $this->l('IF YES : Only authorized IP will have the infinite scroll. ') .
                $this->l('IF NO : All users will have the infinite scroll.'),
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ),
                ),
            ),
            array(
                'type' => 'text',
                'label' => $this->l('Authorized IPs'),
                'name' => 'INFSCRL_SANDBOX_IP',
                'desc' => $this->l('List of IPs separated with a comma.'),
            ),
            array(
                'type' => 'hidden',
                'name' => 'INFSCRL_IMG',
            ),
            array(
                'type' => 'file',
                'label' => $this->l('The URL to your loader image'),
                'name' => 'INFSCRL_LOADER_IMAGE',
                'desc' => $this->l('We recommand using a .gif image.'),
                'display_image' => true,
                'thumb' => $this->thumbImage(),
            ),
        );
        foreach ($this->a_supported_pages as $s_page_name) {
            $aInputs[] = array(
                'type' => 'switch',
                'label' => $this->l('Activate infinite scroll on the page : ') . $s_page_name,
                'name' => 'INFSCRL_ACTIVE_' . Tools::strtoupper($s_page_name),
                'is_bool' => true,
                'values' => array(
                    array(
                        'id' => 'active_on',
                        'value' => 1,
                        'label' => $this->l('Enabled'),
                    ),
                    array(
                        'id' => 'active_off',
                        'value' => 0,
                        'label' => $this->l('Disabled'),
                    ),
                ),
            );
        }

        $aInputs[] = array(
            'type' => 'switch',
            'label' => $this->l('Hide "We have reached the bottom end of the page" message box'),
            'name' => 'INFSCRL_HIDE_BUTTON',
            'is_bool' => true,
            'values' => array(
                array(
                    'id' => 'hide_on',
                    'value' => 1,
                    'label' => $this->l('Hide'),
                ),
                array(
                    'id' => 'hide_off',
                    'value' => 0,
                    'label' => $this->l('Visible'),
                ),
            ),
        );

        //ajout de la gestion du stop bottom
        $aInputs[] = array(
            'type' => 'switch',
            'label' => $this->l('Display a message "Load more products"'),
            'desc' => $this->l('This way your customer will be able to reach the footer.') .
            $this->l(' (without scrolling all the way down)'),
            'name' => 'INFSCRL_STOP_BOTTOM',
            'is_bool' => true,
            'values' => array(
                array(
                    'id' => 'hide_on',
                    'value' => 1,
                    'label' => $this->l('Yes'),
                ),
                array(
                    'id' => 'hide_off',
                    'value' => 0,
                    'label' => $this->l('No'),
                ),
            ),
        );

        $aInputs[] = array(
            'type' => 'text',
            'label' => $this->l('Page to show the "Load more products" message'),
            'name' => 'INFSCRL_STOP_BOTTOM_PAGE',
            'desc' => $this->l('The message will appear at the end of the Nth page'),
        );

        $aInputs[] = array(
            'type' => 'text',
            'label' => $this->l('Frequency for the "Load more products" message'),
            'name' => 'INFSCRL_STOP_BOTTOM_FREQ',
            'desc' => $this->l('The message will appear every Nth pages, set to 0 to disable this feature'),
        );

        $fields_form = array(
            'form' => array(
                'legend' => array(
                    'title' => $this->l('Settings'),
                    'icon' => 'icon-cogs',
                ),
                'input' => $aInputs,
                'submit' => array(
                    'title' => $this->l('Save'),
                ),
            ),
        );

        if (Tools::version_compare(_PS_VERSION_, '1.6', '<')) {
            $fields_form = $this->fixFieldsFor15($fields_form);
        }

        $helper = new HelperForm();
        $helper->show_toolbar = false;
        $helper->table = $this->table;
        $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));
        $helper->default_form_language = $lang->id;
        $this->fields_form = array();
        $helper->identifier = $this->identifier;
        $helper->submit_action = 'submitInfiniteScroll';
        $helper->currentIndex = $this->context->link->getAdminLink('AdminModules', false) .
        '&configure=' . $this->name . '&tab_module=' . $this->tab . '&module_name=' . $this->name;

        $helper->token = Tools::getAdminTokenLite('AdminModules');
        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues(),
            'id_language' => $this->context->language->id,
        );
        return $helper->generateForm(array(
            $fields_form,
        ));
    }

    public function fixFieldsFor15($fields_form)
    {
        foreach ($fields_form['form']['input'] as $k => $inputInfo) {
            //si c'est du switch, sur 1.5 on le change en autre chose :)
            if ($inputInfo['type'] == 'switch') {
                $fields_form['form']['input'][$k]['type'] = 'radio';
            } elseif ($inputInfo['type'] == 'textarea') {
                //on ajoute le truc "cols" sur les textarea
                $fields_form['form']['input'][$k]['cols'] = 100;
                $fields_form['form']['input'][$k]['rows'] = 10;
            }
            //sur 1.5 no retire ce champs car il pète la DB... la flemme
            if ($inputInfo['name'] == 'INFSCRL_JS_SCRIPT_PROCESS_PRODUCTS') {
                unset($fields_form['form']['input'][$k]);
            }
        }

        $fields_form['form']['input'] = array_values($fields_form['form']['input']);
        
        return $fields_form;
    }
}
