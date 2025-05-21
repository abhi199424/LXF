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

class KlarnaPaymentCustomer extends ObjectModel
{
    public $id_klarnapayment_customer;

    public $id_token;
    public $id_refresh_token;
    public $id_customer;
    public $id_address;

    public $id_shop;

    public static $definition = [
        'table' => 'klarnapayment_customers',
        'primary' => 'id_klarnapayment_customer',
        'fields' => [
            'id_token' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'id_refresh_token' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'id_customer' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_address' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isUnsignedId'],
        ],
    ];
}
