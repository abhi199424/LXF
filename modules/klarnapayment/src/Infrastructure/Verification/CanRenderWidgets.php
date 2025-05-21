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

namespace KlarnaPayment\Module\Infrastructure\Verification;

use KlarnaPayment\Module\Core\Shared\Repository\CountryRepository;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\Currency;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CanRenderWidgets
{
    /** @var Context */
    public $context;

    /** @var ModuleFactory */
    public $module;

    /** @var Currency */
    private $currency;

    /** @var CountryRepository */
    private $countryRepository;

    public function __construct(
        ModuleFactory $module,
        Context $context,
        Currency $currency,
        CountryRepository $countryRepository
    ) {
        $this->module = $module;
        $this->context = $context;
        $this->currency = $currency;
        $this->countryRepository = $countryRepository;
    }

    public function verify(): bool
    {
        $availableCountries = $this->countryRepository->getPaymentCountries(
            $this->module->getModule()->id,
            $this->context->getShopId()
        );

        if (!$availableCountries) {
            return false;
        }

        $availableCountries = array_column($availableCountries, 'iso_code');

        $availableCurrencies = array_column($this->currency->getPaymentCurrencies($this->module->getModule()->id), 'iso_code');

        if (in_array($this->context->getCountryIso(), $availableCountries)
            && in_array($this->context->getCurrencyIso(), $availableCurrencies)) {
            return true;
        }

        if (in_array(strtolower($this->context->getLanguageIso()), $availableCountries)) {
            return true;
        }

        return false;
    }
}
