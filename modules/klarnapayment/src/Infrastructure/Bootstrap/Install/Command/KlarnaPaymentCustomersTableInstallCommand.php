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

class KlarnaPaymentCustomersTableInstallCommand implements InstallCommandInterface
{
    public function getName(): string
    {
        return \KlarnaPaymentCustomer::$definition['table'];
    }

    public function getCommand(): string
    {
        return 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . pSQL(\KlarnaPaymentCustomer::$definition['table']) . '` (
                `id_klarnapayment_customer` INT(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_token` TEXT NOT NULL,
                `id_refresh_token` VARCHAR(255) NOT NULL,
                `id_shop` INT(10) NOT NULL,
                `id_customer` INT(10) NOT NULL,
                `id_address` INT(10) NOT NULL,
            PRIMARY KEY(`id_klarnapayment_customer`, `id_shop`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
    }
}
