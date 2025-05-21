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

namespace KlarnaPayment\Module\Infrastructure\Logger\Repository;

use KlarnaPayment\Module\Infrastructure\Repository\CollectionRepository;
use KlarnaPayment\Module\Infrastructure\Utility\VersionUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class PrestashopLoggerRepository extends CollectionRepository implements PrestashopLoggerRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(\PrestaShopLogger::class);
    }

    /** {@inheritDoc} */
    public function getLogIdByObjectId(string $objectId, ?int $shopId): ?int
    {
        $query = new \DbQuery();

        $query
            ->select('l.id_log')
            ->from('log', 'l')
            ->where('l.object_id = "' . pSQL($objectId) . '"')
            ->orderBy('l.id_log DESC');

        if (VersionUtility::isPsVersionGreaterOrEqualTo('1.7.8.0')) {
            $query->where('l.id_shop = ' . (int) $shopId);
        }

        $logId = \Db::getInstance()->getValue($query);

        return (int) $logId ?: null;
    }
}
