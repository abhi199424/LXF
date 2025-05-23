<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

use PrestaShop\PrestaShop\Core\Module\WidgetInterface;
use PrestaShop\PrestaShop\Core\Form\IdentifiableObject\FormDataProviderInterface;

class Prospectextgenerator extends Module
{
    protected $config_form = false;

    public function __construct()
    {
        $this->name = 'prospectextgenerator';
        $this->tab = 'administration';
        $this->version = '1.0.0';
        $this->author = 'DevAbhi';
        $this->need_instance = 0;

        /**
         * Set $this->bootstrap to true if your module is compliant with bootstrap (PrestaShop 1.6)
         */
        $this->bootstrap = true;

        parent::__construct();

        $this->displayName = $this->l('Product SpecText Generator');
        $this->description = $this->l('Adds custom text based on category or product.');

        $this->ps_versions_compliancy = array('min' => '1.6', 'max' => _PS_VERSION_);
    }

    public function install()
    {
        return parent::install()
            && $this->registerHook('displayProductAdditionalInfo')
            && $this->registerHook('displayFooterCategory')
            && $this->createTable();
    }

    public function uninstall()
    {
        return parent::uninstall() && $this->deleteTable();
    }

    private function createTable()
    {
        $sql = "CREATE TABLE IF NOT EXISTS " . _DB_PREFIX_ . "custom_text_widget (
            id_custom_text INT AUTO_INCREMENT PRIMARY KEY,
            text1 TEXT,
            text2 TEXT,
            type ENUM('product', 'category') NOT NULL,
            target_ids TEXT NOT NULL
        ) ENGINE=" . _MYSQL_ENGINE_ . " DEFAULT CHARSET=utf8;";

