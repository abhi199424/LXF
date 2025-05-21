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

class InitialPaymentMethod implements \JsonSerializable
{
    /** @var ?string */
    private $description;
    /** @var ?int */
    private $numberOfInstallments;
    /** @var ?string */
    private $type;

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getNumberOfInstallments(): ?int
    {
        return $this->numberOfInstallments;
    }

    /**
     * @maps description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @maps number_of_installments
     */
    public function setNumberOfInstallments(?int $numberOfInstallments): void
    {
        $this->numberOfInstallments = $numberOfInstallments;
    }

    /**
     * @maps type
     */
    public function setType(?string $type): void
    {
        $this->type = $type;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['description'] = $this->getDescription();
        $json['number_of_installments'] = $this->getNumberOfInstallments();
        $json['type'] = $this->getType();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
