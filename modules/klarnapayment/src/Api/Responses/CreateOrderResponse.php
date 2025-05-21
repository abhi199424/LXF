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

namespace KlarnaPayment\Module\Api\Responses;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @see https://developers.klarna.com/api/#payments-api-create-a-new-order
 */
class CreateOrderResponse implements \JsonSerializable, ResponseInterface
{
    /** @var ?string */
    private $fraudStatus;
    /** @var ?string */
    private $orderId;
    /** @var ?string */
    private $redirectUrl;

    public function getFraudStatus(): ?string
    {
        return $this->fraudStatus;
    }

    public function getOrderId(): ?string
    {
        return $this->orderId;
    }

    public function getRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }

    /**
     * @maps fraud_status
     */
    public function setFraudStatus(?string $fraudStatus): void
    {
        $this->fraudStatus = $fraudStatus;
    }

    /**
     * @maps order_id
     */
    public function setOrderId(?string $orderId): void
    {
        $this->orderId = $orderId;
    }

    /**
     * @maps redirect_url
     */
    public function setRedirectUrl(string $redirectUrl): void
    {
        $this->redirectUrl = $redirectUrl;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['fraud_status'] = $this->getFraudStatus();
        $json['order_id'] = $this->getOrderId();
        $json['redirect_url'] = $this->getRedirectUrl();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
