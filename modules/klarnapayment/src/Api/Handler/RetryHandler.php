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

namespace KlarnaPayment\Module\Api\Handler;

use KlarnaPayment\Module\Api\Exception\KlarnaPaymentApiException;
use KlarnaPayment\Module\Api\Exception\RetryException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class RetryHandler implements RetryHandlerInterface
{
    const DEFAULT_MAX_RETRY = 5;

    /** {@inheritDoc} */
    public function retry(callable $function, int $maxRetries = self::DEFAULT_MAX_RETRY)
    {
        $tries = 0;

        while ($tries++ < $maxRetries) {
            try {
                return call_user_func($function);
            } catch (KlarnaPaymentApiException $exception) {
                if ($tries >= $maxRetries) {
                    throw RetryException::failedToPerformAction($exception);
                }
            }
        }
    }
}
