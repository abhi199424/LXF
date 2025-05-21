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

class LogFormatter implements LogFormatterInterface
{
    const KLARNA_PAYMENT_LOG_PREFIX = 'KLARNA_PAYMENT_MODULE_LOG:';

    public function getMessage(string $message): string
    {
        return self::KLARNA_PAYMENT_LOG_PREFIX . ' ' . $message;
    }
}
