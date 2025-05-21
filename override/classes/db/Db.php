<?php
/**
 * Copyright ETS Software Technology Co., Ltd
 *
 * NOTICE OF LICENSE
 *
 * This file is not open source! Each license that you purchased is only available for 1 website only.
 * If you want to use this file on more websites (or projects), you need to purchase additional licenses.
 * You are not allowed to redistribute, resell, lease, license, sub-license or offer our resources to any third party.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future.
 *
 * @author ETS Software Technology Co., Ltd
 * @copyright  ETS Software Technology Co., Ltd
 * @license    Valid for 1 website (or project) for each purchase of license
 */
if (!defined('_PS_VERSION_')) { exit; }
abstract class Db extends DbCore
{
    /*
    * module: ets_superspeed
    * date: 2025-05-21 05:16:41
    * version: 2.0.4
    */
    public function query($sql)
    {
        $context = Context::getContext();
        if(isset($context->ss_total_sql))
            $context->ss_total_sql++;
        else
            $context->ss_total_sql=1;
        return parent::query($sql);
    }
}