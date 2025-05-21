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

namespace KlarnaPayment\Module\Core\Account\DTO;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CreateAddressData
{
    /** @var string */
    private $givenName;
    /** @var string */
    private $familyName;
    /** @var string */
    private $streetAddress;
    /** @var string */
    private $streetAddress2;
    /** @var string */
    private $postalCode;
    /** @var string */
    private $phone;
    /** @var string */
    private $city;
    /** @var string */
    private $country;
    /** @var string */
    private $state;
    /** @var int */
    private $customerId;

    private function __construct(
        string $givenName,
        string $familyName,
        string $streetAddress,
        string $streetAddress2,
        string $postalCode,
        string $phone,
        string $city,
        string $country,
        string $state,
        int $customerId
    ) {
        $this->givenName = $givenName;
        $this->familyName = $familyName;
        $this->streetAddress = $streetAddress;
        $this->streetAddress2 = $streetAddress2;
        $this->postalCode = $postalCode;
        $this->phone = $phone;
        $this->city = $city;
        $this->country = $country;
        $this->state = $state;
        $this->customerId = $customerId;
    }

    /**
     * @return string
     */
    public function getGivenName(): string
    {
        return $this->givenName;
    }

    /**
     * @return string
     */
    public function getFamilyName(): string
    {
        return $this->familyName;
    }

    /**
     * @return string
     */
    public function getStreetAddress(): string
    {
        return $this->streetAddress;
    }

    /**
     * @return string
     */
    public function getStreetAddress2(): string
    {
        return $this->streetAddress2;
    }

    /**
     * @return string
     */
    public function getPostalCode(): string
    {
        return $this->postalCode;
    }

    /**
     * @return string
     */
    public function getPhone(): string
    {
        return $this->phone;
    }

    /**
     * @return string
     */
    public function getCity(): string
    {
        return $this->city;
    }

    /**
     * @return string
     */
    public function getCountry(): string
    {
        return $this->country;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    public function getCustomerId(): int
    {
        return $this->customerId;
    }

    public static function create(
        string $givenName,
        string $familyName,
        string $streetAddress,
        string $streetAddress2,
        string $postalCode,
        string $phone,
        string $city,
        string $country,
        string $state,
        int $customerId
    ): CreateAddressData {
        return new self(
            $givenName,
            $familyName,
            $streetAddress,
            $streetAddress2,
            $postalCode,
            $phone,
            $city,
            $country,
            $state,
            $customerId
        );
    }
}
