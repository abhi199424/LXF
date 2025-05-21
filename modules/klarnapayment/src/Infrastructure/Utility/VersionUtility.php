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

class VersionUtility
{
    public static function isPsVersionLessThan($version): ?int
    {
        return version_compare(_PS_VERSION_, $version, '<');
    }

    public static function isPsVersionGreaterThan($version): ?int
    {
        return version_compare(_PS_VERSION_, $version, '>');
    }

    public static function isPsVersionGreaterOrEqualTo($version): ?int
    {
        return version_compare(_PS_VERSION_, $version, '>=');
    }

    public static function isPsVersionLessThanOrEqualTo($version): ?int
    {
        return version_compare(_PS_VERSION_, $version, '<=');
    }

    public static function isPsVersionEqualTo($version): ?int
    {
        return version_compare(_PS_VERSION_, $version, '=');
    }

    public static function current(): string
    {
        return _PS_VERSION_;
    }
}
