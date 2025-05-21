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

use KlarnaPayment\Module\Api\Models\Address;
use KlarnaPayment\Module\Api\Models\Customer;
use KlarnaPayment\Module\Api\Models\MerchantUrl;
use KlarnaPayment\Module\Api\Models\Option;
use KlarnaPayment\Module\Api\Models\OrderLine;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @see https://developers.klarna.com/api/#payments-api-create-a-new-order
 */
class CreateOrderRequest implements \JsonSerializable, RequestInterface
{
    /** @var ?string */
    private $authorizationToken;
    /** @var ?string */
    private $locale;
    /** @var ?string */
    private $purchaseCountry;
    /** @var ?string */
    private $purchaseCurrency;
    /** @var ?int */
    private $orderAmount;
    /** @var ?OrderLine[] */
    private $orderLines;
    /** @var ?int */
    private $orderTaxAmount;
    /** @var ?Address */
    private $billingAddress;
    /** @var ?Address */
    private $shippingAddress;
    /** @var ?MerchantUrl */
    private $merchantUrls;
    /** @var ?Option */
    private $options;
    /** @var ?Customer */
    private $customer;
    /** @var ?string */
    private $merchantReference1;
    /** @var ?string */
    private $merchantReference2;

    public function getLocale(): ?string
    {
        return $this->locale;
    }

    public function getPurchaseCountry(): ?string
    {
        return $this->purchaseCountry;
    }

    public function getPurchaseCurrency(): ?string
    {
        return $this->purchaseCurrency;
    }

    public function getOrderAmount(): ?int
    {
        return $this->orderAmount;
    }

    /**
     * @return ?OrderLine[]
     */
    public function getOrderLines(): ?array
    {
        return $this->orderLines;
    }

    public function getOrderTaxAmount(): ?int
    {
        return $this->orderTaxAmount;
    }

    public function getBillingAddress(): ?Address
    {
        return $this->billingAddress;
    }

    public function getShippingAddress(): ?Address
    {
        return $this->shippingAddress;
    }

    public function getAuthorizationToken(): ?string
    {
        return $this->authorizationToken;
    }

    public function getMerchantUrls(): ?MerchantUrl
    {
        return $this->merchantUrls;
    }

    public function getOptions(): ?Option
    {
        return $this->options;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    public function getMerchantReference1(): ?string
    {
        return $this->merchantReference1;
    }

    public function getMerchantReference2(): ?string
    {
        return $this->merchantReference2;
    }

    /**
     * @maps locale
     */
    public function setLocale(string $locale): void
    {
        $this->locale = $locale;
    }

    /**
     * @maps purchase_country
     */
    public function setPurchaseCountry(string $purchaseCountry): void
    {
        $this->purchaseCountry = $purchaseCountry;
    }

    /**
     * @maps purchase_currency
     */
    public function setPurchaseCurrency(string $purchaseCurrency): void
    {
        $this->purchaseCurrency = $purchaseCurrency;
    }

    /**
     * @maps order_amount
     */
    public function setOrderAmount(int $orderAmount): void
    {
        $this->orderAmount = $orderAmount;
    }

    /**
     * @param \KlarnaPayment\Module\Api\Models\OrderLine[] $orderLines
     *
     * @maps order_lines
     */
    public function setOrderLines(array $orderLines): void
    {
        $this->orderLines = $orderLines;
    }

    /**
     * @maps order_tax_amount
     */
    public function setOrderTaxAmount(?int $orderTaxAmount): void
    {
        $this->orderTaxAmount = $orderTaxAmount;
    }

    /**
     * @param ?\KlarnaPayment\Module\Api\Models\Address $billingAddress
     *
     * @maps billing_address
     */
    public function setBillingAddress(?Address $billingAddress): void
    {
        $this->billingAddress = $billingAddress;
    }

    /**
     * @param ?\KlarnaPayment\Module\Api\Models\Address $shippingAddress
     *
     * @maps shipping_address
     */
    public function setShippingAddress(?Address $shippingAddress): void
    {
        $this->shippingAddress = $shippingAddress;
    }

    /**
     * @maps authorization_token
     */
    public function setAuthorizationToken(?string $authorizationToken): void
    {
        $this->authorizationToken = $authorizationToken;
    }

    /**
     * @param \KlarnaPayment\Module\Api\Models\MerchantUrl|null $merchantUrls
     *
     * @maps merchant_urls
     */
    public function setMerchantUrls(?MerchantUrl $merchantUrls): void
    {
        $this->merchantUrls = $merchantUrls;
    }

    /**
     * @param \KlarnaPayment\Module\Api\Models\Option|null $options
     *
     * @maps options
     */
    public function setOptions(?Option $options): void
    {
        $this->options = $options;
    }

    /**
     * @param \KlarnaPayment\Module\Api\Models\Customer|null $customer
     *
     * @maps customer
     */
    public function setCustomer(?Customer $customer): void
    {
        $this->customer = $customer;
    }

    /**
     * @maps merchant_reference1
     */
    public function setMerchantReference1(?string $merchantReference1): void
    {
        $this->merchantReference1 = $merchantReference1;
    }

    /**
     * @maps merchant_reference2
     */
    public function setMerchantReference2(?string $merchantReference2): void
    {
        $this->merchantReference2 = $merchantReference2;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['authorization_token'] = $this->getAuthorizationToken();
        $json['locale'] = $this->getLocale();
        $json['purchase_country'] = $this->getPurchaseCountry();
        $json['purchase_currency'] = $this->getPurchaseCurrency();
        $json['order_amount'] = $this->getOrderAmount();
        $json['order_tax_amount'] = $this->getOrderTaxAmount();
        $json['order_lines'] = $this->getOrderLines();
        $json['billing_address'] = $this->getBillingAddress();
        $json['shipping_address'] = $this->getShippingAddress();
        $json['merchant_urls'] = $this->getMerchantUrls();
        $json['options'] = $this->getOptions();
        $json['customer'] = $this->getCustomer();
        $json['merchant_reference1'] = $this->getMerchantReference1();
        $json['merchant_reference2'] = $this->getMerchantReference2();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
