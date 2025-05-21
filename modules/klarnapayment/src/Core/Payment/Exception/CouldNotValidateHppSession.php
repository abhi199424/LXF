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

namespace KlarnaPayment\Module\Core\Payment\Exception;

use KlarnaPayment\Module\Infrastructure\Exception\ExceptionCode;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CouldNotValidateHppSession extends KlarnaPaymentException
{
    public static function checkoutIsNotCompleted(string $sessionId): self
    {
        return new static(
            sprintf('Checkout is not completed with Session ID: [%s]', $sessionId),
            ExceptionCode::PAYMENT_FAILED_TO_VALIDATE_STATUS,
            null,
            [
                'session_id' => $sessionId,
            ]
        );
    }

    public static function authorizationTokenDoesNotMatch(string $sessionId): self
    {
        return new static(
            sprintf('Authorization tokens does not match with Session ID: [%s]', $sessionId),
            ExceptionCode::PAYMENT_FAILED_TO_VALIDATE_TOKEN,
            null,
            [
                'session_id' => $sessionId,
            ]
        );
    }
}
