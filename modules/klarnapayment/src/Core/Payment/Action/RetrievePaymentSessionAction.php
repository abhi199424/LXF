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
use KlarnaPayment\Module\Api\Responses\RetrievePaymentSessionResponse;
use KlarnaPayment\Module\Core\Payment\Api\Repository\SessionApiRepositoryInterface;
use KlarnaPayment\Module\Core\Payment\DTO\RetrievePaymentSessionData;
use KlarnaPayment\Module\Core\Payment\Exception\CouldNotRetrievePaymentSession;
use KlarnaPayment\Module\Core\Payment\Provider\RetrievePaymentSessionRequestProvider;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class RetrievePaymentSessionAction
{
    private $logger;
    private $sessionApiRepository;
    private $retrievePaymentSessionRequestProvider;

    public function __construct(
        LoggerInterface $logger,
        SessionApiRepositoryInterface $sessionApiRepository,
        RetrievePaymentSessionRequestProvider $retrievePaymentSessionRequestProvider
    ) {
        $this->logger = $logger;
        $this->sessionApiRepository = $sessionApiRepository;
        $this->retrievePaymentSessionRequestProvider = $retrievePaymentSessionRequestProvider;
    }

    /**
     * @throws CouldNotRetrievePaymentSession
     */
    public function run(RetrievePaymentSessionData $data): RetrievePaymentSessionResponse
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        try {
            $request = $this->retrievePaymentSessionRequestProvider->get($data);

            $result = $this->sessionApiRepository->retrievePaymentSession($request);
        } catch (ApiException $exception) {
            throw CouldNotRetrievePaymentSession::failedToGetSuccessfulApiResponse($exception);
        } catch (\Exception $exception) {
            throw CouldNotRetrievePaymentSession::unknownError($exception);
        } catch (\Throwable $exception) {
            throw CouldNotRetrievePaymentSession::failedToCreateApiRequest($exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));

        return $result;
    }
}
