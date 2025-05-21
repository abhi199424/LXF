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

namespace KlarnaPayment\Module\Infrastructure\Config;

if (!defined('_PS_VERSION_')) {
    exit;
}

class OrderStatusConfig
{
    public const KLARNA_PAYMENT_ORDER_STATE_ACCEPTED = [
        'production' => 'KLARNA_PAYMENT_PRODUCTION_ORDER_STATE_ACCEPTED',
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_ORDER_STATE_ACCEPTED',
    ];

    public const KLARNA_PAYMENT_ORDER_STATE_CAPTURED = [
        'production' => 'KLARNA_PAYMENT_PRODUCTION_ORDER_STATE_CAPTURED',
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_ORDER_STATE_CAPTURED',
    ];

    public const KLARNA_PAYMENT_ORDER_STATE_PARTIALLY_CAPTURED = [
        'production' => 'KLARNA_PAYMENT_PRODUCTION_ORDER_STATE_PARTIALLY_CAPTURED',
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_ORDER_STATE_PARTIALLY_CAPTURED',
    ];

    public const KLARNA_PAYMENT_ORDER_STATE_CANCELLED = [
        'production' => 'KLARNA_PAYMENT_PRODUCTION_ORDER_STATE_CANCELLED',
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_ORDER_STATE_CANCELLED',
    ];

    public const KLARNA_PAYMENT_ORDER_STATE_REFUNDED = [
        'production' => 'KLARNA_PAYMENT_PRODUCTION_ORDER_STATE_REFUNDED',
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_ORDER_STATE_REFUNDED',
    ];

    public const KLARNA_PAYMENT_ORDER_STATE_PROCESSING = [
        'production' => 'KLARNA_PAYMENT_PRODUCTION_ORDER_STATE_PROCESSING',
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_ORDER_STATE_PROCESSING',
    ];
}
