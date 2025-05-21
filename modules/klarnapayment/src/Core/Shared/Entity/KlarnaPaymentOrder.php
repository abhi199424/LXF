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

class KlarnaPaymentOrder extends ObjectModel
{
    public $id_klarna_payment_order;

    public $id_internal;

    public $id_external;

    public $id_shop;
    public $id_klarna_merchant;
    public $klarna_reference;
    public $authorization_token;

    public static $definition = [
        'table' => 'klarna_payment_orders',
        'primary' => 'id_klarna_payment_order',
        'fields' => [
            'id_internal' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'id_external' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_klarna_merchant' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'klarna_reference' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'authorization_token' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
        ],
    ];
}
