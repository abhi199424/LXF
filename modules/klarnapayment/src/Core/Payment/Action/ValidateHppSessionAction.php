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

use KlarnaPayment\Module\Core\Payment\DTO\RetrieveHppSessionData;
use KlarnaPayment\Module\Core\Payment\Exception\CouldNotRetrieveHppSession;
use KlarnaPayment\Module\Core\Payment\Exception\CouldNotValidateHppSession;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ValidateHppSessionAction
{
    /**
     * @var LoggerInterface
     */
    private $logger;
    /**
     * @var RetrieveHppSessionAction
     */
    private $retrieveHppSessionAction;

    public function __construct(
        LoggerInterface $logger,
        RetrieveHppSessionAction $retrieveHppSessionAction
    ) {
        $this->logger = $logger;
        $this->retrieveHppSessionAction = $retrieveHppSessionAction;
    }

    /**
     * @throws CouldNotValidateHppSession
     */
    public function run(string $authorizationToken, string $sessionId): void
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        try {
            $hppSessionResponse = $this->retrieveHppSessionAction->run(RetrieveHppSessionData::create($sessionId));
        } catch (\Throwable $exception) {
            throw CouldNotRetrieveHppSession::unknownError($exception);
        }

        if ($hppSessionResponse->getSession()->getStatus() !== 'COMPLETED') {
            throw CouldNotValidateHppSession::checkoutIsNotCompleted($sessionId);
        }

        if ($hppSessionResponse->getSession()->getAuthorizationToken() !== $authorizationToken) {
            throw CouldNotValidateHppSession::authorizationTokenDoesNotMatch($sessionId);
        }
    }
}
