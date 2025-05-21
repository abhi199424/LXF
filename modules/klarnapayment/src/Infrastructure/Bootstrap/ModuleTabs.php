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

namespace KlarnaPayment\Module\Infrastructure\Bootstrap;

use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Translator\AdminModuleTabTranslatorInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ModuleTabs
{
    public const SETTINGS_MODULE_TAB_CONTROLLER_NAME = 'AdminKlarnaPaymentSettings';
    public const PARENT_MODULE_TAB_CONTROLLER_NAME = 'AdminKlarnaPaymentParent';
    public const ORDER_MODULE_TAB_CONTROLLER_NAME = 'AdminKlarnaPaymentOrder';
    public const LOGS_MODULE_TAB_CONTROLLER_NAME = 'AdminKlarnaPaymentLogs';

    private $module;
    private $adminModuleTabTranslator;

    public function __construct(ModuleFactory $moduleFactory, AdminModuleTabTranslatorInterface $adminModuleTabTranslator)
    {
        $this->module = $moduleFactory->getModule();
        $this->adminModuleTabTranslator = $adminModuleTabTranslator;
    }

    public function getTabs(): array
    {
        return [
            [
                'name' => [
                    'en' => $this->module->displayName,
                    'en-US' => $this->module->displayName,
                ],
                'ParentClassName' => 'AdminParentModulesSf',
                'parent_class_name' => 'AdminParentModulesSf',
                'class_name' => self::PARENT_MODULE_TAB_CONTROLLER_NAME,
                'visible' => false,
            ],
            [
                'name' => $this->getTabName(self::SETTINGS_MODULE_TAB_CONTROLLER_NAME),
                'parent_class_name' => self::PARENT_MODULE_TAB_CONTROLLER_NAME,
                'class_name' => self::SETTINGS_MODULE_TAB_CONTROLLER_NAME,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->getTabName(self::LOGS_MODULE_TAB_CONTROLLER_NAME),
                'parent_class_name' => self::PARENT_MODULE_TAB_CONTROLLER_NAME,
                'class_name' => self::LOGS_MODULE_TAB_CONTROLLER_NAME,
                'module_tab' => true,
                'visible' => true,
            ],
            [
                'name' => $this->getTabName(self::ORDER_MODULE_TAB_CONTROLLER_NAME),
                'parent_class_name' => self::PARENT_MODULE_TAB_CONTROLLER_NAME,
                'class_name' => self::ORDER_MODULE_TAB_CONTROLLER_NAME,
                'module_tab' => false,
                'visible' => false,
            ],
        ];
    }

    private function getTabName($controllerName): array
    {
        $translation = $this->adminModuleTabTranslator->translate($controllerName);

        $tabName['en'] = isset($translation['en']) ? $translation['en'] : 'Missing';
        $tabName['en-US'] = isset($translation['en-US']) ? $translation['en-US'] : 'Missing';

        return $tabName;
    }

    /**
     * Filter visible tabs to handle in javascript for ps versions below 174
     */
    public function getTabsClassNames(): array
    {
        $filtered = [];
        $tabs = $this->getTabs();

        foreach ($tabs as $tab) {
            if (isset($tab['visible']) && $tab['visible']) {
                $filtered[] = $tab['class_name'];
            }
        }

        return $filtered;
    }
}
