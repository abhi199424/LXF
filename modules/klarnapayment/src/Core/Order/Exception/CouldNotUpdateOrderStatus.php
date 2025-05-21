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

class CouldNotUpdateOrderStatus extends KlarnaPaymentException
{
    public static function failedToFindOrder(int $orderId): self
    {
        return new self(
            sprintf('Failed to find order [%s]', $orderId),
            ExceptionCode::ORDER_FAILED_TO_FIND_ORDER,
            null,
            [
                'order_id' => $orderId,
            ]
        );
    }

    public static function failedToRetrieveOrder(\Throwable $exception): self
    {
        return new self(
            'Failed to retrieve order',
            ExceptionCode::ORDER_FAILED_TO_RETRIEVE_ORDER,
            $exception
        );
    }

    public static function failedToUpdateOrderStatus(int $orderId, \Throwable $exception): self
    {
        return new static(
            sprintf('Failed to change order [%s] status.', $orderId),
            ExceptionCode::ORDER_FAILED_TO_CHANGE_ORDER_STATUS,
            $exception,
            [
                'order_id' => $orderId,
            ]
        );
    }
}
