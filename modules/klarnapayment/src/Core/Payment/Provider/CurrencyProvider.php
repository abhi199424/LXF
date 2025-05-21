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

namespace KlarnaPayment\Module\Core\Payment\Provider;

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CurrencyProvider implements CurrencyProviderInterface
{
    private const EURO_CURRENCY = 'EUR';

    /** @var Configuration */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function getEuropeCountryCurrency(): string
    {
        $defaultCountry = new \Country($this->configuration->get('PS_COUNTRY_DEFAULT'));
        if (array_key_exists($defaultCountry->iso_code, Config::NON_EURO_CURRENCY_COUNTRIES)) {
            return Config::NON_EURO_CURRENCY_COUNTRIES[$defaultCountry->iso_code];
        }

        return self::EURO_CURRENCY;
    }
}
