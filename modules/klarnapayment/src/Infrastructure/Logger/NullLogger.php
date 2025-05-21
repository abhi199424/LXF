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

namespace KlarnaPayment\Module\Infrastructure\Logger;

if (!defined('_PS_VERSION_')) {
    exit;
}

// NOTE only should be used for tests
final class NullLogger implements LoggerInterface
{
    public function emergency($message, array $context = [])
    {
        return null;
    }

    public function alert($message, array $context = [])
    {
        return null;
    }

    public function critical($message, array $context = [])
    {
        return null;
    }

    public function error($message, array $context = [])
    {
        return null;
    }

    public function warning($message, array $context = [])
    {
        return null;
    }

    public function notice($message, array $context = [])
    {
        return null;
    }

    public function info($message, array $context = [])
    {
        return null;
    }

    public function debug($message, array $context = [])
    {
        return null;
    }

    public function log($level, $message, array $context = [])
    {
        return null;
    }
}
