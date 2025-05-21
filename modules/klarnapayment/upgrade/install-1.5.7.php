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

function upgrade_module_1_5_7(KlarnaPayment $module)
{
    /* @var LoggerInterface $logger */
    $logger = $module->getService(LoggerInterface::class);

    try {
        $module->unregisterHook('displayLeftColumn');
        $module->unregisterHook('displayRightColumn');

        addKlarnaPaymentsRegions157($module);
        migrateKlarnaPaymentsOsmThemeConfig157($module);
        migrateKlarnaPaymentsKecConfig157($module);
        deleteKlarnaPaymentsConfiguration157($module);
        reinstallKlarnaPaymentsTabs157($module);
        setDefaultKlarnaPaymentsEnable157($module);
    } catch (\Throwable $e) {
        $logger->error('Failed to upgrade to version 1.5.7', [
            'context' => [],
            'exceptions' => ExceptionUtility::getExceptions($e),
        ]);

        return false;
    }

    return true;
}

function deleteKlarnaPaymentsConfiguration157(KlarnaPayment $module)
{
    /** @var Configuration $configuration */
    $configuration = $module->getService(Configuration::class);

    $configuration->delete('KLARNA_PAYMENT_SANDBOX_COLOR_DETAILS');
    $configuration->delete('KLARNA_PAYMENT_PRODUCTION_COLOR_DETAILS');
    $configuration->delete('KLARNA_PAYMENT_SANDBOX_COLOR_BORDER');
    $configuration->delete('KLARNA_PAYMENT_PRODUCTION_COLOR_BORDER');
    $configuration->delete('KLARNA_PAYMENT_SANDBOX_COLOR_BORDER_SELECTED');
    $configuration->delete('KLARNA_PAYMENT_PRODUCTION_COLOR_BORDER_SELECTED');
    $configuration->delete('KLARNA_PAYMENT_SANDBOX_COLOR_TEXT');
    $configuration->delete('KLARNA_PAYMENT_PRODUCTION_COLOR_TEXT');
    $configuration->delete('KLARNA_PAYMENT_SANDBOX_RADIUS_BORDER');
    $configuration->delete('KLARNA_PAYMENT_PRODUCTION_RADIUS_BORDER');
    $configuration->delete('KLARNA_PAYMENT_SANDBOX_SEND_EMD');
    $configuration->delete('KLARNA_PAYMENT_PRODUCTION_SEND_EMD');
    $configuration->delete('KLARNA_PAYMENT_SANDBOX_API_USERNAME');
    $configuration->delete('KLARNA_PAYMENT_PRODUCTION_API_USERNAME');
    $configuration->delete('KLARNA_PAYMENT_SANDBOX_API_PASSWORD');
    $configuration->delete('KLARNA_PAYMENT_PRODUCTION_API_PASSWORD');
    $configuration->delete('KLARNA_PAYMENT_SANDBOX_API_ENDPOINT');
    $configuration->delete('KLARNA_PAYMENT_PRODUCTION_API_ENDPOINT');
    $configuration->delete('KLARNA_PAYMENT_SANDBOX_MERCHANT_ID');
    $configuration->delete('KLARNA_PAYMENT_PRODUCTION_MERCHANT_ID');
    $configuration->delete('KLARNA_PAYMENT_SANDBOX_API_KEY');
    $configuration->delete('KLARNA_PAYMENT_PRODUCTION_API_KEY');
    $configuration->delete('KLARNA_PAYMENT_SANDBOX_ONSITE_MESSAGING_DATA_CLIENT_ID');
    $configuration->delete('KLARNA_PAYMENT_PRODUCTION_ONSITE_MESSAGING_DATA_CLIENT_ID');
    $configuration->delete('KLARNA_PAYMENT_SANDBOX_ONSITE_MESSAGING_RIGHT_COLUMN_THEME');
    $configuration->delete('KLARNA_PAYMENT_PRODUCTION_ONSITE_MESSAGING_RIGHT_COLUMN_THEME');
    $configuration->delete('KLARNA_PAYMENT_SANDBOX_ONSITE_MESSAGING_RIGHT_COLUMN_DATA_KEY');
    $configuration->delete('KLARNA_PAYMENT_PRODUCTION_ONSITE_MESSAGING_RIGHT_COLUMN_DATA_KEY');
    $configuration->delete('KLARNA_PAYMENT_SANDBOX_ONSITE_MESSAGING_LEFT_COLUMN_THEME');
    $configuration->delete('KLARNA_PAYMENT_PRODUCTION_ONSITE_MESSAGING_LEFT_COLUMN_THEME');
    $configuration->delete('KLARNA_PAYMENT_SANDBOX_ONSITE_MESSAGING_LEFT_COLUMN_DATA_KEY');
    $configuration->delete('KLARNA_PAYMENT_PRODUCTION_ONSITE_MESSAGING_LEFT_COLUMN_DATA_KEY');
}

