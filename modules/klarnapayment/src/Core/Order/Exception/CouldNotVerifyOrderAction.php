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

class CouldNotVerifyOrderAction extends KlarnaPaymentException
{
    public static function orderStatusIsInvalid(string $orderStatus, array $validStatuses): self
    {
        return new self(
            'Order status is invalid.',
            ExceptionCode::ORDER_STATUS_IS_INVALID,
            null,
            [
                'order_status' => $orderStatus,
                'valid_statuses' => implode(',', $validStatuses),
            ]
        );
    }

    public static function orderHasAlreadyBeenFullyRefunded(string $orderId): self
    {
        return new self(
            sprintf('Order [%s] has already been fully refunded.', $orderId),
            ExceptionCode::ORDER_HAS_ALREADY_BEEN_FULLY_REFUNDED,
            null,
            [
                'order_id' => $orderId,
            ]
        );
    }

    public static function autoCaptureDisabled(): self
    {
        return new self(
            'Auto capture is disabled',
            ExceptionCode::ORDER_AUTO_CAPTURE_DISABLED,
            null
        );
    }

    public static function orderStatusIsNotInAutoCaptureList(string $orderStatus, array $validStatuses): self
    {
        return new self(
            'Order status is not in auto capture list.',
            ExceptionCode::ORDER_STATUS_IS_NOT_IN_AUTO_CAPTURE_LIST,
            null,
            [
                'order_status' => $orderStatus,
                'valid_statuses' => implode(',', $validStatuses),
            ]
        );
    }
}
