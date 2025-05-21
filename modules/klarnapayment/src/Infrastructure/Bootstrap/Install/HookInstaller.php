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

namespace KlarnaPayment\Module\Infrastructure\Bootstrap\Install;

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;

if (!defined('_PS_VERSION_')) {
    exit;
}

class HookInstaller implements InstallerInterface
{
    /** @var \KlarnaPayment */
    private $module;

    public function __construct(
        ModuleFactory $moduleFactory
    ) {
        $this->module = $moduleFactory->getModule();
    }

    /** {@inheritDoc} */
    public function init(): void
    {
        foreach (Config::HOOK_LIST as $hook) {
            $this->module->registerHook($hook);
        }
    }
}
