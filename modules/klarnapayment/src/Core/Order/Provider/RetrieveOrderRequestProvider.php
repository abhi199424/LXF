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

use KlarnaPayment\Module\Api\Requests\RetrieveOrderRequest;
use KlarnaPayment\Module\Core\Order\DTO\RetrieveOrderRequestData;

if (!defined('_PS_VERSION_')) {
    exit;
}

class RetrieveOrderRequestProvider
{
    public function get(RetrieveOrderRequestData $retrieveOrderRequestData): RetrieveOrderRequest
    {
        $retrieveOrderRequest = new RetrieveOrderRequest();

        $retrieveOrderRequest->setOrderId($retrieveOrderRequestData->getOrderId());

        return $retrieveOrderRequest;
    }
}
