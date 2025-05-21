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

use KlarnaPayment\Module\Core\Order\Exception\CouldNotChangeOrderStatus;
use KlarnaPayment\Module\Core\Shared\Repository\OrderRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ChangeOrderStatusAction
{
    private $logger;
    private $orderRepository;
    private $globalShopContext;

    public function __construct(
        LoggerInterface $logger,
        OrderRepositoryInterface $orderRepository,
        GlobalShopContextInterface $globalShopContext
    ) {
        $this->logger = $logger;
        $this->orderRepository = $orderRepository;
        $this->globalShopContext = $globalShopContext;
    }

    /**
     * @throws CouldNotChangeOrderStatus
     * @throws KlarnaPaymentException
     */
    public function run(int $orderId, int $orderStatusId)
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        try {
            /** @var \Order|null $order */
            $order = $this->orderRepository->findOneBy([
                'id_order' => $orderId,
                'id_shop' => $this->globalShopContext->getShopId(),
            ]);
        } catch (\Exception $exception) {
            throw CouldNotChangeOrderStatus::unknownError($exception);
        }

        if (!$order) {
            throw CouldNotChangeOrderStatus::failedToFindInternalOrder($orderId);
        }

        try {
            if ((int) $order->getCurrentState() !== (int) $orderStatusId) {
                $order->setCurrentState($orderStatusId);
                $order->update();
            }
        } catch (\Exception $exception) {
            throw CouldNotChangeOrderStatus::unknownError($exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));
    }
}
