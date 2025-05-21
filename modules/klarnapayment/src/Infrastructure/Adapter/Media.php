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

namespace KlarnaPayment\Module\Infrastructure\Adapter;

use Media as PrestashopMedia;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Media
{
    public function getMediaPath($mediaUrl): string
    {
        return PrestashopMedia::getMediaPath($mediaUrl) ?: '';
    }
}
