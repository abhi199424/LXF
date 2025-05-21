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

class KlarnaPaymentLog extends ObjectModel
{
    public $id_klarnapayment_log;

    public $id_log;

    public $id_shop;

    public $correlation_id;

    public $request;

    public $response;

    public $context;

    public $date_add;

    public static $definition = [
        'table' => 'klarnapayment_logs',
        'primary' => 'id_klarnapayment_log',
        'fields' => [
            'id_log' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'id_shop' => ['type' => self::TYPE_INT, 'validate' => 'isInt'],
            'correlation_id' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'request' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'response' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'context' => ['type' => self::TYPE_STRING, 'validate' => 'isString'],
            'date_add' => ['type' => self::TYPE_DATE, 'validate' => 'isDate'],
        ],
    ];
}
