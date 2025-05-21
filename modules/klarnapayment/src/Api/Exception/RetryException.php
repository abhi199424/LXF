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

namespace KlarnaPayment\Module\Api\Exception;

use KlarnaPayment\Module\Api\ExceptionCode;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class RetryException extends KlarnaPaymentApiException
{
    public static function failedToPerformAction(\Exception $exception): self
    {
        return new static(
            'Failed to perform action.',
            ExceptionCode::FAILED_TO_PERFORM_ACTION,
            $exception
        );
    }
}
