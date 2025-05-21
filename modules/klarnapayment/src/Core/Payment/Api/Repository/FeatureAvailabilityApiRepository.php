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
use KlarnaPayment\Module\Api\Requests\FeatureAvailabilityRequest;
use KlarnaPayment\Module\Api\Responses\RetrieveFeatureAvailabilityResponse;
use KlarnaPayment\Module\Infrastructure\Api\ApiCaller;
use KlarnaPayment\Module\Infrastructure\Factory\ApiClientFactoryInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class FeatureAvailabilityApiRepository implements FeatureAvailabilityApiRepositoryInterface
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
    private function apiClient(): ApiClient
    {
        if ($this->apiClient === null) {
            $this->apiClient = $this->apiClientFactory->create();
        }

        return $this->apiClient;
    }

    public function setApiClient(ApiClient $apiClient): void
    {
        $this->apiClient = $apiClient;
    }

    public function retrieveMerchantFeatureAvailability(FeatureAvailabilityRequest $request): ?RetrieveFeatureAvailabilityResponse
    {
        /** @var ?RetrieveFeatureAvailabilityResponse $result */
        $result = $this->apiCaller->getResult(function () use ($request) {
            return $this->apiClient()->getFeatureAvailabilityApi()->retrieveFeatureAvailability($request);
        });

        return $result;
    }
}
