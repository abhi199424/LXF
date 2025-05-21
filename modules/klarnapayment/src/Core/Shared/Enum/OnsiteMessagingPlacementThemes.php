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

namespace KlarnaPayment\Module\Core\Shared\Enum;

if (!defined('_PS_VERSION_')) {
    exit;
}

class OnsiteMessagingPlacementThemes
{
    public const DEFAULT = 'default';
    public const DARK = 'dark';
    public const LIGHT = 'light';
    public const CUSTOM = 'custom';
}
