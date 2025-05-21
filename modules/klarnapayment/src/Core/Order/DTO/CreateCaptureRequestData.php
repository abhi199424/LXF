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

class CreateCaptureRequestData
{
    /** @var Order */
    private $order;

    /** @var int|null */
    private $capturedAmount;

    /** @var array */
    private $orderLineIds;

    /** @var string|null */
    private $currencyIso;

    private function __construct(
        Order $order,
        int $capturedAmount,
        array $orderLineIds = [],
        ?string $currencyIso = null
    ) {
        $this->order = $order;
        $this->capturedAmount = $capturedAmount;
        $this->orderLineIds = $orderLineIds;
        $this->currencyIso = $currencyIso;
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
    public function getCapturedAmount(): ?int
    {
        return $this->capturedAmount;
    }

    /**
     * @return array
     */
    public function getOrderLineIds(): array
    {
        return $this->orderLineIds;
    }

    /**
     * @return string|null
     */
    public function getCurrencyIso(): ?string
    {
        return $this->currencyIso;
    }

    public static function create(
        Order $order,
        int $capturedAmount,
        array $orderLineIds = [],
        ?string $currencyIso = null
    ): self {
        return new self(
            $order,
            $capturedAmount,
            $orderLineIds,
            $currencyIso
        );
    }
}
