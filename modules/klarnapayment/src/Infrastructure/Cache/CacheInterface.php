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

namespace KlarnaPayment\Module\Infrastructure\Cache;

if (!defined('_PS_VERSION_')) {
    exit;
}

interface CacheInterface extends \Psr\SimpleCache\CacheInterface
{
    /**
     * Get an item from the session, or store the default value.
     *
     * @return mixed
     */
    public function remember(string $key, ?int $seconds, \Closure $callback);
}
