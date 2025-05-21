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

namespace KlarnaPayment\Module\Core\Account\Provider;

use KlarnaPayment\Module\Infrastructure\Utility\ChecksumUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AddressChecksumProvider
{
    public function get(array $addressData): string
    {
        $fields = [
            'city' => (string) ($addressData['city'] ?? ''),
            'country' => (string) ($addressData['country'] ?? ''),
            'family_name' => (string) ($addressData['family_name'] ?? ''),
            'given_name' => (string) ($addressData['given_name'] ?? ''),
            'phone' => (string) ($addressData['phone'] ?? ''),
            'postal_code' => (string) ($addressData['postal_code'] ?? ''),
            'street_address' => (string) ($addressData['street_address'] ?? ''),
            'street_address2' => (string) ($addressData['street_address2'] ?? ''),
            'region' => (string) ($addressData['region'] ?? ''),
        ];

        return ChecksumUtility::calculate(array_filter($fields));
    }
}
