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

function upgrade_module_1_5_8_1(KlarnaPayment $module)
{
    /** @var LoggerInterface $logger */
    $logger = $module->getService(LoggerInterface::class);

    try {
        createKlarnaCustomersDatabase1581();
    } catch (Exception $e) {
        $logger->error('Failed to upgrade to version 1.5.8.1', [
            'context' => [],
            'exceptions' => ExceptionUtility::getExceptions($e),
        ]);

        return false;
    }

    return true;
}

function createKlarnaCustomersDatabase1581(): void
{
    $db = Db::getInstance();
    $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'klarnapayment_customers' . '` (
                `id_klarnapayment_customer` INT(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_token` TEXT NOT NULL,
                `id_refresh_token` VARCHAR(255) NOT NULL,
                `id_shop` INT(10) NOT NULL,
                `id_customer` INT(10) NOT NULL,
                `id_address` INT(10) NOT NULL,
            PRIMARY KEY(`id_klarnapayment_customer`, `id_shop`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

    $db->execute($sql);
}
