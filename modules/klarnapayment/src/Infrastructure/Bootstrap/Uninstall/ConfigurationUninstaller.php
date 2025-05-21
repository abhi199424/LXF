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

namespace KlarnaPayment\Module\Infrastructure\Bootstrap\Uninstall;

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ConfigurationUninstaller implements UninstallerInterface
{
    /** @var Configuration */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    public function init(): void
    {
        $this->configuration->delete(Config::KLARNA_PAYMENT_IS_PRODUCTION_ENVIRONMENT);
        $this->configuration->delete(Config::KLARNA_PAYMENT_SECRET_KEY);
        $this->configuration->deleteFromAllEnvironments(Config::KLARNA_PAYMENT_DEBUG_MODE);
        $this->configuration->deleteFromAllEnvironments(Config::KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE);
        $this->configuration->deleteFromAllEnvironments(Config::KLARNA_PAYMENT_ALLOW_ORDER_STATUS_CHANGE);
        $this->configuration->deleteFromAllEnvironments(Config::KLARNA_PAYMENT_DEFAULT_LOCALE);
        $this->configuration->deleteFromAllEnvironments(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE);
        $this->configuration->deleteFromAllEnvironments(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS);
        $this->configuration->deleteFromAllEnvironments(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME);
        $this->configuration->deleteFromAllEnvironments(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE);
        $this->configuration->deleteFromAllEnvironments(Config::KLARNA_PAYMENT_HPP_SERVICE);

        $this->configuration->deleteFromAllEnvironments(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ACTIVE);
        $this->configuration->deleteFromAllEnvironments(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_PLACEMENTS);

        $this->configuration->delete(Config::ON_SITE_MESSAGING_INFOPAGE_CONFIG);
    }
}
