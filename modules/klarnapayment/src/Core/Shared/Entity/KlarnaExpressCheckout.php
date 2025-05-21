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

class KlarnaExpressCheckout extends ObjectModel
{
    public $id_klarna_expresscheckout;

    public $id_cart;

    public $is_kec;

    public $client_token;

    public $address_checksum;

    public static $definition = [
        'table' => 'klarna_expresscheckout',
        'primary' => 'id_klarna_expresscheckout',
        'fields' => [
            'id_cart' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'is_kec' => ['type' => self::TYPE_BOOL, 'validate' => 'isBool'],
            'client_token' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'address_checksum' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
        ],
    ];
}
