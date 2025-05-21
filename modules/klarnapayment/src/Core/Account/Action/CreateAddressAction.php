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

use KlarnaPayment\Module\Core\Account\DTO\CreateAddressData;
use KlarnaPayment\Module\Core\Account\Exception\CouldNotCreateAddress;
use KlarnaPayment\Module\Core\Shared\Repository\CountryRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\StateRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CreateAddressAction
{
    public const FILE_NAME = 'CreateAddressAction';

    /** @var CountryRepositoryInterface */
    private $countryRepository;
    /** @var \KlarnaPayment */
    private $module;
    /** @var StateRepositoryInterface */
    private $stateRepository;
    /** @var Configuration */
    private $configuration;

    public function __construct(
        CountryRepositoryInterface $countryRepository,
        ModuleFactory $moduleFactory,
        StateRepositoryInterface $stateRepository,
        Configuration $configuration
    ) {
        $this->countryRepository = $countryRepository;
        $this->module = $moduleFactory->getModule();
        $this->stateRepository = $stateRepository;
        $this->configuration = $configuration;
    }

    /**
     * @throws \Exception
     */
    public function run(CreateAddressData $addressData): \Address
    {
        $address = new \Address();

        $address->firstname = $addressData->getGivenName() ?: $this->module->l('Name', self::FILE_NAME);
        $address->lastname = $addressData->getFamilyName() ?: $this->module->l('Last Name', self::FILE_NAME);
        $address->address1 = $addressData->getStreetAddress() ?: $this->module->l('Default Street Address', self::FILE_NAME);
        $address->address2 = $addressData->getStreetAddress2() ?: '';
        $address->postcode = $addressData->getPostalCode() ?: '';
        $address->phone = $addressData->getPhone() ?: '';
        $address->phone_mobile = $addressData->getPhone() ?: '';
        $address->city = $addressData->getCity() ?: $this->module->l('City', self::FILE_NAME);
        $address->id_country = $this->getCountryId($addressData->getCountry());
        $address->id_customer = $addressData->getCustomerId();
        $address->id_state = $this->getStateId($addressData->getState() ?: '');
        $address->alias = $this->module->l('Klarna Address', self::FILE_NAME);

        try {
            $address->save();
        } catch (\Throwable $exception) {
            throw CouldNotCreateAddress::unknownError($exception);
        }

        return $address;
    }

    /**
     * @throws CouldNotCreateAddress
     */
    private function getCountryId(string $isoCode): int
    {
        /** @var \Country|null $country */
        $country = $this->countryRepository->findOneBy([
            'iso_code' => $isoCode,
            'active' => true,
        ]);

        // NOTE: if country is empty fallback to shop default country
        if ($country === null) {
            return $this->getDefaultCountryId();
        }

        return (int) $country->id;
    }

    /**
     * @throws CouldNotCreateAddress
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
            throw CouldNotCreateAddress::inactiveAddressState($isoCode);
        }

        return (int) $state->id;
    }

    /**
     * Get the default country ISO code from the shop settings.
     *
     * @return string
     */
    private function getDefaultCountryId(): string
    {
        $defaultCountry = new \Country($this->configuration->get('PS_COUNTRY_DEFAULT'));

        if (!$defaultCountry->active) {
            throw CouldNotCreateAddress::inactiveCountry($defaultCountry->iso_code ?? 'N/A');
        }

        return $defaultCountry->id;
    }
}
