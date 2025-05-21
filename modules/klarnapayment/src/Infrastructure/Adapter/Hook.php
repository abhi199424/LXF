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

use Hook as PrestashopHook;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Hook implements HookInterface
{
    /**
     * Execute modules for specified hook.
     *
     * @param string $hookName
     */
    public function exec(string $hookName, $hookArgs = [], $idModule = null, $arrayReturn = true): ?array
    {
        return PrestashopHook::exec($hookName, $hookArgs, $idModule, $arrayReturn);
    }
}
