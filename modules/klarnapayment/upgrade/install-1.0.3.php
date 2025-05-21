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

use KlarnaPayment\Module\Infrastructure\Config\OrderStatusConfig;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_0_3($module)
{
    /** @var \KlarnaPayment\Module\Infrastructure\Adapter\Configuration $configuration */
    $configuration = $module->getService(\KlarnaPayment\Module\Infrastructure\Adapter\Configuration::class);

    $configuration->set(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_PENDING['production'], (int) \Configuration::get('PS_OS_PAYMENT'));
    $configuration->set(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_PENDING['sandbox'], (int) \Configuration::get('PS_OS_PAYMENT'));

    if ((int) $configuration->get('PS_OS_PAYMENT') === (int) $configuration->get(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_CAPTURED['production'])) {
        $configuration->set(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_CAPTURED['production'], (int) \Configuration::get('PS_OS_SHIPPING'));
    }

    if ((int) $configuration->get('PS_OS_PAYMENT') === (int) $configuration->get(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_CAPTURED['sandbox'])) {
        $configuration->set(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_CAPTURED['sandbox'], (int) \Configuration::get('PS_OS_SHIPPING'));
    }

    return true;
}
