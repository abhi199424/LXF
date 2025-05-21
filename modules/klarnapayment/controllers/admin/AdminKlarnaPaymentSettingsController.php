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

use KlarnaPayment\Module\Api\Environment;
use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Merchant\Action\CredentialsSubmitAction;
use KlarnaPayment\Module\Core\Merchant\Action\RegionConnectionStatusAction;
use KlarnaPayment\Module\Core\Merchant\Action\SiwkScopeBuilderAction;
use KlarnaPayment\Module\Core\Merchant\Exception\CouldNotLogin;
use KlarnaPayment\Module\Core\Merchant\Handler\FeatureAvailabilityHandler;
use KlarnaPayment\Module\Core\Merchant\Provider\CredentialsConfigurationKeyProvider;
use KlarnaPayment\Module\Core\Merchant\Provider\CredentialsProvider;
use KlarnaPayment\Module\Core\Shared\Enum\KlarnaExpressCheckoutButtonShape;
use KlarnaPayment\Module\Core\Shared\Enum\KlarnaExpressCheckoutButtonTheme;
use KlarnaPayment\Module\Core\Shared\Enum\KlarnaExpressCheckoutPlacement;
use KlarnaPayment\Module\Core\Shared\Enum\KlarnaSignInWithKlarnaButtonShape;
use KlarnaPayment\Module\Core\Shared\Enum\KlarnaSignInWithKlarnaButtonTheme;
use KlarnaPayment\Module\Core\Shared\Enum\OnsiteMessagingPlacementThemes;
use KlarnaPayment\Module\Core\Shared\Enum\SignInWithKlarnaBadgeAlignment;
use KlarnaPayment\Module\Core\Shared\Repository\CountryRepositoryInterface;
use KlarnaPayment\Module\Core\Shared\Repository\OrderStateRepositoryInterface;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\Tools;
use KlarnaPayment\Module\Infrastructure\Bootstrap\ModuleTabs;
use KlarnaPayment\Module\Infrastructure\Context\ApplicationContext;
use KlarnaPayment\Module\Infrastructure\Controller\AbstractAdminController as ModuleAdminController;
use KlarnaPayment\Module\Infrastructure\Enum\PermissionType;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Notification\Handler\NotificationHandlerInterface;
use KlarnaPayment\Module\Infrastructure\Notification\Notifications\ErrorNotification;
use KlarnaPayment\Module\Infrastructure\Notification\Notifications\SuccessNotification;
use KlarnaPayment\Module\Infrastructure\Provider\ApplicationContextProvider;
use KlarnaPayment\Module\Infrastructure\Provider\OrderStatusProvider;
use KlarnaPayment\Module\Infrastructure\Request\Request;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;
use KlarnaPayment\Module\Infrastructure\Utility\MoneyCalculator;
use KlarnaPayment\Module\Infrastructure\Utility\VersionUtility;
use KlarnaPayment\Module\Infrastructure\Verification\FeatureAvailability;
use KlarnaPayment\Module\Presentation\Loader\PrestaShopIntegrationAssetLoader;
use Prestashop\ModuleLibMboInstaller\DependencyBuilder;

require_once dirname(__FILE__) . '/../../vendor/autoload.php';

if (!defined('_PS_VERSION_')) {
    exit;
}

class AdminKlarnaPaymentSettingsController extends ModuleAdminController
{
    const FILE_NAME = 'AdminKlarnaPaymentSettingsController';

    // ADDITIONAL CONFIGURATION SETTINGS
    private const KLARNA_PAYMENT_DEBUG_MODE = 'KLARNA_PAYMENT_DEBUG_MODE';
    private const KLARNA_PAYMENT_ALLOW_ORDER_STATUS_CHANGE = 'KLARNA_PAYMENT_ALLOW_ORDER_STATUS_CHANGE';
    private const KLARNA_PAYMENT_HPP_SERVICE = 'KLARNA_PAYMENT_HPP_SERVICE';
    private const KLARNA_PAYMENT_DEFAULT_LOCALE = 'KLARNA_PAYMENT_DEFAULT_LOCALE';

    // ORDER STATUS MAPPING AND AUTO CAPTURE SETTINGS
    private const KLARNA_PAYMENT_AUTO_CAPTURE_ORDER_STATUSES_INPUT = 'KLARNA_PAYMENT_AUTO_CAPTURE_ORDER_STATUSES[]';
    private const KLARNA_PAYMENT_AUTO_CAPTURE_ORDER_STATUSES_VALUE = 'KLARNA_PAYMENT_AUTO_CAPTURE_ORDER_STATUSES';
    private const KLARNA_PAYMENT_AUTO_CAPTURE_ORDER = 'KLARNA_PAYMENT_AUTO_CAPTURE_ORDER';

    // ON-SITE MESSAGING SETTINGS
    private const KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE = 'KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE';
    private const KLARNA_PAYMENT_ONSITE_MESSAGING_THEME = 'KLARNA_PAYMENT_ONSITE_MESSAGING_THEME';
    private const KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_DATA_KEY = 'KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_DATA_KEY';
    private const KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_DATA_KEY = 'KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_DATA_KEY';
    private const KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_DATA_KEY = 'KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_DATA_KEY';
    private const KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_DATA_KEY = 'KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_DATA_KEY';
    private const KLARNA_PAYMENT_ONSITE_MESSAGING_INFOPAGE_PATH_KEY = 'KLARNA_PAYMENT_ONSITE_MESSAGING_INFOPAGE_PATH_KEY';

    // KLARNA EXPRESS CHECKOUT SETTINGS
    private const KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE = 'KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE';
    private const KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS_INPUT = 'KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS[]';
    private const KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS_VALUE = 'KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS';
    private const KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME = 'KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME';
    private const KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE = 'KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE';

    // KLARNA SIGN IN SETTINGS
    private const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ACTIVE = 'KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ACTIVE';
    private const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_PLACEMENTS_INPUT = 'KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_PLACEMENTS[]';
    private const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_PLACEMENTS_VALUE = 'KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_PLACEMENTS';
    private const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_REQUIRED_CUSTOMER_DATA = 'KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_REQUIRED_CUSTOMER_DATA';
    private const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_CUSTOMER_DATA = 'KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_CUSTOMER_DATA';
    private const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_THEME = 'KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_THEME';
    private const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_SHAPE = 'KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_SHAPE';
    private const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_ALIGNMENT = 'KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_ALIGNMENT';
    private const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_REQUIRED_CUSTOMER_DATA_EMAIL_ADDRESS = 'KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_REQUIRED_CUSTOMER_DATA_email_address';
    private const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_REQUIRED_CUSTOMER_DATA_FULL_NAME = 'KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_REQUIRED_CUSTOMER_DATA_full_name';
    private const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_REQUIRED_CUSTOMER_DATA_PHONE_NUMBER = 'KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_REQUIRED_CUSTOMER_DATA_phone_number';

    private const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_CUSTOMER_DATA_DATE_OF_BIRTH = 'KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_CUSTOMER_DATA_date_of_birth';
    private const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_CUSTOMER_DATA_COUNTRY = 'KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_CUSTOMER_DATA_country';
    private const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_CUSTOMER_DATA_BILLING_ADDRESS = 'KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_CUSTOMER_DATA_billing_address';
    private const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_CUSTOMER_DATA_LANGUAGE_PREFERENCE = 'KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_CUSTOMER_DATA_language_preference';

    private const KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_REDIRECT_URL_NAME = 'KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_REDIRECT_URL_NAME';
    private const KLARNA_PAYMENT_ENABLE_BOX = 'KLARNA_PAYMENT_ENABLE_BOX';
    private const KLARNA_PAYMENT_OSM_INFOPAGE_ACTIVE = 'KLARNA_PAYMENT_OSM_INFOPAGE_ACTIVE';

    public function __construct()
    {
        $this->bootstrap = true;

        parent::__construct();
    }

    /**
     * @throws SmartyException
     */
    public function initContent(): void
    {
        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        /** @var NotificationHandlerInterface $notificationHandler */
        $notificationHandler = $this->module->getService(NotificationHandlerInterface::class);

        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        $this->context->controller->addJqueryPlugin('select2');

        Media::addJsDef([
            'klarnapayment_image_urls' => Config::KLARNA_IMAGE_URL,
            'klarnapayment_translations' => [
                'siwk_required_checkbox_error' => $this->module->l('Please select at least one required customer data', self::FILE_NAME),
            ],
        ]);

        try {
            $mboInstaller = new DependencyBuilder($this->module);
            if ($mboInstaller->areDependenciesMet()) {
                $this->content .= $this->displayPsAccounts();
            } else {
                $this->content .= $this->displayCloudSyncDependencies($mboInstaller);
            }
        } catch (Exception $e) {
            $logger->error('Unable to load PrestaShop CloudSync integration.', [
                'context' => [],
                'exceptions' => ExceptionUtility::getExceptions($e),
            ]);

            $notificationHandler->addNotification(self::FILE_NAME, ErrorNotification::create(
                $this->module->l('Unable to load PrestaShop CloudSync integration. Check logs for more information.', self::FILE_NAME)
            ));
        }

        $this->setEnvironmentDetails();

        $this->content .= $this->displayEndpointSettings();

        if (!$this->isLoggedIn()) {
            parent::initContent();

            return;
        }

        $this->content .= $this->displayKlarnaPaymentsSettings();
        $this->content .= $this->displayOnsiteMessagingConfiguration();
        $this->content .= $this->displayExpressCheckoutPlacementConfiguration();
        $this->content .= $this->displaySignInWithKlarnaSettings();
        $this->content .= $this->displayAutoCaptureSettings();
        $this->content .= $this->displayAdditionalSettings();

        $this->checkAvailableFeatures();

        parent::initContent();
    }

