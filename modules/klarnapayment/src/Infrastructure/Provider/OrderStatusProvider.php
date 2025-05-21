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

namespace KlarnaPayment\Module\Infrastructure\Provider;

use KlarnaPayment\Module\Api\Enum\OrderStatus;
use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Config\OrderStatusConfig;

if (!defined('_PS_VERSION_')) {
    exit;
}

class OrderStatusProvider
{
    const TRANSLATION_ID = 'OrderStatusProvider';

    /**
     * @var \KlarnaPayment
     */
    private $module;
    private $configuration;

    public function __construct(
        ModuleFactory $moduleFactory,
        Configuration $configuration
    ) {
        $this->module = $moduleFactory->getModule();
        $this->configuration = $configuration;
    }

    public function getModuleOrderStatuses(): array
    {
        return [
            $this->getOrderStatusConstantNameByEnvironment(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_ACCEPTED) => [
                'id' => $this->configuration->getByEnvironment(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_ACCEPTED),
                'name' => $this->module->l('New', self::TRANSLATION_ID),
                'definition' => $this->module->l('Payment authorized / order created', self::TRANSLATION_ID),
            ],
            $this->getOrderStatusConstantNameByEnvironment(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_CAPTURED) => [
                'id' => $this->configuration->getByEnvironment(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_CAPTURED),
                'name' => $this->module->l('Captured', self::TRANSLATION_ID),
                'definition' => $this->module->l('Full payment amount captured', self::TRANSLATION_ID),
            ],
            $this->getOrderStatusConstantNameByEnvironment(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_CANCELLED) => [
                'id' => $this->configuration->getByEnvironment(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_CANCELLED),
                'name' => $this->module->l('Cancelled', self::TRANSLATION_ID),
                'definition' => $this->module->l('Payment cancelled', self::TRANSLATION_ID),
            ],
            $this->getOrderStatusConstantNameByEnvironment(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_REFUNDED) => [
                'id' => $this->configuration->getByEnvironment(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_REFUNDED),
                'name' => $this->module->l('Refunded', self::TRANSLATION_ID),
                'definition' => $this->module->l('Refunded', self::TRANSLATION_ID),
            ],
            $this->getOrderStatusConstantNameByEnvironment(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_PARTIALLY_CAPTURED) => [
                'id' => $this->configuration->getByEnvironment(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_PARTIALLY_CAPTURED),
                'name' => $this->module->l('Partially captured', self::TRANSLATION_ID),
                'definition' => $this->module->l('Partial payment amount captured', self::TRANSLATION_ID),
            ],
        ];
    }

    /**
     * @param array{sandbox: string, production: string} $orderStatus
     *
     * @return string
     */
    private function getOrderStatusConstantNameByEnvironment(array $orderStatus): string
    {
        return $orderStatus[
            $this->configuration->get(Config::KLARNA_PAYMENT_IS_PRODUCTION_ENVIRONMENT) ?
                'production' : 'sandbox'
        ];
    }

    public function getMappedOrderStatusValues(): array
    {
        return [
            OrderStatus::CAPTURED => $this->configuration->getByEnvironmentAsInteger(
                OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_CAPTURED
            ),
            OrderStatus::PARTIALLY_CAPTURED => $this->configuration->getByEnvironmentAsInteger(
                OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_PARTIALLY_CAPTURED
            ),
            OrderStatus::CANCELLED => $this->configuration->getByEnvironmentAsInteger(
                OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_CANCELLED
            ),
            OrderStatus::FULLY_REFUNDED => $this->configuration->getByEnvironmentAsInteger(
                OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_REFUNDED
            ),
            // TODO: replace pending with authorized status

            OrderStatus::AUTHORIZED => $this->configuration->getByEnvironmentAsInteger(
                OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_ACCEPTED
            ),
            OrderStatus::EXPIRED => 0,
            OrderStatus::CLOSED => 0,
        ];
    }
}
