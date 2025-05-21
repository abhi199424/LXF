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

class KlarnaPaymentCartsTableInstallCommand implements InstallCommandInterface
{
    public function getName(): string
    {
        return \KlarnaPaymentCart::$definition['table'];
    }

    public function getCommand(): string
    {
        return 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . pSQL(\KlarnaPaymentCart::$definition['table']) . '` (
                `id_klarnapayment_cart` INT(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_cart` INT(10) NOT NULL,
                `id_shop` INT(10) NOT NULL,
            PRIMARY KEY(`id_klarnapayment_cart`, `id_cart`, `id_shop`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';
    }
}
