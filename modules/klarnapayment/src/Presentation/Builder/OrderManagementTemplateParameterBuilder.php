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

namespace KlarnaPayment\Module\Presentation\Builder;

use KlarnaPayment\Module\Api\Models\Capture;
use KlarnaPayment\Module\Api\Models\Order as ExternalOrder;
use KlarnaPayment\Module\Api\Models\OrderLine;
use KlarnaPayment\Module\Api\Models\Refund;
use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Merchant\Provider\MerchantIdProvider;
use KlarnaPayment\Module\Core\Order\Action\RetrieveOrderAction;
use KlarnaPayment\Module\Core\Order\DTO\RetrieveOrderRequestData;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotVerifyOrderAction;
use KlarnaPayment\Module\Core\Order\Repository\KlarnaPaymentOrderRepositoryInterface;
use KlarnaPayment\Module\Core\Order\Verification\CanCancelOrder;
use KlarnaPayment\Module\Core\Order\Verification\CanCaptureOrder;
use KlarnaPayment\Module\Core\Order\Verification\CanRefundOrder;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Adapter\Tools;
use KlarnaPayment\Module\Infrastructure\Bootstrap\ModuleTabs;
use KlarnaPayment\Module\Infrastructure\Context\GlobalShopContextInterface;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Provider\ApplicationContextProvider;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;
use KlarnaPayment\Module\Infrastructure\Utility\MoneyCalculator;
use KlarnaPayment\Module\Infrastructure\Utility\VersionUtility;
use Order as InternalOrder;

if (!defined('_PS_VERSION_')) {
    exit;
}

class OrderManagementTemplateParameterBuilder implements TemplateParameterBuilderInterface
{
    /** @var InternalOrder */
    private $order;
    private $context;
    private $tools;
    /** @var KlarnaPaymentOrderRepositoryInterface */
    private $klarnaPaymentOrderRepository;
    /** @var LoggerInterface */
    private $logger;
    /** @var GlobalShopContextInterface */
    private $globalShopContext;
    /** @var \KlarnaPayment|null */
    private $module;
    /** @var CanCaptureOrder */
    private $canCaptureOrder;
    /** @var CanRefundOrder */
    private $canRefundOrder;
    /** @var CanCancelOrder */
    private $canCancelOrder;
    /** @var ApplicationContextProvider */
    private $applicationContextProvider;
    /** @var MoneyCalculator */
    private $moneyCalculator;
    /** @var MerchantIdProvider */
    private $merchantIdProvider;

    public function __construct(
        Context $context,
        Tools $tools,
        KlarnaPaymentOrderRepositoryInterface $klarnaPaymentOrderRepository,
        LoggerInterface $logger,
        GlobalShopContextInterface $globalShopContext,
        ModuleFactory $module,
        CanCaptureOrder $canCaptureOrder,
        CanRefundOrder $canRefundOrder,
        CanCancelOrder $canCancelOrder,
        ApplicationContextProvider $applicationContextProvider,
        MoneyCalculator $moneyCalculator,
        MerchantIdProvider $merchantIdProvider
    ) {
        $this->context = $context;
        $this->tools = $tools;
        $this->klarnaPaymentOrderRepository = $klarnaPaymentOrderRepository;
        $this->logger = $logger;
        $this->globalShopContext = $globalShopContext;
        $this->module = $module->getModule();
        $this->canCaptureOrder = $canCaptureOrder;
        $this->canRefundOrder = $canRefundOrder;
        $this->canCancelOrder = $canCancelOrder;
        $this->applicationContextProvider = $applicationContextProvider;
        $this->moneyCalculator = $moneyCalculator;
        $this->merchantIdProvider = $merchantIdProvider;
    }

    public function setOrder(InternalOrder $order): OrderManagementTemplateParameterBuilder
    {
        $this->order = $order;

        return $this;
    }

