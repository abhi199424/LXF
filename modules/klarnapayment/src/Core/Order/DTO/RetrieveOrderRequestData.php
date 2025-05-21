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

class RetrieveOrderRequestData
{
    /** @var string */
    private $orderId;

    /** @var string */
    private $currencyIso;

    private function __construct(string $orderId, string $currencyIso)
    {
        $this->orderId = $orderId;
        $this->currencyIso = $currencyIso;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getCurrencyIso(): string
    {
        return $this->currencyIso;
    }

    public static function create(string $orderId, string $currencyIso = ''): self
    {
        return new self(
            $orderId,
            $currencyIso
        );
    }
}
