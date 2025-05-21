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

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Merchant\DTO\RegionCredentials;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CredentialsProvider
{
    /**
     * @var Configuration
     */
    private $configuration;
    /**
     * @var Context
     */
    private $globalShopContext;

    public function __construct(Configuration $configuration, Context $context)
    {
        $this->configuration = $configuration;
        $this->globalShopContext = $context;
    }

    public function getCredentialsByRegion(string $region): RegionCredentials
    {
        if (!array_key_exists($region, Config::SUPPORTED_REGIONS)) {
            // TODO: create exception class
            throw new \Exception('Unsupported region: ' . $region);
        }

        $productionCredentials = $this->getCredentials($region, 'production');
        $sandboxCredentials = $this->getCredentials($region, 'sandbox');

        return new RegionCredentials(
            $productionCredentials['username'],
            $productionCredentials['password'],
            $productionCredentials['clientId'],
            $sandboxCredentials['username'],
            $sandboxCredentials['password'],
            $sandboxCredentials['clientId']
        );
    }

    /**
     * @param string $region
     * @param int|null $shopId
     *
     * @return array
     */
    private function getCredentials(string $region, ?string $environment = null, ?int $shopId = null): array
    {
        if (!$shopId) {
            $shopId = $this->globalShopContext->getShopId();
        }

        if (!$environment) {
            $environment = $this->configuration->getAsBoolean(Config::KLARNA_PAYMENT_IS_PRODUCTION_ENVIRONMENT) ? 'production' : 'sandbox';
        }

        $username = $this->configuration->get(Config::KLARNA_PAYMENT_API_USERNAME[$environment][$region], $shopId);
        $password = $this->configuration->get(Config::KLARNA_PAYMENT_API_PASSWORD[$environment][$region], $shopId);
        $clientId = $this->configuration->get(Config::KLARNA_PAYMENT_CLIENT_IDENTIFIER[$environment][$region], $shopId);

        return [
            'username' => $username ?? '',
            'password' => $password ?? '',
            'clientId' => $clientId ?? '',
        ];
    }
}
