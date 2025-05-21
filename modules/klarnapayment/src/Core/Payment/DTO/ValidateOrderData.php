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

namespace KlarnaPayment\Module\Core\Payment\DTO;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ValidateOrderData
{
    private $customerId;
    private $cartId;
    private $orderTotal;
    private $paymentMethod;
    private $externalOrderId;

    private function __construct(
        int $customerId,
        int $cartId,
        float $orderTotal,
        string $paymentMethod,
        string $externalOrderId
    ) {
        $this->customerId = $customerId;
        $this->cartId = $cartId;
        $this->orderTotal = $orderTotal;
        $this->paymentMethod = $paymentMethod;
        $this->externalOrderId = $externalOrderId;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public function getCartId(): int
    {
        return $this->cartId;
    }

    public function getOrderTotal(): float
    {
        return $this->orderTotal;
    }

    public function getPaymentMethod(): string
    {
        return $this->paymentMethod;
    }

    public function getExternalOrderId(): string
    {
        return $this->externalOrderId;
    }

    public static function create(
        int $customerId,
        int $cartId,
        float $orderTotal,
        string $paymentMethod,
        string $externalOrderId
    ): self {
        return new self(
            $customerId,
            $cartId,
            $orderTotal,
            $paymentMethod,
            $externalOrderId
        );
    }
}
