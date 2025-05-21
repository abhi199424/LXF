<?php
if (!defined('_PS_VERSION_')) {
    exit;
}

class CustomCategoryContent extends Module
{
    public function __construct()
    {
        $this->name = 'customcategorycontent';
        $this->version = '1.0.0';
        $this->author = 'ChatGPT';
        $this->tab = 'front_office_features';
        $this->need_instance = 0;

        parent::__construct();

        $this->displayName = $this->l('Custom Category Content');
        $this->description = $this->l('Adds a custom HTML editor to categories and displays content on the category page.');
    }

    public function install()
    {
        return parent::install() &&
            $this->registerHook('displayCategoryExtra') &&
            $this->registerHook('actionCategoryFormBuilderModifier') &&
            $this->registerHook('actionAfterUpdateCategoryFormHandler') &&
            $this->registerHook('actionAdminControllerSetMedia') &&
            Db::getInstance()->execute("ALTER TABLE "._DB_PREFIX_."category_lang ADD COLUMN custom_content TEXT");
    }

    public function uninstall()
    {
        return parent::uninstall() &&
            Db::getInstance()->execute("ALTER TABLE "._DB_PREFIX_."category_lang DROP COLUMN custom_content");
    }

    public function hookActionCategoryFormBuilderModifier($params)
    {
        $id_category = (int)$params['id'];
        $id_lang = (int)$this->context->language->id;

        // Retrieve the custom content from the database for the current category and language
        $custom_content = Db::getInstance()->getValue(
            "SELECT custom_content FROM "._DB_PREFIX_."category_lang
            WHERE id_category = $id_category AND id_lang = $id_lang"
        );

        // Add the custom content to the form
        $params['form_builder']->add(
            'custom_content',
            'Symfony\\Component\\Form\\Extension\\Core\\Type\\TextareaType',
            [
                'label' => $this->l('Custom HTML Content'),
                'data' => $custom_content, // Load the custom content here
                'required' => false,
                'attr' => ['class' => 'rte form-control'], // Ensures it's editable with TinyMCE
            ]
        );
    }

    public function hookActionAfterUpdateCategoryFormHandler($params)
    {
        $id_category = (int)$params['id'];
        $form_data = $params['form_data'];

        // Check if custom_content exists in the form data and handle accordingly
        if (isset($form_data['custom_content'])) {
            // Sanitize and save the custom content
            $custom_content = pSQL($form_data['custom_content'], true);

            // Update the custom content in the category_lang table
            Db::getInstance()->update(
                'category_lang',
                ['custom_content' => $custom_content],
                'id_category = '.(int)$id_category.' AND id_lang = '.(int)$this->context->language->id
            );
        }
    }

    public function hookDisplayCategoryExtra($params)
    {
        $id_category = (int)Tools::getValue('id_category');
        $id_lang = (int)$this->context->language->id;

        $content = Db::getInstance()->getValue(
            "SELECT custom_content FROM "._DB_PREFIX_."category_lang
             WHERE id_category = $id_category AND id_lang = $id_lang"
        );

        if ($content) {
            return '<div class="category-custom-content">'.$content.'</div>';
        }

        return '';
    }

    public function hookActionAdminControllerSetMedia($params)
    {
        $controller = $this->context->controller;
        if (Tools::getValue('controller') === 'AdminCategories') {
            $controller->addJS(_PS_JS_DIR_.'tiny_mce/tiny_mce.js');
            $controller->addJS(_PS_JS_DIR_.'admin/tinymce.inc.js');
            $controller->addJS($this->_path.'views/js/init_tinymce.js');
        }
    }
}
