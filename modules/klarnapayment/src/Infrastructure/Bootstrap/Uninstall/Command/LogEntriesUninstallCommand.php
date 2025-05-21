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

namespace KlarnaPayment\Module\Infrastructure\Bootstrap\Uninstall\Command;

use KlarnaPayment\Module\Infrastructure\Logger\Logger;

if (!defined('_PS_VERSION_')) {
    exit;
}

class LogEntriesUninstallCommand implements UninstallCommandInterface
{
    public function getName(): string
    {
        return \PrestaShopLogger::$definition['table'];
    }

    public function getCommand(): string
    {
        return '
            DELETE FROM `' . _DB_PREFIX_ . pSQL($this->getName()) . '`
            WHERE object_type = "' . pSQL(Logger::LOG_OBJECT_TYPE) . '"
        ';
    }
}
