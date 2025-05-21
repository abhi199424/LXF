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
use KlarnaPayment\Module\Core\Shared\Enum\KlarnaExpressCheckoutButtonShape;
use KlarnaPayment\Module\Core\Shared\Enum\KlarnaExpressCheckoutButtonTheme;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Bootstrap\Install\DatabaseTableInstaller;
use KlarnaPayment\Module\Infrastructure\Bootstrap\Install\ModuleTabInstaller;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_4_0(KlarnaPayment $module): bool
{
    configurationInstall140($module);

    /** @var DatabaseTableInstaller $databaseTableInstaller */
    $databaseTableInstaller = $module->getService(DatabaseTableInstaller::class);

    try {
        $databaseTableInstaller->init();
    } catch (\Throwable $exception) {
        \PrestaShopLogger::addLog("KlarnaPayment upgrade error: {$exception->getMessage()}");

        return false;
    }

    if (!moduleTabInstall140($module)) {
        return false;
    }

    registerHooks140($module);

    return true;
}

function registerHooks140(KlarnaPayment $module)
{
    $module->registerHook(Config::HOOK_LIST);
}

function configurationInstall140(KlarnaPayment $module): void
{
    /** @var Configuration $configuration */
    $configuration = $module->getService(Configuration::class);

    $configuration->set(
        Config::KLARNA_PAYMENT_DEFAULT_LOCALE['sandbox'],
        (string) $configuration->get('KLARNA_PAYMENT_SANDBOX_ONSITE_MESSAGING_DEFAULT_LOCALE')
    );

    $configuration->set(
        Config::KLARNA_PAYMENT_DEFAULT_LOCALE['production'],
        (string) $configuration->get('KLARNA_PAYMENT_PRODUCTION_ONSITE_MESSAGING_DEFAULT_LOCALE')
    );

    $configuration->delete('KLARNA_PAYMENT_SANDBOX_ONSITE_MESSAGING_DEFAULT_LOCALE');
    $configuration->delete('KLARNA_PAYMENT_PRODUCTION_ONSITE_MESSAGING_DEFAULT_LOCALE');

    $configuration->set(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE['sandbox'], '0');
    $configuration->set(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE['production'], '0');

    $configuration->set(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS['sandbox'], '');
    $configuration->set(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS['production'], '');

    $configuration->set(
        Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME['sandbox'],
        KlarnaExpressCheckoutButtonTheme::DEFAULT
    );
    $configuration->set(
        Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME['production'],
        KlarnaExpressCheckoutButtonTheme::DEFAULT
    );
    $configuration->set(
        Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE['sandbox'],
        KlarnaExpressCheckoutButtonShape::DEFAULT
    );
    $configuration->set(
        Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE['production'],
        KlarnaExpressCheckoutButtonShape::DEFAULT
    );
}

function moduleTabInstall140(KlarnaPayment $module): bool
{
    /** @var ModuleTabInstaller $moduleTabInstaller */
    $moduleTabInstaller = $module->getService(ModuleTabInstaller::class);

    /** @var LoggerInterface $logger */
    $logger = $module->getService(LoggerInterface::class);

    try {
        $moduleTabInstaller->init();
    } catch (\Throwable $exception) {
        $logger->error('Klarna Payment 1.4.0 upgrade error', [
            'context' => [],
            'exceptions' => ExceptionUtility::getExceptions($exception),
        ]);

        return false;
    }

    return true;
}
