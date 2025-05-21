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

namespace KlarnaPayment\Module\Api;

use KlarnaPayment\Module\Api\Apis\AccountApi;
use KlarnaPayment\Module\Api\Apis\FeatureAvailabilityApi;
use KlarnaPayment\Module\Api\Apis\HppSessionApi;
use KlarnaPayment\Module\Api\Apis\OrderApi;
use KlarnaPayment\Module\Api\Apis\SessionApi;
use KlarnaPayment\Module\Api\Exception\CouldNotConfigureApiClient;
use KlarnaPayment\Module\Api\Http\HttpResponse;
use KlarnaPayment\Module\Core\Config\Config;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ApiClient
{
    private $sessionApi;
    private $hppSessionApi;
    private $orderApi;
    private $accountApi;
    private $featureAvailabilityApi;
    private $baseUrl = '';
    private $apiKey = '';
    public $additionalHeaders = [];
    private $environment = Environment::SANDBOX;
    private $userAgent = '';
    private $endpointLocale = '';

    /**
     * @throws CouldNotConfigureApiClient
     */
    public function __construct(array $configOptions = [])
    {
        if (isset($configOptions['environment'], $configOptions['endpoint'])) {
            $this->baseUrl = $this->getBaseUrlByEnvironment($configOptions['environment'], $configOptions['endpoint']);
            $this->environment = $configOptions['environment'];
        }

        if (isset($configOptions['customUrl'])) {
            $this->baseUrl = $configOptions['customUrl'];
        }

        if (!isset($configOptions['apiKey'])) {
            throw CouldNotConfigureApiClient::missingApiKey();
        }

        if (isset($configOptions['additionalHeaders'])) {
            $this->additionalHeaders = $configOptions['additionalHeaders'];
        }

        if (isset($configOptions['userAgent'])) {
            $this->userAgent = $configOptions['userAgent'];
        }

        if (isset($configOptions['endpointLocale'])) {
            $this->endpointLocale = $configOptions['endpointLocale'];
        }

        $this->apiKey = $configOptions['apiKey'];
    }

    public function getSessionApi(): SessionApi
    {
        if ($this->sessionApi == null) {
            $this->sessionApi = new SessionApi($this);
        }

        return $this->sessionApi;
    }

    public function getHppSessionApi(): HppSessionApi
    {
        if ($this->hppSessionApi == null) {
            $this->hppSessionApi = new HppSessionApi($this);
        }

        return $this->hppSessionApi;
    }

    public function getOrderApi(): OrderApi
    {
        if ($this->orderApi == null) {
            $this->orderApi = new OrderApi($this);
        }

        return $this->orderApi;
    }

    public function getAccountApi(): AccountApi
    {
        if ($this->accountApi == null) {
            $this->accountApi = new AccountApi($this);
        }

        return $this->accountApi;
    }

    public function getFeatureAvailabilityApi(): FeatureAvailabilityApi
    {
        if ($this->featureAvailabilityApi == null) {
            $this->featureAvailabilityApi = new FeatureAvailabilityApi($this);
        }

        return $this->featureAvailabilityApi;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getBaseUrl(): string
    {
        return $this->baseUrl;
    }

    public function getAdditionalHeaders(): array
    {
        return $this->additionalHeaders;
    }

    public function getUserAgent(): ?string
    {
        return $this->userAgent;
    }

    public function getEndpointLocale(): string
    {
        return $this->endpointLocale;
    }

    public function getEnvironment(): ?string
    {
        return $this->environment;
    }

    public function isValidResponse(HttpResponse $response): bool
    {
        return $response->getStatusCode() >= 200 && $response->getStatusCode() < 300;
    }

    public function getTimeout(): int
    {
        switch ($this->environment) {
            case Environment::PRODUCTION:
                return 20;
            case Environment::SANDBOX:
            default:
                return 30;
        }
    }

    /**
     * @throws CouldNotConfigureApiClient
     */
    public function getBaseUrlByEnvironment(string $environment, string $endpoint): string
    {
        $map = Config::KLARNA_PAYMENT_ENDPOINT_URLS;

        if (empty($map[$environment])) {
            throw CouldNotConfigureApiClient::unsupportedApiEnvironment($environment);
        }

        if (empty($map[$environment][$endpoint])) {
            throw CouldNotConfigureApiClient::unsupportedApiEndpoint($endpoint);
        }

        return $map[$environment][$endpoint];
    }
}
