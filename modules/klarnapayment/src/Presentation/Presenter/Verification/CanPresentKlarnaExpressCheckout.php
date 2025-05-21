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

namespace KlarnaPayment\Module\Presentation\Presenter\Verification;

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CanPresentKlarnaExpressCheckout
{
    /** @var Configuration */
    private $configuration;

    public function __construct(
        Configuration $configuration
    ) {
        $this->configuration = $configuration;
    }

    public function verify(string $currentControllerName): bool
    {
        if (!$this->configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE)) {
            return false;
        }

        $selectedPlacements = array_map('strtolower', explode(
            ',',
            (string) $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS)
        ));

        $positionData = [
            \CartController::class => in_array('cart', $selectedPlacements, true),
            \ProductController::class => in_array('product', $selectedPlacements, true),
        ];

        return !empty($positionData[$currentControllerName]);
    }
}
