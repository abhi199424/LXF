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

namespace KlarnaPayment\Module\Core\Tools\Exception;

use KlarnaPayment\Module\Infrastructure\Exception\ExceptionCode;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CouldNotPruneOldRecords extends KlarnaPaymentException
{
    public static function failedToPrune(\Throwable $exception): self
    {
        return new static(
            'Failed to prune old records',
            ExceptionCode::TOOLS_FAILED_TO_PRUNE_RECORDS,
            $exception
        );
    }
}
