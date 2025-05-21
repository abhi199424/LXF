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
use KlarnaPayment\Module\Core\Tools\Action\ValidateIsAssetsRequired;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Utility\SecurityTokenUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class PaymentFormAssetLoader
{
    /** @var \KlarnaPayment|null */
    private $module;
    private $context;
    private $configuration;
    /** @var ValidateIsAssetsRequired */
    private $validateIsAssetsRequired;

    public function __construct(
        ModuleFactory $moduleFactory,
        Context $context,
        Configuration $configuration,
        ValidateIsAssetsRequired $validateIsAssetsRequired
    ) {
        $this->module = $moduleFactory->getModule();
        $this->context = $context;
        $this->configuration = $configuration;
        $this->validateIsAssetsRequired = $validateIsAssetsRequired;
    }

    public function register(\FrontController $controller): void
    {
        if (!$this->validateIsAssetsRequired->run($controller)) {
            return;
        }

        $previousKlarnaPaymentJsDef = isset(\Media::getJsDef()['klarnapayment']) ? \Media::getJsDef()['klarnapayment'] : [];

        \Media::addJsDef([
            'klarnapayment' => array_merge($previousKlarnaPaymentJsDef, [
                'cdn_url' => 'https://x.klarnacdn.net/kp/lib/v1/api.js',
                'cart_id' => (int) \Context::getContext()->cart->id,
                'is_hpp_enabled' => $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_HPP_SERVICE),
                'get_order_details_url' => $this->context->getModuleLink(
                    $this->module->name,
                    'order',
                    [
                        'action' => 'GetOrderDetails',
                        'ajax' => 1,
                        'cart_id' => (int) \Context::getContext()->cart->id,
                        'security_token' => SecurityTokenUtility::generateTokenFromCart(\Context::getContext()->cart->id, (string) $this->configuration->get(Config::KLARNA_PAYMENT_SECRET_KEY)),
                    ]
                ),
                'payment_url' => $this->context->getModuleLink(
                    $this->module->name,
                    'payment',
                    [
                        'security_token' => SecurityTokenUtility::generateTokenFromCart(\Context::getContext()->cart->id, (string) $this->configuration->get(Config::KLARNA_PAYMENT_SECRET_KEY)),
                        'cart_id' => (int) \Context::getContext()->cart->id,
                    ]
                ),
            ]),
        ]);

        if (method_exists($controller, 'registerJavascript')) {
            $controller->registerJavascript(
                $this->module->name . '-payment-form',
                'modules/' . $this->module->name . '/views/js/front/payment/payment.js'
            );
        } else {
            $controller->addJS(
                $this->module->getPathUri() . 'views/js/front/payment/payment.js',
                false
            );
        }

        if (method_exists($controller, 'registerStylesheet')) {
            $controller->registerStylesheet(
                $this->module->getPathUri() . 'payment-form-css',
                'modules/' . $this->module->name . '/views/css/front/paymentOption.css'
            );
        } else {
            $controller->addCSS(
                $this->module->getPathUri() . 'views/css/front/paymentOption.css',
                'all',
                null,
                false
            );
        }
    }
}
