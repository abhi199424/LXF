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

namespace KlarnaPayment\Module\Core\Order\Handler\Status;

use KlarnaPayment\Module\Api\Enum\OrderStatus;
use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Order\Action\ChangeOrderStatusAction;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotChangeOrderStatus;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Config\OrderStatusConfig;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class InternalOrderStatusHandler implements InternalOrderStatusHandlerInterface
{
    private $changeOrderStatusAction;
    private $configuration;

    public function __construct(
        ChangeOrderStatusAction $changeOrderStatusAction,
        Configuration $configuration
    ) {
        $this->changeOrderStatusAction = $changeOrderStatusAction;
        $this->configuration = $configuration;
    }

    /**
     * @throws CouldNotChangeOrderStatus
     * @throws KlarnaPaymentException
     */
    public function handle(int $internalOrderId, string $orderStatus): void
    {
        if (!$this->configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_ALLOW_ORDER_STATUS_CHANGE)) {
            return;
        }

        if ($orderStatus === OrderStatus::AUTHORIZED) {
            return;
        }

        if ($orderStatus === OrderStatus::CANCELLED) {
            $this->changeOrderStatusAction->run(
                $internalOrderId,
                $this->configuration->getByEnvironmentAsInteger(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_CANCELLED)
            );

            return;
        }

        if ($orderStatus === OrderStatus::CAPTURED) {
            $this->changeOrderStatusAction->run(
                $internalOrderId,
                $this->configuration->getByEnvironmentAsInteger(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_CAPTURED)
            );

            return;
        }

        if ($orderStatus === OrderStatus::PARTIALLY_CAPTURED) {
            $this->changeOrderStatusAction->run(
                $internalOrderId,
                $this->configuration->getByEnvironmentAsInteger(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_PARTIALLY_CAPTURED)
            );

            return;
        }

        if ($orderStatus === OrderStatus::ACCEPTED) {
            $this->changeOrderStatusAction->run(
                $internalOrderId,
                $this->configuration->getByEnvironmentAsInteger(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_ACCEPTED)
            );

            return;
        }

        if ($orderStatus === OrderStatus::FULLY_REFUNDED) {
            $this->changeOrderStatusAction->run(
                $internalOrderId,
                $this->configuration->getByEnvironmentAsInteger(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_REFUNDED)
            );

            return;
        }

        if ($orderStatus === OrderStatus::PROCESSING) {
            $this->changeOrderStatusAction->run(
                $internalOrderId,
                $this->configuration->getByEnvironmentAsInteger(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_PROCESSING)
            );

            return;
        }

        throw CouldNotChangeOrderStatus::unhandledOrderStatus($orderStatus);
    }
}
