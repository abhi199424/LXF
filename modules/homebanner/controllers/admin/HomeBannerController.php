<?php
/**
* 2007-2016 PrestaShop
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
*  @copyright 2007-2016 PrestaShop SA
*  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

define('_PS_HOMEBANNER_IMG_',_PS_MODULE_DIR_.'homebanner/views/img/');
require_once _PS_MODULE_DIR_ . 'homebanner/homebanner.php';

class HomeBannerController extends ModuleAdminController
{
	public function __construct()
	{
		$this->table = 'homebanner';
		$this->className = 'HomeBannersection';
		$this->moduleName = 'homebanner';
		$this->bootstrap = true;
		$this->lang = true;
		$this->bulk_action = array();
		$this->addRowAction('edit');
		$this->addRowAction('delete');
		$this->context = Context::getContext();
        $this->module = Module::getInstanceByName('homebanner');

        //$trans = new Translate();
		
		$this->defaltFormLanguage = (int)Configuration::get('PS_LANG_DEFAULT');
		$this->fieldImageSettings = array(
			array(
				'name' => 'image',
                'dir' => '../modules/homebanner/views/img'
            ),
        );

		$this->fields_list = array(
            'id_homebanner' => array(
                'title' => $this->module->l('Id'),
                'align' => 'center',
                'class' => 'fixed-width-xs'
            ),
            'title' => array(
                'title' => $this->module->l('Banner title')
            ),
            'active' => array(
                'title' => $this->module->l('Displayed'),
                'active' => 'status',
                'type' => 'bool',
                'name' => 'active',
                'class' => 'fixed-width-xs',
                'align' => 'center',
                'orderby' => false
            ),
        );

        $this->bulk_action = array(
            'delete' => array(
                'text' => $this->module->l('Delete selected'),
                'icon' =>'icon-trash',
                'confirm' => $this->module->l('Are you sure, you want to deleted all categories')
            )
        );

		parent::__construct();
	}
    
    public function init()
    {
        parent::init();
        
        $this->_select = 'sa.active active';
        
        if (Shop::getContext() == Shop::CONTEXT_SHOP) {
            $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'homebanner_shop` sa ON (a.`id_homebanner` = sa.`id_homebanner` AND sa.id_shop = '.(int)$this->context->shop->id.') ';
        } else {
            $this->_join .= ' LEFT JOIN `'._DB_PREFIX_.'homebanner_shop` sa ON (a.`id_homebanner` = sa.`id_homebanner` AND sa.id_shop = 1) ';
        }
        
        // we add restriction for shop
        if (Shop::getContext() == Shop::CONTEXT_SHOP && Shop::isFeatureActive()) {
            $this->_where = ' AND sa.`id_shop` = '.(int)Context::getContext()->shop->id;
        }
    }
    
    public function getList($id_lang, $order_by = null, $order_way = null, $start = 0, $limit = null, $id_lang_shop = false)
    {
        parent::getList($id_lang, $order_by, $order_way, $start, $limit, Context::getContext()->shop->id);
    }


	public function renderForm()
	{
        $id = (int)Tools::getValue('id_homebanner');
		$obj = $this->loadObject(true);
        $imag_name = Db::getInstance()->getRow('SELECT image FROM '._DB_PREFIX_.'homebanner WHERE id_homebanner = '.$id);
        $imag_name = $imag_name['image'];
        $imag_name = _PS_IMG_DIR_.$imag_name;

        
		$image = _PS_HOMEBANNER_IMG_.$obj->id.'.'.$this->imageType;		
        $image_url = ImageManager::thumbnail($imag_name, $this->table.'_'.(int)$obj->id.'.'.$this->imageType, 150, $this->imageType, true, true);

        $image_size = file_exists($image) ? filesize($image) / 1000 : false;

		$context = Context::getContext();

        
		$this->fields_form = array(
            'tinymce' => true,
            'legend' => array(
                'title' => $this->module->l('Banner information'),
                'icon' => 'icon-cogs'
            ),
            'input' => array(
                array(
                    'type' => 'file',
                    'label' => $this->module->l('Banner image'),
                    'name' => 'imagebanner',
                    'display_image' => true,
                    'image' => $image_url ? $image_url : false,
                    'size' => $image_size,
                ),                
                array(
                    'type' => 'text',
                    'label' => $this->module->l('Banner title'),
                    'name' => 'title',
                    'required' => true,
                    'lang' => true,
                ),

                array(
                    'type' => 'textarea',
                    'label' => $this->module->l('URL'),
                    'name' => 'description',
                    'lang' => true,
                    'autoload_rte' => true
                ),
                array(
                    'type' => 'switch',
                    'label' => $this->module->l('Enabled'),
                    'name' => 'active',
                    'is_bool' => true,
                    'values' => array(
                        array(
                            'id' => 'active_on',
                            'value' => 1,
                            'label' => $this->module->l('Yes')
                        ),
                        array(
                            'id' => 'active_off',
                            'value' => 0,
                            'label' => $this->module->l('No')
                        )
                    ),
                ),
            ),
            'submit' => array(
                'title' => $this->module->l('Save'),
            )
		);
        
        // Display this field only if multistore option is enabled AND there are several stores configured
        if (Shop::isFeatureActive()) {
            $this->fields_form['input'][] = array(
                'type' => 'shop',
                'label' => $this->module->l('Shop association'),
                'name' => 'checkBoxShopAsso',
            );
        }
		return parent::renderForm();
	}

    public function postProcess()
    {
        parent::postProcess();
        if (Tools::isSubmit('submitAdd'.$this->table)) {
            
            
            $id = (int)Tools::getValue('id_homebanner');
            if (isset($_FILES['imagebanner']) && $_FILES['imagebanner']['tmp_name']) {
                $image_sticker = $_FILES['imagebanner']['tmp_name'];
                $image_name_sticker = $_FILES['imagebanner']['name'];
                $path = _PS_IMG_DIR_.'homebanner/'.$id.'/';
                $dir = $path.$image_name_sticker;
                if (!is_dir(_PS_IMG_DIR_.'homebanner/'.$id)) {
                    @mkdir(_PS_IMG_DIR_.'homebanner/'.$id, 0777, true);
                }
                move_uploaded_file($image_sticker, $dir);
                 
                $imgPath = 'homebanner/'.$id.'/'.$image_name_sticker;

                Db::getInstance()->execute('UPDATE `'._DB_PREFIX_.'homebanner`
                SET `image` = "'.pSQL($imgPath).'"
                WHERE `id_homebanner` = '.(int)$id);
            }
        }
    }
}
