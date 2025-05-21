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

class KlarnaPaymentOrdersTableInstallCommand implements InstallCommandInterface
{
    public function getName(): string
    {
        return \KlarnaPaymentOrder::$definition['table'];
    }

    public function getCommand(): string
    {
        return 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . pSQL(\KlarnaPaymentOrder::$definition['table']) . '` (
                `id_klarna_payment_order` INT(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_internal` INT(10) NOT NULL,
                `id_external` VARCHAR(64) NOT NULL,
                `id_shop` INT(10) NOT NULL,
                `id_klarna_merchant` VARCHAR(64) NOT NULL,
                `klarna_reference` VARCHAR(64) NOT NULL,
                `authorization_token` VARCHAR(64) NOT NULL UNIQUE,
            PRIMARY KEY(`id_klarna_payment_order`, `id_internal`, `id_external`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
    }
}
