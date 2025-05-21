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

use KlarnaPayment\Module\Api\Requests\CancelOrderRequest;
use KlarnaPayment\Module\Core\Order\DTO\CancelOrderRequestData;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CancelOrderRequestProvider
{
    public function get(CancelOrderRequestData $createCaptureRequestData): CancelOrderRequest
    {
        $cancelOrderRequest = new CancelOrderRequest();

        $cancelOrderRequest->setOrderId($createCaptureRequestData->getOrderId());

        return $cancelOrderRequest;
    }
}
