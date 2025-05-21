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

class HashUtility
{
    public static function convertToUuidV4(string $string): string
    {
        $timeLow = substr($string, 0, 8);
        $timeMid = substr($string, 8, 4);

        $timeHiAndVersion = dechex((hexdec(substr($string, 12, 4)) & 0x0FFF) | 0x4000);
        $clockSeqHiAndReserved = dechex((hexdec(substr($string, 16, 2)) & 0x3F) | 0x80);
        $clockSeqLow = substr($string, 18, 2);
        $node = substr($string, 20, 12);

        return sprintf(
            '%s-%s-%s-%s%s-%s',
            $timeLow,
            $timeMid,
            $timeHiAndVersion,
            $clockSeqHiAndReserved,
            $clockSeqLow,
            $node
        );
    }
}