    public function setMedia($isNewTheme = false): void
    {
        parent::setMedia($isNewTheme);

        if (VersionUtility::isPsVersionLessThan('9.0.0')) {
            $this->addCSS($this->module->getPathUri() . 'views/css/admin/settings/klarna-panel-collapsed.css?v=' . $this->module->version, 'all', null, false);
        }

        $this->addCSS($this->module->getPathUri() . 'views/css/admin/settings/settings.css?v=' . $this->module->version, 'all', null, false);
        $this->addJS($this->module->getPathUri() . 'views/js/admin/settings/settings.js?v=' . $this->module->version, false);
        $this->addJS($this->module->getPathUri() . 'views/js/admin/settings/general.js?v=' . $this->module->version, false);
    }

    public function setEnvironmentDetails(): void
    {
        /** @var ApplicationContextProvider $applicationContextProvider */
        $applicationContextProvider = $this->module->getService(ApplicationContextProvider::class);

        Media::addJsDef([
            'klarnapayment' => [
                'production' => $applicationContextProvider->get()->getIsProduction(),
            ],
        ]);
    }

    public function postProcess(): bool
    {
        if (!$this->ensureHasPermissions([PermissionType::EDIT, PermissionType::VIEW])) {
            return false;
        }

        /**
         * NOTE: this boolean is used to check
         * if we need to refresh page after form submit.
         *
         * This must be changed to true on every isSubmit method.
         */
        $isFormSubmitted = false;

        /** @var OrderStatusProvider $orderStatusProvider */
        $orderStatusProvider = $this->module->getService(OrderStatusProvider::class);

        /** @var NotificationHandlerInterface $notificationHandler */
        $notificationHandler = $this->module->getService(NotificationHandlerInterface::class);

        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        /** @var Tools $tools */
        $tools = $this->module->getService(Tools::class);

        /** @var MoneyCalculator $moneyCalculator */
        $moneyCalculator = $this->module->getService(MoneyCalculator::class);

        /** @var ApplicationContextProvider $applicationContextProvider */
        $applicationContextProvider = $this->module->getService(ApplicationContextProvider::class);

        $isProduction = $applicationContextProvider->get()->getIsProduction();

        // TODO add validation for all requests
        $request = Request::createFromGlobals();

        if ($tools->isSubmit('submit_credential_settings')) {
            try {
                /** @var CredentialsSubmitAction $credentialsSubmitAction * */
                $credentialsSubmitAction = $this->module->getService(CredentialsSubmitAction::class);
                $successfullyLoggedInRegions = $credentialsSubmitAction->run(
                    [
                        'usernames' => $tools->getValue('sandboxUsername'),
                        'passwords' => $tools->getValue('sandboxPassword'),
                        'clientIds' => $tools->getValue('sandboxClientId'),
                    ],
                    [
                        'usernames' => $tools->getValue('prodUsername'),
                        'passwords' => $tools->getValue('prodPassword'),
                        'clientIds' => $tools->getValue('prodClientId'),
                    ]
                );

                /** @var RegionConnectionStatusAction $regionConnectionStatusAction * */
                $regionConnectionStatusAction = $this->module->getService(RegionConnectionStatusAction::class);
                $regionConnectionStatusAction->run(
                    $successfullyLoggedInRegions['sandbox'],
                    $successfullyLoggedInRegions['production']
                );

                if ($this->anySuccessfulConnections($successfullyLoggedInRegions)) {
                    $this->displaySuccessfulConnectedNotification($successfullyLoggedInRegions, $isProduction, $notificationHandler);
                }
            } catch (CouldNotLogin $exception) {
                $notificationHandler->addNotification(self::FILE_NAME, ErrorNotification::create(
                    sprintf(
                        $this->module->l('Failed to log in to %s. Please check your API credentials', self::FILE_NAME),
                        implode(', ', $exception->getRegionsNames())
                    )
                ));
            }
        }

        if ($tools->isSubmit('submit_onsite_messaging_settings')) {
            $onsiteMessagingActive = (int) $request->get(self::KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE . '_');

            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE,
                $onsiteMessagingActive
            );

            foreach ($this->getOnsiteMessagingSettingMaps() as $requestKey => $configurationKey) {
                $configuration->setByEnvironment(
                    $configurationKey,
                    $request->get($requestKey)
                );
            }

            $osmLink = $request->get(self::KLARNA_PAYMENT_ONSITE_MESSAGING_INFOPAGE_PATH_KEY)
                ?: Config::ON_SITE_MESSAGING_INFOPAGE_DEFAULT_PATH;

            $configuration->setByEnvironment(
                Config::ON_SITE_MESSAGING_INFOPAGE_ACTIVE_CONFIG,
                $request->get(self::KLARNA_PAYMENT_OSM_INFOPAGE_ACTIVE . '_')
            );

            // URL cannot be with "/" in front
            if ($osmLink[0] === '/') {
                $osmLink = substr($osmLink, 1);
            }

            $configuration->set(
                Config::ON_SITE_MESSAGING_INFOPAGE_CONFIG,
                $osmLink
            );

            $notificationHandler->addNotification(self::FILE_NAME, SuccessNotification::create(
                $this->module->l('Settings updated', self::FILE_NAME)
            ));

            $isFormSubmitted = true;
        }

