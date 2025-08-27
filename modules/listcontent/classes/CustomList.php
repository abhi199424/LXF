<?php
class CustomList extends ObjectModel {
    public $name;
    public $id_categories;
    public $product_ids;
    public $html_content;

    public static $definition = [
        'table' => 'custom_listcontent',
        'primary' => 'id_custom_list',
        'fields' => [
            'name' => ['type' => self::TYPE_STRING, 'required' => true],
            'id_categories' => ['type' => self::TYPE_STRING],
            'product_ids' => ['type' => self::TYPE_STRING],
            'html_content' => ['type' => self::TYPE_HTML]
        ]
    ];
}