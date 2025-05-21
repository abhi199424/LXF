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

use KlarnaPayment\Module\Infrastructure\Bootstrap\Install\Command\KlarnaPaymentCustomersTableInstallCommand;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

function upgrade_module_1_5_6(KlarnaPayment $module)
{
    /* @var LoggerInterface $logger */
    $logger = $module->getService(LoggerInterface::class);

    /* @var KlarnaPaymentCustomersTableInstallCommand $klarnaPaymentCustomersTableInstallCommand */
    $klarnaPaymentCustomersTableInstallCommand = $module->getService(KlarnaPaymentCustomersTableInstallCommand::class);

    try {
        if (!\Db::getInstance()->execute($klarnaPaymentCustomersTableInstallCommand->getCommand())) {
            return false;
        }
    } catch (\Throwable $e) {
        $logger->error('Failed to upgrade to version 1.5.6', [
            'context' => [],
            'exceptions' => ExceptionUtility::getExceptions($e),
        ]);

        return false;
    }

    return true;
}
