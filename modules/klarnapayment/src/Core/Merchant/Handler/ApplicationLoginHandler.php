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

use KlarnaPayment\Module\Api\Exception\ApiException;
use KlarnaPayment\Module\Api\Models\OrderLine;
use KlarnaPayment\Module\Api\Models\Session;
use KlarnaPayment\Module\Api\Requests\CreatePaymentSessionRequest;
use KlarnaPayment\Module\Api\Responses\CreatePaymentSessionResponse;
use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Merchant\DTO\ApplicationLoginData;
use KlarnaPayment\Module\Core\Merchant\Provider\Currency\LocaleProvider;
use KlarnaPayment\Module\Core\Payment\Api\Repository\SessionApiRepositoryInterface;
use KlarnaPayment\Module\Core\Payment\Provider\CountryProviderInterface;
use KlarnaPayment\Module\Core\Payment\Provider\CurrencyProviderInterface;
use KlarnaPayment\Module\Core\Tools\Exception\ClientTokenIsEmptyException;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Cache\CacheInterface;
use KlarnaPayment\Module\Infrastructure\Factory\ApiClientFactory;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ApplicationLoginHandler
{
    private $configuration;

    /** @var \KlarnaPayment|null */
    private $module;

    private $cache;

    private $context;
    private $currencyProvider;
    private $countryProvider;
    private $localeProvider;

    public function __construct(
        Configuration $configuration,
        ModuleFactory $moduleFactory,
        CacheInterface $cache,
        Context $context,
        CurrencyProviderInterface $currencyProvider,
        CountryProviderInterface $countryProvider,
        LocaleProvider $localeProvider
    ) {
        $this->configuration = $configuration;
        $this->module = $moduleFactory->getModule();
        $this->cache = $cache;
        $this->context = $context;
        $this->currencyProvider = $currencyProvider;
        $this->countryProvider = $countryProvider;
        $this->localeProvider = $localeProvider;
    }

    /**
     * @throws \Exception
     */
    public function handle(ApplicationLoginData $data, string $environment): void
    {
        if ($data->getApiKey()) {
            $this->cache->clear();

            $createPaymentSessionResponse = $this->createDummySession(
                $data->getRegion(),
                $environment,
                $data->getApiKey()
            );

            if (empty($createPaymentSessionResponse->getClientToken())) {
                throw new ClientTokenIsEmptyException('Client token is empty.');
            }
        }
    }

    /**
     * @throws ApiException
     */
    private function createDummySession(string $region, string $environment, string $apiKey): CreatePaymentSessionResponse
    {
        $currency = implode(Config::REGION_CURRENCY_ISO_MAP[$region]);
        $country = '';
        $locale = '';

        switch ($region) {
            case 'EU':
                $country = $this->countryProvider->getDefaultCountryIsoCode();
                $locale = $this->localeProvider->getLocale();

                // Getting EU country currency for countries that are non-euro
                $currency = $this->currencyProvider->getEuropeCountryCurrency();
                break;
            case 'NA-CA':
                $country = 'CA';
                $locale = 'en-CA';
                break;
            case 'NA-US':
                $country = 'US';
                $locale = 'en-US';
                break;
            case 'NA-MX':
                $country = 'MX';
                $locale = 'en-MX';
                break;
            case 'AP-AP':
                $country = 'AU';
                $locale = 'en-AU';
                break;
            case 'AP-NZ':
                $country = 'NZ';
                $locale = 'en-NZ';
                break;
        }

        $request = new CreatePaymentSessionRequest();
        $request->setSession(new Session());
        $request->getSession()->setPurchaseCountry($country);
        $request->getSession()->setPurchaseCurrency($currency);
        $request->getSession()->setLocale($locale);

        $request->getSession()->setOrderAmount(100);
        $request->getSession()->setOrderTaxAmount(0);

        $request->getSession()->setOrderLines([new OrderLine()]);
        $request->getSession()->getOrderLines()[0]->setType('physical');
        $request->getSession()->getOrderLines()[0]->setName('Dummy call');
        $request->getSession()->getOrderLines()[0]->setQuantity(1);

        $request->getSession()->getOrderLines()[0]->setTaxRate(0);
        $request->getSession()->getOrderLines()[0]->setUnitPrice(100);
        $request->getSession()->getOrderLines()[0]->setTotalAmount(100);
        $request->getSession()->getOrderLines()[0]->setTotalTaxAmount(0);
        $request->getSession()->getOrderLines()[0]->setTotalDiscountAmount(0);

        $request->getSession()->setIsDummy(true);
        /**
         * NOTE: need to get from module to "refresh" api client because we just changed api endpoint
         * Did not manage to find better option to do it so just retrieving new service.
         */
        /** @var SessionApiRepositoryInterface $sessionApiRepository */
        $sessionApiRepository = $this->module->getService(SessionApiRepositoryInterface::class);
        /** @var ApiClientFactory $apiClientFactory */
        $apiClientFactory = $this->module->getService(ApiClientFactory::class);

        $apiClient = $apiClientFactory->create([
            'region' => $region,
            'apiKey' => $apiKey,
            'customUrl' => Config::KLARNA_PAYMENT_ENDPOINT_URLS[$environment][$region],
        ]);

        $sessionApiRepository->setApiClient($apiClient);

        return $sessionApiRepository->createPaymentSession($request);
    }
}
