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
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Provider\ApplicationContextProvider;
use KlarnaPayment\Module\Infrastructure\Utility\ArrayUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class HeadSectionAssetLoader
{
    /** @var OnsiteMessagingAssetLoader */
    private $onsiteMessagingAssetLoader;
    /** @var SignInWithKlarnaAssetLoader */
    private $signInWithKlarnaAssetLoader;
    /** @var KlarnaExpressCheckoutAssetLoader */
    private $klarnaExpressCheckoutAssetLoader;
    /** @var Context */
    private $context;
    /** @var ApplicationContextProvider */
    private $applicationContextProvider;
    private $klarnaSdkClientAssetLoader;

    /** @var InteroperabilityAssetLoader */
    private $interoperabilityAssetLoader;

    public function __construct(
        OnsiteMessagingAssetLoader $onsiteMessagingAssetLoader,
        SignInWithKlarnaAssetLoader $signInWithKlarnaAssetLoader,
        KlarnaExpressCheckoutAssetLoader $klarnaExpressCheckoutAssetLoader,
        Context $context,
        ApplicationContextProvider $applicationContextProvider,
        KlarnaSdkClientAssetLoader $klarnaSdkClientAssetLoader,
        InteroperabilityAssetLoader $interoperabilityAssetLoader
    ) {
        $this->onsiteMessagingAssetLoader = $onsiteMessagingAssetLoader;
        $this->signInWithKlarnaAssetLoader = $signInWithKlarnaAssetLoader;
        $this->klarnaExpressCheckoutAssetLoader = $klarnaExpressCheckoutAssetLoader;
        $this->context = $context;
        $this->applicationContextProvider = $applicationContextProvider;
        $this->klarnaSdkClientAssetLoader = $klarnaSdkClientAssetLoader;
        $this->interoperabilityAssetLoader = $interoperabilityAssetLoader;
    }

    public function register(\FrontController $controller): string
    {
        $applicationContext = $this->applicationContextProvider->refresh()->get();

        $previousKlarnaPaymentSmartyVars = $this->context->getSmarty()->getTemplateVars('klarnapayment') ?? [];

        $this->context->getSmarty()->assign([
            'klarnapayment' => ArrayUtility::recursiveArrayMerge($previousKlarnaPaymentSmartyVars, [
                'environment' => $applicationContext->getIsProduction()
                    ? Environment::PRODUCTION
                    : Environment::PLAYGROUND,
            ]),
        ]);

        $onsiteMessagingAssetScript = $this->onsiteMessagingAssetLoader->register($controller);
        $signInWithKlarnaAssetScript = $this->signInWithKlarnaAssetLoader->register($controller);
        $klarnaExpressCheckoutAssetScript = $this->klarnaExpressCheckoutAssetLoader->register($controller);
        $interoperabilityAssetScript = $this->interoperabilityAssetLoader->register($controller);
        $sdkClientScript = $this->klarnaSdkClientAssetLoader->register($controller);

        return $onsiteMessagingAssetScript . $klarnaExpressCheckoutAssetScript . $signInWithKlarnaAssetScript . $interoperabilityAssetScript . $sdkClientScript;
    }
}
