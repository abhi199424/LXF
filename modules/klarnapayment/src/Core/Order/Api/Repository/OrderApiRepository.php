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

use KlarnaPayment\Module\Api\ApiClient;
use KlarnaPayment\Module\Api\Requests\CancelOrderRequest;
use KlarnaPayment\Module\Api\Requests\CreateCaptureRequest;
use KlarnaPayment\Module\Api\Requests\CreateOrderRequest;
use KlarnaPayment\Module\Api\Requests\CreateRefundRequest;
use KlarnaPayment\Module\Api\Requests\RetrieveOrderRequest;
use KlarnaPayment\Module\Api\Requests\UpdateMerchantReferencesRequest;
use KlarnaPayment\Module\Api\Responses\CreateOrderResponse;
use KlarnaPayment\Module\Api\Responses\RetrieveOrderResponse;
use KlarnaPayment\Module\Core\Shared\Provider\KecUserAgentDecorator;
use KlarnaPayment\Module\Infrastructure\Api\ApiCaller;
use KlarnaPayment\Module\Infrastructure\Factory\ApiClientFactoryInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}
class OrderApiRepository implements OrderApiRepositoryInterface
{
    /**
     * @var ApiClient
     */
    private $apiClient;
    /**
     * @var ApiCaller
     */
    private $apiCaller;
    /**
     * @var KecUserAgentDecorator
     */
    private $kecUserAgentProvider;
    /**
     * @var ApiClientFactoryInterface
     */
    private $apiClientFactory;

    public function __construct(
        ApiClientFactoryInterface $apiClientFactory,
        ApiCaller $apiCaller,
        KecUserAgentDecorator $kecUserAgentProvider
    ) {
        $this->apiCaller = $apiCaller;
        $this->kecUserAgentProvider = $kecUserAgentProvider;
        $this->apiClientFactory = $apiClientFactory;
    }

    /** {@inheritDoc} */
    public function retrieveOrder(RetrieveOrderRequest $request, ?string $currencyIso = null): ?RetrieveOrderResponse
    {
        /** @var ?RetrieveOrderResponse $result */
        $result = $this->apiCaller->getResult(function () use ($request, $currencyIso) {
            return $this->apiClient($currencyIso)->getOrderApi()->retrieveOrder($request);
        });

        return $result;
    }

    /** {@inheritDoc} */
    public function createCapture(CreateCaptureRequest $request, ?string $currencyIso = null): void
    {
        $this->apiCaller->getResult(function () use ($request, $currencyIso) {
            return $this->apiClient($currencyIso)->getOrderApi()->createCapture($request);
        });
    }

    /** {@inheritDoc} */
    public function createRefund(CreateRefundRequest $request): void
    {
        $this->apiCaller->getResult(function () use ($request) {
            return $this->apiClient()->getOrderApi()->createRefund($request);
        });
    }

    /** {@inheritDoc} */
    public function cancelOrder(CancelOrderRequest $request): void
    {
        $this->apiCaller->getResult(function () use ($request) {
            return $this->apiClient()->getOrderApi()->cancelOrder($request);
        });
    }

    /** {@inheritDoc} */
    public function createOrder(CreateOrderRequest $request, bool $isKecOrder): ?CreateOrderResponse
    {
        /** @var ?CreateOrderResponse $result */
        $result = $this->apiCaller->getResult(function () use ($request, $isKecOrder) {
            if ($isKecOrder) {
                $apiClient = $this->apiClientFactory->create([
                    'userAgent' => $this->kecUserAgentProvider->get(),
                    'currencyIso' => $request->getPurchaseCurrency(),
                ]);
            } else {
                $apiClient = $this->apiClient($request->getPurchaseCurrency());
            }

            return $apiClient->getOrderApi()->createOrder($request);
        });

        return $result;
    }

    /** {@inheritDoc} */
    public function updateMerchantReferences(UpdateMerchantReferencesRequest $request): void
    {
        $this->apiCaller->getResult(function () use ($request) {
            return $this->apiClient()->getOrderApi()->updateMerchantReferences($request);
        });
    }

    private function apiClient(?string $currencyIso = null): ApiClient
    {
        if ($this->apiClient === null) {
            $this->apiClient = $this->apiClientFactory->create(
                $currencyIso ? ['currencyIso' => $currencyIso] : []
            );
        }

        return $this->apiClient;
    }
}
