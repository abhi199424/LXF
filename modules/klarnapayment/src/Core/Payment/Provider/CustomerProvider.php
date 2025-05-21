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

use KlarnaPayment\Module\Api\Enum\CustomerType;
use KlarnaPayment\Module\Api\Models\Customer;
use KlarnaPayment\Module\Core\Account\Action\RefreshTokenAction;
use KlarnaPayment\Module\Core\Shared\Repository\CustomerRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\KlarnaPaymentCustomerRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CustomerProvider
{
    private $customerRepository;
    private $refreshTokenAction;
    private $klarnaPaymentCustomerRepository;
    private $globalShopContext;
    /** @var LoggerInterface */
    private $logger;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        RefreshTokenAction $refreshTokenAction,
        KlarnaPaymentCustomerRepositoryInterface $klarnaPaymentCustomerRepository,
        GlobalShopContextInterface $globalShopContext,
        LoggerInterface $logger
    ) {
        $this->customerRepository = $customerRepository;
        $this->refreshTokenAction = $refreshTokenAction;
        $this->klarnaPaymentCustomerRepository = $klarnaPaymentCustomerRepository;
        $this->globalShopContext = $globalShopContext;
        $this->logger = $logger;
    }

    public function get(int $customerId): Customer
    {
        $customer = $this->customerRepository->findOneBy([
            'id_customer' => $customerId,
        ]);

        /** @var ?\KlarnaPaymentCustomer $klarnaPaymentCustomer */
        $klarnaPaymentCustomer = $this->klarnaPaymentCustomerRepository->findOneBy([
            'id_customer' => $customerId,
            'id_shop' => $this->globalShopContext->getShopId(),
        ]);

        $apiCustomer = new Customer();

        if (\Validate::isLoadedObject($klarnaPaymentCustomer)) {
            try {
                $refreshTokenResponse = $this->refreshTokenAction->run(
                    $klarnaPaymentCustomer->id_refresh_token,
                    'refresh_token'
                );

                $klarnaPaymentCustomer->id_refresh_token = $refreshTokenResponse->getRefreshToken();
                $klarnaPaymentCustomer->save();

                $apiCustomer->setKlarnaAccessToken($refreshTokenResponse->getAccessToken());
            } catch (\Throwable $exception) {
                $this->logger->error('Failed to refresh token', [
                    'customer_id' => $customerId,
                ]);
            }
        }

        $apiCustomer->setType(CustomerType::PERSON);

        if ($customer->birthday !== '0000-00-00') {
            $apiCustomer->setDateOfBirth((string) $customer->birthday);
        }

        return $apiCustomer;
    }
}
