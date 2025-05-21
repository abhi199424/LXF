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

namespace KlarnaPayment\Module\Api\Requests;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @see https://developers.klarna.com/api/#payments-api-create-a-new-order
 */
class FeatureAvailabilityRequest implements \JsonSerializable, RequestInterface
{
    /** @var string */
    private $pluginInstallationId;

    /** @var ?string */
    private $platformPluginName;

    /** @var ?string */
    private $platformVersion;

    /** @var string */
    private $pluginIdentifier;

    /** @var ?string */
    private $pluginName;

    /** @var string */
    private $pluginVersion;

    /** @var ?string */
    private $storeUrl;

    /** @var ?array */
    private $metaData;

    /** @var string */
    private $platformName;

    public function setPluginInstallationId(string $pluginInstallationId): void
    {
        $this->pluginInstallationId = $pluginInstallationId;
    }

    public function getPluginInstallationId(): ?string
    {
        return $this->pluginInstallationId;
    }

    public function setPlatformPluginName(string $platformPluginName): void
    {
        $this->platformPluginName = $platformPluginName;
    }

    public function getPlatformPluginName(): ?string
    {
        return $this->platformPluginName;
    }

    public function setPlatformName(string $platformName): void
    {
        $this->platformName = $platformName;
    }

    public function getPlatformName(): string
    {
        return $this->platformName;
    }

    public function setPlatformVersion(string $platformVersion): void
    {
        $this->platformVersion = $platformVersion;
    }

    public function getPlatformVersion(): ?string
    {
        return $this->platformVersion;
    }

    public function setPluginIdentifier(string $pluginIdentifier): void
    {
        $this->pluginIdentifier = $pluginIdentifier;
    }

    public function getPluginIdentifier(): string
    {
        return $this->pluginIdentifier;
    }

    public function setPluginName(string $pluginName): void
    {
        $this->pluginName = $pluginName;
    }

    public function getPluginName(): ?string
    {
        return $this->pluginName;
    }

    public function setPluginVersion(string $pluginVersion): void
    {
        $this->pluginVersion = $pluginVersion;
    }

    public function getPluginVersion(): string
    {
        return $this->pluginVersion;
    }

    public function setStoreUrl(string $storeUrl): void
    {
        $this->storeUrl = $storeUrl;
    }

    public function getStoreUrl(): ?string
    {
        return $this->storeUrl;
    }

    public function setMetaData(array $metaData): void
    {
        $this->metaData = $metaData;
    }

    public function getMetaData(): ?array
    {
        return $this->metaData;
    }

    public function jsonSerialize(): array
    {
        $json = [];

        $json['installation_data'] = [
            'platform_data' => [
                'platform_name' => $this->platformName,
                'platform_version' => $this->platformVersion,
                'platform_plugin_name' => $this->platformPluginName,
            ],
            'klarna_plugin_data' => [
                'plugin_identifier' => $this->pluginIdentifier,
                'plugin_version' => $this->pluginVersion,
            ],
            'store_data' => [
                'store_urls' => [$this->storeUrl],
            ],
            'metadata' => $this->metaData,
        ];

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
