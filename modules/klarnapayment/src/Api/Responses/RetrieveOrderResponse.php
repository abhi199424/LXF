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

use KlarnaPayment\Module\Api\Models\Order;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @see https://developers.klarna.com/api/#order-management-api-get-order
 */
class RetrieveOrderResponse implements \JsonSerializable, ResponseInterface
{
    /** @var ?Order */
    private $order;

    public function getOrder(): ?Order
    {
        return $this->order;
    }

    /**
     * @param \KlarnaPayment\Module\Api\Models\Order|null $order
     *
     * @maps order
     */
    public function setOrder(?Order $order)
    {
        $this->order = $order;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['order'] = $this->getOrder();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
