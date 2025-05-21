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

namespace KlarnaPayment\Module\Core\Order\Exception;

use KlarnaPayment\Module\Infrastructure\Exception\ExceptionCode;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CouldNotAutoCapture extends OrderProcessException
{
    public static function failedToAutoCapture(KlarnaPaymentException $exception): self
    {
        return new self(
            'Failed to auto capture',
            ExceptionCode::ORDER_FAILED_TO_AUTO_CAPTURE,
            $exception
        );
    }
}
