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

use KlarnaPayment\Module\Api\Models\ShippingInfo;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @see https://developers.klarna.com/api/#order-management-api-create-capture
 */
class CreateCaptureRequest implements \JsonSerializable, RequestInterface
{
    /** @var string */
    private $orderId;
    /** @var int */
    private $capturedAmount;
    /** @var array|null */
    private $orderLines;
    /** @var array|null */
    private $shippingInfo;

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getCapturedAmount(): int
    {
        return $this->capturedAmount;
    }

    public function getOrderLines(): ?array
    {
        return $this->orderLines;
    }

    /**
     * @return ?ShippingInfo[]
     */
    public function getShippingInfo(): ?array
    {
        return $this->shippingInfo;
    }

    /**
     * @maps order_id
     */
    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
    }

    /**
     * @maps captured_amount
     */
    public function setCapturedAmount(int $capturedAmount): void
    {
        $this->capturedAmount = $capturedAmount;
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
     * @param \KlarnaPayment\Module\Api\Models\ShippingInfo[]|null $shippingInfo
     *
     * @maps shipping_info
     */
    public function setShippingInfo(?array $shippingInfo): void
    {
        $this->shippingInfo = $shippingInfo;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['order_id'] = $this->getOrderId();
        $json['captured_amount'] = $this->getCapturedAmount();
        $json['order_lines'] = $this->getOrderLines();
        $json['shipping_info'] = $this->getShippingInfo();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
