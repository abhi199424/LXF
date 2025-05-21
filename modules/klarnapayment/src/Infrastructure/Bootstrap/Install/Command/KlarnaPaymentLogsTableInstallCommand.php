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

namespace KlarnaPayment\Module\Infrastructure\Bootstrap\Install\Command;

if (!defined('_PS_VERSION_')) {
    exit;
}

class KlarnaPaymentLogsTableInstallCommand implements InstallCommandInterface
{
    public function getName(): string
    {
        return \KlarnaPaymentLog::$definition['table'];
    }

    public function getCommand(): string
    {
        return 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . pSQL(\KlarnaPaymentLog::$definition['table']) . '` (
                `id_klarnapayment_log` INT(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_log` INT(10) NOT NULL,
                `id_shop` INT(10) NOT NULL,
                `correlation_id` VARCHAR(128) NULL,
                `request` MEDIUMTEXT NULL,
                `response` MEDIUMTEXT NULL,
                `context` MEDIUMTEXT NULL,
                `date_add` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY(`id_klarnapayment_log`, `id_log`, `id_shop`),
            INDEX(`id_log`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
    }
}
