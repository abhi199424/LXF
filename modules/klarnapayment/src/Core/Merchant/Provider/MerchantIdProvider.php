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

use function Invertus\Knapsack\first;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;

if (!defined('_PS_VERSION_')) {
    exit;
}

class MerchantIdProvider
{
    /**
     * @var CredentialsConfigurationKeyProvider
     */
    private $configurationKeyProvider;

    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        CredentialsConfigurationKeyProvider $configurationKeyProvider,
        Configuration $configuration
    ) {
        $this->configurationKeyProvider = $configurationKeyProvider;
        $this->configuration = $configuration;
    }

    public function getMerchantId(): string
    {
        $username = $this->configuration->get($this->configurationKeyProvider->getApiUsername());

        return first(explode('_', $username));
    }
}
