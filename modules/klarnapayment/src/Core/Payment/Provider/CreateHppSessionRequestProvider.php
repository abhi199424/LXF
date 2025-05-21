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

namespace KlarnaPayment\Module\Core\Payment\Provider;

use KlarnaPayment\Module\Api\ApiClient;
use KlarnaPayment\Module\Api\Enum\PlaceOrderMode;
use KlarnaPayment\Module\Api\Environment;
use KlarnaPayment\Module\Api\Helper\ApiHelper;
use KlarnaPayment\Module\Api\Models\HppSessionMerchantUrl;
use KlarnaPayment\Module\Api\Models\HppSessionOptions;
use KlarnaPayment\Module\Api\Models\Session;
use KlarnaPayment\Module\Api\Requests\CreateHppSessionRequest;
use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Merchant\Provider\EndpointProvider;
use KlarnaPayment\Module\Core\Payment\DTO\CreateHppSessionData;
use KlarnaPayment\Module\Core\Payment\Enum\ActionStatus;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Factory\ApiClientFactory;
use KlarnaPayment\Module\Infrastructure\Provider\ApplicationContextProvider;
use KlarnaPayment\Module\Infrastructure\Utility\SecurityTokenUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CreateHppSessionRequestProvider
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var ApplicationContextProvider
     */
    private $applicationContextProvider;

    /**
     * @var ApiClient
     */
    private $apiClient;

    /** @var EndpointProvider */
    private $endpointProvider;
    /**
     * @var ApiClientFactory
     */
    private $apiClientFactory;

    public function __construct(
        Context $context,
        Configuration $configuration,
        ApplicationContextProvider $applicationContextProvider,
        ApiClientFactory $apiClientFactory,
        EndpointProvider $endpointProvider
    ) {
        $this->context = $context;
        $this->configuration = $configuration;
        $this->applicationContextProvider = $applicationContextProvider;
        $this->apiClientFactory = $apiClientFactory;
        $this->endpointProvider = $endpointProvider;
    }

    public function get(CreateHppSessionData $data): CreateHppSessionRequest
    {
        $request = new CreateHppSessionRequest();

        $request->setPaymentSessionUrl($this->createPaymentSessionUrl($data->getSession()->getSessionId()));
        $request->setMerchantUrls($this->createMerchantUrls($data->getCart()));
        $request->setOptions($this->createOptions($data->getSession()));

        return $request;
    }

    /**
     * @return HppSessionMerchantUrl
     */
    private function createMerchantUrls(\Cart $cart): ?HppSessionMerchantUrl
    {
        $hppMerchantUrl = new HppSessionMerchantUrl();

        $paymentControllerUrl = $this->context->getModuleLink(
            'klarnapayment',
            'hppPayment'
        );

        ApiHelper::appendUrlWithQueryParameters($paymentControllerUrl, [
            'cart_id' => $cart->id,
            'security_token' => SecurityTokenUtility::generateTokenFromCart($cart->id, (string) $this->configuration->get(Config::KLARNA_PAYMENT_SECRET_KEY)),
            'hppId' => '{{session_id}}',
            'action' => ActionStatus::ERROR,
        ]);
        $paymentControllerUrlForError = urldecode(ApiHelper::cleanUrl($paymentControllerUrl));

        $hppMerchantUrl->setError($paymentControllerUrlForError);
        $hppMerchantUrl->setFailure($paymentControllerUrlForError);

        $paymentControllerUrl = $this->context->getModuleLink(
            'klarnapayment',
            'hppPayment'
        );

        ApiHelper::appendUrlWithQueryParameters($paymentControllerUrl, [
            'cart_id' => $cart->id,
            'security_token' => SecurityTokenUtility::generateTokenFromCart($cart->id, (string) $this->configuration->get(Config::KLARNA_PAYMENT_SECRET_KEY)),
            'hppId' => '{{session_id}}',
            'action' => ActionStatus::CANCEL,
        ]);

        $paymentControllerUrlForCancel = urldecode(ApiHelper::cleanUrl($paymentControllerUrl));

        $hppMerchantUrl->setCancel($paymentControllerUrlForCancel);
        $hppMerchantUrl->setBack($paymentControllerUrlForCancel);

        $paymentControllerUrl = $this->context->getModuleLink(
            'klarnapayment',
            'hppPayment'
        );

        ApiHelper::appendUrlWithQueryParameters($paymentControllerUrl, [
            'cart_id' => $cart->id,
            'security_token' => SecurityTokenUtility::generateTokenFromCart($cart->id, (string) $this->configuration->get(Config::KLARNA_PAYMENT_SECRET_KEY)),
            'hppId' => '{{session_id}}',
            'authorization_token' => '{{authorization_token}}',
            'action' => ActionStatus::SUCCESS,
        ]);

        $hppMerchantUrl->setSuccess(urldecode(ApiHelper::cleanUrl($paymentControllerUrl)));

        $paymentControllerUrl = $this->context->getModuleLink(
            'klarnapayment',
            'hppPayment'
        );

        ApiHelper::appendUrlWithQueryParameters($paymentControllerUrl, [
            'cart_id' => $cart->id,
            'security_token' => SecurityTokenUtility::generateTokenFromCart($cart->id, (string) $this->configuration->get(Config::KLARNA_PAYMENT_SECRET_KEY)),
            'hppId' => '{{session_id}}',
            'authorization_token' => '{{authorization_token}}',
            'action' => ActionStatus::UPDATE,
        ]);

        $hppMerchantUrl->setStatusUpdate(urldecode(ApiHelper::cleanUrl($paymentControllerUrl)));

        return $hppMerchantUrl;
    }

    private function createOptions(Session $session): HppSessionOptions
    {
        $options = new HppSessionOptions();
        $options->setPlaceOrderMode(PlaceOrderMode::NONE);
        $categories = [];

        foreach ($session->getPaymentMethodCategories() ?? [] as $category) {
            $categories[] = strtoupper($category->getIdentifier());
        }

        $options->setPaymentMethodCategories($categories);

        return $options;
    }

    private function createPaymentSessionUrl(string $sessionId): string
    {
        $applicationContext = $this->applicationContextProvider->refresh()->get();

        if ($applicationContext->getIsProduction()) {
            $environment = Environment::PRODUCTION;
        } else {
            $environment = Environment::SANDBOX;
        }

        $baseUrl = $this->apiClient()->getBaseUrlByEnvironment($environment, $this->endpointProvider->getEndpoint());

        $queryParameters = '/payments/v1/sessions/{session_id}';

        $queryParameters = ApiHelper::appendUrlWithTemplateParameters($queryParameters, [
            'session_id' => $sessionId,
        ]);

        return ApiHelper::cleanUrl($baseUrl . $queryParameters);
    }

    private function apiClient(): ApiClient
    {
        if ($this->apiClient === null) {
            $this->apiClient = $this->apiClientFactory->create();
        }

        return $this->apiClient;
    }
}
