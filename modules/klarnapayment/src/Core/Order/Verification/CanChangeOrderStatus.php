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

namespace KlarnaPayment\Module\Core\Order\Verification;

use Invertus\Knapsack\Collection;
use KlarnaPayment\Module\Api\Models\Order as ExternalOrder;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotChangeOrderStatus;
use KlarnaPayment\Module\Core\Order\Repository\KlarnaPaymentOrderRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\OrderStateRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Provider\OrderStatusProvider;
use KlarnaPayment\Module\Infrastructure\Utility\CompareUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CanChangeOrderStatus implements OrderActionVerificationInterface
{
    private $orderStateRepository;
    private $orderStatusProvider;
    private $klarnaPaymentOrderRepository;

    public function __construct(
        OrderStateRepositoryInterface $orderStateRepository,
        OrderStatusProvider $orderStatusProvider,
        KlarnaPaymentOrderRepositoryInterface $klarnaPaymentOrderRepository
    ) {
        $this->orderStateRepository = $orderStateRepository;
        $this->orderStatusProvider = $orderStatusProvider;
        $this->klarnaPaymentOrderRepository = $klarnaPaymentOrderRepository;
    }

    public function verify(ExternalOrder $order): bool
    {
        $externalOrderStatus = strtolower($order->getStatus());

        /** @var \KlarnaPaymentOrder $klarnaPaymentOrder */
        $klarnaPaymentOrder = $this->klarnaPaymentOrderRepository->findOneBy([
            'id_external' => $order->getOrderId(),
        ]);

        if (!$klarnaPaymentOrder) {
            throw CouldNotChangeOrderStatus::failedToFindExternalOrder($order->getOrderId());
        }

        $orderStateHistoryList = $this->orderStateRepository->getCurrentOrderStateHistoryList(
            $klarnaPaymentOrder->id_internal
        );

        $orderStateHistoryListValues = Collection::from($orderStateHistoryList)
            ->map(function (array $item) {
                return (int) $item['id_order_state'];
            })
            ->toArray();

        $mappedOrderStateValues = $this->orderStatusProvider->getMappedOrderStatusValues();

        if (!CompareUtility::inArray((int) $mappedOrderStateValues[$externalOrderStatus], $orderStateHistoryListValues, true)) {
            return true;
        }

        return false;
    }
}
