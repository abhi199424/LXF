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
class HelperList extends HelperListCore
{
    /*
    * module: ets_delete_order
    * date: 2025-08-06 09:39:17
    * version: 1.0.6
    */
    public function displayRestoreLink($token, $id)
    {
        $ets_delete_order = Module::getInstanceByName('ets_delete_order');
        return $ets_delete_order->displayRestoreLink($this->currentIndex,$this->identifier,$id,$this->table,$token != null ? $token : $this->token);
    }
}