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

if (!defined('_PS_VERSION_')) {
    exit;
}

class CouldNotCaptureOrder extends OrderProcessException
{
    public static function invalidCaptureRequest(int $captureAmount): self
    {
        return new static(
            sprintf('Failed to capture amount. Given amount (%s) is invalid', $captureAmount),
            ExceptionCode::ORDER_CAPTURE_AMOUNT_IS_INVALID
        );
    }
}
