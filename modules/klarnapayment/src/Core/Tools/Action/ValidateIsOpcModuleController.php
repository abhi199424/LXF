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

namespace KlarnaPayment\Module\Core\Tools\Action;

use KlarnaPayment\Module\Core\Config\Config;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ValidateIsOpcModuleController
{
    public function run(\FrontController $controller): bool
    {
        if (!isset($controller->module)) {
            return false;
        }

        foreach (Config::KLARNA_PAYMENT_SUPPORTED_OPC_MODULES as $opcModule) {
            if (isset($controller->module->name) && $opcModule === $controller->module->name) {
                return true;
            }
        }

        return false;
    }
}
