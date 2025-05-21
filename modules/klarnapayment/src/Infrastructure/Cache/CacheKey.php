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

namespace KlarnaPayment\Module\Infrastructure\Cache;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CacheKey
{
    public static function paymentSessionById(int $cartId, int $customerId, int $guestId): string
    {
        return sprintf('KLARNA_PAYMENT_SESSION-ID-[%s]-[%s]-[%s]', $cartId, $customerId, $guestId);
    }
}
