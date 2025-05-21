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

namespace KlarnaPayment\Module\Api\Handler;

use KlarnaPayment\Module\Api\Exception\RetryException;

if (!defined('_PS_VERSION_')) {
    exit;
}

interface RetryHandlerInterface
{
    /**
     * @throws RetryException
     */
    public function retry(callable $function);
}
