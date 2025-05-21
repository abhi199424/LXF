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

namespace KlarnaPayment\Module\Infrastructure\Lock;

use Symfony\Component\Filesystem\LockHandler;

if (!defined('_PS_VERSION_')) {
    exit;
}

class LockV2 implements LockInterface
{
    /** @var ?LockHandler */
    private $lock;
    /** @var \Module|null */
    private $module;

    public function __construct(?\Module $module)
    {
        $this->module = $module;
    }

    public function exists(): bool
    {
        return !empty($this->lock);
    }

    public function create(string $resource, int $ttl, bool $autoRelease): void
    {
        $this->lock = new LockHandler($resource, $this->module->getLocalPath() . 'var/cache');
    }

    public function acquire(bool $blocking): bool
    {
        return $this->lock->lock($blocking);
    }

    public function release(): void
    {
        $this->lock->release();

        $this->lock = null;
    }
}
