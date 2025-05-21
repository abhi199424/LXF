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

namespace KlarnaPayment\Module\Infrastructure\Bootstrap\Uninstall;

use KlarnaPayment\Module\Infrastructure\Exception\CouldNotUninstallModule;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Uninstaller implements UninstallerInterface
{
    private $databaseTableUninstaller;
    private $configurationUninstaller;

    public function __construct(
        DatabaseTableUninstaller $databaseTableUninstaller,
        ConfigurationUninstaller $configurationUninstaller
    ) {
        $this->databaseTableUninstaller = $databaseTableUninstaller;
        $this->configurationUninstaller = $configurationUninstaller;
    }

    /**
     * @throws CouldNotUninstallModule
     * @throws KlarnaPaymentException
     */
    public function init(): void
    {
        try {
            $this->databaseTableUninstaller->init();
            $this->configurationUninstaller->init();
        } catch (CouldNotUninstallModule $exception) {
            throw $exception;
        } catch (\Throwable $exception) {
            throw CouldNotUninstallModule::unknownError($exception);
        }
    }
}
