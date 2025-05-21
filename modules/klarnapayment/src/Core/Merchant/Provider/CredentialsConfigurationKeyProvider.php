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

namespace KlarnaPayment\Module\Core\Merchant\Provider;

use KlarnaPayment\Module\Api\Environment;
use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CredentialsConfigurationKeyProvider
{
    /**
     * @var RegionProvider
     */
    private $regionProvider;

    /**
     * @var GlobalShopContextInterface
     */
    private $globalShopContext;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        RegionProvider $regionProvider,
        GlobalShopContextInterface $globalShopContext,
        Configuration $configuration
    ) {
        $this->regionProvider = $regionProvider;
        $this->globalShopContext = $globalShopContext;
        $this->configuration = $configuration;
    }

    public function getApiKey(?int $shopId = null, ?string $currencyIso = null): string
    {
        return $this->getConfigName(Config::KLARNA_PAYMENT_API_KEY, $shopId, $currencyIso);
    }

    public function getApiUsername(?int $shopId = null): string
    {
        return $this->getConfigName(Config::KLARNA_PAYMENT_API_USERNAME, $shopId);
    }

    public function getApiPassword(?int $shopId = null): string
    {
        return $this->getConfigName(Config::KLARNA_PAYMENT_API_PASSWORD, $shopId);
    }

    public function getClientId(?int $shopId = null, ?string $currencyIso = null): string
    {
        return $this->getConfigName(Config::KLARNA_PAYMENT_CLIENT_IDENTIFIER, $shopId, $currencyIso);
    }

    private function getConfigName(array $config, ?int $shopId = null, ?string $currencyIso = null): string
    {
        if (!$shopId) {
            $shopId = $this->globalShopContext->getShopId();
        }

        if ($this->configuration->getAsInteger(Config::KLARNA_PAYMENT_IS_PRODUCTION_ENVIRONMENT, $shopId)) {
            return $config[Environment::PRODUCTION][$this->regionProvider->getIso($currencyIso)];
        }

        return $config[Environment::SANDBOX][$this->regionProvider->getIso($currencyIso)];
    }
}
