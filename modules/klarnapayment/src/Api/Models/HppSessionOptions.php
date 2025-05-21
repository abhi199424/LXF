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

class HppSessionOptions implements \JsonSerializable
{
    /**
     * @var array
     */
    private $paymentMethodCategories;

    /**
     * @var string
     */
    private $placeOrderMode;

    /**
     * @return array
     */
    public function getPaymentMethodCategories(): array
    {
        return $this->paymentMethodCategories;
    }

    /**
     * @return string
     */
    public function getPlaceOrderMode(): string
    {
        return $this->placeOrderMode;
    }

    /**
     * @map payment_method_categories
     */
    public function setPaymentMethodCategories(array $paymentMethodCategories): void
    {
        $this->paymentMethodCategories = $paymentMethodCategories;
    }

    /**
     * @map place_order_mode
     */
    public function setPlaceOrderMode(string $placeOrderMode): void
    {
        $this->placeOrderMode = $placeOrderMode;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['payment_method_categories'] = $this->getPaymentMethodCategories();
        $json['place_order_mode'] = $this->getPlaceOrderMode();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
