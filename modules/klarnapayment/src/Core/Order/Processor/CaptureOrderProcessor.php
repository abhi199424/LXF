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
use KlarnaPayment\Module\Core\Order\Action\CreateCaptureAction;
use KlarnaPayment\Module\Core\Order\DTO\CreateCaptureRequestData;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotCaptureOrder;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotVerifyOrderAction;
use KlarnaPayment\Module\Core\Order\Verification\CanCaptureOrder;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CaptureOrderProcessor
{
    private $canCaptureOrder;
    private $createCaptureAction;

    public function __construct(
        CanCaptureOrder $canCaptureOrder,
        CreateCaptureAction $createCaptureAction
    ) {
        $this->canCaptureOrder = $canCaptureOrder;
        $this->createCaptureAction = $createCaptureAction;
    }

    /**
     * @throws KlarnaPaymentException
     */
    public function processAction(Order $externalOrder, int $capturedAmount, array $orderLineIds = [], ?string $currencyIso = null): void
    {
        try {
            $this->canCaptureOrder->verify($externalOrder);
        } catch (CouldNotVerifyOrderAction $exception) {
            throw CouldNotCaptureOrder::verificationFailed($exception);
        }

        $this->createCaptureAction->run(CreateCaptureRequestData::create(
            $externalOrder,
            $capturedAmount,
            $orderLineIds,
            $currencyIso
        ));
    }
}
