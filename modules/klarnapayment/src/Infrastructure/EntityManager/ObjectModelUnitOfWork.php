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

namespace KlarnaPayment\Module\Infrastructure\EntityManager;

if (!defined('_PS_VERSION_')) {
    exit;
}

/** In memory entity manager object model unit of work */
class ObjectModelUnitOfWork
{
    const UNIT_OF_WORK_SAVE = 'UNIT_OF_WORK_SAVE';
    const UNIT_OF_WORK_DELETE = 'UNIT_OF_WORK_DELETE';

    private $work = [];

    public function setWork(\ObjectModel $objectModel, string $unitOfWorkType, ?string $specificKey): void
    {
        $work = [
            'unit_of_work_type' => $unitOfWorkType,
            'object' => $objectModel,
        ];

        if (!is_null($specificKey)) {
            $this->work[$specificKey] = $work;
        } else {
            $this->work[] = $work;
        }
    }

    /**
     * @return array<string, \ObjectModel>
     */
    public function getWork(): array
    {
        return $this->work;
    }

    public function clearWork(): void
    {
        $this->work = [];
    }
}
