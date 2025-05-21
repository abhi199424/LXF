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

use Language as PrestashopLanguage;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Language
{
    public function getAllLanguages(): array
    {
        return PrestashopLanguage::getLanguages(false);
    }
}