        if ($tools->isSubmit('submit_express_checkout_settings')) {
            $expressCheckoutActive = (int) $request->get(self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE . '_');

            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE,
                $expressCheckoutActive
            );

            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS,
                implode(
                    ',',
                    $tools->getValue(self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS_VALUE) ?: []
                )
            );

            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME,
                $tools->getValue(self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME)
            );

            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE,
                $tools->getValue(self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE)
            );

            $notificationHandler->addNotification(self::FILE_NAME, SuccessNotification::create(
                $this->module->l('Settings updated', self::FILE_NAME)
            ));

            $isFormSubmitted = true;
        }

        $moduleOrderStatuses = $orderStatusProvider->getModuleOrderStatuses();

        if ($tools->isSubmit('submit_statuses')) {
            foreach ($moduleOrderStatuses as $orderStatusKey => $orderStatus) {
                // NOTE there should always be all order status values inside $_POST.
                // But preparing for worst case scenario and we shouldn't change anything if something incorrect happened.
                if (empty($tools->getValue($orderStatusKey))) {
                    continue;
                }
                $configuration->set($orderStatusKey, $tools->getValueAsInt($orderStatusKey));
            }

            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_AUTO_CAPTURE_ORDER,
                $tools->getValueAsInteger(self::KLARNA_PAYMENT_AUTO_CAPTURE_ORDER . '_')
            );

            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_AUTO_CAPTURE_ORDER_STATUSES,
                implode(
                    ',',
                    $tools->getValue(self::KLARNA_PAYMENT_AUTO_CAPTURE_ORDER_STATUSES_VALUE) ?: []
                )
            );

            $notificationHandler->addNotification(self::FILE_NAME, SuccessNotification::create(
                $this->module->l('Settings updated', self::FILE_NAME)
            ));

            $isFormSubmitted = true;
        }

        if ($tools->isSubmit('submit_additional_settings')) {
            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_DEBUG_MODE,
                $tools->getValueAsInteger(self::KLARNA_PAYMENT_DEBUG_MODE . '_')
            );

            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_ALLOW_ORDER_STATUS_CHANGE,
                $tools->getValueAsInteger(self::KLARNA_PAYMENT_ALLOW_ORDER_STATUS_CHANGE . '_')
            );

            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_DEFAULT_LOCALE,
                $tools->getValue(self::KLARNA_PAYMENT_DEFAULT_LOCALE)
            );

            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_HPP_SERVICE,
                $tools->getValueAsInteger(self::KLARNA_PAYMENT_HPP_SERVICE . '_')
            );

            $orderMin = $moneyCalculator->calculateIntoInteger((float) $tools->getValue('minOrderValue'));
            $orderMax = $moneyCalculator->calculateIntoInteger((float) $tools->getValue('maxOrderValue'));

            if ($orderMin < 0) {
                $notificationHandler->addNotification(self::FILE_NAME, ErrorNotification::create(
                    $this->module->l(
                        'Could not update the minimum order value. Value should be positive',
                        self::FILE_NAME
                    )
                ));

                return false;
            }

            if ($orderMax < 0) {
                $notificationHandler->addNotification(self::FILE_NAME, ErrorNotification::create(
                    $this->module->l(
                        'Could not update the maximum order value. Value should be positive',
                        self::FILE_NAME
                    )
                ));

                return false;
            }

            if ($orderMin > $orderMax) {
                $notificationHandler->addNotification(self::FILE_NAME, ErrorNotification::create(
                    $this->module->l(
                        'Could not update the maximum order value. Maximum value should be greater than minimum value.',
                        self::FILE_NAME
                    )
                ));

                return false;
            }

            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_ORDER_MIN,
                $orderMin
            );

            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_ORDER_MAX,
                $orderMax
            );

            $notificationHandler->addNotification(self::FILE_NAME, SuccessNotification::create(
                $this->module->l('Settings updated.', self::FILE_NAME)
            ));

            $isFormSubmitted = true;
        }

        if ($environment = $tools->getValue('environment')) {
            $configuration->set(Config::KLARNA_PAYMENT_IS_PRODUCTION_ENVIRONMENT, $environment === 'production');

            $isFormSubmitted = true;
        }
        if ($tools->isSubmit('submit_enable_klarna_payment')) {
            $klarnaPaymentEnabled = (int) $tools->getValue(self::KLARNA_PAYMENT_ENABLE_BOX . '_');
            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_ENABLE_BOX,
                $klarnaPaymentEnabled
            );

            $message = $klarnaPaymentEnabled
                ? $this->module->l('Klarna Payments enabled.', self::FILE_NAME)
                : $this->module->l('Klarna Payments disabled.', self::FILE_NAME);

            $notificationHandler->addNotification(self::FILE_NAME, SuccessNotification::create($message));

            $isFormSubmitted = true;
        }

        if ($tools->isSubmit('submit_sign_in_with_klarna')) {
            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ACTIVE,
                $tools->getValueAsInteger(self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ACTIVE . '_')
            );

            /** @var SiwkScopeBuilderAction $siwkScopeBuilderAction * */
            $siwkScopeBuilderAction = $this->module->getService(SiwkScopeBuilderAction::class);

            $newScope = $siwkScopeBuilderAction->run(
                [],
                [
                    $tools->getValue(self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_CUSTOMER_DATA_DATE_OF_BIRTH),
                    $tools->getValue(self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_CUSTOMER_DATA_COUNTRY),
                    $tools->getValue(self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_CUSTOMER_DATA_BILLING_ADDRESS),
                    $tools->getValue(self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_CUSTOMER_DATA_LANGUAGE_PREFERENCE),
                ]
            );

            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ON_SIGN_IN_SCOPE,
                $newScope
            );
            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_PLACEMENTS,
                implode(
                    ',',
                    $tools->getValue(self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_PLACEMENTS_VALUE) ?: []
                )
            );
            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_ALIGNMENT,
                $tools->getValue(self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_ALIGNMENT)
            );
            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_THEME,
                $tools->getValue(self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_THEME)
            );
            $configuration->setByEnvironment(
                Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_SHAPE,
                $tools->getValue(self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_SHAPE)
            );

            $notificationHandler->addNotification(self::FILE_NAME, SuccessNotification::create(
                $this->module->l('Settings updated', self::FILE_NAME)
            ));

            $isFormSubmitted = true;
        }

        if ($isFormSubmitted) {
            \Tools::redirectAdmin($this->context->link->getAdminLink(ModuleTabs::SETTINGS_MODULE_TAB_CONTROLLER_NAME));
        }

        return parent::postProcess();
    }

    /**
     * @throws SmartyException
     */
    public function displayEndpointSettings(): string
    {
        /** @var Context $context */
        $context = $this->module->getService(Context::class);

        /** @var CredentialsProvider $credentialsProvider */
        $credentialsProvider = $this->module->getService(CredentialsProvider::class);

        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        /** @var ApplicationContextProvider $applicationContextProvider */
        $applicationContextProvider = $this->module->getService(ApplicationContextProvider::class);

        $isProduction = $applicationContextProvider->get()->getIsProduction();
        $regions = [];

        foreach (Config::SUPPORTED_REGIONS as $regionLocale => $regionName) {
            $region = $credentialsProvider->getCredentialsByRegion($regionLocale);
            $regions[$regionLocale]['regionTitle'] = $regionName;
            $regions[$regionLocale]['prodUsername'] = $region->getProductionUsername();
            $regions[$regionLocale]['prodPassword'] = $region->getProductionPassword();
            $regions[$regionLocale]['prodClientId'] = $region->getProductionClientId();
            $regions[$regionLocale]['sandboxUsername'] = $region->getSandboxUsername();
            $regions[$regionLocale]['sandboxPassword'] = $region->getSandboxPassword();
            $regions[$regionLocale]['sandboxClientId'] = $region->getSandboxClientId();

            $configKey = Config::KLARNA_PAYMENT_CONNECTED_REGIONS[$isProduction ? Environment::PRODUCTION : Environment::SANDBOX][$regionLocale];
            $regions[$regionLocale]['isConnected'] = (bool) $configuration->get($configKey);
        }

        $this->context->smarty->assign([
            'klarnapayment' => [
                'sandbox_current_page_url' => $context->getAdminLink(ModuleTabs::SETTINGS_MODULE_TAB_CONTROLLER_NAME,
                    ['environment' => 'sandbox']),
                'production_current_page_url' => $context->getAdminLink(ModuleTabs::SETTINGS_MODULE_TAB_CONTROLLER_NAME,
                    ['environment' => 'production']),
                'regions' => $regions,
                'merchant_privacy_notice_url' => Config::KLARNA_MERCHANT_PRIVACY_NOTICE_URL,
                'merchant_portal_url' => Config::KLARNA_PAYMENT_MERCHANT_PORTAL_URL[$isProduction ? Environment::PRODUCTION : Environment::SANDBOX],
            ],
        ]);

        $this->fields_value['credentials-settings'] = $this->context->smarty->fetch(
            $this->module->getLocalPath() .
            '/views/templates/admin/settings/credential-settings.tpl'
        );

        $this->context->smarty->assign('infoBox',
            [
                'message' => $this->module->l('To unlock the plugin\'s features, enter your credentials.', self::FILE_NAME),
                'link' => Config::KLARNA_INFO_BOX_URLS['ENDPOINT_DOCS_URL'],
                'linkText' => $this->module->l('Documentation', self::FILE_NAME),
            ]
        );

        $this->fields_value['info-box'] = $this->context->smarty->fetch(
            $this->module->getLocalPath() .
            '/views/templates/admin/settings/info-box.tpl'
        );

        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('Credentials', self::FILE_NAME),
                'icon' => 'icon-chevron-down',
            ],
            'input' => [
                [
                    'type' => 'free',
                    'name' => 'credentials-settings',
                    'form_group_class' => 'no-margin-form-group',
                    'col' => 12,
                ],
                [
                    'type' => 'free',
                    'name' => 'info-box',
                    'form_group_class' => 'no-margin-form-group',
                ],
            ],
            'buttons' => [
                [
                    'title' => $this->module->l('Save', self::FILE_NAME),
                    'class' => 'btn btn-lg pull-right form-btn',
                    'type' => 'submit',
                    'name' => 'submit_credential_settings',
                ],
            ],
        ];

        return parent::renderForm();
    }

    public function displayAdditionalSettings(): string
    {
        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        $this->fields_value[self::KLARNA_PAYMENT_DEBUG_MODE . '_'] =
            $configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_DEBUG_MODE);
        $this->fields_value[self::KLARNA_PAYMENT_ALLOW_ORDER_STATUS_CHANGE . '_'] =
            $configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_ALLOW_ORDER_STATUS_CHANGE);
        $this->fields_value[self::KLARNA_PAYMENT_HPP_SERVICE . '_'] =
            $configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_HPP_SERVICE);

        $this->fields_value[self::KLARNA_PAYMENT_DEFAULT_LOCALE] = $configuration->getByEnvironment(
            Config::KLARNA_PAYMENT_DEFAULT_LOCALE
        );

        /** @var CountryRepositoryInterface $countryRepository */
        $countryRepository = $this->module->getService(CountryRepositoryInterface::class);

        $validCountries = $countryRepository->getCountriesByIsoCode(
            Config::KLARNA_PAYMENT_VALID_COUNTRIES,
            (int) $this->context->language->id
        );

        /** @var Context $context */
        $context = $this->module->getService(Context::class);

        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        /** @var MoneyCalculator $moneyCalculator */
        $moneyCalculator = $this->module->getService(MoneyCalculator::class);

        $this->context->smarty->assign([
            'klarnapayment' => [
                'currency_symbol' => $context->getCurrency()->symbol ?? $context->getCurrency()->sign,
                'min_value' => $moneyCalculator->calculateIntoFloat(
                    $configuration->getByEnvironmentAsInteger(Config::KLARNA_PAYMENT_ORDER_MIN)
                ),
                'max_value' => $moneyCalculator->calculateIntoFloat(
                    $configuration->getByEnvironmentAsInteger(Config::KLARNA_PAYMENT_ORDER_MAX)
                ),
            ],
        ]);

        $this->fields_value['order-min-max-inputs'] = $this->context->smarty->fetch(
            $this->module->getLocalPath() .
            '/views/templates/admin/settings/order-min-max-inputs.tpl'
        );

        $this->context->smarty->assign([
            'klarnapayment' => [
                'text' => $this->module->l('When the setting is enabled, the order status is automatically updated based on the status from the Klarna dashboard to avoid a mismatch between systems.', self::FILE_NAME),
            ],
        ]);

        $helpIconOrder = $this->context->smarty->fetch(
            $this->module->getLocalPath() .
            '/views/templates/admin/settings/help-icon.tpl'
        );

        $this->context->smarty->assign([
            'klarnapayment' => [
                'text' => $this->module->l('Accept payments on a separate Klarna page. Used to improve compatibility with 3rd party checkout modules. Enabled by default when an OPC module is in use.', self::FILE_NAME),
            ],
        ]);

        $helpIconHPP = $this->context->smarty->fetch(
            $this->module->getLocalPath() .
            '/views/templates/admin/settings/help-icon.tpl'
        );

        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('Additional settings', self::FILE_NAME),
                'icon' => 'icon-chevron-down',
            ],
            'input' => [
                [
                    'form_group_class' => 'klarna-admin-checkbox-form-group',
                    'class' => 'klarna-admin-checkbox',
                    'type' => 'checkbox',
                    'name' => self::KLARNA_PAYMENT_DEBUG_MODE,
                    'values' => [
                        'query' => [
                            [
                                'id' => '',
                                'name' => $this->module->l('Enable debug mode', self::FILE_NAME),
                                'val' => '1',
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'checked' => 'checked',
                ],
                [
                    'form_group_class' => 'klarna-admin-checkbox-form-group',
                    'class' => 'klarna-admin-checkbox',
                    'type' => 'checkbox',
                    'name' => self::KLARNA_PAYMENT_ALLOW_ORDER_STATUS_CHANGE,
                    'values' => [
                        'query' => [
                            [
                                'id' => '',
                                'name' => $this->module->l('Enable automatic order status change', self::FILE_NAME) . ' '
                                    . $helpIconOrder,
                                'val' => '1',
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'form_group_class' => 'klarna-admin-checkbox-form-group',
                    'class' => 'klarna-admin-checkbox',
                    'type' => 'checkbox',
                    'name' => self::KLARNA_PAYMENT_HPP_SERVICE,
                    'values' => [
                        'query' => [
                            [
                                'id' => '',
                                'name' => $this->module->l('Enable hosted payment page (HPP)', self::FILE_NAME) . ' '
                                    . $helpIconHPP,
                                'val' => '1',
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'select',
                    'col' => 8,
                    'form_group_class' => 'klarna-admin-form-group',
                    'label' => $this->module->l('Default country', self::FILE_NAME),
                    'desc' => $this->module->l('Select the default country if the locale cannot be determined based on customer\'s address in checkout.',
                        self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_DEFAULT_LOCALE,
                    'options' => [
                        'query' => $validCountries,
                        'id' => 'iso_code',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'free',
                    'col' => 8,
                    'form_group_class' => 'no-margin-form-group',
                    'name' => 'order-min-max-inputs',
                ],
            ],
            'buttons' => [
                [
                    'title' => $this->module->l('Save', self::FILE_NAME),
                    'class' => 'btn btn-lg pull-right form-btn',
                    'type' => 'submit',
                    'name' => 'submit_additional_settings',
                ],
            ],
        ];

        return parent::renderForm();
    }

    private function displayAutoCaptureSettings(): string
    {
        /** @var Context $context */
        $context = $this->module->getService(Context::class);

        /** @var OrderStateRepositoryInterface $orderStateRepositoryInterface */
        $orderStateRepositoryInterface = $this->module->getService(OrderStateRepositoryInterface::class);

        /** @var OrderStatusProvider $orderStatusProvider */
        $orderStatusProvider = $this->module->getService(OrderStatusProvider::class);

        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        if (false === $configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_ENABLE_BOX)) {
            return '';
        }

        $this->show_form_cancel_button = false;

        $moduleOrderStatuses = $orderStatusProvider->getModuleOrderStatuses();

        $shopOrderStatuses = $orderStateRepositoryInterface->getOrderStates($context->getLanguageId());

        $mappedOrderStatuses = [];

        foreach ($shopOrderStatuses as $orderStatus) {
            $mappedOrderStatuses[] = [
                'name' => $orderStatus['name'],
                'id_status' => $orderStatus['id_order_state'],
            ];
        }

        $this->fields_value[self::KLARNA_PAYMENT_AUTO_CAPTURE_ORDER . '_'] = $configuration->getByEnvironmentAsBoolean(
            Config::KLARNA_PAYMENT_AUTO_CAPTURE_ORDER
        );

        $this->fields_value[self::KLARNA_PAYMENT_AUTO_CAPTURE_ORDER_STATUSES_INPUT] = explode(
            ',',
            $configuration->getByEnvironment(Config::KLARNA_PAYMENT_AUTO_CAPTURE_ORDER_STATUSES)
        );

        $input = [];

        foreach ($moduleOrderStatuses as $orderStatusKey => $orderStatus) {
            $input[] = [
                'type' => 'select',
                'col' => 8,
                'form_group_class' => 'klarna-admin-form-group',
                'label' => sprintf($this->module->l('Status for %s orders', self::FILE_NAME), $orderStatus['name']),
                'desc' => sprintf($this->module->l('Definition: %s', self::FILE_NAME), $orderStatus['definition']),
                'name' => $orderStatusKey,
                'options' => [
                    'query' => OrderState::getOrderStates($this->context->language->id) ?: [],
                    'id' => 'id_order_state',
                    'name' => 'name',
                ],
            ];
            $this->fields_value[$orderStatusKey] = $configuration->getAsInteger($orderStatusKey);
        }

        $input = array_merge($input, [
            [
                'form_group_class' => 'klarna-admin-checkbox-form-group',
                'class' => 'klarna-admin-checkbox',
                'type' => 'checkbox',
                'col' => 8,
                'name' => self::KLARNA_PAYMENT_AUTO_CAPTURE_ORDER,
                'values' => [
                    'query' => [
                        [
                            'id' => '',
                            'name' => $this->module->l('Enable capture upon fulfillment', self::FILE_NAME),
                            'val' => '1',
                        ],
                    ],
                    'id' => 'id',
                    'name' => 'name',
                ],
                'hint' => $this->module->l('Enable this setting to trigger the Klarna order capture API call when the PrestaShop order is set to one of the configured status',
                    self::FILE_NAME),
            ],
            [
                'class' => 'multiselect-options',
                'form_group_class' => 'klarna-admin-form-group',
                'type' => 'select',
                'col' => 8,
                'name' => self::KLARNA_PAYMENT_AUTO_CAPTURE_ORDER_STATUSES_INPUT,
                'label' => $this->module->l('Order statuses', self::FILE_NAME),
                'desc' => $this->module->l('Order statuses which trigger the Klarna order to be captured', self::FILE_NAME),
                'options' => [
                    'query' => $mappedOrderStatuses,
                    'id' => 'id_status',
                    'name' => 'name',
                ],
                'multiple' => true,
            ],
        ]);

        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('Prestashop order settings', self::FILE_NAME),
                'icon' => 'icon-chevron-down',
            ],
            'input' => $input,
            'buttons' => [
                [
                    'title' => $this->module->l('Save', self::FILE_NAME),
                    'class' => 'btn btn-lg pull-right form-btn',
                    'type' => 'submit',
                    'name' => 'submit_statuses',
                ],
            ],
        ];

        return parent::renderForm();
    }

    private function displayExpressCheckoutPlacementConfiguration(): string
    {
        /** @var FeatureAvailability $featureAvailability */
        $featureAvailability = $this->module->getService(FeatureAvailability::class);

        $isFeaturesAvailable = $featureAvailability->verify(
            Config::KLARNA_PAYMENTS_FEATURES[Config::KLARNA_FEATURE_EXPRESS_CHECKOUT_1_STEP],
            Config::KLARNA_PAYMENTS_FEATURES[Config::KLARNA_FEATURE_EXPRESS_CHECKOUT_2_STEP]
        );

        if (!$isFeaturesAvailable) {
            return '';
        }

        $placements = [
            [
                'value' => KlarnaExpressCheckoutPlacement::PRODUCT_PAGE,
                'label' => $this->module->l('Product page', self::FILE_NAME),
            ],
            [
                'value' => KlarnaExpressCheckoutPlacement::CART_PAGE,
                'label' => $this->module->l('Cart page', self::FILE_NAME),
            ],
        ];

        $buttonThemes = [
            [
                'value' => KlarnaExpressCheckoutButtonTheme::DEFAULT,
                'label' => $this->module->l('Dark (default)', self::FILE_NAME),
            ],
            [
                'value' => KlarnaExpressCheckoutButtonTheme::LIGHT,
                'label' => $this->module->l('Light', self::FILE_NAME),
            ],
            [
                'value' => KlarnaExpressCheckoutButtonTheme::OUTLINED,
                'label' => $this->module->l('Outlined', self::FILE_NAME),
            ],
        ];

        $buttonShapes = [
            [
                'value' => KlarnaExpressCheckoutButtonShape::DEFAULT,
                'label' => $this->module->l('Rounded corners (default)', self::FILE_NAME),
            ],
            [
                'value' => KlarnaExpressCheckoutButtonShape::PILL,
                'label' => $this->module->l('Pill', self::FILE_NAME),
            ],
            [
                'value' => KlarnaExpressCheckoutButtonShape::RECT,
                'label' => $this->module->l('Rectangular', self::FILE_NAME),
            ],
        ];

        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        $this->fields_value[self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE . '_'] =
            $configuration->getByEnvironment(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE);

        $this->fields_value[self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS_INPUT] = explode(
            ',',
            $configuration->getByEnvironment(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS)
        );

        $this->fields_value[self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME] =
            $configuration->getByEnvironment(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME);

        $this->fields_value[self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE] =
            $configuration->getByEnvironment(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE);

        $this->fields_value['kec-image-preview'] = $this->renderKecImagesPreview();

        $this->context->smarty->assign('infoBox',
            [
                'message' => $this->module->l('An improved way to drive shoppers straight to the checkout, with all their preferences already set.', self::FILE_NAME),
                'link' => Config::KLARNA_INFO_BOX_URLS['EC_LEARN_URL'],
                'linkText' => $this->module->l('Learn more', self::FILE_NAME),
                'link2' => Config::KLARNA_INFO_BOX_URLS['EC_DOCS_URL'],
                'linkText2' => $this->module->l('Documentation', self::FILE_NAME),
            ]
        );

        $this->fields_value['info-box'] = $this->context->smarty->fetch(
            $this->module->getLocalPath() .
            '/views/templates/admin/settings/info-box.tpl'
        );

        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('Express Checkout', self::FILE_NAME),
                'icon' => 'icon-chevron-down',
            ],
            'input' => [
                [
                    'form_group_class' => 'klarna-admin-checkbox-form-group',
                    'class' => 'klarna-admin-checkbox',
                    'type' => 'checkbox',
                    'name' => self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_ACTIVE,
                    'values' => [
                        'query' => [
                            [
                                'id' => '',
                                'name' => $this->module->l('Enable Klarna Express Checkout', self::FILE_NAME),
                                'val' => '1',
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'class' => 'multiselect-options',
                    'form_group_class' => 'klarna-admin-form-group',
                    'type' => 'select',
                    'col' => 8,
                    'name' => self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_PLACEMENTS_INPUT,
                    'label' => $this->module->l('Placement', self::FILE_NAME),
                    'desc' => $this->module->l('Choose where Klarna Express Checkout button will be displayed', self::FILE_NAME),
                    'options' => [
                        'query' => $placements,
                        'id' => 'value',
                        'name' => 'label',
                    ],
                    'multiple' => true,
                ],
                [
                    'type' => 'select',
                    'col' => 8,
                    'class' => 'klarna-admin-select kec-theme-select',
                    'form_group_class' => 'klarna-admin-form-group',
                    'label' => $this->module->l('Theme', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME,
                    'options' => [
                        'query' => $buttonThemes,
                        'id' => 'value',
                        'name' => 'label',
                    ],
                ],
                [
                    'type' => 'select',
                    'col' => 8,
                    'class' => 'klarna-admin-select kec-shape-select',
                    'form_group_class' => 'klarna-admin-form-group',
                    'label' => $this->module->l('Shape', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE,
                    'options' => [
                        'query' => $buttonShapes,
                        'id' => 'value',
                        'name' => 'label',
                    ],
                ],
                [
                    'type' => 'free',
                    'name' => 'info-box',
                    'form_group_class' => 'no-margin-form-group',
                ],
                [
                    'form_group_class' => 'klarna-admin-image-form-group kec-image-form-group',
                    'type' => 'free',
                    'label' => '-', // workaround to fix form styling - this label is not visible
                    'name' => 'kec-image-preview',
                    'col' => 12,
                ],
            ],
            'buttons' => [
                [
                    'title' => $this->module->l('Save', self::FILE_NAME),
                    'class' => 'btn btn-lg pull-right form-btn',
                    'type' => 'submit',
                    'name' => 'submit_express_checkout_settings',
                ],
            ],
        ];

        return $this->renderForm();
    }

    private function displaySignInWithKlarnaSettings(): string
    {
        /** @var FeatureAvailability $featureAvailability */
        $featureAvailability = $this->module->getService(FeatureAvailability::class);

        $isFeaturesAvailable = $featureAvailability->verify(
            Config::KLARNA_PAYMENTS_FEATURES[Config::KLARNA_FEATURE_SIGN_IN_ACCOUNT_CREATION_PAGE],
            Config::KLARNA_PAYMENTS_FEATURES[Config::KLARNA_FEATURE_SIGN_IN_AUTHENTICATION_PAGE],
            Config::KLARNA_PAYMENTS_FEATURES[Config::KLARNA_FEATURE_SIGN_IN_CART_PAGE]
        );

        if (!$isFeaturesAvailable) {
            return '';
        }

        $placements = [
            [
                'value' => SignInWithKlarnaBadgeAlignment::DEFAULT,
                'label' => $this->module->l('Default', self::FILE_NAME),
            ],
            [
                'value' => SignInWithKlarnaBadgeAlignment::LEFT,
                'label' => $this->module->l('Left', self::FILE_NAME),
            ],
            [
                'value' => SignInWithKlarnaBadgeAlignment::CENTER,
                'label' => $this->module->l('Centered', self::FILE_NAME),
            ],
        ];

        $buttonThemes = [
            [
                'value' => KlarnaSignInWithKlarnaButtonTheme::DEFAULT,
                'label' => $this->module->l('Dark (default)', self::FILE_NAME),
            ],
            [
                'value' => KlarnaSignInWithKlarnaButtonTheme::LIGHT,
                'label' => $this->module->l('Light', self::FILE_NAME),
            ],
            [
                'value' => KlarnaSignInWithKlarnaButtonTheme::OUTLINED,
                'label' => $this->module->l('Outlined', self::FILE_NAME),
            ],
        ];

        $buttonShapes = [
            [
                'value' => KlarnaSignInWithKlarnaButtonShape::DEFAULT,
                'label' => $this->module->l('Rounded corners (default)', self::FILE_NAME),
            ],
            [
                'value' => KlarnaSignInWithKlarnaButtonShape::PILL,
                'label' => $this->module->l('Pill', self::FILE_NAME),
            ],
            [
                'value' => KlarnaSignInWithKlarnaButtonShape::RECT,
                'label' => $this->module->l('Rectangular', self::FILE_NAME),
            ],
        ];

        $this->fields_value['siwk-images-preview'] = $this->renderSiwkImagesPreview();

        /** @var ApplicationContextProvider $applicationContextProvider */
        $applicationContextProvider = $this->module->getService(ApplicationContextProvider::class);

        $this->context->smarty->assign([
            'klarnapayment' => [
                'merchant_portal_url' => $applicationContextProvider->get()->getIsProduction()
                    ? Config::KLARNA_PAYMENT_MERCHANT_PORTAL_URL['production']
                    : Config::KLARNA_PAYMENT_MERCHANT_PORTAL_URL['sandbox'],
            ],
        ]);

        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        $placementOptions = [];

        foreach (Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_PLACEMENT_OPTIONS as $key => $value) {
            $placementOptions[] = [
                'name' => $value,
                'id_placement' => $key,
            ];
        }

        $this->fields_value[self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ACTIVE . '_'] = $configuration->getByEnvironmentAsBoolean(
            Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ACTIVE
        );

        $this->fields_value[self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_PLACEMENTS_INPUT] = explode(
            ',',
            $configuration->getByEnvironment(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_PLACEMENTS)
        );

        $scope = $configuration->getByEnvironment(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ON_SIGN_IN_SCOPE);

        $this->fields_value[self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_REQUIRED_CUSTOMER_DATA_EMAIL_ADDRESS] = strpos($scope, 'profile:email') !== false;
        $this->fields_value[self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_REQUIRED_CUSTOMER_DATA_FULL_NAME] = strpos($scope, 'profile:name') !== false;
        $this->fields_value[self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_REQUIRED_CUSTOMER_DATA_PHONE_NUMBER] = strpos($scope, 'profile:phone') !== false;

        $this->fields_value[self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_CUSTOMER_DATA_DATE_OF_BIRTH] = strpos($scope, 'date_of_birth') !== false;
        $this->fields_value[self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_CUSTOMER_DATA_COUNTRY] = strpos($scope, 'country') !== false;
        $this->fields_value[self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_CUSTOMER_DATA_BILLING_ADDRESS] = strpos($scope, 'billing_address') !== false;
        $this->fields_value[self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_CUSTOMER_DATA_LANGUAGE_PREFERENCE] = strpos($scope, 'profile:locale') !== false;

        $this->fields_value[self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_THEME] =
            $configuration->getByEnvironment(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_THEME);

        $this->fields_value[self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_SHAPE] =
            $configuration->getByEnvironment(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_SHAPE);

        $this->fields_value[self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_ALIGNMENT] =
            $configuration->getByEnvironment(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_ALIGNMENT);

        $this->context->smarty->assign([
            'klarnapayment' => [
                'signInWithKlarna' => [
                    'redirectUrl' => $this->generateRedirectUrl(),
                ],
            ],
        ]);

        $this->fields_value[self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_REDIRECT_URL_NAME] = $this->context->smarty->fetch(
            $this->module->getLocalPath() .
            '/views/templates/admin/settings/redirect-url-input.tpl'
        );

        $this->context->smarty->assign('infoBox',
            [
                'message' => $this->module->l('Customer email and full name are mandatory fields in PrestaShop. It is important to ensure that these same settings are also enabled in your Klarna dashboard to maintain consistency across platforms.', self::FILE_NAME),
                'link' => $applicationContextProvider->get()->getIsProduction()
                    ? Config::KLARNA_PAYMENT_MERCHANT_PORTAL_URL['production']
                    : Config::KLARNA_PAYMENT_MERCHANT_PORTAL_URL['sandbox'],
                'linkText' => $this->module->l('Merchant Portal', self::FILE_NAME),
            ]
        );

        $this->fields_value['info-box'] = $this->context->smarty->fetch(
            $this->module->getLocalPath() .
            '/views/templates/admin/settings/info-box.tpl'
        );

        if (!$configuration->getAsBoolean('PS_REWRITING_SETTINGS')) {
            $this->context->smarty->assign('warningBox',
                [
                    'message' => $this->module->l('Important Notice: Enable Friendly URLs for Klarna Integration.', self::FILE_NAME),
                    'description' => $this->module->l('To ensure the proper functionality of the Sign in with Klarna feature, you must enable Friendly URLs in Shop Parameters, then goto Traffic and SEO.', self::FILE_NAME),
                ]
            );

            $this->fields_value['warning-box'] = $this->context->smarty->fetch(
                $this->module->getLocalPath() .
                '/views/templates/admin/settings/warning-box.tpl'
            );
        }

        // Define the form
        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('Sign in with Klarna', self::FILE_NAME),
                'icon' => 'icon-chevron-down',
            ],
            'input' => [
                [
                    'type' => 'free',
                    'name' => 'warning-box',
                    'form_group_class' => 'no-margin-form-group',
                ],
                [
                    'type' => 'checkbox',
                    'name' => self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ACTIVE,
                    'col' => 8,
                    'class' => 'klarna-admin-checkbox',
                    'form_group_class' => 'klarna-admin-checkbox-form-group',
                    'values' => [
                        'query' => [
                            [
                                'id' => '',
                                'name' => $this->module->l('Enable Sign in with Klarna', self::FILE_NAME),
                                'val' => '1',
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                    'checked' => 'checked',
                ],
                [
                    'class' => 'multiselect-options',
                    'form_group_class' => 'klarna-admin-form-group',
                    'type' => 'select',
                    'col' => 8,
                    'name' => self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_PLACEMENTS_INPUT,
                    'label' => $this->module->l('Placement', self::FILE_NAME),
                    'desc' => $this->module->l('Choose where Sign in with Klarna button will be displayed', self::FILE_NAME),
                    'options' => [
                        'query' => $placementOptions,
                        'id' => 'id_placement',
                        'name' => 'name',
                    ],
                    'multiple' => true,
                ],
                [
                    'type' => 'free',
                    'name' => self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_REDIRECT_URL_NAME,
                    'form_group_class' => 'no-margin-form-group',
                ],
                // Required Customer Data Fields
                [
                    'type' => 'checkbox',
                    'title' => $this->module->l('Required Customer Data', self::FILE_NAME),
                    'label' => $this->module->l('Required Customer Data', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_REQUIRED_CUSTOMER_DATA,
                    'col' => 8,
                    'class' => 'klarna-admin-checkbox klarna-siwk-required-checkbox',
                    'form_group_class' => 'klarna-admin-checkbox-form-group klarna-siwk-title',
                    'desc' => $this->module->l('Manage what data you require from the customer in order to create an account. If they choose not to share this data, they will be unable to create an account.', self::FILE_NAME),
                    'values' => [
                        'query' => [
                            [
                                'id' => 'email_address',
                                'name' => $this->module->l('Email Address', self::FILE_NAME),
                                'val' => 'email',
                            ],
                            [
                                'id' => 'full_name',
                                'name' => $this->module->l('Full Name', self::FILE_NAME),
                                'val' => 'profile:name',
                            ],
                            [
                                'id' => 'phone_number',
                                'name' => $this->module->l('Phone Number', self::FILE_NAME),
                                'val' => 'phone',
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                // Info box for required fields
                [
                    'type' => 'free',
                    'name' => 'info-box',
                    'form_group_class' => 'no-margin-form-group',
                ],
                // Optional Customer Data Fields
                [
                    'type' => 'checkbox',
                    'label' => $this->module->l('Request Additional Customer Data', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_ADDITIONAL_CUSTOMER_DATA,
                    'col' => 8,
                    'class' => 'klarna-admin-checkbox',
                    'form_group_class' => 'klarna-admin-checkbox-form-group klarna-siwk-title',
                    'desc' => $this->module->l('The data you request below is optional for the customer, therefore they can decide if they want to provide access to it, or not. The data will be requested during the account creation step.', self::FILE_NAME),
                    'values' => [
                        'query' => [
                            [
                                'id' => 'date_of_birth',
                                'name' => $this->module->l('Date of Birth', self::FILE_NAME),
                                'val' => 'profile:date_of_birth',
                            ],
                            [
                                'id' => 'country',
                                'name' => $this->module->l('Country', self::FILE_NAME),
                                'val' => 'profile:country',
                            ],
                            [
                                'id' => 'billing_address',
                                'name' => $this->module->l('Billing Address', self::FILE_NAME),
                                'val' => 'billing_address',
                            ],
                            [
                                'id' => 'language_preference',
                                'name' => $this->module->l('Language Preference', self::FILE_NAME),
                                'val' => 'profile:locale',
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'select',
                    'col' => 8,
                    'class' => 'klarna-admin-select siwk-theme-select',
                    'form_group_class' => 'klarna-admin-form-group',
                    'label' => $this->module->l('Theme', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_THEME,
                    'options' => [
                        'query' => $buttonThemes,
                        'id' => 'value',
                        'name' => 'label',
                    ],
                ],
                [
                    'type' => 'select',
                    'col' => 8,
                    'class' => 'klarna-admin-select siwk-shape-select',
                    'form_group_class' => 'klarna-admin-form-group',
                    'label' => $this->module->l('Shape', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_SHAPE,
                    'options' => [
                        'query' => $buttonShapes,
                        'id' => 'value',
                        'name' => 'label',
                    ],
                ],
                [
                    'type' => 'select',
                    'col' => 8,
                    'class' => 'klarna-admin-select siwk-alignment-select',
                    'form_group_class' => 'klarna-admin-form-group',
                    'label' => $this->module->l('Alignment', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_ALIGNMENT,
                    'options' => [
                        'query' => $placements,
                        'id' => 'value',
                        'name' => 'label',
                    ],
                ],
                [
                    'form_group_class' => 'klarna-admin-image-form-group siwk-image-form-group',
                    'label' => '-', // workaround to fix form styling - this label is not visible
                    'type' => 'free',
                    'name' => 'siwk-images-preview',
                    'col' => 12,
                ],
            ],
            'buttons' => [
                [
                    'title' => $this->module->l('Save', self::FILE_NAME),
                    'class' => 'btn btn-lg pull-right form-btn',
                    'type' => 'submit',
                    'name' => 'submit_sign_in_with_klarna',
                ],
            ],
        ];

        return parent::renderForm();
    }

    public function displayOnsiteMessagingConfiguration(): string
    {
        /** @var FeatureAvailability $featureAvailability */
        $featureAvailability = $this->module->getService(FeatureAvailability::class);

        $isFeaturesAvailable = $featureAvailability->verify(
            Config::KLARNA_PAYMENTS_FEATURES[Config::KLARNA_FEATURE_ON_SITE_MESSAGING_CART_PAGE],
            Config::KLARNA_PAYMENTS_FEATURES[Config::KLARNA_FEATURE_ON_SITE_MESSAGING_PRODUCT_PAGE]
        );

        if (!$isFeaturesAvailable) {
            return '';
        }

        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        $this->fields_value[self::KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE . '_'] = $configuration->getByEnvironmentAsBoolean(
            Config::KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE
        );

        $this->fields_value['osm-images-preview'] = $this->renderOsmImagesPreview();
        $this->fields_value[self::KLARNA_PAYMENT_ONSITE_MESSAGING_INFOPAGE_PATH_KEY] = $configuration->get(
            Config::ON_SITE_MESSAGING_INFOPAGE_CONFIG
        );

        $this->fields_value[self::KLARNA_PAYMENT_OSM_INFOPAGE_ACTIVE . '_'] = $configuration->getByEnvironmentAsBoolean(
            Config::ON_SITE_MESSAGING_INFOPAGE_ACTIVE_CONFIG
        );

        foreach ($this->getOnsiteMessagingSettingMaps() as $currentClassKey => $configurationKey) {
            $this->fields_value[$currentClassKey] = $configuration->getByEnvironment(
                $configurationKey
            );
        }

        $this->context->smarty->assign('infoBox',
            [
                'message' => $this->module->l('Maximize conversion by letting your customers know about their purchase power with tailored messaging throughout the shopping journey.', self::FILE_NAME),
                'link' => Config::KLARNA_INFO_BOX_URLS['ONSITE_MESSAGING_LEARN_MORE_URL'],
                'linkText' => $this->module->l('Learn more', self::FILE_NAME),
                'link2' => Config::KLARNA_INFO_BOX_URLS['ONSITE_MESSAGING_DOCS_URL'],
                'linkText2' => $this->module->l('Documentation', self::FILE_NAME),
            ]
        );

        $this->context->smarty->assign('osmInfoPage',
            [
                'baseUrl' => $this->context->link->getBaseLink(),
                'infopagePath' => $configuration->get(Config::ON_SITE_MESSAGING_INFOPAGE_CONFIG)
                    ?: Config::ON_SITE_MESSAGING_INFOPAGE_DEFAULT_PATH,
                'configName' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_INFOPAGE_PATH_KEY,
            ]
        );

        $this->fields_value['info-box'] = $this->context->smarty->fetch(
            $this->module->getLocalPath() .
            '/views/templates/admin/settings/info-box.tpl'
        );

        $this->fields_value[self::KLARNA_PAYMENT_ONSITE_MESSAGING_INFOPAGE_PATH_KEY] = $this->context->smarty->fetch(
            $this->module->getLocalPath() .
            '/views/templates/admin/settings/osm-infopage-path-input.tpl'
        );

        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('On-site messaging', self::FILE_NAME),
                'icon' => 'icon-chevron-down',
            ],
            'input' => [
                [
                    'form_group_class' => 'klarna-admin-checkbox-form-group',
                    'class' => 'klarna-admin-checkbox',
                    'type' => 'checkbox',
                    'name' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_ACTIVE,
                    'values' => [
                        'query' => [
                            [
                                'id' => '',
                                'name' => $this->module->l('Enable on-site messaging', self::FILE_NAME),
                                'val' => '1',
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'select',
                    'col' => 8,
                    'class' => 'osm-theme-select klarna-admin-select',
                    'form_group_class' => 'klarna-admin-form-group',
                    'label' => $this->module->l('Theme', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_THEME,
                    'options' => [
                        'query' => [
                            [
                                'value' => OnsiteMessagingPlacementThemes::DEFAULT,
                                'label' => $this->module->l('Default', self::FILE_NAME),
                            ],
                            [
                                'value' => OnsiteMessagingPlacementThemes::DARK,
                                'label' => $this->module->l('Dark', self::FILE_NAME),
                            ],
                            [
                                'value' => OnsiteMessagingPlacementThemes::LIGHT,
                                'label' => $this->module->l('Light', self::FILE_NAME),
                            ],
                            [
                                'value' => OnsiteMessagingPlacementThemes::CUSTOM,
                                'label' => $this->module->l('Custom', self::FILE_NAME),
                            ],
                        ],
                        'id' => 'value',
                        'name' => 'label',
                    ],
                ],
                [
                    'type' => 'select',
                    'form_group_class' => 'klarna-admin-form-group',
                    'class' => 'klarna-admin-select osm-placement-select product-page-place-select',
                    'col' => 8,
                    'label' => $this->module->l('Product page placement', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_DATA_KEY,
                    'options' => [
                        'query' => [
                            [
                                'value' => '',
                                'label' => $this->module->l('None', self::FILE_NAME),
                            ],
                            [
                                'value' => 'credit-promotion-badge',
                                'label' => $this->module->l('Show with Klarna badge (recommended)', self::FILE_NAME),
                            ],
                            [
                                'value' => 'credit-promotion-auto-size',
                                'label' => $this->module->l('Show without Klarna badge', self::FILE_NAME),
                            ],
                        ],
                        'id' => 'value',
                        'name' => 'label',
                    ],
                ],
                [
                    'type' => 'select',
                    'form_group_class' => 'klarna-admin-form-group',
                    'class' => 'klarna-admin-select osm-placement-select cart-page-place-select',
                    'col' => 8,
                    'label' => $this->module->l('Cart page placement', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_DATA_KEY,
                    'options' => [
                        'query' => [
                            [
                                'value' => '',
                                'label' => $this->module->l('None', self::FILE_NAME),
                            ],
                            [
                                'value' => 'credit-promotion-badge',
                                'label' => $this->module->l('Show with Klarna badge (recommended)', self::FILE_NAME),
                            ],
                            [
                                'value' => 'credit-promotion-auto-size',
                                'label' => $this->module->l('Show without Klarna badge', self::FILE_NAME),
                            ],
                        ],
                        'id' => 'value',
                        'name' => 'label',
                    ],
                ],
                [
                    'type' => 'select',
                    'form_group_class' => 'klarna-admin-form-group',
                    'class' => 'klarna-admin-select osm-placement-select top-page-place-select',
                    'col' => 8,
                    'label' => $this->module->l('Top of page placement', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_DATA_KEY,
                    'options' => [
                        'query' => [
                            [
                                'value' => '',
                                'label' => $this->module->l('None', self::FILE_NAME),
                            ],
                            [
                                'value' => 'top-strip-promotion-auto-size',
                                'label' => $this->module->l('Show top banner without Klarna badge', self::FILE_NAME),
                            ],
                            [
                                'value' => 'top-strip-promotion-badge',
                                'label' => $this->module->l('Show top banner with Klarna badge (recommended)', self::FILE_NAME),
                            ],
                        ],
                        'id' => 'value',
                        'name' => 'label',
                    ],
                ],
                [
                    'type' => 'select',
                    'form_group_class' => 'klarna-admin-form-group',
                    'class' => 'footer-place-select osm-placement-select klarna-admin-select',
                    'col' => 8,
                    'label' => $this->module->l('Footer placement', self::FILE_NAME),
                    'name' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_DATA_KEY,
                    'options' => [
                        'query' => [
                            [
                                'value' => '',
                                'label' => $this->module->l('None', self::FILE_NAME),
                            ],
                            [
                                'value' => 'footer-promotion-auto-size',
                                'label' => $this->module->l('Show footer banner', self::FILE_NAME),
                            ],
                        ],
                        'id' => 'value',
                        'name' => 'label',
                    ],
                ],
                [
                    'form_group_class' => 'klarna-admin-checkbox-form-group osm-infopage-spacing',
                    'class' => 'klarna-admin-checkbox',
                    'type' => 'checkbox',
                    'name' => self::KLARNA_PAYMENT_OSM_INFOPAGE_ACTIVE,
                    'values' => [
                        'query' => [
                            [
                                'id' => '',
                                'name' => $this->module->l('Enable on-site messaging infopage', self::FILE_NAME),
                                'val' => '1',
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'free',
                    'name' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_INFOPAGE_PATH_KEY,
                    'form_group_class' => 'no-margin-form-group',
                ],
                [
                    'type' => 'free',
                    'name' => 'info-box',
                    'form_group_class' => 'no-margin-form-group',
                ],
                [
                    'form_group_class' => 'klarna-admin-image-form-group',
                    'label' => '-', // workaround to fix form styling - this label is not visible
                    'type' => 'free',
                    'name' => 'osm-images-preview',
                    'col' => 12,
                ],
            ],
            'buttons' => [
                [
                    'title' => $this->module->l('Save', self::FILE_NAME),
                    'class' => 'btn btn-lg pull-right form-btn',
                    'type' => 'submit',
                    'name' => 'submit_onsite_messaging_settings',
                ],
            ],
        ];

        return $this->renderForm();
    }

    private function getOnsiteMessagingSettingMaps(): array
    {
        return [
            self::KLARNA_PAYMENT_ONSITE_MESSAGING_THEME => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_THEME,
            self::KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_DATA_KEY => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_DATA_KEY,
            self::KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_DATA_KEY => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_DATA_KEY,
            self::KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_DATA_KEY => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_DATA_KEY,
            self::KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_DATA_KEY => Config::KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_DATA_KEY,
        ];
    }

    public function displayKlarnaPaymentsSettings()
    {
        /** @var FeatureAvailability $featureAvailability */
        $featureAvailability = $this->module->getService(FeatureAvailability::class);
        $isFeaturesAvailable = $featureAvailability->verify(
            Config::KLARNA_PAYMENTS_FEATURES[Config::KLARNA_FEATURE_PAYMENTS_PAYMENTS],
            Config::KLARNA_PAYMENTS_FEATURES[Config::KLARNA_FEATURE_PAYMENTS_RECURRING]
        );

        if (!$isFeaturesAvailable) {
            return '';
        }

        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        $this->fields_value[sprintf('%s_', self::KLARNA_PAYMENT_ENABLE_BOX)] =
            $configuration->getByEnvironment(Config::KLARNA_PAYMENT_ENABLE_BOX);

        $this->fields_value['klarna-payments-preview'] = $this->context->smarty->fetch(
            $this->module->getLocalPath() .
            '/views/templates/admin/settings/klarna-payments-settings.tpl'
        );

        $this->context->smarty->assign('infoBox',
            [
                'message' => $this->module->l('Give your customers the ability to pay in flexible ways such as Buy now, Pay Later, Invoicing, Instalments and Financing. Klarna Payments is required to enable the additional solutions.', self::FILE_NAME),
                'link' => Config::KLARNA_INFO_BOX_URLS['KP_DOCS_URL'],
                'linkText' => $this->module->l('Documentation', self::FILE_NAME),
            ]
        );
        $this->fields_value['info-box'] = $this->context->smarty->fetch(
            $this->module->getLocalPath() .
            '/views/templates/admin/settings/info-box.tpl'
        );

        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('Klarna Payments', self::FILE_NAME),
                'icon' => 'icon-chevron-down',
            ],
            'input' => [
                [
                    'form_group_class' => 'klarna-admin-checkbox-form-group col-lg-8',
                    'class' => 'klarna-admin-checkbox',
                    'type' => 'checkbox',
                    'name' => self::KLARNA_PAYMENT_ENABLE_BOX,
                    'values' => [
                        'query' => [
                            [
                                'id' => '',
                                'name' => $this->module->l('Enable Klarna Payments', self::FILE_NAME),
                                'val' => '1',
                            ],
                        ],
                        'id' => 'id',
                        'name' => 'name',
                    ],
                ],
                [
                    'type' => 'free',
                    'name' => 'info-box',
                    'form_group_class' => 'no-margin-form-group',
                ],
                [
                    'type' => 'free',
                    'name' => 'klarna-payments-preview',
                    'form_group_class' => 'no-margin-form-group kp-image-form-group klarna-admin-image-form-group',
                    'col' => 12,
                ],
            ],
            'buttons' => [
                [
                    'title' => $this->module->l('Save', self::FILE_NAME),
                    'class' => 'btn btn-lg pull-right form-btn',
                    'type' => 'submit',
                    'name' => 'submit_enable_klarna_payment',
                ],
            ],
        ];

        return $this->renderForm();
    }

    private function displayCloudSyncDependencies(DependencyBuilder $mboInstaller): string
    {
        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);
        if ($configuration->get(Config::KLARNA_CLOUD_SYNC_DISMISS)) {
            return '';
        }

        $dependencies = $mboInstaller->handleDependencies();
        $this->context->smarty->assign('dependencies', $dependencies);

        $this->fields_value['cloud-sync-dependencies'] = $this->context->smarty->fetch(
            $this->module->getLocalPath() . 'views/templates/admin/dependency_builder.tpl'
        );

        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('PrestaShop CloudSync Dependencies', self::FILE_NAME),
                'icon' => 'icon-chevron-down',
            ],
            'input' => [
                [
                    'type' => 'free',
                    'name' => 'cloud-sync-dependencies',
                    'form_group_class' => 'no-margin-form-group',
                ],
            ],
        ];

        return parent::renderForm();
    }

    private function renderOsmImagesPreview(): string
    {
        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);
        $selectedTheme = $configuration->getByEnvironment(Config::KLARNA_PAYMENT_ONSITE_MESSAGING_THEME);

        if (empty($selectedTheme) || $selectedTheme === OnsiteMessagingPlacementThemes::DEFAULT) {
            $selectedTheme = OnsiteMessagingPlacementThemes::LIGHT;
        }

        $footerPlacement = $configuration->getByEnvironment(Config::KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_DATA_KEY);
        $topPlacement = $configuration->getByEnvironment(Config::KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_DATA_KEY);
        $productPlacement = $configuration->getByEnvironment(Config::KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_DATA_KEY);
        $cartPlacement = $configuration->getByEnvironment(Config::KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_DATA_KEY);

        $this->context->smarty->assign([
            'footerPreviewUrl' => Config::KLARNA_IMAGE_URL['OSM'][$selectedTheme][self::KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_DATA_KEY][$footerPlacement] ?? '',
            'topPreviewUrl' => Config::KLARNA_IMAGE_URL['OSM'][$selectedTheme][self::KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_DATA_KEY][$topPlacement] ?? '',
            'productPreviewUrl' => Config::KLARNA_IMAGE_URL['OSM'][$selectedTheme][self::KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_DATA_KEY][$productPlacement] ?? '',
            'cartPreviewUrl' => Config::KLARNA_IMAGE_URL['OSM'][$selectedTheme][self::KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_DATA_KEY][$cartPlacement] ?? '',
            'footerPlacementKey' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_FOOTER_DATA_KEY,
            'topPlacementKey' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_TOP_OF_PAGE_DATA_KEY,
            'productPlacementKey' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_PRODUCT_PAGE_DATA_KEY,
            'cartPlacementKey' => self::KLARNA_PAYMENT_ONSITE_MESSAGING_CART_PAGE_DATA_KEY,
        ]);

        return $this->context->smarty->fetch(
            $this->module->getLocalPath() .
            '/views/templates/admin/settings/klarna-admin-osm-tab.tpl'
        );
    }

    private function renderKecImagesPreview(): string
    {
        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);
        $selectedTheme = $configuration->getByEnvironment(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_THEME) ?? KlarnaExpressCheckoutButtonTheme::DEFAULT;
        $buttonShape = $configuration->getByEnvironment(Config::KLARNA_PAYMENT_EXPRESS_CHECKOUT_BUTTON_SHAPE) ?? KlarnaExpressCheckoutButtonShape::DEFAULT;

        $this->context->smarty->assign([
            'kecPreviewUrl' => Config::KLARNA_IMAGE_URL['KEC'][$selectedTheme][$buttonShape] ?? '',
        ]);

        return $this->context->smarty->fetch(
            $this->module->getLocalPath() .
            '/views/templates/admin/settings/klarna-admin-express-checkout-tab.tpl'
        );
    }

    private function renderSiwkImagesPreview(): string
    {
        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);
        $selectedTheme = $configuration->getByEnvironment(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_THEME) ?? KlarnaSignInWithKlarnaButtonTheme::DEFAULT;
        $buttonShape = $configuration->getByEnvironment(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_SHAPE) ?? KlarnaSignInWithKlarnaButtonShape::DEFAULT;
        $alignment = $configuration->getByEnvironment(Config::KLARNA_PAYMENT_SIGN_IN_WITH_KLARNA_BUTTON_ALIGNMENT) ?? SignInWithKlarnaBadgeAlignment::DEFAULT;

        $this->context->smarty->assign([
            'siwkPreviewUrl' => Config::KLARNA_IMAGE_URL['SIWK'][$selectedTheme][$buttonShape][$alignment] ?? '',
        ]);

        return $this->context->smarty->fetch(
            $this->module->getLocalPath() .
            '/views/templates/admin/settings/sign-in-with-klarna-tab.tpl'
        );
    }

    private function generateRedirectUrl(): string
    {
        return $this->context->link->getBaseLink() . 'siwk/klarna/callback';
    }

    private function displayPsAccounts()
    {
        $prestashopIntegrationAssetLoader = $this->module->getService(PrestaShopIntegrationAssetLoader::class);
        $prestashopIntegrationAssetLoader->load();

        $this->context->smarty->assign('cloudSyncPathCDC', Config::PRESTASHOP_CLOUDSYNC_CDC);

        $this->fields_value['ps-accounts'] = $this->context->smarty->fetch(
            $this->module->getLocalPath() . '/views/templates/admin/settings/cloudsync.tpl'
        );

        $this->fields_form = [
            'legend' => [
                'title' => $this->module->l('PrestaShop Dependencies', self::FILE_NAME),
                'icon' => 'icon-chevron-down',
            ],
            'input' => [
                [
                    'type' => 'free',
                    'name' => 'ps-accounts',
                    'form_group_class' => 'no-margin-form-group',
                ],
            ],
        ];

        return parent::renderForm();
    }

    private function isLoggedIn(): bool
    {
        /** @var ApplicationContextProvider $applicationContextProvider */
        $applicationContextProvider = $this->module->getService(ApplicationContextProvider::class);

        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        /** @var CredentialsProvider $credentialsProvider */
        $credentialsProvider = $this->module->getService(CredentialsProvider::class);

        $isProduction = $applicationContextProvider->get()->getIsProduction();

        foreach (Config::SUPPORTED_REGIONS as $regionLocale => $regionName) {
            $region = $credentialsProvider->getCredentialsByRegion($regionLocale);

            if ($region->getSandboxUsername() && $region->getSandboxPassword() && !$isProduction) {
                return true;
            }

            if ($region->getProductionUsername() && $region->getProductionPassword() && $isProduction) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $successfullyLoggedInRegions
     *
     * @return bool
     */
    private function anySuccessfulConnections(array $successfullyLoggedInRegions): bool
    {
        foreach ($successfullyLoggedInRegions as $region) {
            if (!empty($region)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param array $successfullyLoggedInRegions
     * @param bool $isProduction
     * @param NotificationHandlerInterface $notificationHandler
     *
     * @return void
     *
     * @throws SmartyException
     */
    private function displaySuccessfulConnectedNotification(array $successfullyLoggedInRegions, bool $isProduction, NotificationHandlerInterface $notificationHandler): void
    {
        $this->context->smarty->assign([
            'successfullyLoggedInRegions' => $successfullyLoggedInRegions,
            'isProduction' => $isProduction,
        ]);

        $notificationHandler->addNotification(self::FILE_NAME, SuccessNotification::create(
            $this->context->smarty->fetch(
                $this->module->getLocalPath() .
                '/views/templates/admin/settings/successful-login-notification.tpl'
            )
        ));
    }

    private function checkAvailableFeatures()
    {
        /** @var FeatureAvailabilityHandler $featureAvailabilityHandler */
        $featureAvailabilityHandler = $this->module->getService(FeatureAvailabilityHandler::class);

        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        /** @var CredentialsConfigurationKeyProvider $credentialsConfigurationKeyProvider */
        $credentialsConfigurationKeyProvider = $this->module->getService(CredentialsConfigurationKeyProvider::class);

        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        $apiKey = $configuration->get($credentialsConfigurationKeyProvider->getApiKey());

        if (empty($apiKey)) {
            $logger->debug(sprintf('%s - API key is empty. Skipping feature availability check.', self::FILE_NAME));

            return;
        }

        try {
            $featureAvailabilityHandler->handle($apiKey);
        } catch (\Exception $e) {
            $logger->info(
                'Unable to check Klarna feature availability. Skipping',
                [
                    'exception' => ExceptionUtility::getExceptions($e),
                ]
            );

            // Enable all features
            foreach (Config::KLARNA_PAYMENTS_FEATURES as $config) {
                $configuration->set($config, 1);
            }
            $configuration->set(Config::KLARNA_PAYMENT_ENABLE_BOX['sandbox'], 1);
            $configuration->set(Config::KLARNA_PAYMENT_ENABLE_BOX['production'], 1);
        }
    }
}
