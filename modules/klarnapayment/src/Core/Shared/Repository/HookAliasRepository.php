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

namespace KlarnaPayment\Module\Core\Shared\Repository;

if (!defined('_PS_VERSION_')) {
    exit;
}

class HookAliasRepository implements HookAliasRepositoryInterface
{
    public function hookAliasExist(string $hookName): bool
    {
        $sql = new \DbQuery();
        $sql->select('COUNT(*)');
        $sql->from('hook_alias');
        $sql->where('alias = \'' . pSQL($hookName) . '\' OR name = \'' . pSQL($hookName) . '\'');

        return (bool) \Db::getInstance()->getValue($sql);
    }
}
