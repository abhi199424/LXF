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
use KlarnaPayment\Module\Infrastructure\Bootstrap\Install\Command\InstallCommandInterface;
use KlarnaPayment\Module\Infrastructure\Bootstrap\Install\Command\KlarnaExpressCheckoutTableInstallCommand;
use KlarnaPayment\Module\Infrastructure\Bootstrap\Install\Command\KlarnaPaymentCartsTableInstallCommand;
use KlarnaPayment\Module\Infrastructure\Bootstrap\Install\Command\KlarnaPaymentCustomersTableInstallCommand;
use KlarnaPayment\Module\Infrastructure\Bootstrap\Install\Command\KlarnaPaymentLogsTableInstallCommand;
use KlarnaPayment\Module\Infrastructure\Bootstrap\Install\Command\KlarnaPaymentOrdersTableInstallCommand;

if (!defined('_PS_VERSION_')) {
    exit;
}

class DatabaseTableInstaller implements InstallerInterface
{
    private $klarnaPaymentLogsTableInstallCommand;
    private $klarnaPaymentOrdersTableInstallCommand;
    private $klarnaPaymentCartsTableInstallCommand;
    private $klarnaExpressCheckoutTableInstallCommand;
    private $klarnaPaymentCustomersTableInstallCommand;

    public function __construct(
        KlarnaPaymentLogsTableInstallCommand $klarnaPaymentLogsTableInstallCommand,
        KlarnaPaymentOrdersTableInstallCommand $klarnaPaymentOrdersTableInstallCommand,
        KlarnaPaymentCartsTableInstallCommand $klarnaPaymentCartsTableInstallCommand,
        KlarnaExpressCheckoutTableInstallCommand $klarnaExpressCheckoutTableInstallCommand,
        KlarnaPaymentCustomersTableInstallCommand $klarnaPaymentCustomersTableInstallCommand
    ) {
        $this->klarnaPaymentLogsTableInstallCommand = $klarnaPaymentLogsTableInstallCommand;
        $this->klarnaPaymentOrdersTableInstallCommand = $klarnaPaymentOrdersTableInstallCommand;
        $this->klarnaPaymentCartsTableInstallCommand = $klarnaPaymentCartsTableInstallCommand;
        $this->klarnaExpressCheckoutTableInstallCommand = $klarnaExpressCheckoutTableInstallCommand;
        $this->klarnaPaymentCustomersTableInstallCommand = $klarnaPaymentCustomersTableInstallCommand;
    }

    /**
     * @return void
     *
     * @throws CouldNotInstallModule
     */
    public function init(): void
    {
        $commands = $this->getCommands();

        foreach ($commands as $command) {
            if (false == \Db::getInstance()->execute($command->getCommand())) {
                throw CouldNotInstallModule::failedToInstallDatabaseTable($command->getName());
            }
        }
    }

    /**
     * @return InstallCommandInterface[]
     */
    private function getCommands(): array
    {
        return [
            $this->klarnaPaymentLogsTableInstallCommand,
            $this->klarnaPaymentOrdersTableInstallCommand,
            $this->klarnaPaymentCartsTableInstallCommand,
            $this->klarnaExpressCheckoutTableInstallCommand,
            $this->klarnaPaymentCustomersTableInstallCommand,
        ];
    }
}
