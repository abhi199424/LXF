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

class ExpressCheckoutAddressData
{
    /** @var int */
    private $countryId;
    /** @var int */
    private $stateId;

    private function __construct(
        int $countryId,
        int $stateId
    ) {
        $this->countryId = $countryId;
        $this->stateId = $stateId;
    }

    /**
     * @return int
     */
    public function getCountryId(): int
    {
        return $this->countryId;
    }

    /**
     * @return int
     */
    public function getStateId(): int
    {
        return $this->stateId;
    }

    public static function create(
        int $countryId,
        int $stateId
    ): self {
        return new self(
            $countryId,
            $stateId
        );
    }
}
