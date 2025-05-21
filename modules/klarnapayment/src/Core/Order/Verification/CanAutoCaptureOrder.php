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

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotVerifyOrderAction;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Utility\CompareUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CanAutoCaptureOrder
{
    /** @var Configuration */
    private $configuration;
    /** @var Context */
    private $context;

    public function __construct(
        Configuration $configuration,
        Context $context
    ) {
        $this->configuration = $configuration;
        $this->context = $context;
    }

    /**
     * @throws CouldNotVerifyOrderAction
     */
    public function verify(\Order $order): bool
    {
        if (!$this->isAutoCaptureEnabled()) {
            throw CouldNotVerifyOrderAction::autoCaptureDisabled();
        }

        if (!$this->isOrderStatusValid($order->getCurrentOrderState())) {
            throw CouldNotVerifyOrderAction::orderStatusIsNotInAutoCaptureList($order->getCurrentOrderState()->name[$this->context->getLanguageId()], $this->getAutoCaptureOrderStatuses());
        }

        return true;
    }

    private function isOrderStatusValid(\OrderState $orderState): bool
    {
        return CompareUtility::inArray($orderState->id, $this->getAutoCaptureOrderStatuses(), true);
    }

    private function getAutoCaptureOrderStatuses(): array
    {
        return explode(
            ',',
            $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_AUTO_CAPTURE_ORDER_STATUSES)
        );
    }

    private function isAutoCaptureEnabled(): bool
    {
        return $this->configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_AUTO_CAPTURE_ORDER);
    }
}
