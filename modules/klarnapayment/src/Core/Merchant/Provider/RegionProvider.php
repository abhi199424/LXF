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

namespace KlarnaPayment\Module\Core\Merchant\Provider;

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;

if (!defined('_PS_VERSION_')) {
    exit;
}

class RegionProvider
{
    /**
     * @var Context
     */
    private $context;

    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    public function getIso(?string $currencyIso = null): string
    {
        if (null === $currencyIso) {
            $currencyIso = $this->context->getCurrencyIso();
        }

        foreach (Config::REGION_CURRENCY_ISO_MAP as $regionIso => $regionCurrencyIso) {
            if (in_array($currencyIso, $regionCurrencyIso)) {
                return $regionIso;
            }
        }

        return Config::DEFAULT_REGION_ISO;
    }
}
