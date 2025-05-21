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

use KlarnaPayment\Module\Api\Exception\ApiException;
use KlarnaPayment\Module\Api\Responses\RetrieveHppSessionResponse;
use KlarnaPayment\Module\Core\Payment\Api\Repository\HppSessionApiRepositoryInterface;
use KlarnaPayment\Module\Core\Payment\DTO\RetrieveHppSessionData;
use KlarnaPayment\Module\Core\Payment\Exception\CouldNotRetrieveHppSession;
use KlarnaPayment\Module\Core\Payment\Exception\CouldNotRetrievePaymentSession;
use KlarnaPayment\Module\Core\Payment\Provider\RetrieveHppSessionRequestProvider;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class RetrieveHppSessionAction
{
    private $logger;
    private $hppSessionApiRepository;
    private $retrieveHppSessionRequestProvider;

    public function __construct(
        LoggerInterface $logger,
        HppSessionApiRepositoryInterface $hppSessionApiRepository,
        RetrieveHppSessionRequestProvider $retrieveHppSessionRequestProvider
    ) {
        $this->logger = $logger;
        $this->hppSessionApiRepository = $hppSessionApiRepository;
        $this->retrieveHppSessionRequestProvider = $retrieveHppSessionRequestProvider;
    }

    /**
     * @throws CouldNotRetrievePaymentSession
     */
    public function run(RetrieveHppSessionData $data): RetrieveHppSessionResponse
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        try {
            $request = $this->retrieveHppSessionRequestProvider->get($data);

            $result = $this->hppSessionApiRepository->retrieveHppSession($request);
        } catch (ApiException $exception) {
            throw CouldNotRetrieveHppSession::failedToGetSuccessfulApiResponse($exception);
        } catch (\Exception $exception) {
            throw CouldNotRetrieveHppSession::unknownError($exception);
        } catch (\Throwable $exception) {
            throw CouldNotRetrieveHppSession::failedToCreateApiRequest($exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));

        return $result;
    }
}
