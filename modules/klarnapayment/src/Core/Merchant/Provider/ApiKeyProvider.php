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

namespace KlarnaPayment\Module\Core\Merchant\Provider;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ApiKeyProvider
{
    public function get(string $username, string $password): string
    {
        return base64_encode($username . ':' . $password);
    }
}