function migrateKlarnaPaymentsOsmThemeConfig157(KlarnaPayment $module)
{
    /** @var Configuration $configuration */
    $configuration = $module->getService(Configuration::class);

    $oldOsmThemeConfigKeys = [
        'sandbox' => [
            'KLARNA_PAYMENT_SANDBOX_ONSITE_MESSAGING_FOOTER_THEME',
            'KLARNA_PAYMENT_SANDBOX_ONSITE_MESSAGING_TOP_OF_PAGE_THEME',
            'KLARNA_PAYMENT_SANDBOX_ONSITE_MESSAGING_PRODUCT_PAGE_THEME',
            'KLARNA_PAYMENT_SANDBOX_ONSITE_MESSAGING_CART_PAGE_THEME',
        ],
        'production' => [
            'KLARNA_PAYMENT_PRODUCTION_ONSITE_MESSAGING_FOOTER_THEME',
            'KLARNA_PAYMENT_PRODUCTION_ONSITE_MESSAGING_TOP_OF_PAGE_THEME',
            'KLARNA_PAYMENT_PRODUCTION_ONSITE_MESSAGING_PRODUCT_PAGE_THEME',
            'KLARNA_PAYMENT_PRODUCTION_ONSITE_MESSAGING_CART_PAGE_THEME',
        ],
    ];

    $newOsmThemeConfigKeys = [
        'sandbox' => 'KLARNA_PAYMENT_SANDBOX_ONSITE_MESSAGING_THEME',
        'production' => 'KLARNA_PAYMENT_PRODUCTION_ONSITE_MESSAGING_THEME',
    ];

    foreach ($oldOsmThemeConfigKeys as $environment => $oldConfigKeys) {
        $themeConfigMigrated = false;

        foreach ($oldConfigKeys as $oldConfigKey) {
            // Migrate theme config from separate values per placement to one global value
            if (!$themeConfigMigrated) {
                $configValue = $configuration->get($oldConfigKey);

                if (null !== $configValue) {
                    // Take the first existing theme value from any placement and use it as global theme value
                    $configuration->set($newOsmThemeConfigKeys[$environment], $configValue);
                    $themeConfigMigrated = true;
                }
            }

            $configuration->delete($oldConfigKey);
        }
    }
}

function migrateKlarnaPaymentsKecConfig157(KlarnaPayment $module)
{
    /** @var Configuration $configuration */
    $configuration = $module->getService(Configuration::class);

    $kecThemeConfigKeys = [
        'KLARNA_PAYMENT_SANDBOX_EXPRESS_CHECKOUT_BUTTON_THEME',
        'KLARNA_PAYMENT_PRODUCTION_EXPRESS_CHECKOUT_BUTTON_THEME',
    ];
    $kecShapeConfigKeys = [
        'KLARNA_PAYMENT_SANDBOX_EXPRESS_CHECKOUT_BUTTON_SHAPE',
        'KLARNA_PAYMENT_PRODUCTION_EXPRESS_CHECKOUT_BUTTON_SHAPE',
    ];

    foreach ($kecThemeConfigKeys as $configKey) {
        $configValue = $configuration->get($configKey);

        // Dark theme option removed - using default instead
        if ($configValue === 'dark') {
            $configuration->set($configKey, 'default');
        }
    }

    foreach ($kecShapeConfigKeys as $configKey) {
        $configValue = $configuration->get($configKey);

        // Rounded shape option removed - using default instead
        if ($configValue === 'rounded') {
            $configuration->set($configKey, 'default');
        }
    }
}

