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

class OrderStateRepository extends CollectionRepository implements OrderStateRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(\OrderState::class);
    }

    public function getOrderStates(int $langId): array
    {
        $query = new \DbQuery();

        $query
            ->select('osl.name, os.id_order_state')
            ->from('order_state', 'os')
            ->leftJoin(
                'order_state_lang', 'osl',
                'os.id_order_state = osl.id_order_state AND osl.id_lang = ' . $langId
            )
            ->where('os.deleted = 0');

        $result = \Db::getInstance()->executeS($query);

        return !empty($result) ? $result : [];
    }

    public function getCurrentOrderStateHistoryList(int $orderId): array
    {
        $query = new \DbQuery();

        $query
            ->select('oh.id_order_state')
            ->from('order_history', 'oh')
            ->where('oh.id_order = ' . $orderId);

        $result = \Db::getInstance()->executeS($query);

        return !empty($result) ? $result : [];
    }
}
