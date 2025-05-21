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

use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

if (!defined('_PS_VERSION_')) {
    exit;
}

class FilesystemCache implements CacheInterface
{
    private $cache;
    private $module;

    public function __construct(ModuleFactory $module)
    {
        $this->module = $module->getModule();
        $this->cache = new FilesystemAdapter('', 0, $this->module->getLocalPath() . 'var/cache');
    }

    /** {@inheritDoc} */
    public function get($key, $default = null)
    {
        return $this->cache->getItem($key)->get();
    }

    /** {@inheritDoc} */
    public function has($key)
    {
        return $this->cache->hasItem($key);
    }

    /** {@inheritDoc} */
    public function set($key, $value, $ttl = 3600)
    {
        $item = $this->cache->getItem($key)
            ->set($value)
            ->expiresAfter($ttl);

        $this->cache->save($item);

        return $item;
    }

    /** {@inheritDoc} */
    public function remember(string $key, ?int $seconds, \Closure $callback)
    {
        if (!is_null($value = $this->get($key))) {
            return $value;
        }

        $result = $callback();

        $this->set($key, $result, $seconds);

        return $result;
    }

    /** {@inheritDoc} */
    public function delete($key)
    {
        return $this->cache->deleteItem($key);
    }

    /** {@inheritDoc} */
    public function clear()
    {
        return $this->cache->clear();
    }

    /** {@inheritDoc} */
    public function getMultiple($keys, $default = null)
    {
        $result = [];

        foreach ($keys as $key) {
            $result[$key] = $this->get($key);
        }

        return $result;
    }

    /** {@inheritDoc} */
    public function setMultiple($values, $ttl = null)
    {
        foreach ($values as $key => $value) {
            $this->set($key, $value, $ttl);
        }

        return true;
    }

    /** {@inheritDoc} */
    public function deleteMultiple($keys)
    {
        foreach ($keys as $key) {
            $this->delete($key);
        }

        return true;
    }
}
