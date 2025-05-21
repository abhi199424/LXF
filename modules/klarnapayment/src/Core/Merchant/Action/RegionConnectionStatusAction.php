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

namespace KlarnaPayment\Module\Core\Merchant\Action;

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;

if (!defined('_PS_VERSION_')) {
    exit;
}

class RegionConnectionStatusAction
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function run(array $sandboxRegions, array $productionRegions): void
    {
        $allRegions = Config::KLARNA_ALL_REGIONS;

        $this->updateRegions($sandboxRegions, 'sandbox', $allRegions);
        $this->updateRegions($productionRegions, 'production', $allRegions);
    }

    private function updateRegions(array $regionsToUpdate, string $environment, array $allRegions): void
    {
        foreach ($allRegions as $region) {
            $value = in_array($region, $regionsToUpdate);
            $this->configuration->set(
                Config::KLARNA_PAYMENT_CONNECTED_REGIONS[$environment][$region],
                $value
            );
        }
    }
}
