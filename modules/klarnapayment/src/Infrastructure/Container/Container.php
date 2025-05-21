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

namespace KlarnaPayment\Module\Infrastructure\Container;

use KlarnaPayment\Module\Infrastructure\Container\Providers\BaseServiceProvider;
use KlarnaPayment\Module\Infrastructure\Container\Providers\RepositoryServiceProvider;
use KlarnaPayment\Vendor\League\Container\ReflectionContainer;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Container
{
    public static $instance;

    public static function getInstance(): \KlarnaPayment\Vendor\League\Container\Container
    {
        if (is_null(self::$instance)) {
            $container = new \KlarnaPayment\Vendor\League\Container\Container();

            $container->delegate(new ReflectionContainer());

            (new BaseServiceProvider($container))->provide();
            (new RepositoryServiceProvider($container))->provide();

            self::$instance = $container;
        }

        return self::$instance;
    }
}
