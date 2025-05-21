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

/**
 * Gets shop context data
 */
interface GlobalShopContextInterface
{
    public function getShopId(): int;

    public function getLanguageId(): int;

    public function getLanguageIso(): string;

    public function getCurrencyIso(): string;

    public function getCurrency(): ?\Currency;

    public function getShopDomain(): string;

    public function getShopName(): string;

    public function isShopSingleShopContext(): bool;
}
