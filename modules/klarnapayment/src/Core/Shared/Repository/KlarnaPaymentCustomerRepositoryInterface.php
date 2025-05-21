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

interface KlarnaPaymentCustomerRepositoryInterface extends ReadOnlyCollectionRepositoryInterface
{
    public function saveKlarnaPaymentCustomer(int $customerId, int $addressId, string $idToken, string $refreshToken, int $shopId): void;
}
