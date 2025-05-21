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

class KlarnaPaymentCustomerRepository extends CollectionRepository implements KlarnaPaymentCustomerRepositoryInterface
{
    public function __construct()
    {
        parent::__construct(\KlarnaPaymentCustomer::class);
    }

    public function saveKlarnaPaymentCustomer(int $customerId, int $addressId, string $idToken, string $refreshToken, int $shopId): void
    {
        $klarnaPaymentCustomer = new \KlarnaPaymentCustomer();
        $klarnaPaymentCustomer->id_customer = $customerId;
        $klarnaPaymentCustomer->id_address = $addressId;
        $klarnaPaymentCustomer->id_shop = $shopId;
        $klarnaPaymentCustomer->id_token = $idToken;
        $klarnaPaymentCustomer->id_refresh_token = $refreshToken;
        $klarnaPaymentCustomer->save();
    }
}
