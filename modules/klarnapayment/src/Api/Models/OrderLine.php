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

class OrderLine implements \JsonSerializable
{
    /** @var ?string */
    private $imageUrl;
    /** @var ?string */
    private $merchantData;
    /** @var ?string */
    private $name;
    /** @var ?string */
    private $productUrl;
    /** @var ?int */
    private $quantity;
    /** @var ?string */
    private $quantityUnit;
    /** @var ?string */
    private $reference;
    /** @var ?int */
    private $taxRate;
    /** @var ?int */
    private $totalAmount;
    /** @var ?int */
    private $totalDiscountAmount;
    /** @var ?int */
    private $totalTaxAmount;
    /** @var ?string */
    private $type;
    /** @var ?int */
    private $unitPrice;

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
    }

    public function getMerchantData(): ?string
    {
        return $this->merchantData;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function getProductUrl(): ?string
    {
        return $this->productUrl;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function getQuantityUnit(): ?string
    {
        return $this->quantityUnit;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function getTaxRate(): ?int
    {
        return $this->taxRate;
    }

    public function getTotalAmount(): ?int
    {
        return $this->totalAmount;
    }

    public function getTotalDiscountAmount(): ?int
    {
        return $this->totalDiscountAmount;
    }

    public function getTotalTaxAmount(): ?int
    {
        return $this->totalTaxAmount;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getUnitPrice(): ?int
    {
        return $this->unitPrice;
    }

    /**
     * @maps image_url
     */
    public function setImageUrl(string $imageUrl): void
    {
        $this->imageUrl = $imageUrl;
    }

    /**
     * @maps merchant_data
     */
    public function setMerchantData(string $merchantData): void
    {
        $this->merchantData = $merchantData;
    }

    /**
     * @maps name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @maps product_url
     */
    public function setProductUrl(string $productUrl): void
    {
        $this->productUrl = $productUrl;
    }

    /**
     * @maps quantity
     */
    public function setQuantity(int $quantity): void
    {
        $this->quantity = $quantity;
    }

    /**
     * @maps quantity_unit
     */
    public function setQuantityUnit(string $quantityUnit): void
    {
        $this->quantityUnit = $quantityUnit;
    }

    /**
     * @maps reference
     */
    public function setReference(?string $reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @maps tax_rate
     */
    public function setTaxRate(int $taxRate): void
    {
        $this->taxRate = $taxRate;
    }

    /**
     * @maps total_amount
     */
    public function setTotalAmount(int $totalAmount): void
    {
        $this->totalAmount = $totalAmount;
    }

    /**
     * @maps total_discount_amount
     */
    public function setTotalDiscountAmount(int $totalDiscountAmount): void
    {
        $this->totalDiscountAmount = $totalDiscountAmount;
    }

    /**
     * @maps total_tax_amount
     */
    public function setTotalTaxAmount(int $totalTaxAmount): void
    {
        $this->totalTaxAmount = $totalTaxAmount;
    }

    /**
     * @maps type
     */
    public function setType(string $type): void
    {
        $this->type = $type;
    }

    /**
     * @maps unit_price
     */
    public function setUnitPrice(int $unitPrice): void
    {
        $this->unitPrice = $unitPrice;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['image_url'] = $this->getImageUrl();
        $json['merchant_data'] = $this->getMerchantData();
        $json['name'] = $this->getName();
        $json['product_url'] = $this->getProductUrl();
        $json['quantity'] = $this->getQuantity();
        $json['quantity_unit'] = $this->getQuantityUnit();
        $json['reference'] = $this->getReference();
        $json['tax_rate'] = $this->getTaxRate();
        $json['total_amount'] = $this->getTotalAmount();
        $json['total_tax_amount'] = $this->getTotalTaxAmount();
        $json['total_discount_amount'] = $this->getTotalDiscountAmount();
        $json['type'] = $this->getType();
        $json['unit_price'] = $this->getUnitPrice();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
