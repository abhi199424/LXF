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

namespace KlarnaPayment\Module\Core\Account\Exception;

use KlarnaPayment\Module\Infrastructure\Exception\ExceptionCode;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CouldNotCreateAddress extends KlarnaPaymentException
{
    public static function inactiveCountry(string $isoCode): self
    {
        return new static(
            sprintf('Inactive country (ISO code - %s)', $isoCode),
            ExceptionCode::ACCOUNT_INACTIVE_COUNTRY,
            null,
            [
                'ISO code' => $isoCode,
            ]
        );
    }

    public static function inactiveAddressState(string $isoCode): self
    {
        return new static(
            sprintf('Inactive address state (ISO code - %s)', $isoCode),
            ExceptionCode::ACCOUNT_INACTIVE_ADDRESS_STATE,
            null,
            [
                'ISO code' => $isoCode,
            ]
        );
    }
}
