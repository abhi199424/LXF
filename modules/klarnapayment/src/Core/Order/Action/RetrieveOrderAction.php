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
use KlarnaPayment\Module\Api\Responses\RetrieveOrderResponse;
use KlarnaPayment\Module\Core\Order\Api\Repository\OrderApiRepositoryInterface;
use KlarnaPayment\Module\Core\Order\DTO\RetrieveOrderRequestData;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotRetrieveOrder;
use KlarnaPayment\Module\Core\Order\Provider\RetrieveOrderRequestProvider;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class RetrieveOrderAction
{
    private $logger;
    private $orderApiRepository;
    private $retrieveOrderRequestProvider;

    public function __construct(
        LoggerInterface $logger,
        OrderApiRepositoryInterface $orderApiRepository,
        RetrieveOrderRequestProvider $retrieveOrderRequestProvider
    ) {
        $this->logger = $logger;
        $this->orderApiRepository = $orderApiRepository;
        $this->retrieveOrderRequestProvider = $retrieveOrderRequestProvider;
    }

    /**
     * @throws KlarnaPaymentException
     */
    public function run(RetrieveOrderRequestData $retrieveOrderRequestData): ?RetrieveOrderResponse
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        try {
            $request = $this->retrieveOrderRequestProvider->get($retrieveOrderRequestData);

            $result = $this->orderApiRepository->retrieveOrder($request, $retrieveOrderRequestData->getCurrencyIso());
        } catch (ApiException $exception) {
            throw CouldNotRetrieveOrder::failedToGetSuccessfulApiResponse($exception);
        } catch (\Exception $exception) {
            throw CouldNotRetrieveOrder::unknownError($exception);
        } catch (\Throwable $exception) {
            throw CouldNotRetrieveOrder::failedToCreateApiRequest($exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));

        return $result;
    }
}
