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

namespace KlarnaPayment\Module\Infrastructure\Bootstrap\Exception;

use KlarnaPayment\Module\Infrastructure\Exception\ExceptionCode;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class CouldNotInstallModule extends KlarnaPaymentException
{
    public static function failedToInstallDatabaseTable($databaseTable): CouldNotInstallModule
    {
        return new self(
            sprintf('Failed to install database table (%s)', $databaseTable),
            ExceptionCode::INFRASTRUCTURE_FAILED_TO_INSTALL_DATABASE_TABLE,
            null,
            [
                'database_table' => $databaseTable,
            ]
        );
    }

    public static function failedToInstallOrderState(string $orderStateName, \Exception $exception): self
    {
        return new self(
            sprintf('Failed to install order state (%s).', $orderStateName),
            ExceptionCode::INFRASTRUCTURE_FAILED_TO_INSTALL_ORDER_STATE,
            $exception,
            [
                'order_state_name' => $orderStateName,
            ]
        );
    }

    public static function failedToInstallModuleTab(\Exception $exception, string $moduleTab): self
    {
        return new self(
            sprintf('Failed to install module tab (%s)', $moduleTab),
            ExceptionCode::INFRASTRUCTURE_FAILED_TO_INSTALL_MODULE_TAB,
            $exception,
            [
                'module_tab' => $moduleTab,
            ]
        );
    }
}
