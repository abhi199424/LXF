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

namespace KlarnaPayment\Module\Api\Enum;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AcquiringChannel
{
    public const ECOMMERCE = 'ECOMMERCE';
    public const IN_STORE = 'IN_STORE';
    public const TELESALES = 'TELESALES';
}
