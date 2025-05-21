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

namespace KlarnaPayment\Module\Core\Order\Processor;

use KlarnaPayment\Module\Api\Models\Order;
use KlarnaPayment\Module\Core\Order\Action\CancelOrderAction;
use KlarnaPayment\Module\Core\Order\DTO\CancelOrderRequestData;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotCancelOrder;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotVerifyOrderAction;
use KlarnaPayment\Module\Core\Order\Verification\CanCancelOrder;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CancelOrderProcessor
{
    private $canCancelOrder;
    private $cancelOrderAction;

    public function __construct(
        CanCancelOrder $canCancelOrder,
        CancelOrderAction $cancelOrderAction
    ) {
        $this->canCancelOrder = $canCancelOrder;
        $this->cancelOrderAction = $cancelOrderAction;
    }

    /**
     * @throws KlarnaPaymentException
     */
    public function processAction(Order $order): void
    {
        try {
            $this->canCancelOrder->verify($order);
        } catch (CouldNotVerifyOrderAction $exception) {
            throw CouldNotCancelOrder::verificationFailed($exception);
        }

        $this->cancelOrderAction->run(CancelOrderRequestData::create(
            $order->getOrderId()
        ));
    }
}
