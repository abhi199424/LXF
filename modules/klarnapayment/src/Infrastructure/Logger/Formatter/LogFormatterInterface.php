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

namespace KlarnaPayment\Module\Infrastructure\Logger\Formatter;

if (!defined('_PS_VERSION_')) {
    exit;
}

interface LogFormatterInterface
{
    /**
     * @param string $message - an actual error message
     *
     * @return string
     */
    public function getMessage(string $message): string;
}
