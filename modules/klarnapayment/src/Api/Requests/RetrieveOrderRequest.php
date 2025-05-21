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

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @see https://developers.klarna.com/api/#order-management-api-get-order
 */
class RetrieveOrderRequest implements \JsonSerializable, RequestInterface
{
    /** @var string */
    private $orderId;

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    /**
     * @maps order_id
     */
    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['order_id'] = $this->getOrderId();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
