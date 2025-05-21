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

use KlarnaPayment\Module\Api\Models\OrderLine;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @see https://developers.klarna.com/api/#order-management-api-create-a-refund
 */
class CreateRefundRequest implements \JsonSerializable, RequestInterface
{
    /** @var string */
    private $orderId;
    /** @var int */
    private $refundedAmount;
    /** @var OrderLine[]|null */
    private $orderLines;

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getRefundedAmount(): int
    {
        return $this->refundedAmount;
    }

    /**
     * @return OrderLine[]|null
     */
    public function getOrderLines(): ?array
    {
        return $this->orderLines;
    }

    /**
     * @maps refunded_amount
     */
    public function setRefundedAmount(int $refundedAmount): void
    {
        $this->refundedAmount = $refundedAmount;
    }

    /**
     * @maps order_id
     */
    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
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

    public function jsonSerialize(): array
    {
        $json = [];
        $json['order_id'] = $this->getOrderId();
        $json['refunded_amount'] = $this->getRefundedAmount();
        $json['order_lines'] = $this->getOrderLines();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
