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

namespace KlarnaPayment\Module\Infrastructure\Obfuscator;

use Invertus\Knapsack\Collection;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Obfuscator
{
    public static function recursiveArray(array $data, array $keysToReplace): array
    {
        $keysToReplace = Collection::from($keysToReplace)
            ->map(function ($keyToReplace) {
                return strtolower($keyToReplace);
            })->toArray();

        array_walk_recursive($data, static function (&$item, $key) use ($keysToReplace) {
            if (in_array(strtolower($key), $keysToReplace, false)) {
                if (($itemLength = strlen($item)) < 8) {
                    $replaceLength = $itemLength;
                } else {
                    $replaceLength = $itemLength - 4;
                }

                $item = substr_replace($item, str_repeat('*', $replaceLength), 0, $replaceLength);
            }
        });

        return $data;
    }

    public static function url(string $url, array $keysToReplace): string
    {
        $result = $url;

        foreach ($keysToReplace as $keyToReplace) {
            $foundValue = '';

            preg_match('/(?i)(?<=' . $keyToReplace . '=)[^&|$]+/', $result, $foundValue);

            if (empty($foundValue)) {
                continue;
            }

            if (($itemLength = strlen($foundValue[0])) < 8) {
                $replaceLength = $itemLength;
                $replacementOffset = $itemLength;
            } else {
                $replaceLength = $itemLength - 4;
                $replacementOffset = -4;
            }

            $replacement = str_repeat('*', $replaceLength) . substr($foundValue[0], $replacementOffset);

            $result = preg_replace('/(?i)(?<=' . $keyToReplace . '=)[^&|$]+/', $replacement, $result);
        }

        /*
         * NOTE: if result becomes empty, unchanged URL is returned
         */
        if (empty($result)) {
            return $url;
        }

        return $result;
    }
}
