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

use KlarnaPayment\Module\Infrastructure\Repository\CollectionRepository;

if (!defined('_PS_VERSION_')) {
    exit;
}

class HookRepository extends CollectionRepository implements HookRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(\Hook::class);
    }

    public function getAttached(int $moduleId, int $shopId): array
    {
        $query = new \DbQuery();

        $query
            ->select('h.name')
            ->from('hook', 'h')
            ->leftJoin('hook_module', 'hm', 'h.id_hook = hm.id_hook')
            ->where('hm.id_shop = ' . (int) $shopId)
            ->where('hm.id_module = ' . (int) $moduleId);

        $result = \Db::getInstance()->executeS($query);

        return !empty($result) ? $result : [];
    }
}
