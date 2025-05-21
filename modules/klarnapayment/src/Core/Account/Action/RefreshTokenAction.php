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

use KlarnaPayment\Module\Api\Requests\RefreshTokenRequest;
use KlarnaPayment\Module\Api\Responses\RefreshTokenResponse;
use KlarnaPayment\Module\Core\Account\Api\Repository\AccountApiRepositoryInterface;
use KlarnaPayment\Module\Core\Merchant\Provider\CredentialsConfigurationKeyProvider;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class RefreshTokenAction
{
    /** @var LoggerInterface */
    private $logger;
    /** @var AccountApiRepositoryInterface */
    private $accountApiRepository;
    /** @var Configuration */
    private $configuration;
    /** @var CredentialsConfigurationKeyProvider */
    private $credentialsConfigurationKeyProvider;

    public function __construct(
        LoggerInterface $logger,
        AccountApiRepositoryInterface $accountApiRepository,
        Configuration $configuration,
        CredentialsConfigurationKeyProvider $credentialsConfigurationKeyProvider
    ) {
        $this->logger = $logger;
        $this->accountApiRepository = $accountApiRepository;
        $this->configuration = $configuration;
        $this->credentialsConfigurationKeyProvider = $credentialsConfigurationKeyProvider;
    }

    /**
     * @throws KlarnaPaymentException
     */
    public function run(string $refreshToken, string $grantType): RefreshTokenResponse
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));

        $request = new RefreshTokenRequest();

        $request->setRefreshToken($refreshToken);
        $request->setClientId($this->configuration->get($this->credentialsConfigurationKeyProvider->getClientId()));
        $request->setGrantType($grantType);

        $response = $this->accountApiRepository->refreshToken($request);

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));

        return $response;
    }
}
