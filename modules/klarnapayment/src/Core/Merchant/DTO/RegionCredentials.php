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

namespace KlarnaPayment\Module\Core\Merchant\DTO;

if (!defined('_PS_VERSION_')) {
    exit;
}

class RegionCredentials
{
    private $productionUsername;
    private $productionPassword;
    private $sandboxUsername;
    private $sandboxPassword;
    private $productionClientId;
    private $sandboxClientId;

    public function __construct(
        string $productionUsername,
        string $productionPassword,
        string $productionClientId,
        string $sandboxUsername,
        string $sandboxPassword,
        string $sandboxClientId
    ) {
        $this->productionUsername = $productionUsername;
        $this->productionPassword = $productionPassword;
        $this->sandboxUsername = $sandboxUsername;
        $this->sandboxPassword = $sandboxPassword;
        $this->productionClientId = $productionClientId;
        $this->sandboxClientId = $sandboxClientId;
    }

    public function getProductionUsername(): string
    {
        return $this->productionUsername;
    }

    public function getProductionPassword(): string
    {
        return $this->productionPassword;
    }

    public function getSandboxUsername(): string
    {
        return $this->sandboxUsername;
    }

    public function getSandboxPassword(): string
    {
        return $this->sandboxPassword;
    }

    public function getProductionClientId(): string
    {
        return $this->productionClientId;
    }

    public function getSandboxClientId(): string
    {
        return $this->sandboxClientId;
    }
}
