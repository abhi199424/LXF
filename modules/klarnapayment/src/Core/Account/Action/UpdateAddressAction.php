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

namespace KlarnaPayment\Module\Core\Account\Action;

use KlarnaPayment\Module\Core\Account\DTO\UpdateAddressData;
use KlarnaPayment\Module\Core\Account\Exception\CouldNotUpdateAddress;
use KlarnaPayment\Module\Core\Shared\Repository\CountryRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\StateRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;

if (!defined('_PS_VERSION_')) {
    exit;
}

class UpdateAddressAction
{
    public const FILE_NAME = 'UpdateAddressAction';

    /** @var CountryRepositoryInterface */
    private $countryRepository;
    /** @var \KlarnaPayment */
    private $module;
    /** @var StateRepositoryInterface */
    private $stateRepository;

    public function __construct(
        CountryRepositoryInterface $countryRepository,
        ModuleFactory $moduleFactory,
        StateRepositoryInterface $stateRepository
    ) {
        $this->countryRepository = $countryRepository;
        $this->module = $moduleFactory->getModule();
        $this->stateRepository = $stateRepository;
    }

    /**
     * @throws \Exception
     */
    public function run(UpdateAddressData $addressData): \Address
    {
        $address = new \Address($addressData->getId());

        if (!\Validate::isLoadedObject($address)) {
            throw CouldNotUpdateAddress::addressNotFound($addressData->getId());
        }

        $address->firstname = $addressData->getGivenName() ?: $this->module->l('Name', self::FILE_NAME);
        $address->lastname = $addressData->getFamilyName() ?: $address->lastname;
        $address->address1 = $addressData->getStreetAddress() ?: $address->address1;
        $address->address2 = $addressData->getStreetAddress2() ?: $address->address2;
        $address->postcode = $addressData->getPostalCode() ?: $address->postcode;
        $address->phone = $addressData->getPhone() ?: $address->phone;
        $address->phone_mobile = $addressData->getPhone() ?: $address->phone_mobile;
        $address->city = $addressData->getCity() ?: $address->city;
        $address->id_country = $this->getCountryId($addressData->getCountry()) ?? $address->id_country;
        $address->id_customer = $addressData->getCustomerId() ?: $address->id_customer;
        $address->id_state = $this->getStateId($addressData->getState() ?: $address->id_state);
        $address->alias = $address->alias ?? $this->module->l('Klarna Address', self::FILE_NAME);

        try {
            $address->save();
        } catch (\Throwable $exception) {
            throw CouldNotUpdateAddress::unknownError($exception);
        }

        return $address;
    }

    /**
     * @throws CouldNotUpdateAddress
     */
    private function getCountryId(string $isoCode): ?int
    {
        /** @var \Country|null $country */
        $country = $this->countryRepository->findOneBy([
            'iso_code' => $isoCode,
            'active' => true,
        ]);

        if ($country === null) {
            return null;
        }

        return (int) $country->id;
    }

    /**
     * @throws CouldNotUpdateAddress
     */
    private function getStateId(string $isoCode): int
    {
        /** @var \State|null $state */
        $state = $this->stateRepository->findOneBy([
            'iso_code' => strtolower(strtoupper($isoCode)),
        ]);

        if ($state === null) {
            return 0;
        }

        if (!$state->active) {
            throw CouldNotUpdateAddress::inactiveAddressState($isoCode);
        }

        return (int) $state->id;
    }
}
