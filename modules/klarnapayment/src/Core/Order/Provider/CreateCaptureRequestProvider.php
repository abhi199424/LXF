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

use KlarnaPayment\Module\Api\Models\ShippingInfo;
use KlarnaPayment\Module\Api\Requests\CreateCaptureRequest;
use KlarnaPayment\Module\Core\Order\DTO\CreateCaptureRequestData;
use KlarnaPayment\Module\Core\Order\Repository\KlarnaPaymentOrderRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\CarrierRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\OrderCarrierRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Utility\NumberUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CreateCaptureRequestProvider
{
    private $context;
    private $orderCarrierRepository;
    private $klarnaPaymentOrderRepository;
    private $carrierRepository;

    public function __construct(
        Context $context,
        OrderCarrierRepositoryInterface $orderCarrierRepository,
        CarrierRepositoryInterface $carrierRepository,
        KlarnaPaymentOrderRepositoryInterface $klarnaPaymentOrderRepository
    ) {
        $this->context = $context;
        $this->orderCarrierRepository = $orderCarrierRepository;
        $this->carrierRepository = $carrierRepository;
        $this->klarnaPaymentOrderRepository = $klarnaPaymentOrderRepository;
    }

    public function get(CreateCaptureRequestData $createCaptureRequestData): CreateCaptureRequest
    {
        $createCaptureRequest = new CreateCaptureRequest();

        $externalOrderId = $createCaptureRequestData->getOrder()->getOrderId();
        $createCaptureRequest->setOrderId($externalOrderId);

        /** @var \KlarnaPaymentOrder $klarnaPaymentOrder */
        $klarnaPaymentOrder = $this->klarnaPaymentOrderRepository->findOneBy(['id_external' => $externalOrderId]);
        /** @var \OrderCarrier[] $orderCarriers */
        $orderCarriers = $this->orderCarrierRepository->findAllInCollection()->where('id_order', '=', $klarnaPaymentOrder->id_internal)->getResults();

        if (!empty($orderCarriers)) {
            $allCarriersShippingInfo = [];

            foreach ($orderCarriers as $orderCarrier) {
                /** @var \Carrier|null $carrier */
                $carrier = $this->carrierRepository->findOneBy(['id_carrier' => $orderCarrier->id_carrier]);

                if (empty($carrier->url)) {
                    $trackingUri = '';
                } else {
                    $trackingUri = str_replace('@', $orderCarrier->tracking_number, $carrier->url);
                }

                $shippingInfo = new ShippingInfo();

                $shippingInfo->setTrackingNumber($orderCarrier->tracking_number);
                $shippingInfo->setTrackingUri($trackingUri);

                $allCarriersShippingInfo[] = $shippingInfo;
            }

            $createCaptureRequest->setShippingInfo($allCarriersShippingInfo);
        }

        if (!empty($createCaptureRequestData->getOrderLineIds())) {
            $orderLinesToCapture = [];

            foreach ($createCaptureRequestData->getOrder()->getOrderLines() as $orderLine) {
                if (in_array($orderLine->getMerchantData(), $createCaptureRequestData->getOrderLineIds())) {
                    $orderLinesToCapture[] = $orderLine;
                }
            }

            $captureAmount = 0;

            foreach ($orderLinesToCapture as $item) {
                $captureAmount = NumberUtility::add((float) $captureAmount, $item->getTotalAmount(), $this->context->getComputingPrecision());
            }

            $createCaptureRequest->setCapturedAmount($captureAmount);
            $createCaptureRequest->setOrderLines($orderLinesToCapture);

            return $createCaptureRequest;
        }

        $createCaptureRequest->setCapturedAmount($createCaptureRequestData->getCapturedAmount());

        return $createCaptureRequest;
    }
}
