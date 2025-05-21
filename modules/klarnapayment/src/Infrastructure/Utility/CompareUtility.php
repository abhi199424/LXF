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

class CompareUtility
{
    public static function inArray(string $needle, array $haystack, bool $strict, bool $caseSensitive = false): bool
    {
        if (!$caseSensitive) {
            $needle = strtolower($needle);
            $haystack = array_map('strtolower', $haystack);
        }

        return in_array($needle, $haystack, $strict);
    }
}
