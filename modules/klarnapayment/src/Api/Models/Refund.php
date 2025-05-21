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

class Refund implements \JsonSerializable
{
    /** @var ?string */
    private $refundId;

    /** @var ?int */
    private $refundedAmount;

    /** @var ?string */
    private $refundedAt;

    /** @var ?string */
    private $description;

    /** @var ?OrderLine[] */
    private $orderLines;

    /** @var ?string */
    private $reference;

    public function getRefundId(): ?string
    {
        return $this->refundId;
    }

    public function getRefundedAmount(): ?int
    {
        return $this->refundedAmount;
    }

    public function getRefundedAt(): ?string
    {
        return $this->refundedAt;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getOrderLines(): ?array
    {
        return $this->orderLines;
    }

    public function getReference(): ?string
    {
        return $this->reference;
    }

    /**
     * @maps refund_id
     */
    public function setRefundId(?string $refundId): void
    {
        $this->refundId = $refundId;
    }

    /**
     * @maps refunded_amount
     */
    public function setRefundedAmount(?int $refundedAmount): void
    {
        $this->refundedAmount = $refundedAmount;
    }

    /**
     * @maps refunded_at
     */
    public function setRefundedAt(?string $refundedAt): void
    {
        $this->refundedAt = $refundedAt;
    }

    /**
     * @maps description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
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

    public function jsonSerialize(): array
    {
        $json = [];
        $json['refund_id'] = $this->getRefundId();
        $json['refunded_amount'] = $this->getRefundedAmount();
        $json['refunded_at'] = $this->getRefundedAt();
        $json['description'] = $this->getDescription();
        $json['order_lines'] = $this->getOrderLines();
        $json['reference'] = $this->getReference();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
