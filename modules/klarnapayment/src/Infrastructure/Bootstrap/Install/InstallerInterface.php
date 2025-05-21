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

if (!defined('_PS_VERSION_')) {
    exit;
}

interface InstallerInterface
{
    /**
     * @throws CouldNotInstallModule
     */
    public function init(): void;
}
