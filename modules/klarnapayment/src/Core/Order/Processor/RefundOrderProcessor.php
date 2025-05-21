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
use KlarnaPayment\Module\Core\Order\Action\CreateRefundAction;
use KlarnaPayment\Module\Core\Order\DTO\CreateRefundRequestData;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotCreateRefund;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotVerifyOrderAction;
use KlarnaPayment\Module\Core\Order\Verification\CanRefundOrder;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class RefundOrderProcessor
{
    private $canRefundOrder;
    private $createRefundAction;

    public function __construct(
        CanRefundOrder $canRefundOrder,
        CreateRefundAction $createRefundAction
    ) {
        $this->canRefundOrder = $canRefundOrder;
        $this->createRefundAction = $createRefundAction;
    }

    /**
     * @throws KlarnaPaymentException
     */
    public function processAction(Order $externalOrder, int $refundedAmount, array $orderLineIds = []): void
    {
        try {
            $this->canRefundOrder->verify($externalOrder);
        } catch (CouldNotVerifyOrderAction $exception) {
            throw CouldNotCreateRefund::verificationFailed($exception);
        }

        $this->createRefundAction->run(CreateRefundRequestData::create(
            $externalOrder,
            $refundedAmount,
            $orderLineIds
        ));
    }
}
