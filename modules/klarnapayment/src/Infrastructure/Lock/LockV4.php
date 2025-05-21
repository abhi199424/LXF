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

use Symfony\Component\Lock\LockFactory;
use Symfony\Component\Lock\Store\FlockStore;

if (!defined('_PS_VERSION_')) {
    exit;
}

class LockV4 implements LockInterface
{
    /** @var LockFactory Factory */
    private $lockFactory;
    /** @var ?LockInterface */
    private $lock;

    public function __construct(?\Module $module)
    {
        $this->lockFactory = new LockFactory(new FlockStore($module->getLocalPath() . 'var/cache'));
    }

    public function exists(): bool
    {
        return !empty($this->lock);
    }

    public function create(string $resource, int $ttl, bool $autoRelease): void
    {
        $this->lock = $this->lockFactory->createLock($resource, $ttl, $autoRelease);
    }

    public function acquire(bool $blocking): bool
    {
        return $this->lock->acquire($blocking);
    }

    public function release(): void
    {
        $this->lock->release();

        $this->lock = null;
    }
}
