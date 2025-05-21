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

class Session implements \JsonSerializable
{
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
    /** @var ?PaymentMethodCategory[] */
    private $paymentMethodCategories;
    /** @var ?string */
    private $clientToken;
    /** @var ?string */
    private $sessionId;
    /** @var ?string */
    private $intent;
    /** @var ?string */
    private $acquiringChannel;
    /** @var ?Attachment */
    private $attachment;

    /** @var bool */
    private $isDummy = false;

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

    /**
     * @return PaymentMethodCategory[]|null
     */
    public function getPaymentMethodCategories(): ?array
    {
        return $this->paymentMethodCategories;
    }

    public function getClientToken(): ?string
    {
        return $this->clientToken;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    public function getIntent(): ?string
    {
        return $this->intent;
    }

    public function getAcquiringChannel(): ?string
    {
        return $this->acquiringChannel;
    }

    /**
     * @return Attachment|null
     */
    public function getAttachment(): ?Attachment
    {
        return $this->attachment;
    }

    /**
     * @return bool
     */
    public function getIsDummy(): bool
    {
        return $this->isDummy;
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
     * @param \KlarnaPayment\Module\Api\Models\PaymentMethodCategory[] $paymentMethodCategories
     *
     * @maps payment_method_categories
     */
    public function setPaymentMethodCategories(array $paymentMethodCategories): void
    {
        $this->paymentMethodCategories = $paymentMethodCategories;
    }

    /**
     * @maps client_token
     */
    public function setClientToken(?string $clientToken): void
    {
        $this->clientToken = $clientToken;
    }

    /**
     * @maps session_id
     */
    public function setSessionId(?string $sessionId): void
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @maps intent
     */
    public function setIntent(?string $intent): void
    {
        $this->intent = $intent;
    }

    /**
     * @maps acquiring_channel
     */
    public function setAcquiringChannel(?string $acquiringChannel): void
    {
        $this->acquiringChannel = $acquiringChannel;
    }

    /**
     * @param \KlarnaPayment\Module\Api\Models\Attachment|null $attachment
     *
     * @maps attachment
     */
    public function setAttachment(?Attachment $attachment): void
    {
        $this->attachment = $attachment;
    }

    /**
     * @param bool $isDummy
     */
    public function setIsDummy(bool $isDummy): void
    {
        $this->isDummy = $isDummy;
    }

    public function jsonSerialize(): array
    {
        $json = [];
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
        $json['payment_method_categories'] = $this->getPaymentMethodCategories();
        $json['client_token'] = $this->getClientToken();
        $json['session_id'] = $this->getSessionId();
        $json['intent'] = $this->getIntent();
        $json['acquiring_channel'] = $this->getAcquiringChannel();
        $json['attachment'] = $this->getAttachment();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
