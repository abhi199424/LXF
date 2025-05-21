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

use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Presentation\Presenter\Verification\CanPresentKlarnaExpressCheckout;
use KlarnaPayment\Module\Presentation\Presenter\Verification\IsCartAmountValidForKlarnaPayments;

if (!defined('_PS_VERSION_')) {
    exit;
}

class KlarnaExpressCheckoutPresenter
{
    /** @var Context */
    private $context;
    /** @var \KlarnaPayment */
    private $module;
    /** @var CanPresentKlarnaExpressCheckout */
    private $canPresentKlarnaExpressCheckout;

    /** @var IsCartAmountValidForKlarnaPayments */
    private $isCartAmountValidForKlarnaPayments;

    public function __construct(
        Context $context,
        ModuleFactory $moduleFactory,
        CanPresentKlarnaExpressCheckout $canPresentKlarnaExpressCheckout,
        IsCartAmountValidForKlarnaPayments $isCartAmountValidForKlarnaPayments
    ) {
        $this->context = $context;
        $this->module = $moduleFactory->getModule();
        $this->canPresentKlarnaExpressCheckout = $canPresentKlarnaExpressCheckout;
        $this->isCartAmountValidForKlarnaPayments = $isCartAmountValidForKlarnaPayments;
    }

    public function present(array $product = []): string
    {
        if (!$this->canPresentKlarnaExpressCheckout->verify(get_class($this->context->getController()))) {
            return '';
        }

        if (!$this->isCartAmountValidForKlarnaPayments->verify((int) $this->context->getCart()->id, $product)) {
            return '';
        }

        return $this->module->display($this->module->getLocalPath(), 'views/templates/front/hook/klarna_express_checkout.tpl');
    }
}
