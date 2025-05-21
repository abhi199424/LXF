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
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_5_8(KlarnaPayment $module)
{
    /** @var LoggerInterface $logger */
    $logger = $module->getService(LoggerInterface::class);

    try {
        /** @var Configuration $configuration */
        $configuration = $module->getService(Configuration::class);

        $module->registerHook('displayCustomerLoginFormAfter');
        $module->registerHook('displayCustomerAccountFormTop');
        $module->registerHook('displayTop');
        $module->registerHook('moduleRoutes');

        $module->unregisterHook('displayWrapperTop');

        $scope = sprintf('%s %s', Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_MANDATORY_SCOPE, Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_SCOPE);

        $configuration->set(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ON_SIGN_IN_SCOPE['sandbox'], $scope);
        $configuration->set(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ON_SIGN_IN_SCOPE['production'], $scope);

        createKlarnaCustomersDatabase158();
    } catch (Exception $e) {
        $logger->error('Failed to upgrade to version 1.5.8', [
            'context' => [],
            'exceptions' => ExceptionUtility::getExceptions($e),
        ]);

        return false;
    }

    return true;
}

function createKlarnaCustomersDatabase158()
{
    $db = Db::getInstance();
    $sql = 'CREATE TABLE IF NOT EXISTS `' . _DB_PREFIX_ . 'klarnapayment_customers' . '` (
                `id_klarnapayment_customer` INT(10) unsigned NOT NULL AUTO_INCREMENT,
                `id_token` TEXT NOT NULL,
                `id_refresh_token` VARCHAR(255) NOT NULL,
                `id_shop` INT(10) NOT NULL,
                `id_customer` INT(10) NOT NULL,
                `id_address` INT(10) NOT NULL,
            PRIMARY KEY(`id_klarnapayment_customer`, `id_shop`)
        ) ENGINE=' . _MYSQL_ENGINE_ . ' DEFAULT CHARSET=utf8;';

    $db->execute($sql);
}
