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

namespace KlarnaPayment\Module\Core\Tools\DTO;

if (!defined('_PS_VERSION_')) {
    exit;
}

class PruneOldRecordsData
{
    /**
     * @var int
     */
    private $daysToKeep;

    private function __construct(
        int $daysToKeep
    ) {
        $this->daysToKeep = $daysToKeep;
    }

    public function getDaysToKeep(): int
    {
        return $this->daysToKeep;
    }

    public static function create(int $daysToKeep): self
    {
        return new self(
            $daysToKeep
        );
    }
}
