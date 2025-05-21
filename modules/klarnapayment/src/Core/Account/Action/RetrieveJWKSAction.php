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

namespace KlarnaPayment\Module\Core\Account\Action;

use KlarnaPayment\Module\Api\Exception\ApiException;
use KlarnaPayment\Module\Api\Responses\RetrieveJWKSResponse;
use KlarnaPayment\Module\Core\Account\Api\Repository\AccountApiRepositoryInterface;
use KlarnaPayment\Module\Core\Account\Exception\CouldNotRetrieveJWKS;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class RetrieveJWKSAction
{
    /** @var LoggerInterface */
    private $logger;
    /** @var AccountApiRepositoryInterface */
    private $accountApiRepository;

    public function __construct(
        LoggerInterface $logger,
        AccountApiRepositoryInterface $accountApiRepository
    ) {
        $this->logger = $logger;
        $this->accountApiRepository = $accountApiRepository;
    }

    /**
     * @throws KlarnaPaymentException
     */
    public function run(): RetrieveJWKSResponse
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        try {
            $result = $this->accountApiRepository->retrieveJWKS();
        } catch (ApiException $exception) {
            throw CouldNotRetrieveJWKS::failedToGetSuccessfulApiResponse($exception);
        } catch (\Throwable $exception) {
            throw CouldNotRetrieveJWKS::unknownError($exception);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));

        return $result;
    }
}
