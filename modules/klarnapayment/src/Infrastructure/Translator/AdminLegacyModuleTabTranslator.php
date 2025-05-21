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

namespace KlarnaPayment\Module\Infrastructure\Translator;

use KlarnaPayment\Module\Infrastructure\Bootstrap\ModuleTabs;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminLegacyModuleTabTranslator implements AdminModuleTabTranslatorInterface
{
    public function translate(string $key)
    {
        return isset($this->getTranslations()[$key]) ? $this->getTranslations()[$key] : $key;
    }

    private function getTranslations(): array
    {
        return [
            ModuleTabs::SETTINGS_MODULE_TAB_CONTROLLER_NAME => [
                'en' => 'Settings',
                'en-US' => 'Settings',
            ],
            ModuleTabs::ORDER_MODULE_TAB_CONTROLLER_NAME => [
                'en' => 'Order',
                'en-US' => 'Order',
            ],
            ModuleTabs::LOGS_MODULE_TAB_CONTROLLER_NAME => [
                'en' => 'Logs',
                'en-US' => 'Logs',
            ],
        ];
    }
}
