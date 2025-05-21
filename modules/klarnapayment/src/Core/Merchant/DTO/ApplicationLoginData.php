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

class ApplicationLoginData
{
    /** @var string */
    private $apiKey;
    /** @var string */
    private $region;

    private function __construct(
        string $apiKey,
        string $region
    ) {
        $this->apiKey = $apiKey;
        $this->region = $region;
    }

    public function getApiKey(): string
    {
        return $this->apiKey;
    }

    public function getRegion(): string
    {
        return $this->region;
    }

    public static function create(
        string $apiKey,
        string $region
    ): self {
        return new self(
            $apiKey,
            $region
        );
    }
}
