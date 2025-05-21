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

namespace KlarnaPayment\Module\Infrastructure\Verification;

use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CanConfigureShop
{
    private $globalShopContext;

    public function __construct(GlobalShopContextInterface $globalShopContext)
    {
        $this->globalShopContext = $globalShopContext;
    }

    public function verify(): bool
    {
        if (!$this->isShopSingleContextShop()) {
            return false;
        }

        return true;
    }

    /**
     * @return mixed
     */
    private function isShopSingleContextShop()
    {
        return $this->globalShopContext->isShopSingleShopContext();
    }
}
