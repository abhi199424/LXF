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

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Exception\CouldNotHandleLocking;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Lock
{
    /** @var LockInterface */
    private $lock;

    public function __construct(LockFactory $lockFactory, ModuleFactory $module)
    {
        $this->lock = $lockFactory->create($module->getModule());
    }

    /**
     * @throws CouldNotHandleLocking
     */
    public function create(string $resource, int $ttl = Config::LOCK_TIME_TO_LIVE, bool $autoRelease = true): void
    {
        if ($this->lock->exists()) {
            throw CouldNotHandleLocking::lockExists();
        }

        $this->lock->create($resource, $ttl, $autoRelease);
    }

    /**
     * @throws CouldNotHandleLocking
     */
    public function acquire(bool $blocking = false): bool
    {
        if (!$this->lock->exists()) {
            throw CouldNotHandleLocking::lockOnAcquireIsMissing();
        }

        return $this->lock->acquire($blocking);
    }

    /**
     * @throws CouldNotHandleLocking
     */
    public function release(): void
    {
        if (!$this->lock->exists()) {
            throw CouldNotHandleLocking::lockOnReleaseIsMissing();
        }

        $this->lock->release();
    }

    public function __destruct()
    {
        try {
            $this->release();
        } catch (CouldNotHandleLocking $exception) {
            return;
        }
    }
}
