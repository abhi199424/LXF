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

namespace KlarnaPayment\Module\Core\Merchant\Handler;

use KlarnaPayment\Module\Api\Exception\CouldNotConfigureApiClient;
use KlarnaPayment\Module\Api\Requests\FeatureAvailabilityRequest;
use KlarnaPayment\Module\Api\Responses\RetrieveFeatureAvailabilityResponse;
use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Payment\Api\Repository\FeatureAvailabilityApiRepository;
use KlarnaPayment\Module\Core\Shared\Provider\UserAgentProviderInterface;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Factory\ApiClientFactory;
use KlarnaPayment\Module\Infrastructure\Provider\ApplicationContextProvider;
use KlarnaPayment\Module\Infrastructure\Utility\SecurityTokenUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class FeatureAvailabilityHandler
{
    /** @var Configuration */
    private $configuration;

    /** @var ApiClientFactory */
    private $apiClientFactory;

    /** @var FeatureAvailabilityApiRepository */
    private $featureAvailabilityApiRepository;

    /** @var ApplicationContextProvider */
    private $applicationContext;

    /** @var \KlarnaPayment */
    private $module;

    /** @var Context */
    private $context;

    /** @var UserAgentProviderInterface */
    private $userAgentProvider;

    public function __construct(
        Configuration $configuration,
        ApiClientFactory $apiClientFactory,
        FeatureAvailabilityApiRepository $featureAvailabilityApiRepository,
        ApplicationContextProvider $applicationContext,
        ModuleFactory $module,
        Context $context,
        UserAgentProviderInterface $userAgentProvider
    ) {
        $this->configuration = $configuration;
        $this->apiClientFactory = $apiClientFactory;
        $this->featureAvailabilityApiRepository = $featureAvailabilityApiRepository;
        $this->applicationContext = $applicationContext;
        $this->module = $module->getModule();
        $this->context = $context;
        $this->userAgentProvider = $userAgentProvider;
    }

    /**
     * @throws CouldNotConfigureApiClient
     */
    public function handle(string $apiKey): void
    {
        $env = $this->applicationContext->get()->getIsProduction() ? Config::KLARNA_ENVIRONMENT_PRODUCTION : Config::KLARNA_ENVIRONMENT_SANDBOX;

        $apiClient = $this->apiClientFactory->create([
            'apiKey' => $apiKey,
            'customUrl' => Config::KLARNA_FEATURE_AVAILABILITY_BASE_URL[$env],
        ]);

        $this->featureAvailabilityApiRepository->setApiClient($apiClient);

        $request = new FeatureAvailabilityRequest();
        $request = $this->setRequestData($request);

        $featuresResponse = $this->featureAvailabilityApiRepository->retrieveMerchantFeatureAvailability($request);

        $this->saveFeatures($featuresResponse);
    }

    private function saveFeatures(RetrieveFeatureAvailabilityResponse $featuresResponse): void
    {
        $features = $featuresResponse->getFeatures();

        foreach ($features as $feature) {
            $featureAvailability = $feature->availability == 'AVAILABLE' ? 1 : 0;

            if (false !== strpos($feature->feature_key, 'payments')
                && !$featureAvailability
            ) {
                $this->configuration->set(Config::KLARNA_PAYMENT_ENABLE_BOX['sandbox'], $featureAvailability);
                $this->configuration->set(Config::KLARNA_PAYMENT_ENABLE_BOX['production'], $featureAvailability);
            }

            $this->configuration->set(Config::KLARNA_PAYMENTS_FEATURES[$feature->feature_key], $featureAvailability);
        }
    }

    private function setRequestData(FeatureAvailabilityRequest $request): FeatureAvailabilityRequest
    {
        $request->setPluginInstallationId(
            SecurityTokenUtility::generateUuidV4(sprintf('%s:%d', _COOKIE_KEY_, $this->context->getShopId()))
        );
        $request->setPlatformPluginName('prestashop_klarna_payments_plugin');
        $request->setPlatformName('PrestaShop');
        $request->setPluginName($this->module->name);
        $request->setPlatformVersion(_PS_VERSION_);
        $request->setPluginIdentifier('klarna:plugins:prestashop:klarna-plugin');
        $request->setPluginVersion($this->module->version);
        $request->setStoreUrl($this->context->getPageLink('index'));
        $request->setMetaData([
            'user_agent' => $this->userAgentProvider->get(),
        ]);

        return $request;
    }
}
