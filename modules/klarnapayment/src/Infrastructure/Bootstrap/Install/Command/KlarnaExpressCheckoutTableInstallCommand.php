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

class KlarnaExpressCheckoutTableInstallCommand implements InstallCommandInterface
{
    public function getName(): string
    {
        return \KlarnaExpressCheckout::$definition['table'];
    }

    public function getCommand(): string
    {
        return 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . pSQL(\KlarnaExpressCheckout::$definition['table']) . '` (
                `id_klarna_expresscheckout` INT(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_cart` INT(10) UNSIGNED NOT NULL,
                `is_kec` TINYINT(1) NOT NULL,
                `client_token` VARCHAR(4096) NOT NULL,
                `address_checksum` VARCHAR(256) NOT NULL,
            PRIMARY KEY(`id_klarna_expresscheckout`),
            UNIQUE KEY(`id_cart`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
    }
}