        return Db::getInstance()->execute($sql);
    }

    private function deleteTable()
    {
        return Db::getInstance()->execute("DROP TABLE IF EXISTS " . _DB_PREFIX_ . "custom_text_widget");
    }

    public function getContent()
    {
        if (Tools::getValue('ajax') == 1 && Tools::getValue('action') == 'getTargets') {
            $this->ajaxGetTargets();
            exit;
        }

        $output = '';
        $id_custom_text = (int) Tools::getValue('id_custom_text');
        $entry = [];

        if (Tools::isSubmit('submitProspectTextGenerator')) {
            $text1 = Tools::getValue('text1');
            $text2 = Tools::getValue('text2');
            $type = Tools::getValue('type');
            $target_ids = Tools::getValue('target_ids');

            if ($text1 && $text2 && $type && !empty($target_ids)) {
                if ($id_custom_text) {
                    Db::getInstance()->update('custom_text_widget', [
                        'text1' => pSQL($text1),
                        'text2' => pSQL($text2),
                        'type' => pSQL($type),
                        'target_ids' => pSQL(implode(',', $target_ids))
                    ], 'id_custom_text = ' . (int) $id_custom_text);
                    $output .= $this->displayConfirmation($this->l('Entry updated successfully'));
                } else {
                    Db::getInstance()->insert('custom_text_widget', [
                        'text1' => pSQL($text1),
                        'text2' => pSQL($text2),
                        'type' => pSQL($type),
                        'target_ids' => pSQL(implode(',', $target_ids))
                    ]);
                    $output .= $this->displayConfirmation($this->l('Entry added successfully'));
                }
                Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name . '&conf=4');
            } else {
                $output .= $this->displayError($this->l('All fields are required'));
            }
        }

        if ($id_custom_text) {
            $entry = Db::getInstance()->getRow("SELECT * FROM " . _DB_PREFIX_ . "custom_text_widget WHERE id_custom_text = " . (int) $id_custom_text);
        }

        if (Tools::getValue('deleteEntry') && $id_custom_text) {
            Db::getInstance()->delete('custom_text_widget', 'id_custom_text = ' . (int) $id_custom_text);
            Tools::redirectAdmin($this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name . '&conf=1');
        }

        return $output . $this->renderForm() . $this->renderList();
    }


    private function ajaxGetTargets()
    {
        header('Content-Type: application/json');
        $type = pSQL(Tools::getValue('type'));
        $id_custom_text = (int) Tools::getValue('id_custom_text');
        $results = [];
        $selectedTargets = [];

        if ($id_custom_text) {
            $selectedEntry = Db::getInstance()->getRow(
                "SELECT target_ids FROM " . _DB_PREFIX_ . "custom_text_widget WHERE id_custom_text = " . (int) $id_custom_text
            );
            if (!empty($selectedEntry['target_ids'])) {
                $selectedTargets = explode(',', $selectedEntry['target_ids']);
            }
        }

        if ($type === 'product') {
            $products = Product::getProducts($this->context->language->id, 0, 0, 'name', 'ASC');
            if (!empty($products)) {
                foreach ($products as $product) {
                    $results[] = [
                        'id' => (int) $product['id_product'],
                        'name' => htmlspecialchars($product['name']),
                        'selected' => in_array($product['id_product'], $selectedTargets)
                    ];
                }
            }
        } elseif ($type === 'category') {
            $categories = Category::getCategories($this->context->language->id, true, false);
            if (!empty($categories)) {
                foreach ($categories as $category) {
                    $results[] = [
                        'id' => (int) $category['id_category'],
                        'name' => htmlspecialchars($category['name']),
                        'selected' => in_array($category['id_category'], $selectedTargets)
                    ];
                }
            }
        }

        die(json_encode($results, JSON_UNESCAPED_UNICODE));
    }

    private function renderForm()
    {
        $this->context->controller->addJS($this->_path . 'views/js/admin.js');
        $this->context->controller->addCSS($this->_path . 'views/css/admin.css');
        Media::addJsDef([
            'prospectAjaxUrl' => $this->context->link->getAdminLink('AdminModules', true) . '&configure=' . $this->name . '&ajax=1'
        ]);

        $id_custom_text = (int) Tools::getValue('id_custom_text');
        $entry = [];

        if ($id_custom_text) {
            $entry = Db::getInstance()->getRow("SELECT * FROM " . _DB_PREFIX_ . "custom_text_widget WHERE id_custom_text = " . (int) $id_custom_text);
        }
        
        $selected_targets = isset($entry['target_ids']) && !empty($entry['target_ids']) 
            ? array_map('trim', explode(',', $entry['target_ids'])) 
            : [];
        
        $helper = new HelperForm();
        $helper->module = $this;
        $helper->submit_action = 'submitProspectTextGenerator';
        
        $helper->fields_value = [
            'id_custom_text' => $entry['id_custom_text'] ?? '',
            'text1' => $entry['text1'] ?? '',
            'text2' => $entry['text2'] ?? '',
            'type' => $entry['type'] ?? '',
            'target_ids[]' => $selected_targets
        ];
        

        return $helper->generateForm([
            [
                'form' => [
                    'legend' => [
                        'title' => $id_custom_text ? $this->l('Edit Entry') : $this->l('Add New Entry'),
                    ],
                    'input' => [
                        ['type' => 'hidden', 'name' => 'id_custom_text'],
                        ['type' => 'text', 'label' => $this->l('Text 1'), 'name' => 'text1', 'required' => true],
                        ['type' => 'text', 'label' => $this->l('Text 2'), 'name' => 'text2', 'required' => true],
                        [
                            'type' => 'select',
                            'label' => $this->l('Type'),
                            'name' => 'type',
                            'options' => [
                                'query' => [
                                    ['id' => 'product', 'name' => $this->l('Product')],
                                    ['id' => 'category', 'name' => $this->l('Category')]
                                ],
                                'id' => 'id',
                                'name' => 'name'
                            ]
                        ],
                        [
                            'type' => 'select',
                            'label' => $this->l('Target'),
                            'name' => 'target_ids[]',
                            'options' => [
                                'query' => [],
                                'id' => 'id',
                                'name' => 'name'
                            ],
                            'required' => true,
                            'multiple' => true,
                            'class' => 'custom-multiselect',
                            'size' => 10
                        ]                        
                    ],
                    'submit' => ['title' => $this->l($id_custom_text ? 'Update' : 'Save')]
                ]
            ]
        ]);
    }


    private function renderList()
    {
        $entries = Db::getInstance()->executeS("SELECT * FROM " . _DB_PREFIX_ . "custom_text_widget");
        
        $html = '<div class="panel">
                    <div class="panel-heading">
                       ' . $this->l('Existing Entries') . '
                    </div>
                    <div class="panel-body">';

        if (!empty($entries)) {
            $html .= '<table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Text 1</th>
                                <th>Text 2</th>
                                <th>Type</th>
                                <th>Target IDs</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>';
            
            foreach ($entries as $entry) {
                $html .= '<tr>
                            <td>' . (int) $entry['id_custom_text'] . '</td>
                            <td>' . htmlspecialchars($entry['text1']) . '</td>
                            <td>' . htmlspecialchars($entry['text2']) . '</td>
                            <td>' . htmlspecialchars($entry['type']) . '</td>
                            <td>' . htmlspecialchars($entry['target_ids']) . '</td>
                            <td>
                                <a href="' . $_SERVER['REQUEST_URI'] . '&editEntry=1&id_custom_text=' . (int) $entry['id_custom_text'] . '" class="btn btn-sm btn-warning">
                                    <i class="icon-edit"></i> ' . $this->l('Edit') . '
                                </a>
                                <a href="' . $_SERVER['REQUEST_URI'] . '&deleteEntry=1&id_custom_text=' . (int) $entry['id_custom_text'] . '" 
                                    class="btn btn-sm btn-danger" 
                                    onclick="return confirm(\'' . $this->l('Are you sure you want to delete this entry?') . '\');">
                                    <i class="icon-trash"></i> ' . $this->l('Delete') . '
                                </a>
                            </td>
                        </tr>';
            }

            $html .= '</tbody></table>';
        } else {
            $html .= '<p class="alert alert-warning">' . $this->l('No entries found.') . '</p>';
        }

        $html .= '</div></div>';
        return $html;
    }

    public function getWidgetVariables($hookName, array $params)
    {
        $id_product = isset($params['product']) ? (int) $params['product']['id_product'] : 0;
        $id_category = isset($params['category']) ? (int) $params['category']->id : 0;

        $sql = "SELECT * FROM " . _DB_PREFIX_ . "custom_text_widget 
                WHERE FIND_IN_SET(" . (int) $id_product . ", target_ids) AND type = 'product' 
                OR FIND_IN_SET(" . (int) $id_category . ", target_ids) AND type = 'category'";

        $entries = Db::getInstance()->executeS($sql);

        return !empty($entries) ? $entries : [];
    }

    public function renderWidget($hookName, array $params)
    {
        $vars = $this->getWidgetVariables($hookName, $params);
        if (empty($vars)) {
            return '';
        }

        $this->context->smarty->assign('entries', $vars);
        return $this->fetch('module:prospectextgenerator/views/templates/hook/widget.tpl');
    }

}