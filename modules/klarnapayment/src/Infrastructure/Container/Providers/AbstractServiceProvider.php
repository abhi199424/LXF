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

namespace KlarnaPayment\Module\Infrastructure\Container\Providers;

use KlarnaPayment\Vendor\League\Container\Container;

if (!defined('_PS_VERSION_')) {
    exit;
}

abstract class AbstractServiceProvider
{
    protected $bindings = [];

    protected $container;

    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    public function provide(): void
    {
        foreach ($this->bindings as $key => $service) {
            $this->container->add($key, function () use ($service) {
                return $this->container->get($service);
            });
        }

        $this->register();
    }

    abstract public function register(): void;
}
