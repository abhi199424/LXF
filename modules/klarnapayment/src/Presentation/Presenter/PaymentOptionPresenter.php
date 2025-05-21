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

use KlarnaPayment\Module\Api\Models\Session;
use KlarnaPayment\Module\Api\Responses\CreateHppSessionResponse;
use KlarnaPayment\Module\Core\Account\Action\GetKecClientTokenAction;
use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Payment\Action\CreateHppSessionAction;
use KlarnaPayment\Module\Core\Payment\DTO\CreateHppSessionData;
use KlarnaPayment\Module\Core\Payment\Exception\CouldNotCreateHppSession;
use KlarnaPayment\Module\Core\Payment\Processor\PaymentSessionProcessor;
use KlarnaPayment\Module\Core\Payment\Provider\KecPaymentMethodCategoriesProvider;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;
use KlarnaPayment\Module\Infrastructure\Provider\ApplicationContextProvider;
use KlarnaPayment\Module\Infrastructure\Utility\SecurityTokenUtility;
use KlarnaPayment\Module\Presentation\Presenter\Exception\CouldNotPresentPaymentOption;
use KlarnaPayment\Module\Presentation\Presenter\Option\KlarnaPaymentOption;

if (!defined('_PS_VERSION_')) {
    exit;
}

class PaymentOptionPresenter
{
    private $applicationContextProvider;
    private $klarnaPaymentOption;
    private $kecPaymentMethodCategoriesProvider;
    private $configuration;
    private $createHppSessionAction;
    private $paymentSessionProcessor;
    private $getKecClientTokenAction;

    public function __construct(
        ApplicationContextProvider $applicationContextProvider,
        KlarnaPaymentOption $klarnaPaymentOption,
        KecPaymentMethodCategoriesProvider $kecPaymentMethodCategoriesProvider,
        Configuration $configuration,
        CreateHppSessionAction $createHppSessionAction,
        PaymentSessionProcessor $paymentSessionProcessor,
        GetKecClientTokenAction $getKecClientTokenAction
    ) {
        $this->applicationContextProvider = $applicationContextProvider;
        $this->klarnaPaymentOption = $klarnaPaymentOption;
        $this->kecPaymentMethodCategoriesProvider = $kecPaymentMethodCategoriesProvider;
        $this->configuration = $configuration;
        $this->createHppSessionAction = $createHppSessionAction;
        $this->paymentSessionProcessor = $paymentSessionProcessor;
        $this->getKecClientTokenAction = $getKecClientTokenAction;
    }

    /**
     * @throws KlarnaPaymentException
     */
    public function present(int $cartId): array
    {
        if (!$this->applicationContextProvider->get()->isValid()) {
            throw CouldNotPresentPaymentOption::merchantIsNotLoggedIn();
        }

        $cart = new \Cart($cartId);

        $paymentOptions = [];
        $paymentSession = [];

        $isHppEnabled = $this->configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_HPP_SERVICE);

        try {
            $clientToken = $this->getKecClientTokenAction->run(
                $cartId, $cart->id_address_invoice
            );
            $paymentMethodCategories = $this->kecPaymentMethodCategoriesProvider->get();
            $isKec = 1;
        } catch (\Throwable $exception) {
            // If KEC flow is not available - fallback to regular KP session client token
            $paymentSession = $this->paymentSessionProcessor->run((int) \Context::getContext()->cart->id);
            $paymentMethodCategories = $paymentSession->getPaymentMethodCategories();
            $clientToken = $paymentSession->getClientToken();
            $isKec = 0;
        }

        if ($isHppEnabled && !empty($paymentSession)) {
            try {
                $hppSession = $this->createHppSession($paymentSession);
            } catch (\Exception $exception) {
                throw CouldNotCreateHppSession::unknownError($exception);
            }
        }

        try {
            foreach ($paymentMethodCategories ?? [] as $paymentMethodCategory) {
                $paymentOption = $this->klarnaPaymentOption
                    ->setPaymentMethodIdentifier($paymentMethodCategory->getIdentifier())
                    ->setPaymentMethodName($paymentMethodCategory->getName())
                    ->setLogo($paymentMethodCategory->getAssetUrls()->getStandard())
                    ->setIsKec($isKec)
                    ->setClientToken($clientToken);

                if (isset($hppSession)) {
                    $this->klarnaPaymentOption->setAction(\Context::getContext()->link->getModuleLink(
                        'klarnapayment',
                        'order',
                        [
                            'action' => 'HppExternalWebsite',
                            'ajax' => 1,
                            'cart_id' => (int) \Context::getContext()->cart->id,
                            'security_token' => SecurityTokenUtility::generateTokenFromCart(\Context::getContext()->cart->id, (string) $this->configuration->get(Config::KLARNA_PAYMENT_SECRET_KEY)),
                            'redirection_url' => $hppSession->getRedirectUrl(),
                        ],
                        true
                    ));
                }

                if ($paymentOption->isSupported()) {
                    $paymentOptions[] = $paymentOption->getOption();
                }
            }
        } catch (\Exception $exception) {
            throw CouldNotPresentPaymentOption::unknownError($exception);
        }

        return $paymentOptions;
    }

    /**
     * @param Session $session
     *
     * @return CreateHppSessionResponse
     *
     * @throws KlarnaPaymentException
     */
    private function createHppSession(Session $session): CreateHppSessionResponse
    {
        $hppSession = $this->createHppSessionAction->run(CreateHppSessionData::create(
            $session,
            \Context::getContext()->cart
        ));

        return $hppSession;
    }
}
