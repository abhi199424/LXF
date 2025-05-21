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

class EuroCurrencyLocaleProvider
{
    public function getLocale(string $languageIsoCode, string $billingCountryIsoCode): ?string
    {
        $countryLocales = [
            'AT' => ['DE' => 'de-AT', 'default' => 'en-AT'],
            'BE' => ['NL' => 'nl-BE', 'FR' => 'fr-BE', 'default' => 'en-BE'],
            'DE' => ['DE' => 'de-DE', 'default' => 'en-DE'],
            'ES' => ['ES' => 'es-ES', 'default' => 'en-ES'],
            'FI' => ['FI' => 'fi-FI', 'SV' => 'sv-FI', 'default' => 'en-FI'],
            'FR' => ['FR' => 'fr-FR', 'default' => 'en-FR'],
            'GR' => ['EL' => 'el-GR', 'default' => 'en-GR'],
            'EL' => ['EL' => 'el-GR', 'default' => 'en-GR'], // Greece
            'IE' => ['default' => 'en-IE'],
            'IT' => ['IT' => 'it-IT', 'default' => 'en-IT'],
            'NL' => ['NL' => 'nl-NL', 'default' => 'en-NL'],
            'PT' => ['PT' => 'pt-PT', 'default' => 'en-PT'],
        ];

        if (!isset($countryLocales[$billingCountryIsoCode])) {
            return null;
        }

        return $countryLocales[$billingCountryIsoCode][$languageIsoCode] ?? $countryLocales[$billingCountryIsoCode]['default'];
    }
}
