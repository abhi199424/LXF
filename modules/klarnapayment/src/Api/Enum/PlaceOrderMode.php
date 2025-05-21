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

class PlaceOrderMode
{
    public const PLACE_ORDER = 'PLACE_ORDER';
    public const CAPTURE_ORDER = 'CAPTURE_ORDER';
    public const NONE = 'NONE';
}
