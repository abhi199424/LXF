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

class Capture implements \JsonSerializable
{
    /** @var ?Address */
    private $billingAddress;

    /** @var ?string */
    private $captureId;

    /** @var ?int */
    private $capturedAmount;

    /** @var ?string */
    private $capturedAt;

    /** @var ?string */
    private $description;

    /** @var ?string */
    private $klarnaReference;

    /** @var ?OrderLine[] */
    private $orderLines;

    /** @var ?string */
    private $reference;

    /** @var ?int */
    private $refundedAmount;

    /** @var ?Address */
    private $shippingAddress;

    /** @var ?array */
    private $shippingInfo;

    public function getBillingAddress(): ?Address
    {
        return $this->billingAddress;
    }

    public function getCaptureId(): ?string
    {
        return $this->captureId;
    }

    public function getCapturedAmount(): ?int
    {
        return $this->capturedAmount;
    }

    public function getCapturedAt(): ?string
    {
        return $this->capturedAt;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getKlarnaReference(): ?string
    {
        return $this->klarnaReference;
    }

    public function getOrderLines(): ?array
    {
        return $this->orderLines;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    public function getRefundedAmount(): ?int
    {
        return $this->refundedAmount;
    }

    public function getShippingAddress(): ?Address
    {
        return $this->shippingAddress;
    }

    public function getShippingInfo(): ?array
    {
        return $this->shippingInfo;
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
     * @maps capture_id
     */
    public function setCaptureId(?string $captureId): void
    {
        $this->captureId = $captureId;
    }

    /**
     * @maps captured_amount
     */
    public function setCapturedAmount(?int $capturedAmount): void
    {
        $this->capturedAmount = $capturedAmount;
    }

    /**
     * @maps captured_at
     */
    public function setCapturedAt(?string $capturedAt): void
    {
        $this->capturedAt = $capturedAt;
    }

    /**
     * @maps description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @maps klarna_reference
     */
    public function setKlarnaReference(?string $klarnaReference): void
    {
        $this->klarnaReference = $klarnaReference;
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
     * @maps reference
     */
    public function setReference(?string $reference): void
    {
        $this->reference = $reference;
    }

    /**
     * @maps refunded_amount
     */
    public function setRefundedAmount(?int $refundedAmount): void
    {
        $this->refundedAmount = $refundedAmount;
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
     * @maps shipping_info
     */
    public function setShippingInfo(?array $shippingInfo): void
    {
        $this->shippingInfo = $shippingInfo;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['billing_address'] = $this->getBillingAddress();
        $json['capture_id'] = $this->getCaptureId();
        $json['captured_amount'] = $this->getCapturedAmount();
        $json['captured_at'] = $this->getCapturedAt();
        $json['description'] = $this->getDescription();
        $json['klarna_reference'] = $this->getKlarnaReference();
        $json['order_lines'] = $this->getOrderLines();
        $json['reference'] = $this->getReference();
        $json['refunded_amount'] = $this->getRefundedAmount();
        $json['shipping_address'] = $this->getShippingAddress();
        $json['shipping_info'] = $this->getShippingInfo();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
