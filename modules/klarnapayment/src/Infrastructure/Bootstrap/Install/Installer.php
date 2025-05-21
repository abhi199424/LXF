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

use KlarnaPayment\Module\Infrastructure\Bootstrap\Exception\CouldNotInstallModule;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Installer implements InstallerInterface
{
    private $configurationInstaller;
    private $databaseTableInstaller;
    private $hookInstaller;
    private $moduleTabInstaller;

    private $orderStateInstaller;

    public function __construct(
        ConfigurationInstaller $configurationInstaller,
        DatabaseTableInstaller $databaseTableInstaller,
        HookInstaller $hookInstaller,
        OrderStateInstaller $orderStateInstaller,
        ModuleTabInstaller $moduleTabInstaller
    ) {
        $this->configurationInstaller = $configurationInstaller;
        $this->databaseTableInstaller = $databaseTableInstaller;
        $this->hookInstaller = $hookInstaller;
        $this->orderStateInstaller = $orderStateInstaller;
        $this->moduleTabInstaller = $moduleTabInstaller;
    }

    /**
     * @return void
     *
     * @throws CouldNotInstallModule
     * @throws KlarnaPaymentException
     */
    public function init(): void
    {
        try {
            $this->configurationInstaller->init();
            $this->databaseTableInstaller->init();
            $this->hookInstaller->init();
            $this->orderStateInstaller->init();
            $this->moduleTabInstaller->init();
            $this->configurationInstaller->setConfigurationDefaultValues();
        } catch (CouldNotInstallModule $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw CouldNotInstallModule::unknownError($exception);
        }
    }
}
