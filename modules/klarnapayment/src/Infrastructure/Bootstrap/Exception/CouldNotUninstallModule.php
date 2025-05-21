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

namespace KlarnaPayment\Module\Infrastructure\Exception;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class CouldNotUninstallModule extends KlarnaPaymentException
{
    public static function failedToUninstallDatabaseTable($databaseTable): CouldNotUninstallModule
    {
        return new self(
            sprintf('Failed to uninstall database table (%s)', $databaseTable),
            ExceptionCode::INFRASTRUCTURE_FAILED_TO_UNINSTALL_DATABASE_TABLE,
            null,
            [
                'database_table' => $databaseTable,
            ]
        );
    }
}
