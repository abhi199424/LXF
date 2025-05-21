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

class CustomerAccountInfo implements \JsonSerializable
{
    /** @var ?string */
    private $uniqueAccountIdentifier;

    /** @var ?string */
    private $accountRegistrationDate;

    /** @var ?string */
    private $accountLastModified;

    public function getUniqueAccountIdentifier(): ?string
    {
        return $this->uniqueAccountIdentifier;
    }

    public function getAccountRegistrationDate(): ?string
    {
        return $this->accountRegistrationDate;
    }

    public function getAccountLastModified(): ?string
    {
        return $this->accountLastModified;
    }

    /**
     * @maps unique_account_identifier
     */
    public function setUniqueAccountIdentifier(?string $uniqueAccountIdentifier): void
    {
        $this->uniqueAccountIdentifier = $uniqueAccountIdentifier;
    }

    /**
     * @maps account_registration_date
     */
    public function setAccountRegistrationDate(?string $accountRegistrationDate): void
    {
        $this->accountRegistrationDate = $accountRegistrationDate;
    }

    /**
     * @maps account_last_modified
     */
    public function setAccountLastModified(?string $accountLastModified): void
    {
        $this->accountLastModified = $accountLastModified;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['unique_account_identifier'] = $this->getUniqueAccountIdentifier();
        $json['account_registration_date'] = $this->getAccountRegistrationDate();
        $json['account_last_modified'] = $this->getAccountLastModified();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
