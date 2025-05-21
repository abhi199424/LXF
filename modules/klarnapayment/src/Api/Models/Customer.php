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

class Customer implements \JsonSerializable
{
    /** @var ?string */
    private $dateOfBirth;
    /** @var ?string */
    private $gender;
    /** @var ?string */
    private $type;
    /** @var ?string */
    private $title;
    /** @var ?string */
    private $klarnaAccessToken;

    public function getDateOfBirth(): ?string
    {
        return $this->dateOfBirth;
    }

    public function getGender(): ?string
    {
        return $this->gender;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getKlarnaAccessToken(): ?string
    {
        return $this->klarnaAccessToken;
    }

    /**
     * @maps date_of_birth
     */
    public function setDateOfBirth(?string $dateOfBirth): void
    {
        $this->dateOfBirth = $dateOfBirth;
    }

    /**
     * @maps gender
     */
    public function setGender(?string $gender): void
    {
        $this->gender = $gender;
    }

    /**
     * @maps type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    /**
     * @maps title
     */
    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function setKlarnaAccessToken(?string $klarnaAccessToken): void
    {
        $this->klarnaAccessToken = $klarnaAccessToken;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['date_of_birth'] = $this->getDateOfBirth();
        $json['gender'] = $this->getGender();
        $json['type'] = $this->getType();
        $json['title'] = $this->getTitle();
        $json['klarna_access_token'] = $this->getKlarnaAccessToken();

        return array_filter($json, function ($val) {
            return $val !== null && $val !== '';
        });
    }
}
