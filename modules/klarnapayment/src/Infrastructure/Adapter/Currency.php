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

use Currency as PrestashopCurrency;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Currency
{
    public function getIsoCodeById(int $id, bool $forceRefreshCache = false): string
    {
        if (method_exists(PrestashopCurrency::class, 'getIsoCodeById')) {
            return PrestashopCurrency::getIsoCodeById($id, $forceRefreshCache) ?: '';
        }

        return (new PrestashopCurrency($id))->iso_code;
    }

    public function getPaymentCurrencies($idModule, $idShop = null): array
    {
        return PrestashopCurrency::getPaymentCurrencies($idModule, $idShop) ?? [];
    }
}
