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

namespace KlarnaPayment\Module\Infrastructure\Utility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class SecurityTokenUtility
{
    public static function generateTokenFromCart(int $id, string $secretKey): string
    {
        return hash('sha256', sprintf('cart_id=%s-%s', $id, $secretKey));
    }

    public static function generateUuidV4($sourceData): string
    {
        $data = hash('sha256', $sourceData);

        return HashUtility::convertToUuidV4($data);
    }
}
