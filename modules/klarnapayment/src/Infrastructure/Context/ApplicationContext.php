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

namespace KlarnaPayment\Module\Infrastructure\Context;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ApplicationContext
{
    /** @var bool */
    private $isProduction;

    /** @var string|null */
    private $apiKey;

    /** @var \Currency|null */
    private $defaultCurrency;

    public function __construct(
        $isProduction,
        $apiKey,
        $defaultCurrency
    ) {
        $this->isProduction = $isProduction;
        $this->apiKey = $apiKey;
        $this->defaultCurrency = $defaultCurrency;
    }

    /**
     * @return bool
     */
    public function getIsProduction(): bool
    {
        return $this->isProduction;
    }

    /**
     * @return string|null
     */
    public function getApiKey(): ?string
    {
        return $this->apiKey;
    }

    /**
     * @return \Currency|null
     */
    public function getDefaultCurrency(): ?\Currency
    {
        return $this->defaultCurrency;
    }

    public function isValid(): bool
    {
        return !empty($this->apiKey);
    }
}
