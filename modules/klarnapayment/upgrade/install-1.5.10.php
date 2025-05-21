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

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Config\OrderStatusConfig;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_5_10(KlarnaPayment $module)
{
    /** @var LoggerInterface $logger */
    $logger = $module->getService(LoggerInterface::class);
    try {
        $orderState = new \OrderState();

        $orderState->send_email = false;
        $orderState->color = Config::KLARNA_ORDER_PROCESSING_COLOR;
        $orderState->hidden = false;
        $orderState->delivery = false;
        $orderState->logable = true;
        $orderState->invoice = true;
        $orderState->unremovable = false;
        $orderState->module_name = $module->name;

        $languages = \Language::getLanguages(false);
        foreach ($languages as $language) {
            $orderState->name[$language['id_lang']] = 'Processing Klarna order creation';
        }
        $orderState->add();

        \Configuration::updateValue(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_PROCESSING['production'], (int) $orderState->id);
        \Configuration::updateValue(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_PROCESSING['sandbox'], (int) $orderState->id);

        $pendingOrderId = [
            'production' => \Configuration::get('KLARNA_PAYMENT_PRODUCTION_ORDER_STATE_PENDING'),
            'sandbox' => \Configuration::get('KLARNA_PAYMENT_SANDBOX_ORDER_STATE_PENDING'),
        ];

        \Configuration::deleteByName('KLARNA_PAYMENT_PRODUCTION_ORDER_STATE_PENDING');
        \Configuration::deleteByName('KLARNA_PAYMENT_SANDBOX_ORDER_STATE_PENDING');

        \Configuration::updateValue(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_ACCEPTED['production'], (int) $pendingOrderId['production']);
        \Configuration::updateValue(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_ACCEPTED['sandbox'], (int) $pendingOrderId['sandbox']);
    } catch (Exception $e) {
        $logger->error('Failed to upgrade to version 1.5.10', [
            'context' => [],
            'exceptions' => ExceptionUtility::getExceptions($e),
        ]);

        return false;
    }

    return true;
}
