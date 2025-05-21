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

namespace KlarnaPayment\Module\Presentation\Presenter\Verification;

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Merchant\Provider\CredentialsConfigurationKeyProvider;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CanPresentSignInWithKlarna
{
    /** @var Configuration */
    private $configuration;

    /** @var CredentialsConfigurationKeyProvider */
    private $credentialsConfigurationKeyProvider;

    public function __construct(
        Configuration $configuration,
        CredentialsConfigurationKeyProvider $credentialsConfigurationKeyProvider
    ) {
        $this->configuration = $configuration;
        $this->credentialsConfigurationKeyProvider = $credentialsConfigurationKeyProvider;
    }

    public function verify(string $currentControllerName): bool
    {
        $clientId = $this->configuration->get($this->credentialsConfigurationKeyProvider->getClientId());

        if (!$this->configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ACTIVE)) {
            return false;
        }

        if (empty($clientId)) {
            return false;
        }

        $selectedPlacements = array_map('strtolower', explode(
            ',',
            $this->configuration->getByEnvironment(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_PLACEMENTS)
        ));

        $positionData = [
            \CartController::class => in_array('cart', $selectedPlacements, true),
            \AuthController::class => in_array('authentication', $selectedPlacements, true),
        ];

        return !empty($positionData[$currentControllerName]);
    }
}
