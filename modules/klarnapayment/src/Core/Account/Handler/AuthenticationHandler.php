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

namespace KlarnaPayment\Module\Core\Account\Handler;

use KlarnaPayment\Module\Core\Account\Action\CreateAddressAction;
use KlarnaPayment\Module\Core\Account\Action\CreateCustomerAction;
use KlarnaPayment\Module\Core\Account\Action\UpdateAddressAction;
use KlarnaPayment\Module\Core\Account\Action\UpdateCustomerAction;
use KlarnaPayment\Module\Core\Account\DTO\CreateAddressData;
use KlarnaPayment\Module\Core\Account\DTO\CreateCustomerData;
use KlarnaPayment\Module\Core\Account\DTO\UpdateAddressData;
use KlarnaPayment\Module\Core\Account\DTO\UpdateCustomerData;
use KlarnaPayment\Module\Core\Shared\Repository\AddressRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\KlarnaPaymentCustomerRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AuthenticationHandler
{
    /** @var CreateCustomerAction */
    private $createCustomerAction;
    /** @var KlarnaPaymentCustomerRepositoryInterface */
    private $klarnaPaymentCustomerRepository;
    /** @var UpdateCustomerAction */
    private $updateCustomerAction;
    /** @var CreateAddressAction */
    private $createAddressAction;
    /** @var UpdateAddressAction */
    private $updateAddressAction;
    /** @var GlobalShopContextInterface */
    private $globalShopContext;
    /** @var AddressRepositoryInterface */
    private $addressRepository;
    /** @var Context */
    private $context;

    public function __construct(
        CreateCustomerAction $createCustomerAction,
        KlarnaPaymentCustomerRepositoryInterface $klarnaPaymentCustomerRepository,
        UpdateCustomerAction $updateCustomerAction,
        CreateAddressAction $createAddressAction,
        UpdateAddressAction $updateAddressAction,
        GlobalShopContextInterface $globalShopContext,
        AddressRepositoryInterface $addressRepository,
        Context $context
    ) {
        $this->createCustomerAction = $createCustomerAction;
        $this->klarnaPaymentCustomerRepository = $klarnaPaymentCustomerRepository;
        $this->updateCustomerAction = $updateCustomerAction;
        $this->createAddressAction = $createAddressAction;
        $this->updateAddressAction = $updateAddressAction;
        $this->globalShopContext = $globalShopContext;
        $this->addressRepository = $addressRepository;
        $this->context = $context;
    }

    public function authenticate(string $idToken, string $refreshToken, array $decodedData): void
    {
        $customerData = [
            'email' => $decodedData['email'] ?? '',
            'firstName' => $decodedData['given_name'] ?? '',
            'lastName' => $decodedData['family_name'] ?? '',
            'phone' => $decodedData['phone'] ?? '',
            'birthday' => $decodedData['date_of_birth'] ?? '',
        ];

        $addressData = [
            'givenName' => $decodedData['given_name'] ?? '',
            'familyName' => $decodedData['family_name'] ?? '',
            'streetAddress' => $decodedData['billing_address']['street_address'] ?? '',
            'streetAddress2' => $decodedData['billing_address']['street_address_2'] ?? '',
            'postalCode' => $decodedData['billing_address']['postal_code'] ?? '',
            'phone' => $decodedData['phone'] ?? '',
            'city' => $decodedData['billing_address']['city'] ?? '',
            'country' => $decodedData['billing_address']['country'] ?? '',
            'state' => $decodedData['billing_address']['region'] ?? '',
        ];

        if (!empty($customerData['email'])) {
            $customerId = \Customer::customerExists($customerData['email'], true);
        } else {
            $customerId = 0;
        }

        if ($customerId) {
            /** @var ?\KlarnaPaymentCustomer $klarnaPaymentCustomer */
            $klarnaPaymentCustomer = $this->klarnaPaymentCustomerRepository->findOneBy([
                'id_customer' => $customerId,
            ]);

            if (\Validate::isLoadedObject($klarnaPaymentCustomer)) {
                $this->handleExistingKlarnaCustomer($klarnaPaymentCustomer, $customerData, $addressData, $idToken, $refreshToken);

                return;
            }

            $this->handleExistingCustomer($customerData, $addressData, $idToken, $refreshToken);

            return;
        }

        $this->handleNewCustomer($customerData, $addressData, $idToken, $refreshToken);
    }

    private function handleNewCustomer(array $userData, array $addressData, string $idToken, string $refreshToken): void
    {
        $customer = $this->upsertCustomer($userData, null);

        $addressData['customerId'] = (int) $customer->id;

        $address = null;

        if (!empty($addressData['country'])) {
            $address = $this->upsertAddress($addressData, null);
        }

        $this->klarnaPaymentCustomerRepository->saveKlarnaPaymentCustomer(
            (int) $customer->id,
            (int) $address->id,
            $idToken,
            $refreshToken,
            $this->globalShopContext->getShopId()
        );

        $this->context->updateCustomer($customer);
    }

    private function handleExistingKlarnaCustomer(\KlarnaPaymentCustomer $klarnaPaymentCustomer, array $userData, array $addressData, string $idToken, string $refreshToken): void
    {
        $customer = $this->upsertCustomer($userData, (int) (int) $klarnaPaymentCustomer->id_customer);

        $address = $this->addressRepository->findOneBy([
            'id_address' => $klarnaPaymentCustomer->id_address,
            'id_customer' => $klarnaPaymentCustomer->id_customer,
        ]);

        $addressData['customerId'] = (int) $customer->id;

        if (!empty($addressData['country'])) {
            $address = $this->upsertAddress(
                $addressData,
                \Validate::isLoadedObject($address) ? (int) $address->id : null
            );
        }

        $klarnaPaymentCustomer->id_token = $idToken;
        $klarnaPaymentCustomer->id_address = $address->id;
        $klarnaPaymentCustomer->id_customer = $customer->id;
        $klarnaPaymentCustomer->id_refresh_token = $refreshToken;
        $klarnaPaymentCustomer->save();

        $this->context->updateCustomer($customer);
    }

    /**
     * By default we are merging it.
     *
     * @throws \Exception
     */
    private function handleExistingCustomer(array $userData, array $addressData, string $idToken, string $refreshToken): void
    {
        $customer = new \Customer();
        $customer = $customer->getByEmail($userData['email']);

        $this->upsertCustomer($userData, (int) $customer->id);

        $addressData['customerId'] = (int) $customer->id;

        $address = $this->upsertAddress($addressData, null);

        $this->klarnaPaymentCustomerRepository->saveKlarnaPaymentCustomer(
            (int) $customer->id,
            (int) $address->id,
            $idToken,
            $refreshToken,
            $this->globalShopContext->getShopId()
        );

        $this->context->updateCustomer($customer);
    }

    private function upsertCustomer(array $userData, ?int $customerId): \Customer
    {
        if ($customerId) {
            return $this->updateCustomerAction->run(UpdateCustomerData::create(
                $customerId,
                $userData['email'],
                $userData['firstName'],
                $userData['lastName'],
                $userData['birthday']
            ));
        }

        return $this->createCustomerAction->run(CreateCustomerData::create(
            $userData['email'],
            $userData['firstName'],
            $userData['lastName'],
            $userData['birthday']
        ));
    }

    private function upsertAddress(array $addressData, ?int $addressId): \Address
    {
        if ($addressId) {
            return $this->updateAddressAction->run(UpdateAddressData::create(
                $addressId,
                $addressData['givenName'] ?? '',
                $addressData['familyName'] ?? '',
                $addressData['streetAddress'] ?? '',
                $addressData['streetAddress2'] ?? '',
                $addressData['postalCode'] ?? '',
                $addressData['phone'] ?? '',
                $addressData['city'] ?? '',
                $addressData['country'] ?? '',
                $addressData['state'] ?? '',
                $addressData['customerId'] ?? 0
            ));
        }

        return $this->createAddressAction->run(CreateAddressData::create(
            $addressData['givenName'] ?? '',
            $addressData['familyName'] ?? '',
            $addressData['streetAddress'] ?? '',
            $addressData['streetAddress2'] ?? '',
            $addressData['postalCode'] ?? '',
            $addressData['phone'] ?? '',
            $addressData['city'] ?? '',
            $addressData['country'] ?? '',
            $addressData['state'] ?? '',
            $addressData['customerId'] ?? 0
        ));
    }
}
