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
use KlarnaPayment\Module\Api\Requests\CreateHppSessionRequest;
use KlarnaPayment\Module\Api\Requests\RetrieveHppSessionRequest;
use KlarnaPayment\Module\Api\Responses\CreateHppSessionResponse;
use KlarnaPayment\Module\Api\Responses\RetrieveHppSessionResponse;
use KlarnaPayment\Module\Infrastructure\Api\ApiCaller;
use KlarnaPayment\Module\Infrastructure\Factory\ApiClientFactoryInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class HppSessionApiRepository implements HppSessionApiRepositoryInterface
{
    /**
     * @var \KlarnaPayment\Module\Api\ApiClient
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

    /** {@inheritDoc} */
    public function createHppSession(CreateHppSessionRequest $request): ?CreateHppSessionResponse
    {
        /** @var ?CreateHppSessionResponse $result */
        $result = $this->apiCaller->getResult(function () use ($request) {
            return $this->apiClient()->getHppSessionApi()->createHppSession($request);
        });

        return $result;
    }

    public function retrieveHppSession(RetrieveHppSessionRequest $request): ?RetrieveHppSessionResponse
    {
        /** @var ?RetrieveHppSessionResponse $result */
        $result = $this->apiCaller->getResult(function () use ($request) {
            return $this->apiClient()->getHppSessionApi()->retrieveHppSession($request);
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
