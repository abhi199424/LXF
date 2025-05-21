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

class ObjectModelEntityManager implements EntityManagerInterface
{
    private $unitOfWork;

    public function __construct(ObjectModelUnitOfWork $unitOfWork)
    {
        $this->unitOfWork = $unitOfWork;
    }

    /**
     * @param \ObjectModel $model
     * @param string $unitOfWorkType
     * @param string|null $specificKey
     *                                 for example external_id key to make it easier to keep
     *                                 track of which object model is related to which external_id
     */
    public function persist(
        \ObjectModel $model,
        string $unitOfWorkType,
        ?string $specificKey = null
    ): EntityManagerInterface {
        $this->unitOfWork->setWork($model, $unitOfWorkType, $specificKey);

        return $this;
    }

    /**
     * @return array<\ObjectModel>
     *
     * @throws \PrestaShopDatabaseException
     * @throws \PrestaShopException
     */
    public function flush(): array
    {
        $persistenceModels = $this->unitOfWork->getWork();
        $persistedModels = [];

        foreach ($persistenceModels as $externalId => $persistenceModel) {
            if ($persistenceModel['unit_of_work_type'] === ObjectModelUnitOfWork::UNIT_OF_WORK_SAVE) {
                $persistenceModel['object']->save();
            }

            if ($persistenceModel['unit_of_work_type'] === ObjectModelUnitOfWork::UNIT_OF_WORK_DELETE) {
                $persistenceModel['object']->delete();
            }

            if (!empty($externalId)) {
                $persistedModels[$externalId] = $persistenceModel['object'];
            } else {
                $persistedModels[] = $persistenceModel['object'];
            }
        }
        $this->unitOfWork->clearWork();

        return $persistedModels;
    }
}
