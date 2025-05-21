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

if (!defined('_PS_VERSION_')) {
    exit;
}

class CancelOrderRequestData
{
    /** @var string */
    private $orderId;

    private function __construct(string $orderId)
    {
        $this->orderId = $orderId;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public static function create(string $orderId): self
    {
        return new self(
            $orderId
        );
    }
}
