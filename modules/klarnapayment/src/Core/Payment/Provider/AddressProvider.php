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

use KlarnaPayment\Module\Api\Models\Address;
use KlarnaPayment\Module\Core\Shared\Repository\AddressRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\CountryRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\CustomerRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\GenderRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\StateRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AddressProvider
{
    private $customerRepository;
    private $genderRepository;
    private $addressRepository;
    private $stateRepository;
    private $countryRepository;
    private $configuration;

    public function __construct(
        CustomerRepositoryInterface $customerRepository,
        GenderRepositoryInterface $genderRepository,
        AddressRepositoryInterface $addressRepository,
        StateRepositoryInterface $stateRepository,
        CountryRepositoryInterface $countryRepository,
        Configuration $configuration
    ) {
        $this->customerRepository = $customerRepository;
        $this->genderRepository = $genderRepository;
        $this->addressRepository = $addressRepository;
        $this->stateRepository = $stateRepository;
        $this->countryRepository = $countryRepository;
        $this->configuration = $configuration;
    }

    public function get(array $addressData): Address
    {
        $customerId = $addressData['id_customer'];
        $addressId = $addressData['id_address'];

        /** @var \Customer $customer */
        $customer = $this->customerRepository->findOneBy(['id_customer' => $customerId]);
        /** @var \Gender|null $gender */
        $gender = $this->genderRepository->findOneBy(['id_gender' => $customer->id_gender]);
        /** @var \Address $address */
        $address = $this->addressRepository->findOneBy(['id_address' => (int) $addressId]);
        /** @var \Country $country */
        $country = $this->countryRepository->findOneBy(['id_country' => (int) $address->id_country]);
        /** @var \State|null $state */
        $state = $this->stateRepository->findOneBy(['id_state' => (int) $address->id_state]);

        $apiAddress = new Address();

        $apiAddress->setGivenName((string) $address->firstname);
        $apiAddress->setFamilyName((string) $address->lastname);
        $apiAddress->setEmail((string) $customer->email);
        $apiAddress->setStreetAddress((string) $address->address1);
        $apiAddress->setStreetAddress2((string) $address->address2);
        $apiAddress->setPostalCode((string) $address->postcode);
        $apiAddress->setCity((string) $address->city);
        $apiAddress->setCountry((string) $country->iso_code);
        $apiAddress->setRegion($state ? (string) $state->iso_code : '');
        $apiAddress->setPhone((string) $address->phone);

        $languageId = $this->configuration->getAsInteger('PS_LANG_DEFAULT');

        if ($gender && isset($gender->name[$languageId])) {
            $apiAddress->setTitle((string) $gender->name[$languageId]);
        }

        return $apiAddress;
    }
}
