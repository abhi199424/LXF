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

use KlarnaPayment\Module\Api\Enum\OrderStatus;
use KlarnaPayment\Module\Api\Models\Order;
use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Order\Action\RetrieveOrderAction;
use KlarnaPayment\Module\Core\Order\DTO\RetrieveOrderRequestData;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotCancelOrder;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotCaptureOrder;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotCreateRefund;
use KlarnaPayment\Module\Core\Order\Handler\Status\InternalOrderStatusHandler;
use KlarnaPayment\Module\Core\Order\Processor\CancelOrderProcessor;
use KlarnaPayment\Module\Core\Order\Processor\CaptureOrderProcessor;
use KlarnaPayment\Module\Core\Order\Processor\RefundOrderProcessor;
use KlarnaPayment\Module\Core\Order\Repository\KlarnaPaymentOrderRepositoryInterface;
use KlarnaPayment\Module\Core\Order\Verification\CanChangeOrderStatus;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\PrestaShopCookie;
use KlarnaPayment\Module\Infrastructure\Adapter\Tools;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;
use KlarnaPayment\Module\Infrastructure\Controller\AbstractAdminController as ModuleAdminController;
use KlarnaPayment\Module\Infrastructure\Enum\PermissionType;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Notification\Handler\NotificationHandlerInterface;
use KlarnaPayment\Module\Infrastructure\Notification\Notifications\ErrorNotification;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;
use KlarnaPayment\Module\Infrastructure\Utility\MoneyCalculator;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminKlarnaPaymentOrderController extends ModuleAdminController
{
    const FILE_NAME = 'AdminKlarnaPaymentOrderController';

    /** @var KlarnaPayment */
    public $module;

    public function __construct()
    {
        $this->bootstrap = true;

        parent::__construct();
    }

    /**
     * @throws CouldNotCaptureOrder
     * @throws KlarnaPaymentException
     */
    public function postProcess(): bool
    {
        if (!$this->ensureHasPermissions([PermissionType::EDIT, PermissionType::VIEW])) {
            return false;
        }

        /* @var Tools $tools */
        $tools = $this->module->getService(Tools::class);

        /* @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        /* @var PrestaShopCookie $cookie */
        $cookie = $this->module->getService(PrestaShopCookie::class);

        /** @var NotificationHandlerInterface $notificationHandler */
        $notificationHandler = $this->module->getService(NotificationHandlerInterface::class);

        /** @var MoneyCalculator $moneyCalculator */
        $moneyCalculator = $this->module->getService(MoneyCalculator::class);

        $internalOrderId = $tools->getValueAsInt('orderId');

        $errors = json_decode($cookie->get(Config::KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_ERRORS), false) ?: [];

        $externalOrderId = $this->getExternalOrderId($internalOrderId);

        if (!$externalOrderId) {
            $notificationHandler->addNotification(self::FILE_NAME, ErrorNotification::create(
                $this->module->l('Failed to retrieve external order ID', self::FILE_NAME)
            ));

            $this->redirectToOrderController('AdminOrders', $internalOrderId);
        }

        $internalOrder = new \Order($internalOrderId);
        $this->context->currency = new Currency($internalOrder->id_currency);

        $order = $this->retrieveOrder($externalOrderId);

        if (!$order) {
            $notificationHandler->addNotification(self::FILE_NAME, ErrorNotification::create(
                $this->module->l('Failed to retrieve Klarna order', self::FILE_NAME)
            ));

            $this->redirectToOrderController('AdminOrders', $internalOrderId);
        }

        if ($tools->isSubmit('capture-order-amount')) {
            try {
                $capturedAmount = $moneyCalculator->calculateIntoInteger((float) $tools->getValue('capture_amount'));

                $this->captureOrder($order, $internalOrderId, $capturedAmount);
            } catch (\Throwable $exception) {
                $errors[$internalOrderId] = $this->module->l('Capture order amount failed. View error logs for more information.', self::FILE_NAME);

                $cookie->set(Config::KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_ERRORS, json_encode($errors));

                $logger->error('Failed to capture order amount.', [
                    'context' => [
                        'id_internal' => $internalOrderId,
                        'id_external' => $externalOrderId,
                        'amount' => $capturedAmount ?? null,
                    ],
                    'exceptions' => ExceptionUtility::getExceptions($exception),
                ]);
            }
        }

        if ($tools->isSubmit('capture-order-lines')) {
            try {
                $orderLineIds = $tools->getValue('order_line_ids');

                $orderLineIds = !empty($orderLineIds) ? $orderLineIds : [];

                $this->captureOrder($order, $internalOrderId, 0, $orderLineIds);
            } catch (\Throwable $exception) {
                $errors[$internalOrderId] = $this->module->l('Capture order lines failed. View error logs for more information.', self::FILE_NAME);

                $cookie->set(Config::KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_ERRORS, json_encode($errors));

                $logger->error('Failed to capture order lines.', [
                    'context' => [
                        'id_internal' => $internalOrderId,
                        'id_external' => $externalOrderId,
                        'order_lines' => isset($orderLineIds) ? json_encode($orderLineIds) : '',
                    ],
                    'exceptions' => ExceptionUtility::getExceptions($exception),
                ]);
            }
        }

        if ($tools->isSubmit('refund-order-amount')) {
            try {
                $refundedAmount = $moneyCalculator->calculateIntoInteger((float) $tools->getValue('refund_amount'));

                $this->refundOrder($order, $internalOrderId, $refundedAmount);
            } catch (CouldNotCreateRefund $exception) {
                $errors[$internalOrderId] = $this->module->l('Refund order amount failed. View error logs for more information.', self::FILE_NAME);

                $cookie->set(Config::KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_ERRORS, json_encode($errors));

                $logger->error('Failed to refund order amount.', [
                    'context' => [
                        'id_internal' => $internalOrderId,
                        'id_external' => $externalOrderId,
                        'amount' => $refundedAmount,
                    ],
                    'exceptions' => ExceptionUtility::getExceptions($exception),
                ]);
            }
        }

        if ($tools->isSubmit('refund-order-lines')) {
            try {
                $orderLineIds = !empty($tools->getValue('order_line_ids')) ? $tools->getValue('order_line_ids') : [];

                $this->refundOrder($order, $internalOrderId, 0, $orderLineIds);
            } catch (CouldNotCreateRefund $exception) {
                $errors[$internalOrderId] = $this->module->l('Refund order lines failed. View error logs for more information.', self::FILE_NAME);

                $cookie->set(Config::KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_ERRORS, json_encode($errors));

                $logger->error('Failed to refund order lines.', [
                    'context' => [
                        'id_internal' => $internalOrderId,
                        'id_external' => $externalOrderId,
                        'order_lines' => json_encode($orderLineIds),
                    ],
                    'exceptions' => ExceptionUtility::getExceptions($exception),
                ]);
            }
        }

        if ($tools->isSubmit('cancel-order')) {
            try {
                $this->cancelOrder($internalOrderId, $order);
            } catch (CouldNotCancelOrder $exception) {
                $errors[$internalOrderId] = $this->module->l('Failed to cancel order, check logs for more information.', self::FILE_NAME);

                $cookie->set(Config::KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_ERRORS, json_encode($errors));

                $logger->error('Failed to cancel order.', [
                    'context' => [
                        'id_internal' => $internalOrderId,
                        'id_external' => $externalOrderId,
                    ],
                    'exceptions' => ExceptionUtility::getExceptions($exception),
                ]);
            }
        }

        $this->redirectToOrderController('AdminOrders', $internalOrderId);

        return true;
    }

    /**
     * @param int $internalOrderId
     *
     * @return ?string
     */
    private function getExternalOrderId(int $internalOrderId): ?string
    {
        /** @var KlarnaPaymentOrderRepositoryInterface $klarnaPaymentOrderRepository */
        $klarnaPaymentOrderRepository = $this->module->getService(KlarnaPaymentOrderRepositoryInterface::class);

        /** @var GlobalShopContextInterface $globalShopContext */
        $globalShopContext = $this->module->getService(GlobalShopContextInterface::class);

        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        /** @var KlarnaPaymentOrder|null $klarnaPaymentOrder */
        $klarnaPaymentOrder = $klarnaPaymentOrderRepository->findOneBy([
            'id_internal' => $internalOrderId,
            'id_shop' => $globalShopContext->getShopId(),
        ]);

        if (!$klarnaPaymentOrder) {
            $logger->error('Failed to find order mapping.', [
                'context' => [
                    'id_internal' => $internalOrderId,
                    'id_shop' => $globalShopContext->getShopId(),
                ],
                'exceptions' => [],
            ]);

            return null;
        }

        return $klarnaPaymentOrder->id_external;
    }

    private function retrieveOrder(string $externalOrderId): ?Order
    {
        /** @var RetrieveOrderAction $retrieveOrderAction */
        $retrieveOrderAction = $this->module->getService(RetrieveOrderAction::class);

        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        try {
            $retrieveOrderResponse = $retrieveOrderAction->run(RetrieveOrderRequestData::create($externalOrderId));

            return $retrieveOrderResponse ? $retrieveOrderResponse->getOrder() : null;
        } catch (KlarnaPaymentException $exception) {
            $logger->error('Failed to retrieve order', [
                'context' => [
                    'id_external' => $externalOrderId,
                ],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            return null;
        }
    }

    /**
     * @throws KlarnaPaymentException
     */
    private function captureOrder(Order $order, int $internalOrderId, int $capturedAmount, array $orderLineIds = []): void
    {
        /** @var PrestaShopCookie $cookie */
        $cookie = $this->module->getService(PrestaShopCookie::class);

        /** @var CaptureOrderProcessor $captureOrderProcessor */
        $captureOrderProcessor = $this->module->getService(CaptureOrderProcessor::class);

        /** @var InternalOrderStatusHandler $internalOrderStatusHandler */
        $internalOrderStatusHandler = $this->module->getService(InternalOrderStatusHandler::class);

        /** @var CanChangeOrderStatus $canChangeOrderStatus */
        $canChangeOrderStatus = $this->module->getService(CanChangeOrderStatus::class);

        if ($capturedAmount <= 0 && empty($orderLineIds)) {
            throw CouldNotCaptureOrder::invalidCaptureRequest($capturedAmount);
        }

        $captureOrderProcessor->processAction($order, $capturedAmount, $orderLineIds);

        $order = $this->retrieveOrder($order->getOrderId());

        if ($order->getStatus() === strtoupper(OrderStatus::CAPTURED)) {
            $internalOrderStatusHandler->handle($internalOrderId, OrderStatus::CAPTURED);
        } else {
            if ($canChangeOrderStatus->verify($order)) {
                $internalOrderStatusHandler->handle($internalOrderId, OrderStatus::PARTIALLY_CAPTURED);
            }
        }

        $cookie->set(Config::KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_CAPTURED, true);
    }

    /**
     * @throws KlarnaPaymentException
     */
    private function refundOrder(Order $order, int $internalOrderId, int $refundedAmount, array $orderLineIds = []): void
    {
        /* @var PrestaShopCookie $cookie */
        $cookie = $this->module->getService(PrestaShopCookie::class);

        /** @var RefundOrderProcessor $refundOrderProcessor */
        $refundOrderProcessor = $this->module->getService(RefundOrderProcessor::class);

        /** @var InternalOrderStatusHandler $internalOrderStatusHandler */
        $internalOrderStatusHandler = $this->module->getService(InternalOrderStatusHandler::class);

        if ($refundedAmount <= 0 && empty($orderLineIds)) {
            throw CouldNotCreateRefund::invalidRefundRequest($refundedAmount);
        }

        $refundOrderProcessor->processAction($order, $refundedAmount, $orderLineIds);

        $order = $this->retrieveOrder($order->getOrderId());

        /*
         * NOTE: status via API doesn't change after full refund
         */
        if ($order->getOrderAmount() === $order->getRefundedAmount()) {
            $internalOrderStatusHandler->handle($internalOrderId, OrderStatus::FULLY_REFUNDED);
        }

        $cookie->set(Config::KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_REFUNDED, true);
    }

    private function redirectToOrderController(string $controller, int $orderId): void
    {
        /** @var Tools $tools */
        $tools = $this->module->getService(Tools::class);

        /** @var Context $context */
        $context = $this->module->getService(Context::class);

        $tools->redirectAdmin(
            $context->getAdminLink($controller, ['id_order' => $orderId, 'vieworder' => 1])
        );
    }

    /**
     * @throws KlarnaPaymentException
     */
    private function cancelOrder(int $internalOrderId, Order $order): void
    {
        /** @var PrestaShopCookie $cookie */
        $cookie = $this->module->getService(PrestaShopCookie::class);

        /** @var CancelOrderProcessor $cancelOrderProcessor */
        $cancelOrderProcessor = $this->module->getService(CancelOrderProcessor::class);

        /** @var InternalOrderStatusHandler $internalOrderStatusHandler */
        $internalOrderStatusHandler = $this->module->getService(InternalOrderStatusHandler::class);

        $cancelOrderProcessor->processAction($order);
        $internalOrderStatusHandler->handle($internalOrderId, OrderStatus::CANCELLED);

        $cookie->set(Config::KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_CANCELED, true);
    }
}
