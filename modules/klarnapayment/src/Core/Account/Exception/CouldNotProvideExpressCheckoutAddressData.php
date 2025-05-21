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

class CouldNotProvideExpressCheckoutAddressData extends KlarnaPaymentException
{
    public static function failedToFindCountry(string $countryIsoCode): self
    {
        return new self(
            sprintf(
                'Failed to find country. Country ISO code: (%s)',
                $countryIsoCode
            ),
            ExceptionCode::ACCOUNT_FAILED_TO_FIND_COUNTRY
        );
    }

    public static function failedToFindState(string $stateIsoCode): self
    {
        return new self(
            sprintf(
                'Failed to find state. State ISO code: (%s)',
                $stateIsoCode
            ),
            ExceptionCode::ACCOUNT_FAILED_TO_FIND_STATE
        );
    }
}
