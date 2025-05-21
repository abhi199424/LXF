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

namespace KlarnaPayment\Module\Core\Tools\Action;

use KlarnaPayment\Module\Core\Tools\DTO\PruneOldRecordsData;
use KlarnaPayment\Module\Core\Tools\Exception\CouldNotPruneOldRecords;
use KlarnaPayment\Module\Infrastructure\Logger\Repository\KlarnaPaymentLogRepositoryInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class PruneOldRecordsAction
{
    private $prestashopLoggerRepository;
    private $klarnaPaymentLogRepository;

    public function __construct(
        KlarnaPaymentLogRepositoryInterface $klarnaPaymentLogRepository
    ) {
        $this->klarnaPaymentLogRepository = $klarnaPaymentLogRepository;
    }

    /**
     * @throws CouldNotPruneOldRecords
     */
    public function run(PruneOldRecordsData $data)
    {
        try {
            $this->klarnaPaymentLogRepository->prune($data->getDaysToKeep());
        } catch (\Throwable $exception) {
            throw CouldNotPruneOldRecords::failedToPrune($exception);
        }
    }
}
