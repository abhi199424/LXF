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

namespace KlarnaPayment\Module\Core\Merchant\Action;

use KlarnaPayment\Module\Api\Environment;
use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Merchant\DTO\ApplicationLoginData;
use KlarnaPayment\Module\Core\Merchant\Exception\CouldNotLogin;
use KlarnaPayment\Module\Core\Merchant\Handler\ApplicationLoginHandler;
use KlarnaPayment\Module\Core\Merchant\Handler\FeatureAvailabilityHandler;
use KlarnaPayment\Module\Core\Merchant\Provider\ApiKeyProvider;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Cache\CacheInterface;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Provider\ApplicationContextProvider;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CredentialsSubmitAction
{
    private $logger;

    private $cache;

    private $applicationLoginHandler;

    private $configuration;

    private $apiKeyProvider;
    /**
     * @var ApplicationContextProvider
     */
    private $applicationContextProvider;
    /**
     * @var FeatureAvailabilityHandler
     */
    private $featureAvailabilityHandler;

    public function __construct(
        LoggerInterface $logger,
        CacheInterface $cache,
        Configuration $configuration,
        ApplicationLoginHandler $applicationLoginHandler,
        ApiKeyProvider $apiKeyProvider,
        ApplicationContextProvider $applicationContextProvider,
        FeatureAvailabilityHandler $featureAvailabilityHandler
    ) {
        $this->logger = $logger;
        $this->cache = $cache;
        $this->applicationLoginHandler = $applicationLoginHandler;
        $this->configuration = $configuration;
        $this->apiKeyProvider = $apiKeyProvider;
        $this->applicationContextProvider = $applicationContextProvider;
        $this->featureAvailabilityHandler = $featureAvailabilityHandler;
    }

    /**
     * @param array{usernames: array, passwords: array, clientIds: array, array: string} $sandboxCredentials
     * @param array{usernames: array, passwords: array, clientIds: array, array: string} $productionCredentials
     *
     * @return array with successfully logged in regions and environments
     */
    public function run(array $sandboxCredentials, array $productionCredentials): array
    {
        $this->logger->debug(sprintf('%s - Function called', __METHOD__));
        $this->cache->clear();
        $successfullyLoggedInRegions = [
            Environment::PRODUCTION => [],
            Environment::SANDBOX => [],
        ];
        $failedToLogInRegions = [
            Environment::PRODUCTION => [],
            Environment::SANDBOX => [],
        ];

        $credentialsPerEnvironment = [
            Environment::SANDBOX => $sandboxCredentials,
            Environment::PRODUCTION => $productionCredentials,
        ];

        $activeEnvironment = $this->applicationContextProvider->refresh()->get()->getIsProduction() ? Environment::PRODUCTION : Environment::SANDBOX;

        foreach ($credentialsPerEnvironment as $environment => $credentials) {
            foreach (Config::SUPPORTED_REGIONS as $regionLocale => $region) {
                try {
                    $this->saveCredentialsPerEnvironment(
                        $environment,
                        $regionLocale,
                        $credentials['usernames'][$regionLocale],
                        $credentials['passwords'][$regionLocale]
                    );

                    // Note: Client ID must be saved separately and without credentials check
                    $this->saveClientIdPerEnvironment(
                        $environment,
                        $regionLocale,
                        $credentials['clientIds'][$regionLocale]
                    );

                    if ($environment !== $activeEnvironment) {
                        continue;
                    }

                    $apiKey = $this->configuration->get(Config::KLARNA_PAYMENT_API_KEY[$environment][$regionLocale]);

                    if (!empty($apiKey)) {
                        $this->applicationLoginHandler->handle(
                            ApplicationLoginData::create($apiKey, $regionLocale),
                            $environment
                        );

                        try {
                            $this->featureAvailabilityHandler->handle($apiKey);
                        } catch (\Exception $e) {
                            $this->logger->info(
                                'Unable to check Klarna feature availability. Skipping',
                                [
                                    'exception' => ExceptionUtility::getExceptions($e),
                                    'region' => $region,
                                    'environment' => $environment,
                                ]
                            );

                            // Enable all features
                            foreach (Config::KLARNA_PAYMENTS_FEATURES as $config) {
                                $this->configuration->set($config, 1);
                            }
                            $this->configuration->set(Config::KLARNA_PAYMENT_ENABLE_BOX['sandbox'], 1);
                            $this->configuration->set(Config::KLARNA_PAYMENT_ENABLE_BOX['production'], 1);
                        }

                        $successfullyLoggedInRegions[$environment][] = $regionLocale;
                    }
                } catch (\Exception $e) {
                    $failedToLogInRegions[$environment][$regionLocale] = ExceptionUtility::getExceptions($e) + ['region' => $region, 'environment' => $environment];
                    $this->configuration->delete(Config::KLARNA_PAYMENT_CONNECTED_REGIONS[$environment][$regionLocale]);
                }
            }
        }

        if (!empty($failedToLogInRegions[$activeEnvironment])) {
            $this->logger->error(
                'Failed to login to Klarna.',
                [
                    'failedToLogInRegions' => $failedToLogInRegions,
                ]
            );

            throw CouldNotLogin::genericError($failedToLogInRegions[$activeEnvironment]);
        }

        $this->logger->debug(sprintf('%s - Function ended', __METHOD__));

        return $successfullyLoggedInRegions;
    }

    private function saveCredentialsPerEnvironment(string $environment, string $region, string $username, string $password): void
    {
        if (preg_match('/^[l*]+$/', $password) === 1) {
            $key = (string) $this->configuration->get(Config::KLARNA_PAYMENT_API_KEY[$environment][$region]);
            $decoded = base64_decode($key);

            $password = explode(':', $decoded)[1];
        }

        $apiKey = empty($password) ? '' : $this->apiKeyProvider->get($username, $password);
        $this->configuration->set(Config::KLARNA_PAYMENT_API_KEY[$environment][$region], $apiKey);
        $this->configuration->set(Config::KLARNA_PAYMENT_API_USERNAME[$environment][$region], $username);
        $this->configuration->set(Config::KLARNA_PAYMENT_API_PASSWORD[$environment][$region], str_repeat('*', strlen($password)));
    }

    private function saveClientIdPerEnvironment(string $environment, string $region, string $clientId): void
    {
        $this->configuration->set(Config::KLARNA_PAYMENT_CLIENT_IDENTIFIER[$environment][$region], $clientId);
    }
}
