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
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class OnsiteMessagingAssetLoader
{
    /** @var Configuration */
    private $configuration;
    /** @var LoggerInterface */
    private $logger;
    /** @var Context */
    private $context;
    /** @var \KlarnaPayment */
    private $module;

    public function __construct(
        Configuration $configuration,
        LoggerInterface $logger,
        Context $context,
        ModuleFactory $moduleFactory
    ) {
        $this->configuration = $configuration;
        $this->logger = $logger;
        $this->context = $context;
        $this->module = $moduleFactory->getModule();
    }

    public function register(\FrontController $controller): string
    {
        if (!$this->configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE)) {
            return '';
        }

        if (!in_array($this->context->getCurrencyIso(), Config::KLARNA_PAYMENT_ONSITE_MESSAGING_VALID_CURRENCIES)) {
            $this->logger->debug('Failed to find valid currency for on-site messaging.', [
                'context' => [
                    'currency_iso' => $this->context->getCurrencyIso(),
                ],
            ]);

            return '';
        }

        $previousKlarnaPaymentJsDef = \Media::getJsDef()['klarnapayment'] ?? [];

        \Media::addJsDef([
            'klarnapayment' => array_merge($previousKlarnaPaymentJsDef, [
                'precision' => $this->context->getComputingPrecision(),
            ]),
        ]);

        $controller->registerJavascript(
            $this->module->name . '-onsite-messaging',
            'modules/' . $this->module->name . '/views/js/front/onsite_messaging/onsite_messaging.js'
        );

        return '';
    }
}
