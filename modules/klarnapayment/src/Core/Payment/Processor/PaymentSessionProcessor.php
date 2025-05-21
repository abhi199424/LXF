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

namespace KlarnaPayment\Module\Core\Payment\Processor;

use KlarnaPayment\Module\Api\Models\Session;
use KlarnaPayment\Module\Core\Payment\Action\CreatePaymentSessionAction;
use KlarnaPayment\Module\Core\Payment\Action\RetrievePaymentSessionAction;
use KlarnaPayment\Module\Core\Payment\Action\UpdatePaymentSessionAction;
use KlarnaPayment\Module\Core\Payment\DTO\CreatePaymentSessionData;
use KlarnaPayment\Module\Core\Payment\DTO\RetrievePaymentSessionData;
use KlarnaPayment\Module\Core\Payment\DTO\UpdatePaymentSessionData;
use KlarnaPayment\Module\Core\Payment\Exception\CouldNotProcessSession;
use KlarnaPayment\Module\Core\Payment\Provider\PaymentSessionProvider;
use KlarnaPayment\Module\Core\Shared\Repository\CartRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Cache\CacheInterface;
use KlarnaPayment\Module\Infrastructure\Cache\CacheKey;

if (!defined('_PS_VERSION_')) {
    exit;
}

class PaymentSessionProcessor
{
    private $cache;
    private $retrievePaymentSessionAction;
    private $paymentSessionProvider;
    private $createPaymentSessionAction;
    private $updatePaymentSessionAction;
    /**
     * @var CartRepositoryInterface
     */
    private $cartRepository;

    public function __construct(
        CacheInterface $cache,
        RetrievePaymentSessionAction $retrievePaymentSessionAction,
        PaymentSessionProvider $paymentSessionProvider,
        CreatePaymentSessionAction $createPaymentSessionAction,
        UpdatePaymentSessionAction $updatePaymentSessionAction,
        CartRepositoryInterface $cartRepository
    ) {
        $this->cache = $cache;
        $this->retrievePaymentSessionAction = $retrievePaymentSessionAction;
        $this->paymentSessionProvider = $paymentSessionProvider;
        $this->createPaymentSessionAction = $createPaymentSessionAction;
        $this->updatePaymentSessionAction = $updatePaymentSessionAction;
        $this->cartRepository = $cartRepository;
    }

    public function run(int $cartId): Session
    {
        /** @var \Cart|null $cart */
        $cart = $this->cartRepository->findOneBy([
            'id_cart' => $cartId,
        ]);

        if (!$cart) {
            throw CouldNotProcessSession::failedToFindCart($cartId);
        }

        $cacheKey = CacheKey::paymentSessionById(
            (int) $cart->id,
            (int) $cart->id_customer,
            (int) $cart->id_guest
        );

        if (!$cart->id_customer) {
            return $this->createAndCacheSession($cart, $cacheKey, false);
        }

        if (!$this->cache->has($cacheKey)) {
            return $this->createAndCacheSession($cart, $cacheKey);
        }

        $paymentSession = $this->updateSession($cart, $this->cache->get($cacheKey));
        $currentPaymentSession = $this->paymentSessionProvider->get($cart, false);

        if ($this->hasCustomerChangedPurchaseCountry($currentPaymentSession, $paymentSession)
        ) {
            return $this->createAndCacheSession($cart, $cacheKey);
        }

        return $paymentSession;
    }

    private function createAndCacheSession(\Cart $cart, string $cacheKey, bool $addCustomerDetails = true): ?Session
    {
        $paymentSession = $this->createSession($cart, $addCustomerDetails);
        $this->cache->set($cacheKey, $paymentSession->getSessionId(), 60 * 60 * 48);

        return $paymentSession;
    }

    private function hasCustomerChangedPurchaseCountry(Session $currentSession, Session $apiSession): bool
    {
        // Sometimes session returns country iso code in lowercase
        return strtoupper($currentSession->getPurchaseCountry()) !== strtoupper($apiSession->getPurchaseCountry());
    }

    private function createSession(\Cart $cart, bool $addCustomerDetails): Session
    {
        $createPaymentSessionResponse = $this->createPaymentSessionAction->run(CreatePaymentSessionData::create(
            $cart,
            $addCustomerDetails
        ));

        return $this->retrievePaymentSessionAction->run(RetrievePaymentSessionData::create(
            $createPaymentSessionResponse->getSessionId()
        ))
            ->getSession();
    }

    private function updateSession(\Cart $cart, string $sessionId, $addCustomerData = true): Session
    {
        $this->updatePaymentSessionAction->run(UpdatePaymentSessionData::create(
            $sessionId,
            $cart,
            $addCustomerData
        ));

        return $this->retrievePaymentSessionAction->run(RetrievePaymentSessionData::create(
            $sessionId
        ))
            ->getSession();
    }
}
