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

namespace KlarnaPayment\Module\Core\Payment\Enum;

use KlarnaPayment\Module\Core\Config\Config;

if (!defined('_PS_VERSION_')) {
    exit;
}

class OnsiteMessagingPlacement
{
    public const PRODUCT_PAGE = [
        'theme' => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_THEME,
        'data_key' => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_DATA_KEY,
    ];

    public const CART_PAGE = [
        'theme' => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_THEME,
        'data_key' => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_DATA_KEY,
    ];

    public const FOOTER = [
        'theme' => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_THEME,
        'data_key' => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_DATA_KEY,
    ];

    public const TOP_OF_PAGE = [
        'theme' => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_THEME,
        'data_key' => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_DATA_KEY,
    ];
}
