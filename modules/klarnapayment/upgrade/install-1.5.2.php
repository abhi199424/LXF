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
use KlarnaPayment\Module\Infrastructure\Bootstrap\Install\ModuleTabInstaller;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_5_2(KlarnaPayment $module)
{
    /* @var LoggerInterface $logger */
    $logger = $module->getService(LoggerInterface::class);

    try {
        /** @var Configuration $configuration */
        $configuration = $module->getService(Configuration::class);

        $configuration->set(Config::KLARNA_PAYMENT_ORDER_MIN['sandbox'], '');
        $configuration->set(Config::KLARNA_PAYMENT_ORDER_MIN['production'], '');

        $configuration->set(Config::KLARNA_PAYMENT_ORDER_MAX['sandbox'], '');
        $configuration->set(Config::KLARNA_PAYMENT_ORDER_MAX['production'], '');

        reinstallModuleTabs152($module);
    } catch (\Throwable $e) {
        $logger->error('Failed to upgrade to version 1.5.2', [
            'context' => [],
            'exceptions' => ExceptionUtility::getExceptions($e),
        ]);

        return false;
    }

    return true;
}

function reinstallModuleTabs152($module): void
{
    $db = Db::getInstance();
    $dbQuery = new DbQuery();
    $dbQuery->select('id_tab');
    $dbQuery->from('tab');
    $dbQuery->where("`module` = '" . pSQL($module->name) . "'");
    $tabs = $db->executeS($dbQuery);

    foreach ($tabs as $tab) {
        $tabClass = new TabCore($tab['id_tab']);
        $tabClass->delete();
    }

    /* @var ModuleTabInstaller $tabsInstaller */
    $tabsInstaller = $module->getService(ModuleTabInstaller::class);

    $tabsInstaller->init();
}
