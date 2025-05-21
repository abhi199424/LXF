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

use KlarnaPayment\Module\Infrastructure\Repository\ReadOnlyCollectionRepositoryInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

interface ImageRepositoryInterface extends ReadOnlyCollectionRepositoryInterface
{
    public function getImageFromProduct(int $productId, int $langId, int $shopId): ?int;
}
