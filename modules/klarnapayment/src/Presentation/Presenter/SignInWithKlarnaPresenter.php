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

namespace KlarnaPayment\Module\Presentation\Presenter;

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Utility\ArrayUtility;
use KlarnaPayment\Module\Presentation\Presenter\Verification\CanPresentSignInWithKlarna;

if (!defined('_PS_VERSION_')) {
    exit;
}

class SignInWithKlarnaPresenter
{
    /** @var Context */
    private $context;
    /** @var \Module */
    private $module;
    /** @var CanPresentSignInWithKlarna */
    private $canPresentSignInWithKlarna;

    public function __construct(
        Context $context,
        ModuleFactory $moduleFactory,
        CanPresentSignInWithKlarna $canPresentSignInWithKlarna
    ) {
        $this->context = $context;
        $this->module = $moduleFactory->getModule();
        $this->canPresentSignInWithKlarna = $canPresentSignInWithKlarna;
    }

    public function present(): string
    {
        if (!$this->canPresentSignInWithKlarna->verify(
            get_class($this->context->getController())
        )) {
            return '';
        }

        $previousKlarnaPaymentSmartyVars = $this->context->getSmarty()->getTemplateVars('klarnapayment') ?? [];

        $this->context->getSmarty()->assign([
            'klarnapayment' => ArrayUtility::recursiveArrayMerge($previousKlarnaPaymentSmartyVars, [
                'sign_in_with_klarna' => [
                    'theme' => Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_DEFAULT_THEME,
                    'shape' => Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_DEFAULT_SHAPE,
                    'logo_alignment' => Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_DEFAULT_ALIGNMENT,
                ],
            ]),
        ]);

        return $this->module->display($this->module->getLocalPath(), 'views/templates/front/hook/sign_in_with_klarna.tpl');
    }
}
