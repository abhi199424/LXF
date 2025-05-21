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

namespace KlarnaPayment\Module\Core\Payment\Api\Repository;

use KlarnaPayment\Module\Api\ApiClient;
use KlarnaPayment\Module\Api\Requests\CreatePaymentSessionRequest;
use KlarnaPayment\Module\Api\Requests\RetrievePaymentSessionRequest;
use KlarnaPayment\Module\Api\Requests\UpdatePaymentSessionRequest;
use KlarnaPayment\Module\Api\Responses\CreatePaymentSessionResponse;
use KlarnaPayment\Module\Api\Responses\RetrievePaymentSessionResponse;
use KlarnaPayment\Module\Infrastructure\Api\ApiCaller;
use KlarnaPayment\Module\Infrastructure\Factory\ApiClientFactoryInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class SessionApiRepository implements SessionApiRepositoryInterface
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
     * @var ApiClientFactoryInterface
     */
    private $apiClientFactory;

    public function __construct(ApiClientFactoryInterface $apiClientFactory, ApiCaller $apiCaller)
    {
        $this->apiClientFactory = $apiClientFactory;
        $this->apiCaller = $apiCaller;
    }

    public function setApiClient(ApiClient $apiClient): void
    {
        $this->apiClient = $apiClient;
    }

    /** {@inheritDoc} */
    public function createPaymentSession(CreatePaymentSessionRequest $request): ?CreatePaymentSessionResponse
    {
        /** @var ?CreatePaymentSessionResponse $result */
        $result = $this->apiCaller->getResult(function () use ($request) {
            return $this->apiClient()->getSessionApi()->createPaymentSession($request);
        });

        return $result;
    }

    /** {@inheritDoc} */
    public function updatePaymentSession(UpdatePaymentSessionRequest $request): void
    {
        $this->apiCaller->getResult(function () use ($request) {
            return $this->apiClient()->getSessionApi()->updatePaymentSession($request);
        });
    }

    /** {@inheritDoc} */
    public function retrievePaymentSession(RetrievePaymentSessionRequest $request): RetrievePaymentSessionResponse
    {
        /** @var RetrievePaymentSessionResponse $result */
        $result = $this->apiCaller->getResult(function () use ($request) {
            return $this->apiClient()->getSessionApi()->retrievePaymentSession($request);
        });

        return $result;
    }

    private function apiClient(): ApiClient
    {
        if ($this->apiClient === null) {
            $this->apiClient = $this->apiClientFactory->create();
        }

        return $this->apiClient;
    }
}
