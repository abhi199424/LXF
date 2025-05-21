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

use KlarnaPayment\Module\Api\Exception\ApiException;
use KlarnaPayment\Module\Core\Order\Api\Repository\OrderApiRepositoryInterface;
use KlarnaPayment\Module\Core\Order\DTO\CancelOrderRequestData;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotCancelOrder;
use KlarnaPayment\Module\Core\Order\Provider\CancelOrderRequestProvider;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CancelOrderAction
{
    private $logger;
    private $orderApiRepository;
    private $cancelOrderRequestProvider;

    public function __construct(
        LoggerInterface $logger,
        OrderApiRepositoryInterface $orderApiRepository,
        CancelOrderRequestProvider $cancelOrderRequestProvider
    ) {
        $this->logger = $logger;
        $this->orderApiRepository = $orderApiRepository;
        $this->cancelOrderRequestProvider = $cancelOrderRequestProvider;
    }

    /**
     * @throws KlarnaPaymentException
     */
    public function run(CancelOrderRequestData $cancelOrderRequestData): void
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        try {
            $request = $this->cancelOrderRequestProvider->get($cancelOrderRequestData);

            $this->orderApiRepository->cancelOrder($request);
        } catch (ApiException $exception) {
            throw CouldNotCancelOrder::failedToGetSuccessfulApiResponse($exception);
        } catch (\Exception $exception) {
            throw CouldNotCancelOrder::unknownError($exception);
        } catch (\Throwable $exception) {
            throw CouldNotCancelOrder::failedToCreateApiRequest($exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));
    }
}
