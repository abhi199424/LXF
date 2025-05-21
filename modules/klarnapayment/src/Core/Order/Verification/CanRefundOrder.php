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

namespace KlarnaPayment\Module\Core\Order\Verification;

use KlarnaPayment\Module\Api\Enum\OrderStatus;
use KlarnaPayment\Module\Api\Models\Order;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotVerifyOrderAction;
use KlarnaPayment\Module\Infrastructure\Utility\CompareUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CanRefundOrder
{
    private const VALID_ORDER_STATUSES = [
        OrderStatus::CAPTURED,
        OrderStatus::PARTIALLY_CAPTURED,
    ];

    /**
     * @throws CouldNotVerifyOrderAction
     */
    public function verify(Order $order): bool
    {
        if (!$this->isOrderStatusValid($order)) {
            throw CouldNotVerifyOrderAction::orderStatusIsInvalid($order->getStatus(), self::VALID_ORDER_STATUSES);
        }

        if ($order->getCapturedAmount() === $order->getRefundedAmount()) {
            throw CouldNotVerifyOrderAction::orderHasAlreadyBeenFullyRefunded($order->getOrderId());
        }

        return true;
    }

    private function isOrderStatusValid(Order $order): bool
    {
        return CompareUtility::inArray($order->getStatus(), self::VALID_ORDER_STATUSES, true);
    }
}
