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

namespace KlarnaPayment\Module\Core\Order\Action;

use KlarnaPayment\Module\Core\Order\DTO\RetrieveOrderRequestData;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotUpdateOrderStatus;
use KlarnaPayment\Module\Core\Order\Handler\Status\InternalOrderStatusHandler;
use KlarnaPayment\Module\Core\Order\Repository\KlarnaPaymentOrderRepositoryInterface;
use KlarnaPayment\Module\Core\Order\Verification\CanChangeOrderStatus;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class UpdateOrderStatusAction
{
    private $logger;
    private $klarnaPaymentOrderRepository;
    private $globalShopContext;
    private $retrieveOrderAction;
    private $internalOrderStatusHandler;
    private $canChangeOrderStatus;

    public function __construct(
        LoggerInterface $logger,
        KlarnaPaymentOrderRepositoryInterface $klarnaPaymentOrderRepository,
        GlobalShopContextInterface $globalShopContext,
        RetrieveOrderAction $retrieveOrderAction,
        InternalOrderStatusHandler $internalOrderStatusHandler,
        CanChangeOrderStatus $canChangeOrderStatus
    ) {
        $this->logger = $logger;
        $this->klarnaPaymentOrderRepository = $klarnaPaymentOrderRepository;
        $this->globalShopContext = $globalShopContext;
        $this->retrieveOrderAction = $retrieveOrderAction;
        $this->internalOrderStatusHandler = $internalOrderStatusHandler;
        $this->canChangeOrderStatus = $canChangeOrderStatus;
    }

    /**
     * @throws CouldNotUpdateOrderStatus
     */
    public function run(int $orderId)
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        /** @var \KlarnaPaymentOrder|null $klarnaPaymentOrder */
        $klarnaPaymentOrder = $this->klarnaPaymentOrderRepository->findOneBy([
            'id_internal' => $orderId,
            'id_shop' => $this->globalShopContext->getShopId(),
        ]);

        if (!$klarnaPaymentOrder) {
            throw CouldNotUpdateOrderStatus::failedToFindOrder($orderId);
        }

        try {
            $retrieveOrderResponse = $this->retrieveOrderAction->run(
                RetrieveOrderRequestData::create($klarnaPaymentOrder->id_external)
            );
        } catch (\Throwable $exception) {
            throw CouldNotUpdateOrderStatus::failedToRetrieveOrder($exception);
        }

        try {
            if (!$this->canChangeOrderStatus->verify($retrieveOrderResponse->getOrder())) {
                return;
            }

            $this->internalOrderStatusHandler->handle(
                $orderId,
                strtolower($retrieveOrderResponse->getOrder()->getStatus())
            );
        } catch (\Throwable $exception) {
            throw CouldNotUpdateOrderStatus::failedToUpdateOrderStatus($orderId, $exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));
    }
}
