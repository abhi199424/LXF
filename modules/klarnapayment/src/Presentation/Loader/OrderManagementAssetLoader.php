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

use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;

if (!defined('_PS_VERSION_')) {
    exit;
}

final class OrderManagementAssetLoader
{
    const TRANSLATION_ID = 'OrderManagementAssetLoader';

    private $module;
    private $context;

    public function __construct(ModuleFactory $moduleFactory, Context $context)
    {
        $this->module = $moduleFactory->getModule();
        $this->context = $context;
    }

    public function register($controller, string $purchaseCurrency): void
    {
        \Media::addJsDef([
            'klarnapayment' => [
                'orderManagementMessages' => [
                    'confirmations' => [
                        'refundOrder' => $this->module->l('Are you sure you want to refund this order?', self::TRANSLATION_ID),
                        'captureOrder' => $this->module->l('Are you sure you want to capture this payment?', self::TRANSLATION_ID),
                        'cancelOrder' => $this->module->l('Are you sure you want to cancel this order?', self::TRANSLATION_ID),
                    ],
                    'errors' => [
                        'belowMinimumAllowedRefundAmount' => $this->module->l('Refund amount cannot be below minimum allowed', self::TRANSLATION_ID),
                        'aboveMaximumAllowedRefundAmount' => $this->module->l('Refund amount cannot be above maximum allowed', self::TRANSLATION_ID),
                    ],
                ],
                'captureButtonText' => $this->module->l('Capture', self::TRANSLATION_ID),
                'refundButtonText' => $this->module->l('Refund', self::TRANSLATION_ID),
                'currentLocaleIsoCode' => $this->context->getLanguageCode(),
                'currentCurrencyIsoCode' => $purchaseCurrency,
            ],
        ]);

        $controller->addCSS($this->module->getPathUri() . 'views/css/admin/hook/order_management.css');
        $controller->addJs($this->module->getPathUri() . 'views/js/admin/hook/order_management.js');
    }
}
