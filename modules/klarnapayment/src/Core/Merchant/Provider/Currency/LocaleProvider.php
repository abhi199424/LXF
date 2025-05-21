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

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;

if (!defined('_PS_VERSION_')) {
    exit;
}

class LocaleProvider
{
    /** @var Context */
    private $context;
    /** @var Configuration */
    private $configuration;
    /** @var GlobalCurrencyLocaleProvider */
    private $globalCurrencyLocaleProvider;
    /** @var EuroCurrencyLocaleProvider */
    private $euroCurrencyLocaleProvider;

    public function __construct(
        Context $context,
        Configuration $configuration,
        GlobalCurrencyLocaleProvider $globalCurrencyLocaleProvider,
        EuroCurrencyLocaleProvider $euroCurrencyLocaleProvider
    ) {
        $this->context = $context;
        $this->configuration = $configuration;
        $this->globalCurrencyLocaleProvider = $globalCurrencyLocaleProvider;
        $this->euroCurrencyLocaleProvider = $euroCurrencyLocaleProvider;
    }

    public function getLocale(): string
    {
        $languageIsoCode = strtoupper(strtolower($this->context->getLanguageIso()));
        $currencyIsoCode = strtoupper(strtolower($this->context->getCurrencyIso()));

        $defaultLocale = sprintf(
            'en-%s',
            $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_DEFAULT_LOCALE)
        );

        if ($currencyIsoCode === 'EUR') {
            $isoCode = $this->context->isCustomerLoggedIn() ? $this->context->getCountryIso() : $this->context->getLanguageIso();
            $locale = $this->euroCurrencyLocaleProvider->getLocale(
                $languageIsoCode,
                strtoupper(strtolower($isoCode))
            );

            return $locale ?? $defaultLocale;
        }

        $locale = $this->globalCurrencyLocaleProvider->getLocale($languageIsoCode, $currencyIsoCode);

        return $locale ?? $defaultLocale;
    }
}
