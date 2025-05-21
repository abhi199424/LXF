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

use PrestaShop\Decimal\Exception\DivisionByZeroException;
use PrestaShop\Decimal\Number;

if (!defined('_PS_VERSION_')) {
    exit;
}

class NumberUtility
{
    public const FLOAT_PRECISION = 6;

    public static function multiplyBy(float $argument, int $times, int $precision = 0, ?int $roundingMode = null): string
    {
        $number = new Number((string) $argument);
        $number = $number->times(new Number((string) $times));

        return \Tools::ps_round((float) (string) $number, $precision, $roundingMode);
    }

    public static function multiplyByFloat(float $argument1, float $argument2, int $precision = 0, ?int $roundingMode = null): string
    {
        $number1 = new Number((string) $argument1);
        $number2 = new Number((string) $argument2);

        $number = $number1->times($number2);

        return \Tools::ps_round((float) (string) $number, $precision, $roundingMode);
    }

    public static function minus(float $argument1, float $argument2, int $precision = 0, ?int $roundingMode = null): string
    {
        $number1 = new Number((string) $argument1);
        $number2 = new Number((string) $argument2);

        $number = $number1->minus($number2);

        return \Tools::ps_round((float) (string) $number, $precision, $roundingMode);
    }

    public static function add(float $argument1, float $argument2, int $precision = 0, ?int $roundingMode = null): string
    {
        $number1 = new Number((string) $argument1);
        $number2 = new Number((string) $argument2);

        $number = $number1->plus($number2);

        return \Tools::ps_round((float) (string) $number, $precision, $roundingMode);
    }

    public static function round(float $argument1, int $precision = 1, ?int $roundingMode = null): string
    {
        $number = new Number((string) $argument1);

        return \Tools::ps_round((float) (string) $number, $precision, $roundingMode);
    }

    /**
     * @throws DivisionByZeroException
     */
    public static function divideBy(float $argument, float $times, int $precision = 0, ?int $roundingMode = null): string
    {
        $number = new Number((string) $argument);
        $number = $number->dividedBy(new Number((string) $times));

        return \Tools::ps_round((float) (string) $number, $precision, $roundingMode);
    }
}
