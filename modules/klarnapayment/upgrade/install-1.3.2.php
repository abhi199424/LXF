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
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_3_2(KlarnaPayment $module)
{
    /* @var LoggerInterface $logger */
    $logger = $module->getService(LoggerInterface::class);

    /** @var Configuration $configuration */
    $configuration = $module->getService(Configuration::class);

    $sql = 'ALTER TABLE ' . _DB_PREFIX_ . 'klarna_payment_orders
        ADD COLUMN `id_klarna_merchant` VARCHAR(64) NOT NULL,
        ADD COLUMN `klarna_reference` VARCHAR(64) NOT NULL
    ';

    try {
        if (!Db::getInstance()->execute($sql)) {
            return false;
        }
    } catch (\Throwable $e) {
        $logger->error('Failed to upgrade to version 1.3.2', [
            'context' => [],
            'exceptions' => ExceptionUtility::getExceptions($e),
        ]);

        return false;
    }

    $idKlarnaMerchant = $configuration->getByEnvironment(Config::KLARNA_PAYMENT_MERCHANT_ID);

    $sql = '
        UPDATE `' . _DB_PREFIX_ . 'klarna_payment_orders`
        SET `id_klarna_merchant` = "' . pSQL($idKlarnaMerchant) . '"
    ';

    try {
        if (!Db::getInstance()->execute($sql)) {
            return false;
        }
    } catch (\Throwable $e) {
        $logger->error('Failed to update column values', [
            'context' => [],
            'exceptions' => ExceptionUtility::getExceptions($e),
        ]);

        return false;
    }

    /** @var Configuration $configuration */
    $configuration = $module->getService(Configuration::class);

    $configuration->set(Config::KLARNA_PAYMENT_ALLOW_ORDER_STATUS_CHANGE['sandbox'], '1');
    $configuration->set(Config::KLARNA_PAYMENT_ALLOW_ORDER_STATUS_CHANGE['production'], '1');

    return true;
}
