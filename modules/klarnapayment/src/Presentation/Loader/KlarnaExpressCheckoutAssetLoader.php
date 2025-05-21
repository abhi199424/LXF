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
use KlarnaPayment\Module\Core\Merchant\Provider\MerchantIdProvider;
use KlarnaPayment\Module\Core\Shared\Enum\KlarnaExpressCheckoutButtonShape;
use KlarnaPayment\Module\Core\Shared\Enum\KlarnaExpressCheckoutButtonTheme;
use KlarnaPayment\Module\Core\Tools\Action\ValidateIsAssetsRequired;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Adapter\Tools;
use KlarnaPayment\Module\Infrastructure\Utility\ArrayUtility;
use KlarnaPayment\Module\Presentation\Presenter\Verification\CanPresentKlarnaExpressCheckout;

if (!defined('_PS_VERSION_')) {
    exit;
}

class KlarnaExpressCheckoutAssetLoader
{
    /** @var Configuration */
    private $configuration;
    /** @var Context */
    private $context;
    /** @var \KlarnaPayment */
    private $module;
    /** @var CanPresentKlarnaExpressCheckout */
    private $canPresentKlarnaExpressCheckout;
    /** @var Tools */
    private $tools;
    /** @var ValidateIsAssetsRequired */
    private $validateIsAssetsRequired;
    /** @var MerchantIdProvider */
    private $merchantIdProvider;
    /** @var CredentialsConfigurationKeyProvider */
    private $configurationKeyProvider;

    public function __construct(
        Configuration $configuration,
        Context $context,
        ModuleFactory $moduleFactory,
        CanPresentKlarnaExpressCheckout $canPresentKlarnaExpressCheckout,
        Tools $tools,
        ValidateIsAssetsRequired $validateIsAssetsRequired,
        MerchantIdProvider $merchantIdProvider,
        CredentialsConfigurationKeyProvider $configurationKeyProvider
    ) {
        $this->configuration = $configuration;
        $this->context = $context;
        $this->module = $moduleFactory->getModule();
        $this->canPresentKlarnaExpressCheckout = $canPresentKlarnaExpressCheckout;
        $this->tools = $tools;
        $this->validateIsAssetsRequired = $validateIsAssetsRequired;
        $this->merchantIdProvider = $merchantIdProvider;
        $this->configurationKeyProvider = $configurationKeyProvider;
    }

    public function register(\FrontController $controller): string
    {
        if (!$this->configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE)) {
            return '';
        }

        $theme = !empty((string) $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME)) ?
            (string) $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME) :
            KlarnaExpressCheckoutButtonTheme::DEFAULT
        ;

        $shape = !empty((string) $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE)) ?
            (string) $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE) :
            KlarnaExpressCheckoutButtonShape::DEFAULT
        ;

        $generalJsDef = [
            'expressCheckoutData' => 'klarnapayment_express_checkout_data',
            'expressCheckoutUrl' => $this->context->getModuleLink(
                $this->module->name,
                'expressCheckout'
            ),
            'staticToken' => $this->tools->getToken(false),
            'client_identifier' => $this->configuration->get($this->configurationKeyProvider->getClientId($this->context->getShopId(), $this->context->getCurrencyIso())),
            'theme' => $theme,
            'shape' => $shape,
            'container' => '.klarnapayment-kec-wrapper',
            'locale' => \Context::getContext()->language->locale,
        ];

        if ($this->validateIsAssetsRequired->run($controller)) {
            $controller->registerJavascript(
                $this->module->name . '-klarna-express-checkout-prefill',
                'modules/' . $this->module->name . '/views/js/front/klarna_express_checkout/klarna_express_checkout_prefill.js');

            $previousKlarnaPaymentJsDef = \Media::getJsDef()['klarnapayment'] ?? [];

            \Media::addJsDef([
                'klarnapayment' => array_merge($previousKlarnaPaymentJsDef, $generalJsDef),
            ]);

            return '';
        }

        if (!$this->canPresentKlarnaExpressCheckout->verify(
            get_class($controller)
        )) {
            return '';
        }

        $controller->registerStylesheet(
            $this->module->name . '-klarna-express-checkout',
            'modules/' . $this->module->name . '/views/css/front/klarna_express_checkout.css'
        );

        $controller->registerJavascript(
            $this->module->name . '-klarna-express-checkout-listener',
            'modules/' . $this->module->name . '/views/js/front/klarna_express_checkout/klarna_express_checkout_listener.js'
        );

        $previousKlarnaPaymentJsDef = \Media::getJsDef()['klarnapayment'] ?? [];

        \Media::addJsDef([
            'klarnapayment' => array_merge($previousKlarnaPaymentJsDef, $generalJsDef, [
                'checkoutUrl' => $this->context->getPageLink(
                    'order',
                    null,
                    null,
                    $this->context->getCustomerId() ? ['newAddress' => 'delivery'] : [] // Force new address form for logged in customers
                ),
                'isProductPage' => $controller instanceof \ProductControllerCore,
            ]),
        ]);

        $previousKlarnaPaymentSmartyVars = $this->context->getSmarty()->getTemplateVars('klarnapayment') ?? [];

        $this->context->getSmarty()->assign([
            'klarnapayment' => ArrayUtility::recursiveArrayMerge($previousKlarnaPaymentSmartyVars, [
                'klarna_express_checkout' => [
                    'mid' => $this->merchantIdProvider->getMerchantId(),
                    'sdk_url' => Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_SDK_URL,
                    'callback_script_path' => $this->context->getBaseLink() .
                        'modules/' .
                        $this->module->name .
                        '/views/js/front/klarna_express_checkout/klarna_express_checkout.js',
                ],
            ]),
        ]);

        return $this->module->display($this->module->getLocalPath(), 'views/templates/front/hook/klarna_express_checkout_script.tpl');
    }
}
