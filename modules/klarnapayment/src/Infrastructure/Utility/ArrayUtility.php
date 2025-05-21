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

class ArrayUtility
{
    public static function recursiveArrayMerge($firstArray, $secondArray): array
    {
        foreach ($secondArray as $key => $value) {
            if (is_array($value) && isset($firstArray[$key]) && is_array($firstArray[$key])) {
                $firstArray[$key] = self::recursiveArrayMerge($firstArray[$key], $value);
            } else {
                $firstArray[$key] = $value;
            }
        }

        return $firstArray;
    }
}
