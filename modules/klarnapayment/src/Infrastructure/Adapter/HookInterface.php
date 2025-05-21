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

namespace KlarnaPayment\Module\Infrastructure\Adapter;

if (!defined('_PS_VERSION_')) {
    exit;
}

interface HookInterface
{
    public function exec(string $hookName, $hookArgs = [], $idModule = null, $arrayReturn = true): ?array;
}
