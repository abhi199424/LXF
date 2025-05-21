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

use KlarnaPayment\Module\Infrastructure\Bootstrap\Install\OrderStateInstaller;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_1_0(KlarnaPayment $module)
{
    /** @var OrderStateInstaller $orderStateInstaller */
    $orderStateInstaller = $module->getService(OrderStateInstaller::class);

    /* @var LoggerInterface $logger */
    $logger = $module->getService(LoggerInterface::class);

    $orderStateInstaller->init();

    $sql = 'RENAME TABLE ' . _DB_PREFIX_ . 'karna_payment_orders TO ' . _DB_PREFIX_ . 'klarna_payment_orders';

    try {
        if (!Db::getInstance()->execute($sql)) {
            return false;
        }
    } catch (Exception $e) {
        $logger->error('Failed to upgrade to version 1.1.0', [
            'context' => [],
            'exceptions' => ExceptionUtility::getExceptions($e),
        ]);

        return false;
    }

    $module->unregisterHook('actionOrderStatusPostUpdate');
    $module->registerHook('actionOrderHistoryAddAfter');

    return true;
}
