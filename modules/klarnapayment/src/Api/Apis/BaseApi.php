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

namespace KlarnaPayment\Module\Api\Apis;

use apimatic\jsonmapper\JsonMapper;

if (!defined('_PS_VERSION_')) {
    exit;
}

class BaseApi
{
    /**
     * Get a new JsonMapper instance for mapping objects
     *
     * @return JsonMapper instance
     */
    protected function getJsonMapper(): JsonMapper
    {
        return new JsonMapper();
    }
}
