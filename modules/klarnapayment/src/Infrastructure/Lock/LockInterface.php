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

if (!defined('_PS_VERSION_')) {
    exit;
}

interface LockInterface
{
    public function exists(): bool;

    public function create(string $resource, int $ttl, bool $autoRelease): void;

    public function acquire(bool $blocking): bool;

    public function release(): void;
}
