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

use KlarnaPayment\Module\Infrastructure\Bootstrap\Uninstall\Command\KlarnaExpressCheckoutTableUninstallCommand;
use KlarnaPayment\Module\Infrastructure\Bootstrap\Uninstall\Command\KlarnaPaymentCartsTableUninstallCommand;
use KlarnaPayment\Module\Infrastructure\Bootstrap\Uninstall\Command\KlarnaPaymentCustomersTableUninstallCommand;
use KlarnaPayment\Module\Infrastructure\Bootstrap\Uninstall\Command\KlarnaPaymentLogsTableUninstallCommand;
use KlarnaPayment\Module\Infrastructure\Bootstrap\Uninstall\Command\KlarnaPaymentOrdersTableUninstallCommand;
use KlarnaPayment\Module\Infrastructure\Bootstrap\Uninstall\Command\LogEntriesUninstallCommand;
use KlarnaPayment\Module\Infrastructure\Bootstrap\Uninstall\Command\UninstallCommandInterface;
use KlarnaPayment\Module\Infrastructure\Exception\CouldNotUninstallModule;

if (!defined('_PS_VERSION_')) {
    exit;
}

class DatabaseTableUninstaller implements UninstallerInterface
{
    private $logEntriesUninstallCommand;
    private $klarnaPaymentLogsTableUninstallCommand;

    private $klarnaPaymentOrdersTableUninstallCommand;
    private $klarnaPaymentCartsTableUninstallCommand;
    private $klarnaExpressCheckoutTableUninstallCommand;
    private $klarnaPaymentCustomersTableUninstallCommand;

    public function __construct(
        LogEntriesUninstallCommand $logEntriesUninstallCommand,
        KlarnaPaymentLogsTableUninstallCommand $klarnaPaymentLogsTableUninstallCommand,
        KlarnaPaymentOrdersTableUninstallCommand $klarnaPaymentOrdersTableUninstallCommand,
        KlarnaPaymentCartsTableUninstallCommand $klarnaPaymentCartsTableUninstallCommand,
        KlarnaExpressCheckoutTableUninstallCommand $klarnaExpressCheckoutTableUninstallCommand,
        KlarnaPaymentCustomersTableUninstallCommand $klarnaPaymentCustomersTableUninstallCommand
    ) {
        $this->logEntriesUninstallCommand = $logEntriesUninstallCommand;
        $this->klarnaPaymentLogsTableUninstallCommand = $klarnaPaymentLogsTableUninstallCommand;
        $this->klarnaPaymentOrdersTableUninstallCommand = $klarnaPaymentOrdersTableUninstallCommand;
        $this->klarnaPaymentCartsTableUninstallCommand = $klarnaPaymentCartsTableUninstallCommand;
        $this->klarnaExpressCheckoutTableUninstallCommand = $klarnaExpressCheckoutTableUninstallCommand;
        $this->klarnaPaymentCustomersTableUninstallCommand = $klarnaPaymentCustomersTableUninstallCommand;
    }

    /**
     * @throws CouldNotUninstallModule
     */
    public function init(): void
    {
        $commands = $this->getCommands();

        foreach ($commands as $command) {
            if (false == \Db::getInstance()->execute($command->getCommand())) {
                throw CouldNotUninstallModule::failedToUninstallDatabaseTable($command->getName());
            }
        }
    }

    /**
     * @return UninstallCommandInterface[]
     */
    private function getCommands(): array
    {
        return [
            $this->logEntriesUninstallCommand,
            $this->klarnaPaymentLogsTableUninstallCommand,
            $this->klarnaPaymentOrdersTableUninstallCommand,
            $this->klarnaPaymentCartsTableUninstallCommand,
            $this->klarnaExpressCheckoutTableUninstallCommand,
            $this->klarnaPaymentCustomersTableUninstallCommand,
        ];
    }
}
