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
use KlarnaPayment\Module\Api\Responses\CreatePaymentSessionResponse;
use KlarnaPayment\Module\Core\Payment\Api\Repository\SessionApiRepositoryInterface;
use KlarnaPayment\Module\Core\Payment\DTO\CreatePaymentSessionData;
use KlarnaPayment\Module\Core\Payment\Exception\CouldNotCreatePaymentSession;
use KlarnaPayment\Module\Core\Payment\Provider\CreatePaymentSessionRequestProvider;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CreatePaymentSessionAction
{
    private $logger;
    private $sessionApiRepository;
    private $createPaymentSessionRequestProvider;

    public function __construct(
        LoggerInterface $logger,
        SessionApiRepositoryInterface $sessionApiRepository,
        CreatePaymentSessionRequestProvider $createPaymentSessionRequestProvider
    ) {
        $this->logger = $logger;
        $this->sessionApiRepository = $sessionApiRepository;
        $this->createPaymentSessionRequestProvider = $createPaymentSessionRequestProvider;
    }

    /**
     * @throws CouldNotCreatePaymentSession
     */
    public function run(CreatePaymentSessionData $data): ?CreatePaymentSessionResponse
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        try {
            $request = $this->createPaymentSessionRequestProvider->get($data);

            $result = $this->sessionApiRepository->createPaymentSession($request);
        } catch (ApiException $exception) {
            throw CouldNotCreatePaymentSession::failedToGetSuccessfulApiResponse($exception);
        } catch (\Exception $exception) {
            throw CouldNotCreatePaymentSession::unknownError($exception);
        } catch (\Throwable $exception) {
            throw CouldNotCreatePaymentSession::failedToCreateApiRequest($exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));

        return $result;
    }
}
