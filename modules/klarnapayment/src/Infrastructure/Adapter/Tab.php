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

namespace KlarnaPayment\Module\Infrastructure\Adapter;

use Tab as PrestashopTab;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Tab
{
    public function initTab(int $idTab = null): PrestashopTab
    {
        return new PrestashopTab($idTab);
    }

    public function getIdFromClassName(?string $parent): ?int
    {
        $tabId = (int) PrestashopTab::getIdFromClassName($parent);

        if (!$tabId) {
            return null;
        }

        return $tabId;
    }
}
