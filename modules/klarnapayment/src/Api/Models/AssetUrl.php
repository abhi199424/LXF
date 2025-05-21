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

class AssetUrl implements \JsonSerializable
{
    /** @var ?string */
    private $descriptive;
    /** @var ?string */
    private $standard;

    public function getDescriptive(): ?string
    {
        return $this->descriptive;
    }

    public function getStandard(): ?string
    {
        return $this->standard;
    }

    /**
     * @maps descriptive
     */
    public function setDescriptive(string $descriptive): void
    {
        $this->descriptive = $descriptive;
    }

    /**
     * @maps standard
     */
    public function setStandard(string $standard): void
    {
        $this->standard = $standard;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['descriptive'] = $this->getDescriptive();
        $json['standard'] = $this->getStandard();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
