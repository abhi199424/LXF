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

namespace KlarnaPayment\Module\Api\Models;

if (!defined('_PS_VERSION_')) {
    exit;
}

class PaymentMethodCategory implements \JsonSerializable
{
    /** @var AssetUrl */
    private $assetUrls;
    /** @var ?string */
    private $identifier;
    /** @var ?string */
    private $name;

    /**
     * @return ?AssetUrl
     */
    public function getAssetUrls(): ?AssetUrl
    {
        return $this->assetUrls;
    }

    public function getIdentifier(): ?string
    {
        return $this->identifier;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @maps asset_urls
     */
    public function setAssetUrls(AssetUrl $assetUrls): void
    {
        $this->assetUrls = $assetUrls;
    }

    /**
     * @maps identifier
     */
    public function setIdentifier(?string $identifier): void
    {
        $this->identifier = $identifier;
    }

    /**
     * @maps name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['asset_urls'] = $this->getAssetUrls();
        $json['name'] = $this->getName();
        $json['identifier'] = $this->getIdentifier();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
