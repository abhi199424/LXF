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

class CouldNotUpdateAddress extends KlarnaPaymentException
{
    public static function addressNotFound(int $id): self
    {
        return new static(
            sprintf('Could not find address with ID: [%s]', $id),
            ExceptionCode::ACCOUNT_COULD_NOT_FIND_ADDRESS,
            null,
            [
                'id' => $id,
            ]
        );
    }

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
