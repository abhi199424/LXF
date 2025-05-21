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

class HppSession implements \JsonSerializable
{
    /** @var ?string */
    private $authorizationToken;

    /** @var ?string */
    private $klarnaReference;

    /** @var ?string */
    private $orderId;

    /** @var string */
    private $sessionId;

    /** @var string */
    private $status;

    /** @var ?Customer */
    private $customer;

    /**
     * @return string|null
     */
    public function getAuthorizationToken(): ?string
    {
        return $this->authorizationToken;
    }

    /**
     * @return string|null
     */
    public function getKlarnaReference(): ?string
    {
        return $this->klarnaReference;
    }

    /**
     * @return string|null
     */
    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    /**
     * @return string
     */
    public function getSessionId(): string
    {
        return $this->sessionId;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    public function getCustomer(): ?Customer
    {
        return $this->customer;
    }

    /**
     * @map authorization_token
     */
    public function setAuthorizationToken(string $authorizationToken): void
    {
        $this->authorizationToken = $authorizationToken;
    }

    /**
     * @map klarna_reference
     */
    public function setKlarnaReference(?string $klarnaReference): void
    {
        $this->klarnaReference = $klarnaReference;
    }

    /**
     * @map order_id
     */
    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
    }

    /**
     * @map session_id
     */
    public function setSessionId(string $sessionId): void
    {
        $this->sessionId = $sessionId;
    }

    /**
     * @map status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @param \KlarnaPayment\Module\Api\Models\Customer|null $customer
     * @map customer
     */
    public function setCustomer(?Customer $customer): void
    {
        $this->customer = $customer;
    }

    public function jsonSerialize(): array
    {
        $json = [
            'authorization_token' => $this->getAuthorizationToken(),
            'klarna_reference' => $this->getKlarnaReference(),
            'order_id' => $this->getOrderId(),
            'session_id' => $this->getSessionId(),
            'status' => $this->getStatus(),
            'customer' => $this->getCustomer(),
        ];

        // Filter out null values
        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
