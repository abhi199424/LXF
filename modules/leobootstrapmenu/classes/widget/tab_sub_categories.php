<?php
/**
 * 2007-2015 Leotheme
 *
 * NOTICE OF LICENSE
 *
 * Leo Bootstrap Menu
 *
 * DISCLAIMER
 *
 *  @author    leotheme <leotheme@gmail.com>
 *  @copyright 2007-2015 Leotheme
 *  @license   http://leotheme.com - prestashop template provider
 */

if (!defined('_PS_VERSION_')) {
    # module validation
    exit;
}

class LeoWidgetTabSubcategories extends LeoWidgetBase
{
    public $name = 'tab_sub_categories';
    public $for_module = 'all';

    public function getWidgetInfo()
    {
        return array('label' => $this->l('Tab Sub Categories In Parent'), 'explain' => $this->l('Show category sub with Vertical tab'));
    }

    public function renderForm($args, $data)
    {
        # validate module
        unset($args);
        $helper = $this->getFormHelper();

        $this->fields_form[1]['form'] = array(
            'legend' => array(
                'title' => $this->l('Widget Form.'),
            ),
            'input' => array(
                array(
                    'type' => 'text',
                    'label' => $this->l('Parent Category ID'),
                    'name' => 'category_id',
                    'default' => '6',
                ),
            ),
            'buttons' => array(
                array(
                    'title' => $this->l('Save And Stay'),
                    'icon' => 'process-icon-save',
                    'class' => 'pull-right',
                    'type' => 'submit',
                    'name' => 'saveandstayleowidget'
                ),
                array(
                    'title' => $this->l('Save'),
                    'icon' => 'process-icon-save',
                    'class' => 'pull-right',
                    'type' => 'submit',
                    'name' => 'saveleowidget'
                ),
            )
        );

        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');

        $helper->tpl_vars = array(
            'fields_value' => $this->getConfigFieldsValues($data),
            'languages' => Context::getContext()->controller->getLanguages(),
            'id_language' => $default_lang
        );

        return $helper->generateForm($this->fields_form);
    }

    public function renderContent($args, $setting)
    {
        # validate module
        unset($args);
        $t = array(
            'category_id' => '',
        );
        $setting = array_merge($t, $setting);

        $setting['menu_level3'] = 1;

        $category = new Category($setting['category_id'], $this->langID);
        //check if category id not exists
        if ($category->id != '') {
            $subCategories = $category->getSubCategories($this->langID);
            if (isset($setting['menu_level3']) && $setting['menu_level3']) {
                foreach ($subCategories as &$subCategory) {
                    $sub = new Category($subCategory['id_category'], $this->langID);
                    $subSubCategories = $sub->getSubCategories($this->langID);
                    $subCategory['subsubcategories'] = $subSubCategories;
                }
            }
            $setting['subcategories'] = $subCategories;
        } else {
            $setting['subcategories'] = array();
        }
        $setting['title'] = $category->name;
        $setting['cat'] = $category;
        $output = array('type' => 'tab_sub_categories', 'data' => $setting);
        // echo '<pre>';
        // print_r($setting);
        // die;
        return $output;
    }

    /**
     * 0 no multi_lang
     * 1 multi_lang follow id_lang
     * 2 multi_lnag follow code_lang
     */
    public function getConfigKey($multi_lang = 0)
    {
        if ($multi_lang == 0) {
            return array(
                'category_id',
                'menu_level3',
                'level3_only_mobile',
            );
        } elseif ($multi_lang == 1) {
            return array(
            );
        } elseif ($multi_lang == 2) {
            return array(
            );
        }
    }
}
