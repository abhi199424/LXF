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

namespace KlarnaPayment\Module\Api\Models;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Address implements \JsonSerializable
{
    /** @var ?string */
    private $city;
    /** @var ?string */
    private $country;
    /** @var ?string */
    private $email;
    /** @var ?string */
    private $familyName;
    /** @var ?string */
    private $givenName;
    /** @var ?string */
    private $organizationName;
    /** @var ?string */
    private $phone;
    /** @var ?string */
    private $postalCode;
    /** @var ?string */
    private $region;
    /** @var ?string */
    private $streetAddress;
    /** @var ?string */
    private $streetAddress2;
    /** @var ?string */
    private $title;

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    public function getGivenName(): ?string
    {
        return $this->givenName;
    }

    public function getOrganizationName(): ?string
    {
        return $this->organizationName;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function getPostalCode(): ?string
    {
        return $this->postalCode;
    }

    public function getRegion(): ?string
    {
        return $this->region;
    }

    public function getStreetAddress(): ?string
    {
        return $this->streetAddress;
    }

    public function getStreetAddress2(): ?string
    {
        return $this->streetAddress2;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    /**
     * @maps city
     */
    public function setCity(?string $city): void
    {
        $this->city = $city;
    }

    /**
     * @maps country
     */
    public function setCountry(?string $country): void
    {
        $this->country = $country;
    }

    /**
     * @maps email
     */
    public function setEmail(?string $email): void
    {
        $this->email = $email;
    }

    /**
     * @maps family_name
     */
    public function setFamilyName(?string $familyName): void
    {
        $this->familyName = $familyName;
    }

    /**
     * @maps given_name
     */
    public function setGivenName(?string $givenName): void
    {
        $this->givenName = $givenName;
    }

    /**
     * @maps organization_name
     */
    public function setOrganizationName(?string $organizationName): void
    {
        $this->organizationName = $organizationName;
    }

    /**
     * @maps phone
     */
    public function setPhone(?string $phone): void
    {
        $this->phone = $phone;
    }

    /**
     * @maps postal_code
     */
    public function setPostalCode(?string $postalCode): void
    {
        $this->postalCode = $postalCode;
    }

    /**
     * @maps region
     */
    public function setRegion(?string $region): void
    {
        $this->region = $region;
    }

    /**
     * @maps street_address
     */
    public function setStreetAddress(?string $streetAddress): void
    {
        $this->streetAddress = $streetAddress;
    }

    /**
     * @maps street_address2
     */
    public function setStreetAddress2(?string $streetAddress2): void
    {
        $this->streetAddress2 = $streetAddress2;
    }

    /**
     * @maps title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['city'] = $this->getCity();
        $json['country'] = $this->getCountry();
        $json['email'] = $this->getEmail();
        $json['family_name'] = $this->getFamilyName();
        $json['given_name'] = $this->getGivenName();
        $json['organization_name'] = $this->getOrganizationName();
        $json['phone'] = $this->getPhone();
        $json['postal_code'] = $this->getPostalCode();
        $json['region'] = $this->getRegion();
        $json['street_address'] = $this->getStreetAddress();
        $json['street_address2'] = $this->getStreetAddress2();
        $json['title'] = $this->getTitle();

        return array_filter($json, function ($val) {
            return $val !== null && $val !== '';
        });
    }
}
