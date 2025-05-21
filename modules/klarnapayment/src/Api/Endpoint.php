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

namespace KlarnaPayment\Module\Api;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Endpoint
{
    public const EUROPE = 'EU';
    public const NORTH_AMERICA_US = 'NA-US';
    public const NORTH_AMERICA_CA = 'NA-CA';
    public const NORTH_AMERICA_MX = 'NA-MX';
    public const ASIA_PACIFIC = 'AP-AP';
    public const ASIA_PACIFIC_NZ = 'AP-NZ';
}
