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

class Environment
{
    /**
     * NOTE: used only for OSM placement
     */
    public const PLAYGROUND = 'playground';

    public const SANDBOX = 'sandbox';
    public const PRODUCTION = 'production';
}
