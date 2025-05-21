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

namespace KlarnaPayment\Module\Infrastructure\Bootstrap\Uninstall\Command;

if (!defined('_PS_VERSION_')) {
    exit;
}

interface UninstallCommandInterface
{
    public function getName(): string;

    public function getCommand(): string;
}
