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

namespace KlarnaPayment\Module\Infrastructure\Utility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ChecksumUtility
{
    public static function calculate(array $payload): string
    {
        return md5(json_encode($payload));
    }
}
