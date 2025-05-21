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

namespace KlarnaPayment\Module\Core\Order\Api\Repository;

use KlarnaPayment\Module\Api\Exception\ApiException;
use KlarnaPayment\Module\Api\Requests\CancelOrderRequest;
use KlarnaPayment\Module\Api\Requests\CreateCaptureRequest;
use KlarnaPayment\Module\Api\Requests\CreateOrderRequest;
use KlarnaPayment\Module\Api\Requests\CreateRefundRequest;
use KlarnaPayment\Module\Api\Requests\RetrieveOrderRequest;
use KlarnaPayment\Module\Api\Requests\UpdateMerchantReferencesRequest;
use KlarnaPayment\Module\Api\Responses\CreateOrderResponse;
use KlarnaPayment\Module\Api\Responses\RetrieveOrderResponse;

if (!defined('_PS_VERSION_')) {
    exit;
}

interface OrderApiRepositoryInterface
{
    /**
     * @throws ApiException
     */
    public function retrieveOrder(RetrieveOrderRequest $request): ?RetrieveOrderResponse;

    /**
     * @throws ApiException
     */
    public function createCapture(CreateCaptureRequest $request): void;

    /**
     * @throws ApiException
     */
    public function createRefund(CreateRefundRequest $request): void;

    /**
     * @throws ApiException
     */
    public function cancelOrder(CancelOrderRequest $request): void;

    /**
     * @throws ApiException
     */
    public function createOrder(CreateOrderRequest $request, bool $isKecOrder): ?CreateOrderResponse;

    /**
     * @throws ApiException
     */
    public function updateMerchantReferences(UpdateMerchantReferencesRequest $request): void;
}
