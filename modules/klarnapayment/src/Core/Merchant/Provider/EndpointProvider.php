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

class EndpointProvider
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

    public function getEndpoint(?string $currencyIso = null): string
    {
        $contextCurrencyIso = $currencyIso ?? $this->context->getCurrencyIso();

        foreach (Config::ENDPOINT_CURRENCY_ISO_MAP as $regionIso => $currencies) {
            if (in_array($contextCurrencyIso, $currencies)) {
                return $regionIso;
            }
        }

        return Config::DEFAULT_ENDPOINT;
    }
}
