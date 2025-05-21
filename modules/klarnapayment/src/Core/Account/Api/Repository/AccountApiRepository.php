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

namespace KlarnaPayment\Module\Core\Account\Api\Repository;

use KlarnaPayment\Module\Api\ApiClient;
use KlarnaPayment\Module\Api\Requests\RefreshTokenRequest;
use KlarnaPayment\Module\Api\Responses\RefreshTokenResponse;
use KlarnaPayment\Module\Api\Responses\RetrieveJWKSResponse;
use KlarnaPayment\Module\Infrastructure\Api\ApiCaller;
use KlarnaPayment\Module\Infrastructure\Factory\ApiClientFactoryInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AccountApiRepository implements AccountApiRepositoryInterface
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

    /** {@inheritDoc} */
    public function retrieveJWKS(): ?RetrieveJWKSResponse
    {
        /** @var ?RetrieveJWKSResponse $result */
        $result = $this->apiCaller->getResult(function () {
            return $this->apiClient()->getAccountApi()->retrieveJWKS();
        });

        return $result;
    }

    /** {@inheritDoc} */
    public function refreshToken(RefreshTokenRequest $request): RefreshTokenResponse
    {
        /** @var ?RefreshTokenResponse $result */
        $result = $this->apiCaller->getResult(function () use ($request) {
            return $this->apiClient()->getAccountApi()->refreshToken($request);
        });

        return $result;
    }

    private function apiClient(): ApiClient
    {
        if ($this->apiClient === null) {
            $this->apiClient = $this->apiClientFactory->create([
                'apiKey' => '',
            ]);
        }

        return $this->apiClient;
    }
}
