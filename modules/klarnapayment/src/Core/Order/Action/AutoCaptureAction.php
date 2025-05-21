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

use KlarnaPayment\Module\Api\Enum\OrderStatus;
use KlarnaPayment\Module\Api\Models\Order as ExternalOrder;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotAutoCapture;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotVerifyOrderAction;
use KlarnaPayment\Module\Core\Order\Handler\Status\InternalOrderStatusHandler;
use KlarnaPayment\Module\Core\Order\Processor\CaptureOrderProcessor;
use KlarnaPayment\Module\Core\Order\Verification\CanAutoCaptureOrder;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Currency;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Utility\NumberUtility;
use Order as InternalOrder;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AutoCaptureAction
{
    /** @var LoggerInterface */
    private $logger;
    /** @var CanAutoCaptureOrder */
    private $canAutoCaptureOrder;
    /** @var CaptureOrderProcessor */
    private $captureOrderProcessor;
    /** @var Configuration */
    private $configuration;
    /** @var InternalOrderStatusHandler */
    private $internalOrderStatusHandler;
    /** @var Currency */
    private $currency;

    public function __construct(
        LoggerInterface $logger,
        CanAutoCaptureOrder $canAutoCaptureOrder,
        CaptureOrderProcessor $captureOrderProcessor,
        Configuration $configuration,
        InternalOrderStatusHandler $internalOrderStatusHandler,
        Currency $currency
    ) {
        $this->logger = $logger;
        $this->canAutoCaptureOrder = $canAutoCaptureOrder;
        $this->captureOrderProcessor = $captureOrderProcessor;
        $this->configuration = $configuration;
        $this->internalOrderStatusHandler = $internalOrderStatusHandler;
        $this->currency = $currency;
    }

    /**
     * @throws CouldNotAutoCapture
     */
    public function run(ExternalOrder $externalOrder, InternalOrder $internalOrder): void
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        try {
            $this->canAutoCaptureOrder->verify($internalOrder);
        } catch (CouldNotVerifyOrderAction $exception) {
            return;
        }

        $orderCaptures = !empty($externalOrder->getCaptures()) ? $externalOrder->getCaptures() : [];
        $capturedAmount = 0;

        foreach ($orderCaptures as $orderCapture) {
            $capturedAmount = (int) NumberUtility::add(
                $capturedAmount,
                $orderCapture->getCapturedAmount(),
                0,
                (int) $this->configuration->get('PS_PRICE_ROUND_MODE')
            );
        }

        $currencyIso = $this->currency->getIsoCodeById((int) $internalOrder->id_currency);

        try {
            $this->captureOrderProcessor->processAction(
                $externalOrder,
                NumberUtility::minus(
                    $externalOrder->getOrderAmount(),
                    $capturedAmount,
                    0,
                    (int) $this->configuration->get('PS_PRICE_ROUND_MODE')),
                [],
                $currencyIso
            );

            $this->internalOrderStatusHandler->handle((int) $internalOrder->id, OrderStatus::CAPTURED);
        } catch (KlarnaPaymentException $exception) {
            throw CouldNotAutoCapture::failedToAutoCapture($exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));
    }
}