    public function buildParams(): array
    {
        $currency = new \Currency($this->order->id_currency);
        $currencyIso = $currency->iso_code;

        $env = $this->applicationContextProvider->get($currencyIso)->getIsProduction() ? Config::KLARNA_ENVIRONMENT_PRODUCTION : Config::KLARNA_ENVIRONMENT_SANDBOX;

        if (!$this->applicationContextProvider->get($currencyIso)->isValid()) {
            return [];
        }

        if ($this->order->module !== $this->module->name) {
            return [];
        }

        if (empty($this->order)) {
            return [];
        }

        $orderId = (int) $this->order->id;

        $klarnaPaymentOrder = $this->getKlarnaPaymentOrder($orderId);

        if (empty($klarnaPaymentOrder)) {
            return [];
        }

        $externalOrderId = $klarnaPaymentOrder->id_external;

        $order = $this->retrieveOrder($externalOrderId, $currencyIso);

        if ($order === null) {
            return [
                'id' => $orderId,
                'orderReference' => $klarnaPaymentOrder->klarna_reference,
                'orderUrl' => sprintf(
                    '%s%s/orders/%s',
                    $this->merchantIdProvider->getMerchantId(),
                    $klarnaPaymentOrder->id_klarna_merchant,
                    $externalOrderId
                ),
            ];
        }

        if ($klarnaPaymentOrder->klarna_reference !== $order->getKlarnaReference()) {
            $klarnaPaymentOrder->klarna_reference = $order->getKlarnaReference();
            $klarnaPaymentOrder->save();
        }

        $paymentMethod = $order->getInitialPaymentMethod() ?
            $order->getInitialPaymentMethod()->getDescription() : 'Klarna';

        try {
            $canCapture = $this->canCaptureOrder->verify($order);
        } catch (CouldNotVerifyOrderAction $exception) {
            $canCapture = false;
        }

        try {
            $canRefund = $this->canRefundOrder->verify($order);
        } catch (CouldNotVerifyOrderAction $exception) {
            $canRefund = false;
        }

        try {
            $canCancel = $this->canCancelOrder->verify($order);
        } catch (CouldNotVerifyOrderAction $exception) {
            $canCancel = false;
        }

        $remainingCaptureAmount = $this->moneyCalculator->calculateIntoFloat(
            $order->getOrderAmount() - $order->getCapturedAmount()
        );

        $remainingRefundAmount = $this->moneyCalculator->calculateIntoFloat(
            $order->getCapturedAmount() - $order->getRefundedAmount()
        );

        $capturedAmount = $this->moneyCalculator->calculateIntoFloat(
            $order->getCapturedAmount()
        );

        $refundedAmount = $this->moneyCalculator->calculateIntoFloat(
            $order->getRefundedAmount()
        );

        $orderCurrency = $order->getPurchaseCurrency();

        return [
            'id' => $orderId,
            'orderReference' => $order->getKlarnaReference(),
            'paymentMethod' => $paymentMethod,
            'orderUrl' => $this->buildOrderUrl($this->merchantIdProvider->getMerchantId(), $externalOrderId, $env),
            'canInteract' => $canRefund || $canCapture || $canCancel,
            'canRefund' => $canRefund,
            'canCapture' => $canCapture,
            'canCancel' => $canCancel,
            'action' => $this->context->getAdminLink(
                ModuleTabs::ORDER_MODULE_TAB_CONTROLLER_NAME,
                [
                    'orderId' => $orderId,
                ]
            ),
            'productsToCapture' => $this->getOrderProductsToCapture($order),
            'remainingCaptureAmount' => $remainingCaptureAmount,
            'remainingCaptureAmountFormatted' => $this->formatPrice($remainingCaptureAmount, $orderCurrency),
            'remainingRefundAmount' => $remainingRefundAmount,
            'remainingRefundAmountFormatted' => $this->formatPrice($remainingRefundAmount, $orderCurrency),
            'capturedAmountFormatted' => $this->formatPrice($capturedAmount, $orderCurrency),
            'refundedAmountFormatted' => $this->formatPrice($refundedAmount, $orderCurrency),
            'captures' => $this->getCaptureHistory($order->getCaptures(), $order),
            'refunds' => $this->getRefundHistory($order->getRefunds(), $orderCurrency),
            'isZeroComputingPrecision' => $this->context->getComputingPrecision() === 0,
        ];
    }

