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

class CouldNotChangeOrderStatus extends KlarnaPaymentException
{
    public static function unhandledOrderStatus(string $orderStatus): self
    {
        return new self(
            sprintf('Unhandled order status (%s)', $orderStatus),
            ExceptionCode::ORDER_UNHANDLED_ORDER_STATUS,
            null,
            [
                'order_status' => $orderStatus,
            ]
        );
    }

    public static function failedToFindInternalOrder(int $orderId): self
    {
        return new self(
            sprintf('Failed to find order %s', $orderId),
            ExceptionCode::ORDER_FAILED_TO_FIND_ORDER,
            null,
            [
                'order_id' => $orderId,
            ]
        );
    }

    public static function failedToFindExternalOrder(string $orderId): self
    {
        return new self(
            sprintf('Failed to find order %s', $orderId),
            ExceptionCode::ORDER_FAILED_TO_FIND_KLARNA_ORDER,
            null,
            [
                'order_id' => $orderId,
            ]
        );
    }
}
