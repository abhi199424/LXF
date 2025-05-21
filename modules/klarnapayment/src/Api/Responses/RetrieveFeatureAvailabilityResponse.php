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

namespace KlarnaPayment\Module\Api\Responses;

if (!defined('_PS_VERSION_')) {
    exit;
}

class RetrieveFeatureAvailabilityResponse implements ResponseInterface
{
    /* @var string $pluginInstallationId */
    private $pluginInstallationId;

    /** @var array */
    private $availableMarkets;

    /** @var array */
    private $features;

    /**
     * @param string $pluginInstallationId
     *
     * @maps plugin_installation_id
     */
    public function setPluginInstallationId(string $pluginInstallationId)
    {
        $this->pluginInstallationId = $pluginInstallationId;
    }

    /**
     * @return string
     */
    public function getPluginInstallationId(): string
    {
        return $this->pluginInstallationId;
    }

    /**
     * @return array
     */
    public function getAvailableMarkets(): array
    {
        return $this->availableMarkets;
    }

    /**
     * @param array $availableMarkets
     *
     * @maps available_markets
     */
    public function setAvailableMarkets(array $availableMarkets)
    {
        $this->availableMarkets = $availableMarkets;
    }

    /**
     * @param array $features
     *
     * @maps features
     */
    public function setFeatures(array $features): void
    {
        $this->features = $features;
    }

    public function getFeatures(): array
    {
        return $this->features;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['plugin_installation_id'] = $this->getPluginInstallationId();
        $json['available_markets'] = $this->getAvailableMarkets();
        $json['features'] = $this->getFeatures();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
