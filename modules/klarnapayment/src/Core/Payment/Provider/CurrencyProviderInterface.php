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

if (!defined('_PS_VERSION_')) {
    exit;
}

interface CurrencyProviderInterface
{
    /**
     * @return string EU country currency or Euro
     */
    public function getEuropeCountryCurrency(): string;
}
