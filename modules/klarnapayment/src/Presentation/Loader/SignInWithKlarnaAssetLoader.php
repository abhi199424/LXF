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

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Merchant\Provider\CredentialsConfigurationKeyProvider;
use KlarnaPayment\Module\Core\Merchant\Provider\Currency\LocaleProvider;
use KlarnaPayment\Module\Core\Shared\Enum\KlarnaSignInWithKlarnaButtonShape;
use KlarnaPayment\Module\Core\Shared\Enum\KlarnaSignInWithKlarnaButtonTheme;
use KlarnaPayment\Module\Core\Shared\Enum\SignInWithKlarnaBadgeAlignment;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Adapter\Tools;
use KlarnaPayment\Module\Infrastructure\Utility\ArrayUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class SignInWithKlarnaAssetLoader
{
    /** @var Configuration */
    private $configuration;
    /** @var Context */
    private $context;
    /** @var \KlarnaPayment */
    private $module;
    /** @var LocaleProvider */
    private $localeProvider;
    /** @var Tools */
    private $tools;

    /** @var CredentialsConfigurationKeyProvider */
    private $credentialsConfigurationKeyProvider;

    public function __construct(
        Configuration $configuration,
        Context $context,
        ModuleFactory $moduleFactory,
        LocaleProvider $localeProvider,
        CredentialsConfigurationKeyProvider $credentialsConfigurationKeyProvider
    ) {
        $this->configuration = $configuration;
        $this->context = $context;
        $this->module = $moduleFactory->getModule();
        $this->localeProvider = $localeProvider;
        $this->credentialsConfigurationKeyProvider = $credentialsConfigurationKeyProvider;
    }

    public function register(\FrontController $controller): string
    {
        if (!$this->configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ACTIVE)) {
            return '';
        }

        $selectedPlacements = explode(
            ',',
            $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_PLACEMENTS)
        );

        if (!in_array(
            $controller->php_self,
            $selectedPlacements,
            true
        )) {
            return '';
        }

        $controller->registerStylesheet(
            $this->module->name . '-sign-in-with-klarna',
            'modules/' . $this->module->name . '/views/css/front/sign_in_with_klarna.css'
        );

        $controller->registerJavascript(
            $this->module->name . '-sign-in-with-klarna',
            'modules/' . $this->module->name . '/views/js/front/sign_in_with_klarna/sign_in_with_klarna.js'
        );

        $previousKlarnaPaymentSmartyVars = $this->context->getSmarty()->getTemplateVars('klarnapayment') ?? [];

        $locale = $this->localeProvider->getLocale();

        $previousKlarnaPaymentJsDef = isset(\Media::getJsDef()['klarnapayment']) ? \Media::getJsDef()['klarnapayment'] : [];

        $theme = !empty((string) $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_THEME)) ?
            (string) $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_THEME) :
            KlarnaSignInWithKlarnaButtonTheme::DEFAULT
        ;

        $shape = !empty((string) $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_SHAPE)) ?
            (string) $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_SHAPE) :
            KlarnaSignInWithKlarnaButtonShape::DEFAULT
        ;

        $alignment = !empty((string) $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_ALIGNMENT)) ?
            (string) $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_ALIGNMENT) :
            SignInWithKlarnaBadgeAlignment::DEFAULT
        ;

        \Media::addJsDef([
            'klarnapayment' => array_merge($previousKlarnaPaymentJsDef, [
                'signInWithKlarna' => [
                    'authenticationUrl' => $this->context->getModuleLink($this->module->name, 'authentication'),
                    'clientId' => $this->configuration->get($this->credentialsConfigurationKeyProvider->getClientId($this->context->getShopId())),
                    'locale' => $locale,
                    'redirectUri' => $this->context->getBaseLink() . Config::SIGN_IN_WITH_KLARNA_CALLBACK_URL_PATH,
                    'theme' => $theme,
                    'shape' => $shape,
                    'alignment' => $alignment,
                    'scope' => $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ON_SIGN_IN_SCOPE),
                    'market' => preg_replace('/^[a-z]+-/', '', $locale),
                    'environment' => $this->configuration->getAsBoolean(Config::KLARNA_PAYMENT_IS_PRODUCTION_ENVIRONMENT) ? 'production' : 'playground',
                    'isCustomerLoggedIn' => $this->context->isCustomerLoggedIn(),
                ],
            ]),
        ]);

        $this->context->getSmarty()->assign([
            'klarnapayment' => ArrayUtility::recursiveArrayMerge($previousKlarnaPaymentSmartyVars, [
                'signInWithKlarna' => [
                    'sdk_url' => Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_SDK_URL,
                    'client_id' => $this->configuration->get(
                        $this->credentialsConfigurationKeyProvider->getClientId($this->context->getShopId()),
                        $this->context->getShopId()
                    ),
                    'market' => preg_replace('/^[a-z]+-/', '', $locale),
                    'locale' => $locale,
                    'scope' => $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ON_SIGN_IN_SCOPE),
                    'onSignIn' => Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ON_SIGN_IN_SUCCESS_CALLBACK_FUNCTION,
                    'onError' => Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ON_SIGN_IN_FAILURE_CALLBACK_FUNCTION,
                    'redirectUri' => $this->context->getBaseLink() . Config::SIGN_IN_WITH_KLARNA_CALLBACK_URL_PATH,
                    'callback_script_path' => $this->context->getBaseLink() .
                        'modules/' .
                        $this->module->name .
                        '/views/js/front/sign_in_with_klarna/sign_in_with_klarna.js',
                ],
            ]),
        ]);

        return '';
    }
}
