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

use Invertus\Knapsack\Collection;
use KlarnaPayment\Module\Infrastructure\Repository\CollectionRepository;

if (!defined('_PS_VERSION_')) {
    exit;
}

class KlarnaPaymentLogRepository extends CollectionRepository implements KlarnaPaymentLogRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(\KlarnaPaymentLog::class);
    }

    public function prune(int $daysToKeep): void
    {
        Collection::from(
            $this->findAllInCollection()
                ->sqlWhere('DATEDIFF(NOW(),date_add) >= ' . $daysToKeep)
        )
            ->each(function (\KlarnaPaymentLog $log) {
                $log->delete();
            })
            ->realize();
    }
}
