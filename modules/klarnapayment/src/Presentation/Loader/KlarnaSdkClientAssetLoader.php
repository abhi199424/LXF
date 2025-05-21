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
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Utility\ArrayUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class KlarnaSdkClientAssetLoader
{
    /** @var Configuration */
    private $configuration;
    /** @var LoggerInterface */
    private $logger;
    /** @var Context */
    private $context;
    /** @var \KlarnaPayment */
    private $module;
    /** @var CredentialsConfigurationKeyProvider */
    private $credentialsConfigurationKeyProvider;

    public function __construct(
        Configuration $configuration,
        Context $context,
        ModuleFactory $moduleFactory,
        CredentialsConfigurationKeyProvider $credentialsConfigurationKeyProvider
    ) {
        $this->configuration = $configuration;
        $this->context = $context;
        $this->module = $moduleFactory->getModule();
        $this->credentialsConfigurationKeyProvider = $credentialsConfigurationKeyProvider;
    }

    public function register(\FrontController $controller): string
    {
        $isOnsiteMessagingEnabled = $this->configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE);
        $isSignInWithKlarnaEnabled = $this->configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ACTIVE);

        $signInWithKlarnaPlacements = explode(
            ',',
            $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_PLACEMENTS)
        );

        $previousKlarnaPaymentSmartyVars = $this->context->getSmarty()->getTemplateVars('klarnapayment') ?? [];

        $this->context->getSmarty()->assign([
            'klarnapayment' => ArrayUtility::recursiveArrayMerge($previousKlarnaPaymentSmartyVars, [
                'sdk_url' => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_SDK_URL,
                'client_id' => $this->configuration->get(
                    $this->credentialsConfigurationKeyProvider->getClientId($this->context->getShopId()),
                    $this->context->getShopId()
                ),
            ]),
        ]);

        return $this->module->display($this->module->getLocalPath(), 'views/templates/front/hook/klarna_sdk_script.tpl');
    }
}
