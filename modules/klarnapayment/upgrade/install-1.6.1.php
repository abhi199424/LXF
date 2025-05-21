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

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_6_1(KlarnaPayment $module)
{
    try {
        \Configuration::updateValue(Config::ON_SITE_MESSAGING_INFOPAGE_CONFIG, Config::ON_SITE_MESSAGING_INFOPAGE_DEFAULT_PATH);
        \Configuration::updateValue(Config::ON_SITE_MESSAGING_INFOPAGE_ACTIVE_CONFIG['sandbox'], 0);
        \Configuration::updateValue(Config::ON_SITE_MESSAGING_INFOPAGE_ACTIVE_CONFIG['production'], 0);
    } catch (Exception $e) {
        \PrestaShopLogger::addLog("KlarnaPayment upgrade error: {$e->getMessage()}");

        return false;
    }

    return true;
}
