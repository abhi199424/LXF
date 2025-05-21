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
use KlarnaPayment\Module\Api\Responses\CreateHppSessionResponse;
use KlarnaPayment\Module\Core\Payment\Api\Repository\HppSessionApiRepositoryInterface;
use KlarnaPayment\Module\Core\Payment\DTO\CreateHppSessionData;
use KlarnaPayment\Module\Core\Payment\Exception\CouldNotCreateHppSession;
use KlarnaPayment\Module\Core\Payment\Provider\CreateHppSessionRequestProvider;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CreateHppSessionAction
{
    private $logger;
    private $hppSessionApiRepository;
    private $createHppSessionProvider;

    public function __construct(
        LoggerInterface $logger,
        HppSessionApiRepositoryInterface $hppSessionApiRepository,
        CreateHppSessionRequestProvider $createHppSessionProvider
    ) {
        $this->logger = $logger;
        $this->hppSessionApiRepository = $hppSessionApiRepository;
        $this->createHppSessionProvider = $createHppSessionProvider;
    }

    /**
     * @throws KlarnaPaymentException
     */
    public function run(CreateHppSessionData $data): ?CreateHppSessionResponse
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        try {
            $request = $this->createHppSessionProvider->get($data);

            $result = $this->hppSessionApiRepository->createHppSession($request);
        } catch (ApiException $exception) {
            throw CouldNotCreateHppSession::failedToGetSuccessfulApiResponse($exception);
        } catch (\Exception $exception) {
            throw CouldNotCreateHppSession::unknownError($exception);
        } catch (\Throwable $exception) {
            throw CouldNotCreateHppSession::failedToCreateApiRequest($exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));

        return $result;
    }
}