    /**
     * @param int $orderId
     *
     * @return ?\KlarnaPaymentOrder
     */
    private function getKlarnaPaymentOrder(int $orderId): ?\KlarnaPaymentOrder
    {
        /** @var \KlarnaPaymentOrder|null $klarnaPaymentOrder */
        $klarnaPaymentOrder = $this->klarnaPaymentOrderRepository->findOneBy([
            'id_internal' => $orderId,
            'id_shop' => $this->globalShopContext->getShopId(),
        ]);

        if (!$klarnaPaymentOrder) {
            $this->logger->error('Failed to find order mapping.', [
                'context' => [
                    'id_internal' => $orderId,
                    'id_shop' => $this->globalShopContext->getShopId(),
                ],
                'exceptions' => [],
            ]);

            return null;
        }

        return $klarnaPaymentOrder;
    }

    private function retrieveOrder(string $externalOrderId, string $currencyIso): ?ExternalOrder
    {
        /** @var RetrieveOrderAction $retrieveOrderAction */
        $retrieveOrderAction = $this->module->getService(RetrieveOrderAction::class);

        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        try {
            $retrieveOrderResponse = $retrieveOrderAction->run(RetrieveOrderRequestData::create($externalOrderId, $currencyIso));

            return $retrieveOrderResponse->getOrder();
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

    private function getOrderProductsToCapture(?ExternalOrder $order): array
    {
        $capturedOrderLineIds = [];
        $orderCurrency = $order->getPurchaseCurrency();

        foreach ($order->getCaptures() as $orderCapture) {
            foreach ($orderCapture->getOrderLines() as $orderLine) {
                $capturedOrderLineIds[] = $orderLine->getMerchantData();
            }
        }

        $orderLinesToCapture = [];

        foreach ($order->getOrderLines() as $orderLine) {
            if (in_array($orderLine->getMerchantData(), $capturedOrderLineIds, true)) {
                continue;
            }

            // NOTE: Hide decimal precision adjustment in order management
            if ($orderLine->getMerchantData() === Config::DECIMAL_PRECISION_ADJUSTMENT) {
                continue;
            }

            $orderLineToCapture = [];

            $orderLineToCapture['id_order_line'] = $orderLine->getMerchantData();
            $orderLineToCapture['product_quantity'] = (int) $orderLine->getQuantity();
            $orderLineToCapture['product_name'] = $orderLine->getName();
            $orderLineToCapture['total_price'] = $this->moneyCalculator->calculateIntoFloat($orderLine->getTotalAmount());

            $orderLineToCapture['total_price_formatted'] = $this->formatPrice(
                $this->moneyCalculator->calculateIntoFloat($orderLine->getTotalAmount()),
                $orderCurrency
            );

            $orderLinesToCapture[] = $orderLineToCapture;
        }

        return $orderLinesToCapture;
    }

    private function getCapturedProductsOrderLines(array $capturedOrderLines, ExternalOrder $order): array
    {
        $capturedProductsOrderLineIds = [];
        $orderCurrency = $order->getPurchaseCurrency();

        /** @var OrderLine $capturedOrderLine */
        foreach ($capturedOrderLines as $capturedOrderLine) {
            $capturedProductsOrderLineIds[] = $capturedOrderLine->getMerchantData();
        }

        $capturedProductsOrderLines = [];

        foreach ($order->getOrderLines() as $orderLine) {
            if (!in_array($orderLine->getMerchantData(), $capturedProductsOrderLineIds, true)) {
                continue;
            }

            $capturedProductsOrderLine = [];

            $capturedProductsOrderLine['id_order_line'] = $orderLine->getMerchantData();
            $capturedProductsOrderLine['product_quantity'] = (int) $orderLine->getQuantity();
            $capturedProductsOrderLine['product_name'] = $orderLine->getName();
            $capturedProductsOrderLine['total_price'] = $this->moneyCalculator->calculateIntoFloat($orderLine->getTotalAmount());

            $capturedProductsOrderLine['total_price_formatted'] = $this->formatPrice(
                $this->moneyCalculator->calculateIntoFloat($orderLine->getTotalAmount()),
                $orderCurrency
            );

            $capturedProductsOrderLines[] = $capturedProductsOrderLine;
        }

        return $capturedProductsOrderLines;
    }

    /**
     * @param array<Capture>|null $captures
     */
    private function getCaptureHistory(?array $captures, ExternalOrder $order): array
    {
        $result = [];
        $orderCurrency = $order->getPurchaseCurrency();

        foreach ($captures as $capture) {
            $mappedCapture = [];

            $mappedCapture['capture_id'] = $capture->getCaptureId();

            $mappedCapture['captured_amount'] = $this->formatPrice(
                $this->moneyCalculator->calculateIntoFloat($capture->getCapturedAmount()),
                $orderCurrency
            );

            $mappedCapture['captured_products'] = $this->getCapturedProductsOrderLines($capture->getOrderLines(), $order);

            $mappedCapture['captured_by_amount'] = empty($capture->getOrderLines()) && $capture->getCapturedAmount() > 0;

            foreach ($capture->getOrderLines() as $orderLine) {
                $mappedOrderLine = [];

                $mappedOrderLine['id_order_line'] = $orderLine->getMerchantData();
                $mappedOrderLine['product_quantity'] = (int) $orderLine->getQuantity();
                $mappedOrderLine['product_name'] = $orderLine->getName();

                $mappedOrderLine['total_price'] = $this->formatPrice(
                    $this->moneyCalculator->calculateIntoFloat($orderLine->getTotalAmount()),
                    $orderCurrency
                );

                $mappedOrderLine['total_price_formatted'] = $this->formatPrice(
                    $this->moneyCalculator->calculateIntoFloat($orderLine->getTotalAmount()),
                    $orderCurrency
                );

                $mappedCapture['order_lines'][] = $mappedOrderLine;
            }

            $result[] = $mappedCapture;
        }

        return $result;
    }

    /**
     * @param array<Refund>|null $refunds
     */
    private function getRefundHistory(?array $refunds, string $orderCurrency): array
    {
        $result = [];

        foreach ($refunds as $refund) {
            $mappedCapture = [];

            $mappedCapture['refunded_amount'] = $this->formatPrice(
                $this->moneyCalculator->calculateIntoFloat($refund->getRefundedAmount()),
                $orderCurrency
            );

            foreach ($refund->getOrderLines() as $orderLine) {
                $mappedOrderLine = [];

                $mappedOrderLine['id_order_line'] = $orderLine->getMerchantData();
                $mappedOrderLine['product_quantity'] = (int) $orderLine->getQuantity();
                $mappedOrderLine['product_name'] = $orderLine->getName();

                $mappedOrderLine['total_price'] = $this->formatPrice(
                    $this->moneyCalculator->calculateIntoFloat($orderLine->getTotalAmount()),
                    $orderCurrency
                );

                $mappedOrderLine['total_price_formatted'] = $this->formatPrice(
                    $this->moneyCalculator->calculateIntoFloat($orderLine->getTotalAmount()),
                    $orderCurrency
                );

                $mappedCapture['order_lines'][] = $mappedOrderLine;
            }

            $result[] = $mappedCapture;
        }

        return $result;
    }

    private function formatPrice(float $price, string $orderCurrency): string
    {
        if (VersionUtility::isPsVersionLessThan('1.7.6.0')) {
            return $this->tools->displayPrice(
                $price,
                $orderCurrency
            );
        }

        return $this->context->formatPrice(
            $price,
            $orderCurrency
        );
    }

    public function buildOrderUrl(string $mid, string $externalOrderId, string $env): string
    {
        if (strpos($mid, '_') === false) {
            return sprintf(
                '%s',
                Config::KLARNA_PAYMENT_MERCHANT_PORTAL_URL[$env]
            );
        }

        return sprintf(
            '%s%s/orders/%s',
            Config::KLARNA_PAYMENT_MERCHANT_URL[$env],
            $mid,
            $externalOrderId
        );
    }
}
