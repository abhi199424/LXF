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

interface EntityManagerInterface
{
    /**
     * @param \ObjectModel $model
     * @param string $unitOfWorkType - @see ObjectModelUnitOfWork
     * @param string|null $specificKey
     *
     * @return EntityManagerInterface
     */
    public function persist(
        \ObjectModel $model,
        string $unitOfWorkType,
        ?string $specificKey = null
    ): EntityManagerInterface;

    /**
     * @return array<\ObjectModel>
     *
     * @throws \PrestaShopException
     */
    public function flush(): array;
}
