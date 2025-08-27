<?php
/**
 * package SP Filter Products
 *
 * @version 1.0.0
 * @author    MagenTech http://www.magentech.com
 * @copyright (c) 2018 YouTech Company. All Rights Reserved.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */
 
if (!defined ('_PS_VERSION_'))
    exit;

class SpFilterProductsClass extends ObjectModel
{
	
    public $id_spfilterproducts;
    public $title_module;
    public $short_desc;
    public $identifier_block;
    public $active = 1;
    public $hook;
    public $params;
    public $position;
    public static $definition = [
        'table' => 'spfilterproducts',
        'primary' => 'id_spfilterproducts',
        'multilang' => true,
        'fields' => [
			'hook' => ['type' => self::TYPE_INT, 'validate' => 'isunsignedInt' ],
            'title_module' => ['type' => self::TYPE_HTML, 'lang' => true, 'required' => true,'validate' => 'isCleanHtml','size' => 255 ],
			'active' => ['type' => self::TYPE_INT, 'shop' => true, 'validate' => 'isunsignedInt'],
            'params' => ['type' => self::TYPE_HTML, 'validate' => 'isString'],
            'position' => ['type' => self::TYPE_INT, 'validate' => 'isInt'] 
			]
		];

    public function __construct($id_tab = null, $id_lang = null, $id_shop = null)
    {
        Shop::addTableAssociation ('spfilterproducts', array('type' => 'shop'));
        parent::__construct ($id_tab, $id_lang, $id_shop);
    }

    public function add($autodate = true, $null_values = false)
    {
        if ($this->position <= 0) {
            $this->position = $this->getHigherPosition() + 1;
        }
        $res = parent::add($autodate, $null_values);
        return $res;
    }

    public function duplicate($autodate = true)
    {
        $this->position = $this->getHigherPosition() + 1;
        $return = parent::add ($autodate, true);
        return $return;
    }

    public function delete()
    {
        $res = true;
        $res &= $this->cleanPositions();
        $res &= parent::delete();
        return $res;
    }

    public function getHigherPosition()
    {
        $sql = 'SELECT MAX(`position`)
				FROM `'._DB_PREFIX_.'spfilterproducts`';
        $position = DB::getInstance ()->getValue ($sql);
        return (is_numeric($position)) ? $position : -1;
    }

    public function getHigherModuleID()
    {
        $sql = 'SELECT MAX(`id_spfilterproducts`)
				FROM `'._DB_PREFIX_.'spfilterproducts`';
        $id_spfilterproducts = DB::getInstance ()->getValue ($sql);
        return ( is_numeric ($id_spfilterproducts) )?$id_spfilterproducts:1;
    }

    public static function getAssociatedIdsShop($id_module)
    {
        $result = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT css.`id_shop`
			FROM `'._DB_PREFIX_.'spfilterproducts` cs
			LEFT JOIN `'._DB_PREFIX_.'spfilterproducts_shop` css ON (css.`id_spfilterproducts` = cs.`id_spfilterproducts`)
			WHERE cs.`id_spfilterproducts` = '.(int)$id_module
        );

        if (!is_array($result))
            return false;

        $return = array();

        foreach ($result as $id_shop)
            $return[] = (int)$id_shop['id_shop'];
        return $return;
    }

    /**
     * Reorder group attribute position
     * Call it after deleting a group attribute.
     *
     * @return bool $return
     */
    public  function cleanPositions()
    {
        $id_spfilterproducts = $this->id;
        $context = Context::getContext();
        $id_shop = $context->shop->id;

        $max = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT MAX(cs.`position`) as position
			FROM `'._DB_PREFIX_.'spfilterproducts` cs, `'._DB_PREFIX_.'spfilterproducts_shop` css
			WHERE css.`id_spfilterproducts` = cs.`id_spfilterproducts` AND css.`id_shop` = '.(int)$id_shop
        );

        if ((int)$max == (int)$id_spfilterproducts)
            return true;
        $rows = Db::getInstance(_PS_USE_SQL_SLAVE_)->executeS('
			SELECT cs.`position` as position, cs.`id_spfilterproducts` as id_spfilterproducts
			FROM `'._DB_PREFIX_.'spfilterproducts` cs
			LEFT JOIN `'._DB_PREFIX_.'spfilterproducts_shop` css ON (css.`id_spfilterproducts` = cs.`id_spfilterproducts`)
			WHERE css.`id_shop` = '.(int)$id_shop.' AND cs.`position` > '.(int)$this->position
        );
        foreach ($rows as $row)
        {
            $customs = new SpFilterProductsClass($row['id_spfilterproducts']);
            --$customs->position;
            $customs->update();
            unset($customs);
        }

        return true;
    }

    public static function  getGridModules($id_lang, $id_shop)
    {
        if (!$result = Db::getInstance ()->ExecuteS ('
			SELECT b.`id_spfilterproducts`, b.`hook`, b.`position`, bs.`active`, bl.`title_module`
			FROM `'._DB_PREFIX_.'spfilterproducts` b
			LEFT JOIN `'._DB_PREFIX_.'spfilterproducts_shop` bs ON (b.`id_spfilterproducts` = bs.`id_spfilterproducts`)
			LEFT JOIN `'._DB_PREFIX_.'spfilterproducts_lang` bl ON (b.`id_spfilterproducts` = bl.`id_spfilterproducts`'
            .( $id_shop?'AND bs.`id_shop` = '.$id_shop:' ' ).')
			WHERE bl.`id_lang` = '.(int)$id_lang.( $id_shop?' AND bs.`id_shop` = '.$id_shop:' ' ).'
			ORDER BY b.`position`'))
            return false;
        return $result;
    }


}