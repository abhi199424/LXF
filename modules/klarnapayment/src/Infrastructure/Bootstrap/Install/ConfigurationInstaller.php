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

namespace KlarnaPayment\Module\Infrastructure\Bootstrap\Install;

use KlarnaPayment\Module\Api\Environment;
use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Shared\Enum\KlarnaExpressCheckoutButtonShape;
use KlarnaPayment\Module\Core\Shared\Enum\KlarnaExpressCheckoutButtonTheme;
use KlarnaPayment\Module\Core\Shared\Enum\KlarnaExpressCheckoutPlacement;
use KlarnaPayment\Module\Infrastructure\Config\OrderStatusConfig;
use Module;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ConfigurationInstaller implements InstallerInterface
{
    public function init(): void
    {
        // NOTE only covering these order states because other will be created via OrderStateInstaller and their value will be set there.
        $this->setForAllEnvironments(
            OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_ACCEPTED, \Configuration::get('PS_OS_PAYMENT')
        );

        $this->setForAllEnvironments(
            OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_CAPTURED, \Configuration::get('PS_OS_SHIPPING')
        );

        $this->setForAllEnvironments(
            OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_CANCELLED, \Configuration::get('PS_OS_CANCELED')
        );

        $this->setForAllEnvironments(
            OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_REFUNDED, \Configuration::get('PS_OS_REFUND')
        );

        // When installing Klarna payment module, environment is automatically set to production
        \Configuration::updateValue(Config::KLARNA_PAYMENT_IS_PRODUCTION_ENVIRONMENT, '1');

        $this->setForAllEnvironments(Config::KLARNA_PAYMENT_DEBUG_MODE, '0');

        $this->setForAllEnvironments(Config::KLARNA_PAYMENT_AUTO_CAPTURE_ORDER, '0');
        $this->setForAllEnvironments(Config::KLARNA_PAYMENT_AUTO_CAPTURE_ORDER_STATUSES, '');
        $this->setForAllEnvironments(Config::KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE, '0');
        $this->setForAllEnvironments(Config::KLARNA_PAYMENT_ALLOW_ORDER_STATUS_CHANGE, '1');

        $this->setForAllEnvironments(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ACTIVE, '0');
        $this->setForAllEnvironments(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_PLACEMENTS, '');
        $this->setForAllEnvironments(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ON_SIGN_IN_SCOPE, Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_MANDATORY_SCOPE);

        /** @var \KlarnaPayment\Module\Core\Tools\Action\ValidateOpcModuleCompatibilityAction $opcModuleValidator */
        $opcModuleValidator = new \KlarnaPayment\Module\Core\Tools\Action\ValidateOpcModuleCompatibilityAction();
        $isOpcModuleInUse = $opcModuleValidator->run();

        $this->setForAllEnvironments(Config::KLARNA_PAYMENT_HPP_SERVICE, (int) $isOpcModuleInUse);

        $this->setForAllEnvironments(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE, '0');
        $this->setForAllEnvironments(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS, '');
        $this->setForAllEnvironments(
            Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME,
            KlarnaExpressCheckoutButtonTheme::DEFAULT
        );
        $this->setForAllEnvironments(
            Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE,
            KlarnaExpressCheckoutButtonShape::DEFAULT
        );

        $this->setForAllEnvironments(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE, '0');
        $this->setForAllEnvironments(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS, '');
        $this->setForAllEnvironments(
            Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME,
            KlarnaExpressCheckoutButtonTheme::DEFAULT
        );
        $this->setForAllEnvironments(
            Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE,
            KlarnaExpressCheckoutButtonShape::DEFAULT
        );

        if (
            empty(\Configuration::get(Config::KLARNA_PAYMENT_DEFAULT_LOCALE['sandbox']))
            || empty(\Configuration::get(Config::KLARNA_PAYMENT_DEFAULT_LOCALE['production']))
        ) {
            $locale = Config::KLARNA_PAYMENT_VALID_COUNTRIES[0];

            $country = new \Country((int) \Configuration::get('PS_COUNTRY_DEFAULT'));

            if (
                !empty($country->id)
                && in_array($country->iso_code, Config::KLARNA_PAYMENT_VALID_COUNTRIES)
            ) {
                $locale = $country->iso_code;
            }

            $this->setForAllEnvironments(
                Config::KLARNA_PAYMENT_DEFAULT_LOCALE,
                strtoupper(strtolower($locale))
            );
        }

        \Configuration::updateValue(Config::KLARNA_PAYMENT_SECRET_KEY, uniqid('', true));

        \Configuration::updateValue(Config::ON_SITE_MESSAGING_INFOPAGE_CONFIG, Config::ON_SITE_MESSAGING_INFOPAGE_DEFAULT_PATH);
        \Configuration::updateValue(Config::ON_SITE_MESSAGING_INFOPAGE_ACTIVE_CONFIG[Environment::SANDBOX], 0);
        \Configuration::updateValue(Config::ON_SITE_MESSAGING_INFOPAGE_ACTIVE_CONFIG[Environment::PRODUCTION], 0);
    }

    private function setForAllEnvironments(array $idByEnvironment, string $value): void
    {
        \Configuration::updateValue($idByEnvironment['production'], $value);
        \Configuration::updateValue($idByEnvironment['sandbox'], $value);
    }

    public function setConfigurationDefaultValues(): void
    {
        $kecDefaultPlacements = sprintf(
            '%s, %s',
            KlarnaExpressCheckoutPlacement::PRODUCT_PAGE,
            KlarnaExpressCheckoutPlacement::CART_PAGE
        );

        $defaultSiwkScope = sprintf(
            '%s %s',
            Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_MANDATORY_SCOPE,
            Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_SCOPE
        );
        $defaultValues = [
            Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS['sandbox'] => $kecDefaultPlacements,
            Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS['production'] => $kecDefaultPlacements,
            Config::KLARNA_PAYMENT_ENABLE_BOX['sandbox'] => Config::KLARNA_PAYMENT_ENABLE_BOX_DEFAULT,
            Config::KLARNA_PAYMENT_ENABLE_BOX['production'] => Config::KLARNA_PAYMENT_ENABLE_BOX_DEFAULT,
            Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ON_SIGN_IN_SCOPE['sandbox'] => $defaultSiwkScope,
            Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ON_SIGN_IN_SCOPE['production'] => $defaultSiwkScope,
        ];

        foreach ($defaultValues as $configKey => $value) {
            \Configuration::updateValue($configKey, $value);
        }
    }
}
