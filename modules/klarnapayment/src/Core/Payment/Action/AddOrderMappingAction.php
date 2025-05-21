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

use KlarnaPayment\Module\Api\Models\Order;
use KlarnaPayment\Module\Core\Merchant\Provider\MerchantIdProvider;
use KlarnaPayment\Module\Core\Payment\Exception\CouldNotAddOrderMapping;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;
use KlarnaPayment\Module\Infrastructure\EntityManager\EntityManagerInterface;
use KlarnaPayment\Module\Infrastructure\EntityManager\ObjectModelUnitOfWork;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AddOrderMappingAction
{
    private $logger;
    private $globalShopContext;
    private $entityManager;
    private $merchantIdProvider;

    public function __construct(
        LoggerInterface $logger,
        GlobalShopContextInterface $globalShopContext,
        EntityManagerInterface $entityManager,
        MerchantIdProvider $merchantIdProvider
    ) {
        $this->logger = $logger;
        $this->globalShopContext = $globalShopContext;
        $this->entityManager = $entityManager;
        $this->merchantIdProvider = $merchantIdProvider;
    }

    /**
     * @throws CouldNotAddOrderMapping
     */
    public function run($internalOrderId, Order $externalOrder, string $authorizationToken)
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        try {
            $order = new \KlarnaPaymentOrder();
            $order->id_internal = $internalOrderId;
            $order->id_external = $externalOrder->getOrderId();
            $order->id_shop = $this->globalShopContext->getShopId();
            $order->id_klarna_merchant = $this->merchantIdProvider->getMerchantId();
            $order->klarna_reference = $externalOrder->getKlarnaReference();
            $order->authorization_token = $authorizationToken;

            $this->entityManager->persist($order, ObjectModelUnitOfWork::UNIT_OF_WORK_SAVE);
            $this->entityManager->flush();
        } catch (\Exception $exception) {
            throw CouldNotAddOrderMapping::unknownError($exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));
    }
}
