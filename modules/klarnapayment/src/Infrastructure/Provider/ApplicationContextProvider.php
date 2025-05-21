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

namespace KlarnaPayment\Module\Infrastructure\Provider;

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Merchant\Provider\CredentialsConfigurationKeyProvider;
use KlarnaPayment\Module\Core\Shared\Repository\CurrencyRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Context\ApplicationContext;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ApplicationContextProvider
{
    /** @var Configuration */
    private $configuration;

    /** @var GlobalShopContextInterface */
    private $globalShopContext;

    /** @var ApplicationContext */
    private $applicationContext;

    /** @var CurrencyRepositoryInterface */
    private $currencyRepository;

    /** @var CredentialsConfigurationKeyProvider */
    private $configurationKeyProvider;

    public function __construct(
        Configuration $configuration,
        GlobalShopContextInterface $globalShopContext,
        CurrencyRepositoryInterface $currencyRepository,
        CredentialsConfigurationKeyProvider $configurationKeyProvider
    ) {
        $this->configuration = $configuration;
        $this->globalShopContext = $globalShopContext;
        $this->currencyRepository = $currencyRepository;
        $this->configurationKeyProvider = $configurationKeyProvider;
    }

    public function refresh(): self
    {
        $this->applicationContext = null;

        return $this;
    }

    public function get(?string $currencyIso = null): ApplicationContext
    {
        if ($this->applicationContext) {
            return $this->applicationContext;
        }

        $this->applicationContext = new ApplicationContext(
            $this->configuration->getAsBoolean(Config::KLARNA_PAYMENT_IS_PRODUCTION_ENVIRONMENT, $this->globalShopContext->getShopId()),
            $this->configuration->get($this->configurationKeyProvider->getApiKey(null, $currencyIso), $this->globalShopContext->getShopId()),
            $this->currencyRepository->findOneBy([
                'id_currency' => $this->configuration->get(Config::PS_CURRENCY_DEFAULT, $this->globalShopContext->getShopId()),
            ])
        );

        return $this->applicationContext;
    }
}
