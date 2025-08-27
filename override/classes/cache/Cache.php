<?php
/**
 * Redis Cache
 * Version: 3.0.0
 * Copyright (c) 2020-2023. Mateusz Szymański Teamwant
 * https://teamwant.pl
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
 *
 * @author    Teamwant <kontakt@teamwant.pl>
 * @copyright Copyright 2020-2023 © Teamwant Mateusz Szymański All right reserved
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *
 * @category  Teamwant
 */
if (!defined('_PS_VERSION_')) {
    exit;
}
abstract class Cache extends CacheCore
{
    /**
     * @return Cache
     */
    /*
    * module: teamwant_redis
    * date: 2025-08-13 11:25:49
    * version: 3.6.8
    */
    public static function getInstance()
    {
        if (!self::$instance) {
            $caching_system = _PS_CACHING_SYSTEM_;
            if ($caching_system === 'Redis') {
                if (!class_exists(Teamwant\Prestashop17\Redis\Classes\Cache\Redis::class)) {
                    require_once _PS_MODULE_DIR_ . 'teamwant_redis/vendor/autoload.php';
                }
                $caching_system = Teamwant\Prestashop17\Redis\Classes\Cache\Redis::class;
            }
            self::$instance = new $caching_system();
        }
        return self::$instance;
    }
}
