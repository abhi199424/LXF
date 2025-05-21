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

use Symfony\Component\Lock\Factory as SymfonyFactoryV3;
use Symfony\Component\Lock\LockFactory as SymfonyFactoryV4;

if (!defined('_PS_VERSION_')) {
    exit;
}

class LockFactory
{
    public function create(?\Module $module): LockInterface
    {
        if (class_exists(SymfonyFactoryV4::class)) {
            // Symfony 4.4+
            return new LockV4($module);
        }

        if (class_exists(SymfonyFactoryV3::class)) {
            // Symfony 3.4+
            return new LockV3($module);
        }

        // Symfony 2.8+
        return new LockV2($module);
    }
}
