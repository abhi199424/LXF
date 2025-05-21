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

namespace KlarnaPayment\Module\Infrastructure\Adapter;

use Configuration as PrestashopConfiguration;
use Context as PrestashopContext;
use KlarnaPayment\Module\Core\Config\Config;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Context
{
    public function getShopId(): int
    {
        return (int) PrestashopContext::getContext()->shop->id;
    }

    public function getLanguageId(): int
    {
        return (int) PrestashopContext::getContext()->language->id;
    }

    public function getLanguageIso(): string
    {
        return (string) PrestashopContext::getContext()->language->iso_code ?: 'en';
    }

    /**
     * NOTE: Works only for PrestaShop 1.7.6.0 and above
     */
    public function formatPrice(float $price, string $currencyIso): string
    {
        $locale = PrestashopContext::getContext()->getCurrentLocale();

        if (empty($locale)) {
            return $price;
        }

        return $locale->formatPrice(
            $price,
            $currencyIso
        );
    }

    public function getLanguageCode(): string
    {
        return (string) PrestashopContext::getContext()->language->language_code ?: 'en-us';
    }

    public function getCurrencyIso(): string
    {
        if (!PrestashopContext::getContext()->currency) {
            return '';
        }

        return (string) PrestashopContext::getContext()->currency->iso_code;
    }

    public function getCountryIso(): string
    {
        if (!PrestashopContext::getContext()->country) {
            return '';
        }

        return (string) PrestashopContext::getContext()->country->iso_code;
    }

    public function getCurrency(): ?\Currency
    {
        return PrestashopContext::getContext()->currency;
    }

    public function getCurrencyId(): int
    {
        $currency = PrestashopContext::getContext()->currency;

        if (!$currency) {
            return 0;
        }

        return (int) $currency->id;
    }

    public function getCustomerId(): int
    {
        if (!PrestashopContext::getContext()->customer) {
            return 0;
        }

        return (int) PrestashopContext::getContext()->customer->id;
    }

    public function isCustomerLoggedIn(): bool
    {
        if (!PrestashopContext::getContext()->customer) {
            return false;
        }

        return (bool) PrestashopContext::getContext()->customer->isLogged();
    }

    public function getCustomerEmail(): string
    {
        if (!PrestashopContext::getContext()->customer) {
            return '';
        }

        return PrestashopContext::getContext()->customer->email;
    }

    public function getShopDomain(): string
    {
        return (string) PrestashopContext::getContext()->shop->domain;
    }

    public function getShopName(): string
    {
        return (string) PrestashopContext::getContext()->shop->name;
    }

    public function getAdminLink($controllerName, array $params = []): string
    {
        /* @noinspection PhpMethodParametersCountMismatchInspection - its valid for PS1.7 */
        return (string) PrestashopContext::getContext()->link->getAdminLink($controllerName, true, [], $params);
    }

    public function getModuleLink(
        $module,
        $controller = 'default',
        array $params = [],
        $ssl = null,
        $idLang = null,
        $idShop = null,
        $relativeProtocol = false
    ): string {
        return (string) PrestashopContext::getContext()->link->getModuleLink(
            $module,
            $controller,
            $params,
            $ssl,
            $idLang,
            $idShop,
            $relativeProtocol
        );
    }

    public function getSmarty(): \Smarty
    {
        return PrestashopContext::getContext()->smarty;
    }

    public function getComputingPrecision(): int
    {
        if (method_exists(PrestashopContext::getContext(), 'getComputingPrecision')) {
            return (int) PrestashopContext::getContext()->getComputingPrecision();
        }

        return (int) PrestashopConfiguration::get('PS_PRICE_DISPLAY_PRECISION');
    }

    public function getPageLink(
        $controller,
        $ssl = null,
        $idLang = null,
        $request = null,
        $requestUrlEncode = false,
        $idShop = null,
        $relativeProtocol = false
    ): string {
        return (string) PrestashopContext::getContext()->link->getPageLink(
            $controller,
            $ssl,
            $idLang,
            $request,
            $requestUrlEncode,
            $idShop,
            $relativeProtocol
        );
    }

    public function getController()
    {
        return PrestashopContext::getContext()->controller;
    }

    public function getCountryId(): int
    {
        return (int) PrestashopContext::getContext()->country->id;
    }

    /**
     * @throws \Throwable
     */
    public function setCurrentCart(\Cart $cart): void
    {
        PrestashopContext::getContext()->cart = $cart;
        PrestashopContext::getContext()->cart->update();

        PrestashopContext::getContext()->cookie->__set('id_cart', (int) $cart->id);
        PrestashopContext::getContext()->cookie->write();
    }

    public function setCountry(\Country $country): void
    {
        PrestashopContext::getContext()->country = $country;
    }

    public function setCurrency(\Currency $currency): void
    {
        PrestashopContext::getContext()->currency = $currency;
    }

    public function getBaseLink(int $shopId = null, bool $ssl = null): string
    {
        return (string) PrestashopContext::getContext()->link->getBaseLink($shopId, $ssl);
    }

    public function getCartProducts(): array
    {
        $cart = PrestashopContext::getContext()->cart;

        if (!$cart) {
            return [];
        }

        return $cart->getProducts();
    }

    public function getCart(): ?\Cart
    {
        return PrestashopContext::getContext()->cart ?? null;
    }

    public function getShopThemeName(): string
    {
        return PrestashopContext::getContext()->shop->theme_name;
    }

    public function getActiveOpcModules(): string
    {
        $activeModules = [];
        foreach (Config::KLARNA_PAYMENT_SUPPORTED_OPC_MODULES as $opcModule) {
            if (\Module::isInstalled($opcModule) && \Module::isEnabled($opcModule)) {
                $activeModules[] = $opcModule;
            }
        }

        return implode(', ', $activeModules);
    }

    public function updateCustomer(\Customer $customer): void
    {
        PrestashopContext::getContext()->updateCustomer($customer);
    }
}
