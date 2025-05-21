<?php
/*
* 2007-2016 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Open Software License (OSL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/osl-3.0.php
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
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2016 PrestaShop SA
*  @license    http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

class HomeBannersection extends ObjectModel
{
    /** @var int */
    public $id_shop_default;

    /** @var int */

    /** @var bool */
    public $active;
    
    /** @var sting */
    public $title;

    
    /** @var text */
    public $description;


    public static $definition = array(
		'table'=> 'homebanner',
		'primary' => 'id_homebanner',
		'multilang' => true,
		'multilang_shop' => true,
		'fields' => array(			
            'id_shop_default' =>    array('type' => self::TYPE_INT, 'validate' => 'isUnsignedId'),

            /* Shop Field */
            'active' =>             array('type' => self::TYPE_BOOL, 'validate' => 'isBool', 'shop' => true, 'required' => true),

            /* Lang fields */
            'title' =>              array('type' => self::TYPE_STRING, 'lang' => true, 'validate' => 'isCatalogName'),
            "description" =>        array('type' =>self::TYPE_HTML, 'lang' =>true, 'validate' => 'isCleanHtml'),
        ),
    );
    
    public function add($autodate = true, $null_values = false)
    {
        $this->id_shop_default = Configuration::get('PS_SHOP_DEFAULT');
        return parent::add($autodate, $null_values);
    }
}
