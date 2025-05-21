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

namespace KlarnaPayment\Module\Core\Merchant\Provider\Currency;

if (!defined('_PS_VERSION_')) {
    exit;
}

class GlobalCurrencyLocaleProvider
{
    public function getLocale(string $languageIsoCode, string $currencyIsoCode): ?string
    {
        $currencyLocales = [
            'AUD' => ['default' => 'en-AU'],
            'CAD' => ['FR' => 'fr-CA', 'default' => 'en-CA'],
            'CHF' => [
                'DE' => 'de-CH',
                'FR' => 'fr-CH',
                'IT' => 'it-CH',
                'default' => 'en-CH',
            ],
            'DKK' => ['DA' => 'da-DK', 'default' => 'en-DK'],
            'GBP' => ['default' => 'en-GB'],
            'NOK' => ['NO' => 'nb-NO', 'NN' => 'nb-NO', 'default' => 'en-NO'],
            'SEK' => ['SV' => 'sv-SE', 'default' => 'en-SE'],
            'PLN' => ['PL' => 'pl-PL', 'default' => 'en-PL'],
            'USD' => ['ES' => 'es-US', 'default' => 'en-US'],
            'NZD' => ['default' => 'en-NZ'],
            'RON' => ['RO' => 'ro-RO', 'default' => 'en-RO'],
            'CZK' => ['CS' => 'cs-CZ', 'default' => 'en-CZ'],
            'MXN' => ['ES' => 'es-MX', 'default' => 'en-MX'],
        ];

        if (!isset($currencyLocales[$currencyIsoCode])) {
            return null;
        }

        return $currencyLocales[$currencyIsoCode][$languageIsoCode] ?? $currencyLocales[$currencyIsoCode]['default'];
    }
}
