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

namespace KlarnaPayment\Module\Core\Payment\Provider;

use KlarnaPayment\Module\Api\Models\CustomerAccountInfo;
use KlarnaPayment\Module\Core\Shared\Repository\CustomerRepositoryInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CustomerAccountInfoProvider
{
    private $customerRepository;

    public function __construct(
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->customerRepository = $customerRepository;
    }

    public function get(int $customerId): CustomerAccountInfo
    {
        /** @var \Customer $customer */
        $customer = $this->customerRepository->findOneBy([
            'id_customer' => $customerId,
        ]);

        $apiCustomerAccountInfo = new CustomerAccountInfo();

        $apiCustomerAccountInfo->setUniqueAccountIdentifier($customer->email);
        $apiCustomerAccountInfo->setAccountRegistrationDate(date("Y-m-d\TH:i:s\Z", strtotime($customer->date_add)));
        $apiCustomerAccountInfo->setAccountLastModified(date("Y-m-d\TH:i:s\Z", strtotime($customer->date_upd)));

        return $apiCustomerAccountInfo;
    }
}
