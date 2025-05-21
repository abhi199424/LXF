<?php
/**
 * NOTICE OF LICENSE
 *
 * @author    Klarna Bank AB www.klarna.com
 * @copyright Copyright (c) permanent, Klarna Bank AB
 * @license   ISC
 *
 * @see       /LICENSE
 *
 * International Registered Trademark & Property of Klarna Bank AB
 */
if (!defined('_PS_VERSION_')) {
    exit;
}

class KlarnaPaymentCart extends ObjectModel
{
    public $id_klarnapayment_cart;

    public $id_cart;

    public $id_shop;

    public static $definition = [
        'table' => 'klarnapayment_carts',
        'primary' => 'id_klarnapayment_cart',
        'fields' => [
            'id_cart' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
        ],
    ];
}
