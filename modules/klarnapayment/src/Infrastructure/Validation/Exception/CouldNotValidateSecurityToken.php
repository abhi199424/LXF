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

namespace KlarnaPayment\Module\Infrastructure\Validation\Exception;

use KlarnaPayment\Module\Infrastructure\Exception\ExceptionCode;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CouldNotValidateSecurityToken extends KlarnaPaymentException
{
    public static function couldNotValidateSecurityToken(): CouldNotValidateSecurityToken
    {
        return new self(
            'Could not validate security token.',
            ExceptionCode::PAYMENT_FAILED_COULD_NOT_VALIDATE_SECURITY_TOKEN
        );
    }
}
