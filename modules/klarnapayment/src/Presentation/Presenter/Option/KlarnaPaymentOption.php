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

namespace KlarnaPayment\Module\Presentation\Presenter\Option;

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use PrestaShop\PrestaShop\Adapter\Entity\Address;
use PrestaShop\PrestaShop\Adapter\Entity\Cart;
use PrestaShop\PrestaShop\Adapter\Entity\Country;
use PrestaShop\PrestaShop\Adapter\Entity\Validate;
use PrestaShop\PrestaShop\Core\Payment\PaymentOption;

if (!defined('_PS_VERSION_')) {
    exit;
}

class KlarnaPaymentOption implements PaymentOptionInterface
{
    private $context;
    /** @var \KlarnaPayment|null */
    private $module;
    /** @var string */
    private $logo;
    /** @var string */
    private $paymentMethodName;
    /** @var string */
    private $paymentMethodIdentifier;
    /**
     * @var string
     */
    private $action;
    private $isKec;
    private $clientToken;

    public function __construct(
        Context $context,
        ModuleFactory $moduleFactory
    ) {
        $this->context = $context;
        $this->module = $moduleFactory->getModule();
    }

    public function setLogo(string $logo): self
    {
        $this->logo = $logo;

        return $this;
    }

    public function setPaymentMethodName(string $paymentMethodName): self
    {
        $this->paymentMethodName = $paymentMethodName;

        return $this;
    }

    public function setPaymentMethodIdentifier(string $paymentMethodIdentifier): self
    {
        $this->paymentMethodIdentifier = $paymentMethodIdentifier;

        return $this;
    }

    public function setAction(string $actionUrl): self
    {
        $this->action = $actionUrl;

        return $this;
    }

    public function setClientToken(string $clientToken): self
    {
        $this->clientToken = $clientToken;

        return $this;
    }

    public function setIsKec(bool $isKec): self
    {
        $this->isKec = $isKec;

        return $this;
    }

    private function getPurchaseCountry($cartId)
    {
        $cart = new Cart((int) $cartId);
        $address = new Address((int) $cart->id_address_delivery);
        if (!Validate::isLoadedObject($address)) {
            return null;
        }
        $countryId = (int) $address->id_country;
        $country = new Country($countryId);

        return $country->iso_code;
    }

    public function getOption(): PaymentOption
    {
        $additionalInfoUrls =
            isset(Config::KLARNA_ADDITIONAL_INFO_LINKS[$this->context->getLanguageIso()]) ?
        Config::KLARNA_ADDITIONAL_INFO_LINKS[$this->context->getLanguageIso()] :
        Config::KLARNA_ADDITIONAL_INFO_LINKS['en'];

        $this->context->getSmarty()->assign('klarnapayment', [
            'cart_id' => \Context::getContext()->cart->id,
            'payment_option' => [
                'payment_method_category' => $this->paymentMethodIdentifier,
            ],
            'termsAndConditionsUrl' => $additionalInfoUrls['termsAndConditionsUrl'],
            'privacyStatementUrl' => $additionalInfoUrls['privacyStatementUrl'],
            'cookieStatementUrl' => $additionalInfoUrls['cookieStatementUrl'],
            'purchase_country' => $this->getPurchaseCountry(\Context::getContext()->cart->id),
        ]);

        if ($this->action === null) {
            $defaultAction = $this->context->getModuleLink(
                $this->module->name,
                'payment'
            );
            $this->setAction($defaultAction);
        }

        return (new PaymentOption())
            ->setLogo($this->logo)
            ->setModuleName($this->module->name)
            ->setCallToActionText($this->paymentMethodName)
            ->setAction($this->action)
            ->setInputs([
                'paymentMethod' => [
                    'name' => 'klarnapaymentPaymentMethod',
                    'type' => 'hidden',
                    'value' => $this->paymentMethodIdentifier,
                ],
                'paymentMethodId' => [
                    'name' => 'klarnapaymentPaymentMethodId',
                    'type' => 'hidden',
                    'value' => $this->paymentMethodIdentifier,
                ],
                'client_token' => [
                    'name' => 'klarnapaymentClientToken',
                    'type' => 'hidden',
                    'value' => $this->clientToken,
                ],
                'is_kec' => [
                    'name' => 'klarnapaymentIsKec',
                    'type' => 'hidden',
                    'value' => $this->isKec,
                ],
            ])
            ->setAdditionalInformation($this->context->getSmarty()->fetch(
                $this->module->getLocalPath() . '/views/templates/front/partials/klarna_payment_info.tpl'
            ));
    }

    public function isSupported(): bool
    {
        return !empty($this->paymentMethodIdentifier);
    }
}
