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

use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_3_6(KlarnaPayment $module)
{
    /* @var LoggerInterface $logger */
    $logger = $module->getService(LoggerInterface::class);

    $sql = 'ALTER TABLE ' . _DB_PREFIX_ . 'klarna_payment_orders
    ADD UNIQUE (`authorization_token`)';

    try {
        if (!Db::getInstance()->execute($sql)) {
            return false;
        }
    } catch (\Throwable $e) {
        $logger->error('Failed to upgrade to version 1.3.6', [
            'context' => [],
            'exceptions' => ExceptionUtility::getExceptions($e),
        ]);

        return false;
    }

    return true;
}
