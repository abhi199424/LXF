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

class Order implements \JsonSerializable
{
    /** @var ?string */
    private $orderId;

    /** @var ?int */
    private $orderAmount;

    /** @var ?int */
    private $originalOrderAmount;

    /** @var ?int */
    private $refundedAmount;

    /** @var ?int */
    private $capturedAmount;

    /** @var ?string */
    private $purchaseCurrency;

    /** @var ?string */
    private $status;

    /** @var ?string */
    private $fraudStatus;

    /** @var ?InitialPaymentMethod */
    private $initialPaymentMethod;

    /** @var ?string */
    private $purchaseCountry;

    /** @var ?OrderLine[] */
    private $orderLines;

    /** @var ?string */
    private $klarnaReference;
    /** @var ?Capture[] */
    private $captures;
    /** @var ?Refund[] */
    private $refunds;

    public function getFraudStatus(): ?string
    {
        return $this->fraudStatus;
    }

    public function getInitialPaymentMethod(): ?InitialPaymentMethod
    {
        return $this->initialPaymentMethod;
    }

    public function getPurchaseCountry(): ?string
    {
        return $this->purchaseCountry;
    }

    public function getRefundedAmount(): ?int
    {
        return $this->refundedAmount;
    }

    public function getCapturedAmount(): ?int
    {
        return $this->capturedAmount;
    }

    public function getPurchaseCurrency(): ?string
    {
        return $this->purchaseCurrency;
    }

    public function getStatus(): ?string
    {
        return $this->status;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function getOrderAmount(): ?int
    {
        return $this->orderAmount;
    }

    public function getOriginalOrderAmount(): ?int
    {
        return $this->originalOrderAmount;
    }

    /**
     * @return OrderLine[]|null
     */
    public function getOrderLines(): ?array
    {
        return $this->orderLines;
    }

    /**
     * @return string|null
     */
    public function getKlarnaReference(): ?string
    {
        return $this->klarnaReference;
    }

    /**
     * @return Capture[]|null
     */
    public function getCaptures(): ?array
    {
        return $this->captures;
    }

    /**
     * @return Refund[]|null
     */
    public function getRefunds(): ?array
    {
        return $this->refunds;
    }

    /**
     * @maps fraud_status
     */
    public function setFraudStatus(?string $fraudStatus): void
    {
        $this->fraudStatus = $fraudStatus;
    }

    /**
     * @param \KlarnaPayment\Module\Api\Models\InitialPaymentMethod|null $initialPaymentMethod
     *
     * @maps initial_payment_method
     */
    public function setInitialPaymentMethod(?InitialPaymentMethod $initialPaymentMethod): void
    {
        $this->initialPaymentMethod = $initialPaymentMethod;
    }

    /**
     * @maps purchase_country
     */
    public function setPurchaseCountry(?string $purchaseCountry): void
    {
        $this->purchaseCountry = $purchaseCountry;
    }

    /**
     * @maps order_id
     */
    public function setOrderId(?string $orderId): void
    {
        $this->orderId = $orderId;
    }

    /**
     * @maps original_order_amount
     */
    public function setOriginalOrderAmount(?int $originalOrderAmount): void
    {
        $this->originalOrderAmount = $originalOrderAmount;
    }

    /**
     * @maps order_amount
     */
    public function setOrderAmount(?int $orderAmount): void
    {
        $this->orderAmount = $orderAmount;
    }

    /**
     * @maps captured_amount
     */
    public function setCapturedAmount(?int $capturedAmount): void
    {
        $this->capturedAmount = $capturedAmount;
    }

    /**
     * @maps purchase_currency
     */
    public function setPurchaseCurrency(?string $purchaseCurrency): void
    {
        $this->purchaseCurrency = $purchaseCurrency;
    }

    /**
     * @maps status
     */
    public function setStatus(?string $status): void
    {
        $this->status = $status;
    }

    /**
     * @maps refunded_amount
     */
    public function setRefundedAmount(?int $refundedAmount): void
    {
        $this->refundedAmount = $refundedAmount;
    }

    /**
     * @param \KlarnaPayment\Module\Api\Models\OrderLine[]|null $orderLines
     *
     * @maps order_lines
     */
    public function setOrderLines(?array $orderLines): void
    {
        $this->orderLines = $orderLines;
    }

    /**
     * @maps klarna_reference
     */
    public function setKlarnaReference(?string $klarnaReference): void
    {
        $this->klarnaReference = $klarnaReference;
    }

    /**
     * @param \KlarnaPayment\Module\Api\Models\Capture[]|null $captures
     *
     * @maps captures
     */
    public function setCaptures(?array $captures): void
    {
        $this->captures = $captures;
    }

    /**
     * @param \KlarnaPayment\Module\Api\Models\Refund[]|null $refunds
     */
    public function setRefunds(?array $refunds): void
    {
        $this->refunds = $refunds;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['order_id'] = $this->getOrderId();
        $json['order_amount'] = $this->getOrderAmount();
        $json['original_order_amount'] = $this->getOriginalOrderAmount();
        $json['captured_amount'] = $this->getCapturedAmount();
        $json['status'] = $this->getStatus();
        $json['refunded_amount'] = $this->getRefundedAmount();
        $json['purchase_currency'] = $this->getPurchaseCurrency();
        $json['initial_payment_method'] = $this->getInitialPaymentMethod();
        $json['fraud_status'] = $this->getFraudStatus();
        $json['purchase_country'] = $this->getPurchaseCountry();
        $json['order_lines'] = $this->getOrderLines();
        $json['klarna_reference'] = $this->getKlarnaReference();
        $json['captures'] = $this->getCaptures();
        $json['refunds'] = $this->getRefunds();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
