<?php

require_once(dirname(__FILE__) . '/../../classes/CustomList.php');

class AdminCustomListContentController extends ModuleAdminController
{
    public function __construct()
    {
        $this->context = Context::getContext();
        $this->table = 'custom_listcontent';
        $this->className = 'CustomList';
        $this->identifier = 'id_custom_list';
        $this->_defaultOrderBy = 'id_custom_list';
        $this->lang = false;
        $this->bootstrap = true;

        $this->addRowAction('edit');
        $this->addRowAction('delete');

        $this->bulk_actions = array(
            'delete' => array(
                'text' => $this->context->getTranslator()->trans('Delete selected'),
                'confirm' => $this->context->getTranslator()->trans('Delete selected items?')
            )
        );

        parent::__construct();

    }

    public function renderList()
    {
        $this->fields_list = array(
            'id_custom_list' => array(
                'title' => $this->context->getTranslator()->trans('ID'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'name' => array(
                'title' => $this->context->getTranslator()->trans('List Name')
            ),
            'id_categories' => array(
                'title' => $this->context->getTranslator()->trans('Category ID')
            ),
            'product_ids' => array(
                'title' => $this->context->getTranslator()->trans('Product IDs')
            )
        );

        return parent::renderList();
    }

    public function renderForm()
    {
        if (!($obj = $this->loadObject(true))) {
            return;
        }

        $selected_categories = [];
        if (Validate::isLoadedObject($obj) && !empty($obj->id_categories)) {
            $selected_categories = array_map('intval', explode(',', $obj->id_categories));
        }

        $this->fields_form = array(
            'legend' => array('title' => $this->context->getTranslator()->trans('Custom List')),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->context->getTranslator()->trans('List Name'),
                    'name' => 'name',
                    'required' => true,
                ),
                array(
                    'type' => 'categories',
                    'label' => $this->context->getTranslator()->trans('Select Categories'),
                    'name' => 'id_categories',
                    'tree' => array(
                        'id' => 'categories-tree',
                        'selected_categories' => $selected_categories,
                        'use_search' => true,
                        'use_checkbox' => true
                    )
                ),
                array(
                    'type' => 'text',
                    'label' => $this->context->getTranslator()->trans('Product IDs (comma-separated)'),
                    'name' => 'product_ids',
                ),
                array(
                    'type' => 'textarea',
                    'label' => $this->context->getTranslator()->trans('HTML Content'),
                    'name' => 'html_content',
                    'autoload_rte' => true,
                ),
            ),
            'submit' => array('title' => $this->context->getTranslator()->trans('Save'))
        );

        return parent::renderForm();
    }

    public function processSave()
    {
        if (Tools::isSubmit('id_categories')) {
            $categories = Tools::getValue('id_categories');
            if (is_array($categories)) {
                $_POST['id_categories'] = implode(',', array_map('intval', $categories));
            }
        }

        return parent::processSave();
    }
}