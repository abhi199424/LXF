<?php

class Category extends CategoryCore
{
    public $custom_switch;

    public function __construct($id_category = null, $id_lang = null, $id_shop = null)
    {
        self::$definition['fields']['custom_switch'] = array('type' => self::TYPE_INT);

        parent::__construct($id_category, $id_lang, $id_shop);
    }
}