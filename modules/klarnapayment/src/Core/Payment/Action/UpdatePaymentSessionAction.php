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
use KlarnaPayment\Module\Core\Payment\Api\Repository\SessionApiRepositoryInterface;
use KlarnaPayment\Module\Core\Payment\DTO\UpdatePaymentSessionData;
use KlarnaPayment\Module\Core\Payment\Exception\CouldNotUpdatePaymentSession;
use KlarnaPayment\Module\Core\Payment\Provider\UpdatePaymentSessionRequestProvider;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class UpdatePaymentSessionAction
{
    private $logger;
    private $sessionApiRepository;
    private $updatePaymentSessionRequestProvider;

    public function __construct(
        LoggerInterface $logger,
        SessionApiRepositoryInterface $sessionApiRepository,
        UpdatePaymentSessionRequestProvider $updatePaymentSessionRequestProvider
    ) {
        $this->logger = $logger;
        $this->sessionApiRepository = $sessionApiRepository;
        $this->updatePaymentSessionRequestProvider = $updatePaymentSessionRequestProvider;
    }

    /**
     * @throws CouldNotUpdatePaymentSession
     */
    public function run(UpdatePaymentSessionData $data): void
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        try {
            $request = $this->updatePaymentSessionRequestProvider->get($data);

            $this->sessionApiRepository->updatePaymentSession($request);
        } catch (ApiException $exception) {
            throw CouldNotUpdatePaymentSession::failedToGetSuccessfulApiResponse($exception);
        } catch (\Exception $exception) {
            throw CouldNotUpdatePaymentSession::unknownError($exception);
        } catch (\Throwable $exception) {
            throw CouldNotUpdatePaymentSession::failedToCreateApiRequest($exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));
    }
}
