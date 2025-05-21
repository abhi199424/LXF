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
use KlarnaPayment\Module\Core\Order\DTO\CreateCaptureRequestData;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotCreateCapture;
use KlarnaPayment\Module\Core\Order\Provider\CreateCaptureRequestProvider;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CreateCaptureAction
{
    private $logger;
    private $orderApiRepository;
    private $createCaptureRequestProvider;

    public function __construct(
        LoggerInterface $logger,
        OrderApiRepositoryInterface $orderApiRepository,
        CreateCaptureRequestProvider $createCaptureRequestProvider
    ) {
        $this->logger = $logger;
        $this->orderApiRepository = $orderApiRepository;
        $this->createCaptureRequestProvider = $createCaptureRequestProvider;
    }

    /**
     * @throws KlarnaPaymentException
     */
    public function run(CreateCaptureRequestData $createCaptureRequestData): void
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        try {
            $request = $this->createCaptureRequestProvider->get($createCaptureRequestData);

            $this->orderApiRepository->createCapture($request, $createCaptureRequestData->getCurrencyIso());
        } catch (ApiException $exception) {
            throw CouldNotCreateCapture::failedToGetSuccessfulApiResponse($exception);
        } catch (\Exception $exception) {
            throw CouldNotCreateCapture::unknownError($exception);
        } catch (\Throwable $exception) {
            throw CouldNotCreateCapture::failedToCreateApiRequest($exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));
    }
}
