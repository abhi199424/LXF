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

use KlarnaPayment\Module\Api\Enum\OrderStatus;
use KlarnaPayment\Module\Core\Order\Action\AddOrderMappingAction;
use KlarnaPayment\Module\Core\Order\Action\AutoCaptureAction;
use KlarnaPayment\Module\Core\Order\Action\RetrieveOrderAction;
use KlarnaPayment\Module\Core\Order\Action\UpdateMerchantReferencesAction;
use KlarnaPayment\Module\Core\Order\DTO\RetrieveOrderRequestData;
use KlarnaPayment\Module\Core\Order\DTO\UpdateMerchantReferencesRequestData;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotAutoCapture;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotChangeOrderStatus;
use KlarnaPayment\Module\Core\Order\Handler\Status\InternalOrderStatusHandler;
use KlarnaPayment\Module\Core\Payment\Action\CreateOrderAction;
use KlarnaPayment\Module\Core\Payment\Action\LockCartAction;
use KlarnaPayment\Module\Core\Payment\Action\UnlockCartAction;
use KlarnaPayment\Module\Core\Payment\Action\ValidateOrderAction;
use KlarnaPayment\Module\Core\Payment\DTO\CreateOrderRequestData;
use KlarnaPayment\Module\Core\Payment\DTO\ValidateOrderData;
use KlarnaPayment\Module\Core\Payment\Exception\CouldNotProcessCheckout;
use KlarnaPayment\Module\Core\Payment\Exception\CouldNotUnlockCart;
use KlarnaPayment\Module\Core\Shared\Repository\CartRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\KlarnaPaymentCartRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;
use KlarnaPayment\Module\Infrastructure\Utility\MoneyCalculator;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CheckoutProcessor
{
    private $validateOrderAction;
    private $createOrderAction;
    private $addOrderMappingAction;
    private $retrieveOrderAction;
    private $internalOrderStatusHandler;
    private $moneyCalculator;
    private $updateMerchantReferencesAction;
    private $lockCartAction;
    private $klarnaPaymentCartRepository;
    private $globalShopContext;
    /** @var AutoCaptureAction */
    private $autoCaptureAction;
    /** @var CartRepositoryInterface */
    private $cartRepository;
    /** @var UnlockCartAction */
    private $unlockCartAction;

    private $context;

    public function __construct(
        ValidateOrderAction $validateOrderAction,
        CreateOrderAction $createOrderAction,
        AddOrderMappingAction $addOrderMappingAction,
        RetrieveOrderAction $retrieveOrderAction,
        InternalOrderStatusHandler $internalOrderStatusHandler,
        MoneyCalculator $moneyCalculator,
        UpdateMerchantReferencesAction $updateMerchantReferencesAction,
        LockCartAction $lockCartAction,
        KlarnaPaymentCartRepositoryInterface $klarnaPaymentCartRepository,
        GlobalShopContextInterface $globalShopContext,
        AutoCaptureAction $autoCaptureAction,
        CartRepositoryInterface $cartRepository,
        UnlockCartAction $unlockCartAction,
        Context $context
    ) {
        $this->validateOrderAction = $validateOrderAction;
        $this->createOrderAction = $createOrderAction;
        $this->addOrderMappingAction = $addOrderMappingAction;
        $this->retrieveOrderAction = $retrieveOrderAction;
        $this->internalOrderStatusHandler = $internalOrderStatusHandler;
        $this->moneyCalculator = $moneyCalculator;
        $this->updateMerchantReferencesAction = $updateMerchantReferencesAction;
        $this->lockCartAction = $lockCartAction;
        $this->klarnaPaymentCartRepository = $klarnaPaymentCartRepository;
        $this->globalShopContext = $globalShopContext;
        $this->autoCaptureAction = $autoCaptureAction;
        $this->cartRepository = $cartRepository;
        $this->unlockCartAction = $unlockCartAction;
        $this->context = $context;
    }

    /**
     * @throws CouldNotAutoCapture
     * @throws CouldNotChangeOrderStatus
     * @throws CouldNotProcessCheckout
     * @throws KlarnaPaymentException
     */
    public function run(int $cartId, string $authorizationToken): void
    {
        /** @var \Cart|null $cart */
        $cart = $this->cartRepository->findOneBy([
            'id_cart' => $cartId,
        ]);

        if (!$cart) {
            throw CouldNotProcessCheckout::failedToFindCart($cartId);
        }

        $this->context->setCurrentCart($cart);
        $this->context->setCountry(new \Country((new \Address($cart->id_address_invoice))->id_country));
        $this->context->setCurrency(new \Currency($cart->id_currency));

        /** @var ?\KlarnaPaymentCart $klarnaPaymentCart */
        $klarnaPaymentCart = $this->klarnaPaymentCartRepository->findOneBy([
            'id_cart' => $cartId,
            'id_shop' => $this->globalShopContext->getShopId(),
        ]);

        // NOTE: if payment cart exists, we don't need to perform it again as authorization most likely has been called.
        if (!empty($klarnaPaymentCart) || empty($cartId)) {
            return;
        }

        register_shutdown_function([$this, 'shutdown'], (int) $cart->id);

        try {
            $this->lockCartAction->run((int) $cart->id);
        } catch (\Throwable $exception) {
            throw CouldNotProcessCheckout::failedToLockCart($exception, (int) $cart->id);
        }

        try {
            $createOrderResponse = $this->createOrderAction->run(CreateOrderRequestData::create(
                $cart,
                $authorizationToken
            ));
        } catch (\Throwable $exception) {
            throw CouldNotProcessCheckout::failedToCreateExternalOrder($exception, $cart->id);
        }

        try {
            $externalOrder = $this->retrieveOrderAction->run(RetrieveOrderRequestData::create(
                $createOrderResponse->getOrderId()
            ))->getOrder();
        } catch (\Throwable $exception) {
            throw CouldNotProcessCheckout::failedToRetrieveExternalOrder($exception, $createOrderResponse->getOrderId());
        }

        try {
            $internalOrder = $this->validateOrderAction->run(ValidateOrderData::create(
                (int) $cart->id_customer,
                (int) $cart->id,
                $this->moneyCalculator->calculateIntoFloat($externalOrder->getOrderAmount()),
                'Klarna',
                $externalOrder->getOrderId()
            ));
        } catch (\Throwable $exception) {
            throw CouldNotProcessCheckout::failedToValidateOrder($exception, $cart->id);
        }

        try {
            $this->updateMerchantReferencesAction->run(UpdateMerchantReferencesRequestData::create(
                $externalOrder->getOrderId(),
                $internalOrder->id,
                $internalOrder->reference
            ));
        } catch (\Throwable $exception) {
            throw CouldNotProcessCheckout::failedToUpdateMerchantReferences($exception, $externalOrder->getOrderId(), $internalOrder->id);
        }

        try {
            $this->addOrderMappingAction->run(
                (int) $internalOrder->id,
                $externalOrder,
                $authorizationToken
            );
        } catch (\Throwable $exception) {
            throw CouldNotProcessCheckout::failedToAddOrderMapping($exception, $externalOrder->getOrderId(), $internalOrder->id);
        }

        try {
            $this->autoCaptureAction->run($externalOrder, $internalOrder);
        } catch (\Throwable $exception) {
            throw CouldNotProcessCheckout::failedToAutoCapture($exception, $externalOrder->getOrderId(), (int) $internalOrder->id);
        }

        try {
            $this->internalOrderStatusHandler->handle((int) $internalOrder->id, OrderStatus::ACCEPTED);
        } catch (\Throwable $exception) {
            throw CouldNotProcessCheckout::failedToChangeOrderStatus($exception, (int) $internalOrder->id);
        }

        try {
            $this->unlockCartAction->run((int) $cart->id);
        } catch (\Throwable $exception) {
            throw CouldNotProcessCheckout::failedToUnlockCart($exception, (int) $cart->id);
        }
    }

    /**
     * On shutdown, we will unlock the cart.
     *
     * @param int $cartId
     *
     * @return void
     *
     * @throws CouldNotUnlockCart
     */
    public function shutdown(int $cartId)
    {
        $this->unlockCartAction->run($cartId);
    }
}
