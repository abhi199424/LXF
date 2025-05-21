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

namespace KlarnaPayment\Module\Core\Account\Processor;

use KlarnaPayment\Module\Core\Account\Exception\CouldNotProcessExpressCheckout;
use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CanProcessExpressCheckout
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(
        Configuration $configuration
    ) {
        $this->configuration = $configuration;
    }

    public function run(?\KlarnaExpressCheckout $checkoutFlow): void
    {
        if (!$this->configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE)) {
            throw CouldNotProcessExpressCheckout::isNotActive();
        }

        if (!$checkoutFlow || !$checkoutFlow->is_kec) {
            throw CouldNotProcessExpressCheckout::flowFinished();
        }
    }
}
