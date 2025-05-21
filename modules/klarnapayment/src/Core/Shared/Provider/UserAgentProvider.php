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

namespace KlarnaPayment\Module\Core\Shared\Provider;

use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;
use KlarnaPayment\Module\Infrastructure\Utility\VersionUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class UserAgentProvider implements UserAgentProviderInterface
{
    private $globalShopContext;
    private $moduleFactory;

    public function __construct(
        GlobalShopContextInterface $globalShopContext,
        ModuleFactory $moduleFactory
    ) {
        $this->globalShopContext = $globalShopContext;
        $this->moduleFactory = $moduleFactory;
    }

    public function get(): string
    {
        $shopName = urlencode($this->globalShopContext->getShopName());

        return implode('-', [
            sprintf('[PHP version: (%s)]', phpversion()),
            sprintf('[Shop name: (%s)]', $shopName),
            sprintf('[PrestaShop version: (%s)]', VersionUtility::current()),
            sprintf('[Module version: (%s)]', $this->moduleFactory->getModule()->version),
        ]);
    }
}
