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

namespace KlarnaPayment\Module\Core\Order\Provider;

use KlarnaPayment\Module\Api\Requests\CreateRefundRequest;
use KlarnaPayment\Module\Core\Order\DTO\CreateRefundRequestData;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Utility\NumberUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CreateRefundRequestProvider
{
    /** @var Context */
    private $context;

    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    public function get(CreateRefundRequestData $createRefundRequestData): CreateRefundRequest
    {
        $createRefundRequest = new CreateRefundRequest();

        $createRefundRequest->setOrderId($createRefundRequestData->getOrder()->getOrderId());

        if (!empty($createRefundRequestData->getOrderLineIds())) {
            $orderLinesToCapture = [];

            foreach ($createRefundRequestData->getOrder()->getOrderLines() as $orderLine) {
                if (in_array($orderLine->getMerchantData(), $createRefundRequestData->getOrderLineIds())) {
                    $orderLinesToCapture[] = $orderLine;
                }
            }

            $refundedAmount = 0;

            foreach ($orderLinesToCapture as $item) {
                $refundedAmount = NumberUtility::add((float) $refundedAmount, $item->getTotalAmount(), $this->context->getComputingPrecision());
            }

            $createRefundRequest->setRefundedAmount($refundedAmount);
            $createRefundRequest->setOrderLines($orderLinesToCapture);

            return $createRefundRequest;
        }

        $createRefundRequest->setRefundedAmount($createRefundRequestData->getRefundedAmount());

        return $createRefundRequest;
    }
}