function reinstallKlarnaPaymentsTabs157($module): void
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

function addKlarnaPaymentsRegions157(KlarnaPayment $module)
{
    $credentialsMap = [
        'EUROPE' => 'EU',
        'NORTH_AMERICA' => 'NA-US',
        'OCEANIA' => 'AP-AP',
    ];

    /** @var Configuration $configuration */
    $configuration = $module->getService(Configuration::class);

    $sandboxPaymentEndpoint = $configuration->get('KLARNA_PAYMENT_SANDBOX_API_ENDPOINT');
    $productionPaymentEndpoint = $configuration->get('KLARNA_PAYMENT_PRODUCTION_API_ENDPOINT');

    $sandboxClientId = $configuration->get('KLARNA_PAYMENT_SANDBOX_ONSITE_MESSAGING_DATA_CLIENT_ID');
    $productionClientId = $configuration->get('KLARNA_PAYMENT_PRODUCTION_ONSITE_MESSAGING_DATA_CLIENT_ID');

    $sandboxUsername = $configuration->get('KLARNA_PAYMENT_SANDBOX_API_USERNAME');
    $productionUsername = $configuration->get('KLARNA_PAYMENT_PRODUCTION_API_USERNAME');

    $sandboxPassword = $configuration->get('KLARNA_PAYMENT_SANDBOX_API_PASSWORD');
    $productionPassword = $configuration->get('KLARNA_PAYMENT_PRODUCTION_API_PASSWORD');

    $sandboxApiKey = $configuration->get('KLARNA_PAYMENT_SANDBOX_API_KEY');
    $productionApiKey = $configuration->get('KLARNA_PAYMENT_PRODUCTION_API_KEY');

    if (!empty($sandboxUsername) && !empty($sandboxPassword)) {
        $configuration->set(Config::KLARNA_PAYMENT_API_USERNAME['sandbox'][$credentialsMap[$sandboxPaymentEndpoint]], $sandboxUsername);
        $configuration->set(Config::KLARNA_PAYMENT_API_PASSWORD['sandbox'][$credentialsMap[$sandboxPaymentEndpoint]], $sandboxPassword);
        $configuration->set(Config::KLARNA_PAYMENT_API_KEY['sandbox'][$credentialsMap[$sandboxPaymentEndpoint]], $sandboxApiKey);
        $configuration->set(Config::KLARNA_PAYMENT_CLIENT_IDENTIFIER['sandbox'][$credentialsMap[$sandboxPaymentEndpoint]], $sandboxClientId);
        $configuration->set(Config::KLARNA_PAYMENT_CONNECTED_REGIONS['sandbox'][$credentialsMap[$sandboxPaymentEndpoint]], '1');
    }

    if (!empty($productionUsername) && !empty($productionPassword)) {
        $configuration->set(Config::KLARNA_PAYMENT_API_USERNAME['production'][$credentialsMap[$productionPaymentEndpoint]], $productionUsername);
        $configuration->set(Config::KLARNA_PAYMENT_API_PASSWORD['production'][$credentialsMap[$productionPaymentEndpoint]], $productionPassword);
        $configuration->set(Config::KLARNA_PAYMENT_API_KEY['production'][$credentialsMap[$productionPaymentEndpoint]], $productionApiKey);
        $configuration->set(Config::KLARNA_PAYMENT_CLIENT_IDENTIFIER['production'][$credentialsMap[$productionPaymentEndpoint]], $productionClientId);
        $configuration->set(Config::KLARNA_PAYMENT_CONNECTED_REGIONS['production'][$credentialsMap[$productionPaymentEndpoint]], '1');
    }
}

function setDefaultKlarnaPaymentsEnable157(KlarnaPayment $module): void
{
    /** @var Configuration $configuration */
    $configuration = $module->getService(Configuration::class);

    $configuration->set('KLARNA_PAYMENT_SANDBOX_ENABLE_BOX', Config::KLARNA_PAYMENT_ENABLE_BOX_DEFAULT);
    $configuration->set('KLARNA_PAYMENT_PRODUCTION_ENABLE_BOX', Config::KLARNA_PAYMENT_ENABLE_BOX_DEFAULT);
}
