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

namespace KlarnaPayment\Module\Infrastructure\Factory;

use KlarnaPayment\Module\Api\ApiClient;
use KlarnaPayment\Module\Api\Environment;
use KlarnaPayment\Module\Core\Merchant\Provider\EndpointProvider;
use KlarnaPayment\Module\Core\Shared\Provider\UserAgentProvider;
use KlarnaPayment\Module\Infrastructure\Provider\ApplicationContextProvider;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class ApiClientFactory implements ApiClientFactoryInterface
{
    /** @var ApplicationContextProvider */
    private $applicationContextProvider;

    /** @var UserAgentProvider */
    private $userAgentProvider;

    /** @var EndpointProvider */
    private $endpointProvider;

    public function __construct(
        ApplicationContextProvider $applicationContextProvider,
        UserAgentProvider $userAgentProvider,
        EndpointProvider $endpointProvider
    ) {
        $this->applicationContextProvider = $applicationContextProvider;
        $this->userAgentProvider = $userAgentProvider;
        $this->endpointProvider = $endpointProvider;
    }

    /**
     * @throws \KlarnaPayment\Module\Api\Exception\CouldNotConfigureApiClient
     */
    public function create(array $customOptions = []): ApiClient
    {
        $currencyIso = $customOptions['currencyIso'] ?? null;

        $applicationContext = $this->applicationContextProvider->refresh()->get(
            $currencyIso
        );

        if ($currencyIso) {
            $customOptions['region'] = $this->endpointProvider->getEndpoint($customOptions['currencyIso']);
        }

        if ($applicationContext->getIsProduction()) {
            $environment = Environment::PRODUCTION;
        } else {
            $environment = Environment::SANDBOX;
        }
        $configuration = [
            'apiKey' => $customOptions['apiKey'] ?? $applicationContext->getApiKey(),
            'environment' => $environment,
            'endpoint' => $customOptions['region'] ?? $this->endpointProvider->getEndpoint(),
            'userAgent' => $this->userAgentProvider->get(),
        ];

        $configuration = array_merge($configuration, $customOptions);

        return new ApiClient($configuration);
    }
}
