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
use KlarnaPayment\Module\Core\Order\DTO\CreateRefundRequestData;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotCreateRefund;
use KlarnaPayment\Module\Core\Order\Provider\CreateRefundRequestProvider;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CreateRefundAction
{
    private $logger;
    private $orderApiRepository;
    private $createRefundRequestProvider;

    public function __construct(
        LoggerInterface $logger,
        OrderApiRepositoryInterface $orderApiRepository,
        CreateRefundRequestProvider $createRefundRequestProvider
    ) {
        $this->logger = $logger;
        $this->orderApiRepository = $orderApiRepository;
        $this->createRefundRequestProvider = $createRefundRequestProvider;
    }

    /**
     * @throws KlarnaPaymentException
     */
    public function run(CreateRefundRequestData $createRefundRequestData): void
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        try {
            $request = $this->createRefundRequestProvider->get($createRefundRequestData);

            $this->orderApiRepository->createRefund($request);
        } catch (ApiException $exception) {
            throw CouldNotCreateRefund::failedToGetSuccessfulApiResponse($exception);
        } catch (\Exception $exception) {
            throw CouldNotCreateRefund::unknownError($exception);
        } catch (\Throwable $exception) {
            throw CouldNotCreateRefund::failedToCreateApiRequest($exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));
    }
}
