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

namespace KlarnaPayment\Module\Core\Order\DTO;

use KlarnaPayment\Module\Api\Models\Order;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CreateRefundRequestData
{
    /** @var Order */
    private $order;

    /** @var int|null */
    private $refundedAmount;

    /** @var array */
    private $orderLineIds;

    private function __construct(
        Order $order,
        int $refundedAmount,
        array $orderLineIds = []
    ) {
        $this->order = $order;
        $this->refundedAmount = $refundedAmount;
        $this->orderLineIds = $orderLineIds;
    }

    /**
     * @return Order
     */
    public function getOrder(): Order
    {
        return $this->order;
    }

    /**
     * @return int|null
     */
    public function getRefundedAmount(): ?int
    {
        return $this->refundedAmount;
    }

    /**
     * @return array
     */
    public function getOrderLineIds(): array
    {
        return $this->orderLineIds;
    }

    public static function create(
        Order $order,
        int $refundedAmount,
        array $orderLineIds = []
    ): self {
        return new self(
            $order,
            $refundedAmount,
            $orderLineIds
        );
    }
}
