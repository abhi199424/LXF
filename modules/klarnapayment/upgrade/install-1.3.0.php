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
use KlarnaPayment\Module\Infrastructure\Bootstrap\Install\DatabaseTableInstaller;
use KlarnaPayment\Module\Infrastructure\Bootstrap\Install\ModuleTabInstaller;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_3_0(KlarnaPayment $module): bool
{
    /** @var DatabaseTableInstaller $databaseTableInstaller */
    $databaseTableInstaller = $module->getService(DatabaseTableInstaller::class);

    try {
        $databaseTableInstaller->init();
    } catch (\Throwable $exception) {
        \PrestaShopLogger::addLog('KlarnaPayment upgrade error: {$exception->getMessage()}');

        return false;
    }

    /** @var ModuleTabInstaller $moduleTabInstaller */
    $moduleTabInstaller = $module->getService(ModuleTabInstaller::class);

    try {
        $moduleTabInstaller->init();
    } catch (\Throwable $exception) {
        \PrestaShopLogger::addLog('KlarnaPayment upgrade error: {$exception->getMessage()}');

        return false;
    }

    $module->registerHook([
        'displayHeader',
        'displayProductPriceBlock',
        'displayShoppingCart',
        'displayBanner',
        'displayLeftColumn',
        'displayRightColumn',
        'displayFooter',
    ]);

    /** @var Configuration $configuration */
    $configuration = $module->getService(Configuration::class);

    $configuration->set(Config::KLARNA_PAYMENT_SEND_EMD['sandbox'], '0');
    $configuration->set(Config::KLARNA_PAYMENT_SEND_EMD['production'], '0');

    return true;
}
