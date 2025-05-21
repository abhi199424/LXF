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

class ShippingInfo implements \JsonSerializable
{
    /** @var ?string */
    private $returnShippingCompany;
    /** @var ?string */
    private $returnTrackingNumber;
    /** @var ?string */
    private $returnTrackingUri;
    /** @var ?string */
    private $shippingCompany;
    /** @var ?string */
    private $shippingMethod;
    /** @var ?string */
    private $trackingNumber;
    /** @var ?string */
    private $trackingUri;

    public function getReturnShippingCompany(): ?string
    {
        return $this->returnShippingCompany;
    }

    public function getReturnTrackingNumber(): ?string
    {
        return $this->returnTrackingNumber;
    }

    public function getReturnTrackingUri(): ?string
    {
        return $this->returnTrackingUri;
    }

    public function getShippingCompany(): ?string
    {
        return $this->shippingCompany;
    }

    public function getShippingMethod(): ?string
    {
        return $this->shippingMethod;
    }

    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }

    public function getTrackingUri(): ?string
    {
        return $this->trackingUri;
    }

    /**
     * @maps return_shipping_company
     */
    public function setReturnShippingCompany(?string $returnShippingCompany): void
    {
        $this->returnShippingCompany = $returnShippingCompany;
    }

    /**
     * @maps return_tracking_number
     */
    public function setReturnTrackingNumber(?string $returnTrackingNumber): void
    {
        $this->returnTrackingNumber = $returnTrackingNumber;
    }

    /**
     * @maps return_tracking_uri
     */
    public function setReturnTrackingUri(?string $returnTrackingUri): void
    {
        $this->returnTrackingUri = $returnTrackingUri;
    }

    /**
     * @maps shipping_company
     */
    public function setShippingCompany(?string $shippingCompany): void
    {
        $this->shippingCompany = $shippingCompany;
    }

    /**
     * @maps shipping_method
     */
    public function setShippingMethod(?string $shippingMethod): void
    {
        $this->shippingMethod = $shippingMethod;
    }

    /**
     * @maps tracking_number
     */
    public function setTrackingNumber(?string $trackingNumber): void
    {
        $this->trackingNumber = $trackingNumber;
    }

    /**
     * @maps tracking_uri
     */
    public function setTrackingUri(?string $trackingUri): void
    {
        $this->trackingUri = $trackingUri;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['return_shipping_company'] = $this->getReturnShippingCompany();
        $json['return_tracking_number'] = $this->getReturnTrackingNumber();
        $json['return_tracking_uri'] = $this->getReturnTrackingUri();
        $json['shipping_company'] = $this->getShippingCompany();
        $json['shipping_method'] = $this->getShippingMethod();
        $json['tracking_number'] = $this->getTrackingNumber();
        $json['tracking_uri'] = $this->getTrackingUri();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
