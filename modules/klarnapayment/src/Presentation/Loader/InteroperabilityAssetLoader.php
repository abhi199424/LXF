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

namespace KlarnaPayment\Module\Presentation\Loader;

use KlarnaPayment\Module\Api\Environment;
use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Merchant\Provider\CredentialsConfigurationKeyProvider;
use KlarnaPayment\Module\Core\Merchant\Provider\Currency\LocaleProvider;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;

if (!defined('_PS_VERSION_')) {
    exit;
}

class InteroperabilityAssetLoader
{
    /** @var \KlarnaPayment */
    private $module;

    /** @var LocaleProvider */
    private $localeProvider;

    /** @var Configuration */
    private $configuration;

    /** @var CredentialsConfigurationKeyProvider */
    private $credentialsConfigurationKeyProvider;

    /** @var Context */
    private $context;

    public function __construct(
        ModuleFactory $moduleFactory,
        LocaleProvider $localeProvider,
        Configuration $configuration,
        CredentialsConfigurationKeyProvider $credentialsConfigurationKeyProvider,
        Context $context
    ) {
        $this->module = $moduleFactory->getModule();
        $this->localeProvider = $localeProvider;
        $this->configuration = $configuration;
        $this->credentialsConfigurationKeyProvider = $credentialsConfigurationKeyProvider;
        $this->context = $context;
    }

    public function register(\FrontController $controller): string
    {
        $controller->registerJavascript(
            $this->module->name . '-interoperability-script',
            'modules/' . $this->module->name . '/views/js/front/interoperability/interoperability.js'
        );

        $previousKlarnaPaymentJsDef = isset(\Media::getJsDef()['klarnapayment']) ? \Media::getJsDef()['klarnapayment'] : [];

        \Media::addJsDef([
            'klarnapayment' => array_merge($previousKlarnaPaymentJsDef, [
                'interoperability' => [
                    'locale' => $this->localeProvider->getLocale(),
                    'environment' => $this->configuration->getAsBoolean(Config::KLARNA_PAYMENT_IS_PRODUCTION_ENVIRONMENT) ? Environment::PRODUCTION : Environment::PLAYGROUND,
                    'clientId' => $this->configuration->get($this->credentialsConfigurationKeyProvider->getClientId($this->context->getShopId())),
                ],
            ]),
        ]);

        return '';
    }
}
