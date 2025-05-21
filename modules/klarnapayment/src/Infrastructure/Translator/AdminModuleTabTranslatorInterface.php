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

namespace KlarnaPayment\Module\Infrastructure\Translator;

if (!defined('_PS_VERSION_')) {
    exit;
}

interface AdminModuleTabTranslatorInterface
{
    /**
     * @param string $key
     *
     * @return string|array
     */
    public function translate(string $key);
}
