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

namespace KlarnaPayment\Module\Core\Account\Action;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ValidateKecAddressAction
{
    /**
     * Checks is address has empty fields, required check for OPC and KEC functionality
     * Optional fields are not being checked it includes states and phone number as on normal flow these values could be removed
     *
     * @param \Address $address
     *
     * @return bool
     */
    public function run(\Address $address): bool
    {
        $fieldsToCheck = [
            'city' => $address->city,
            'country' => $address->id_country,
            'family_name' => $address->lastname,
            'given_name' => $address->firstname,
            'postal_code' => $address->postcode,
            'street_address' => $address->address1,
        ];

        // Use array_filter to remove non-empty values
        $emptyFields = array_filter($fieldsToCheck, function ($value) {
            return empty(trim($value));
        });

        // If there are any empty fields, return true
        return empty($emptyFields);
    }
}
