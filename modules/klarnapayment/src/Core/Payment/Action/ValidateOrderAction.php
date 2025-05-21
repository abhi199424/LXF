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

namespace KlarnaPayment\Module\Core\Payment\Action;

use KlarnaPayment\Module\Core\Payment\DTO\ValidateOrderData;
use KlarnaPayment\Module\Core\Payment\Exception\CouldNotValidateOrder;
use KlarnaPayment\Module\Core\Shared\Repository\CustomerRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\OrderRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Config\OrderStatusConfig;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ValidateOrderAction
{
    private $logger;
    private $module;
    private $configuration;
    private $globalShopContext;
    private $customerRepository;
    private $orderRepository;

    public function __construct(
        ModuleFactory $moduleFactory,
        LoggerInterface $logger,
        Configuration $configuration,
        GlobalShopContextInterface $globalShopContext,
        CustomerRepositoryInterface $customerRepository,
        OrderRepositoryInterface $orderRepository
    ) {
        $this->module = $moduleFactory->getModule();
        $this->logger = $logger;
        $this->configuration = $configuration;
        $this->globalShopContext = $globalShopContext;
        $this->customerRepository = $customerRepository;
        $this->orderRepository = $orderRepository;
    }

    /**
     * @throws CouldNotValidateOrder
     */
    public function run(ValidateOrderData $data): ?\Order
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        try {
            /** @var \Customer|null $customer */
            $customer = $this->customerRepository->findOneBy([
                'id_customer' => $data->getCustomerId(),
                'id_shop' => $this->globalShopContext->getShopId(),
            ]);
        } catch (\Exception $exception) {
            throw CouldNotValidateOrder::unknownError($exception);
        }

        if (!$customer) {
            throw CouldNotValidateOrder::failedToFindCustomer($data->getCustomerId());
        }

        try {
            $this->module->validateOrder(
                $data->getCartId(),
                $this->configuration->getByEnvironment(
                    OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_PROCESSING,
                    $this->globalShopContext->getShopId()
                ),
                $data->getOrderTotal(),
                $data->getPaymentMethod(),
                null,
                ['transaction_id' => $data->getExternalOrderId()],
                null,
                false,
                (string) $customer->secure_key
            );
        } catch (\Exception $exception) {
            throw CouldNotValidateOrder::unknownError($exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));

        /** @var ?\Order $order */
        $order = $this->orderRepository->findOneBy([
            'id_cart' => $data->getCartId(),
            'id_shop' => $this->globalShopContext->getShopId(),
        ]);

        return $order;
    }
}
