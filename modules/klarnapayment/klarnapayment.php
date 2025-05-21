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

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Core\Order\Action\AutoCaptureAction;
use KlarnaPayment\Module\Core\Order\Action\RetrieveOrderAction;
use KlarnaPayment\Module\Core\Order\Action\UpdateOrderStatusAction;
use KlarnaPayment\Module\Core\Order\DTO\RetrieveOrderRequestData;
use KlarnaPayment\Module\Core\Order\Exception\CouldNotAutoCapture;
use KlarnaPayment\Module\Core\Order\Repository\KlarnaPaymentOrderRepositoryInterface;
use KlarnaPayment\Module\Core\Payment\Enum\OnsiteMessagingPlacement;
use KlarnaPayment\Module\Core\Tools\Action\PruneOldRecordsAction;
use KlarnaPayment\Module\Core\Tools\DTO\PruneOldRecordsData;
use KlarnaPayment\Module\Core\Tools\Exception\CouldNotPruneOldRecords;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Context;
use KlarnaPayment\Module\Infrastructure\Adapter\PrestaShopCookie;
use KlarnaPayment\Module\Infrastructure\Adapter\Tools;
use KlarnaPayment\Module\Infrastructure\Bootstrap\Exception\CouldNotInstallModule;
use KlarnaPayment\Module\Infrastructure\Bootstrap\Install\Installer;
use KlarnaPayment\Module\Infrastructure\Bootstrap\Uninstall\Uninstaller;
use KlarnaPayment\Module\Infrastructure\Config\OrderStatusConfig;
use KlarnaPayment\Module\Infrastructure\Container\Container;
use KlarnaPayment\Module\Infrastructure\Exception\CouldNotUninstallModule;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Provider\ApplicationContextProvider;
use KlarnaPayment\Module\Infrastructure\Translator\ExceptionTranslatorInterface;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;
use KlarnaPayment\Module\Infrastructure\Utility\NumberUtility;
use KlarnaPayment\Module\Infrastructure\Utility\VersionUtility;
use KlarnaPayment\Module\Infrastructure\Verification\CanRenderWidgets;
use KlarnaPayment\Module\Presentation\Builder\OrderManagementTemplateParameterBuilder;
use KlarnaPayment\Module\Presentation\Loader\HeadSectionAssetLoader;
use KlarnaPayment\Module\Presentation\Loader\OrderManagementAssetLoader;
use KlarnaPayment\Module\Presentation\Loader\PaymentFormAssetLoader;
use KlarnaPayment\Module\Presentation\Presenter\KlarnaExpressCheckoutPresenter;
use KlarnaPayment\Module\Presentation\Presenter\OnsiteMessagingPresenter;
use KlarnaPayment\Module\Presentation\Presenter\PaymentOptionPresenter;
use KlarnaPayment\Module\Presentation\Presenter\SignInWithKlarnaNotificationPresenter;
use KlarnaPayment\Module\Presentation\Presenter\SignInWithKlarnaPresenter;
use KlarnaPayment\Module\Presentation\Presenter\Verification\IsCartAmountValidForKlarnaPayments;

if (!defined('_PS_VERSION_')) {
    exit;
}

class KlarnaPayment extends PaymentModule
{
    const SETTINGS_MODULE_TAB_CONTROLLER_NAME = 'AdminKlarnaPaymentSettings';

    public function __construct()
    {
        $this->name = 'klarnapayment';
        $this->version = '1.6.3';
        $this->author = 'Invertus for Klarna';
        $this->tab = 'payments_gateways';
        $this->module_key = 'fd230b542ac9ba24d3de8fcd0cb36a5f';

        parent::__construct();

        $this->displayName = $this->l('Klarna Official');
        $this->description = $this->l('Grow your business for increased sales and enhanced shopping experiences at no extra costs. Power your growth with Klarna!');
        $this->ps_versions_compliancy = [
            'min' => '1.7.2.0',
            'max' => _PS_VERSION_,
        ];

        $this->autoload();
    }

    public function install(): bool
    {
        $install = parent::install();

        if (!$install) {
            return false;
        }

        /** @var LoggerInterface $logger */
        $logger = $this->getService(LoggerInterface::class);

        /** @var Installer $installer */
        $installer = $this->getService(Installer::class);

        /** @var ExceptionTranslatorInterface $exceptionTranslator */
        $exceptionTranslator = $this->getService(ExceptionTranslatorInterface::class);

        try {
            $installer->init();
        } catch (CouldNotInstallModule $exception) {
            $logger->error('Failed to install module.', [
                'context' => [],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            $this->_errors[] = $exceptionTranslator->translate($exception);

            return false;
        }

        return true;
    }

    public function uninstall(): bool
    {
        $uninstall = parent::uninstall();

        if (!$uninstall) {
            return false;
        }

        /** @var LoggerInterface $logger */
        $logger = $this->getService(LoggerInterface::class);

        /** @var Uninstaller $uninstaller */
        $uninstaller = $this->getService(Uninstaller::class);

        /** @var ExceptionTranslatorInterface $exceptionTranslator */
        $exceptionTranslator = $this->getService(ExceptionTranslatorInterface::class);

        try {
            $uninstaller->init();

            return true;
        } catch (CouldNotUninstallModule $exception) {
            $logger->error('Failed to uninstall module.', [
                'context' => [],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            $this->_errors[] = $exceptionTranslator->translate($exception);

            return false;
        }
    }

    /**
     * Gets service that is defined by module container.
     *
     * @param string $serviceName
     * @returns mixed
     */
    public function getService(string $serviceName)
    {
        return Container::getInstance()->get($serviceName);
    }

    public function getContent(): void
    {
        \Tools::redirectAdmin($this->context->link->getAdminLink(self::SETTINGS_MODULE_TAB_CONTROLLER_NAME));
    }

    public function hookDisplayHeader(): string
    {
        /** @var HeadSectionAssetLoader $headSectionAssetLoader */
        $headSectionAssetLoader = $this->getService(HeadSectionAssetLoader::class);

        return $headSectionAssetLoader->register($this->context->controller);
    }

    public function hookPaymentOptions(array $params): array
    {
        /** @var Configuration $configuration */
        $configuration = $this->getService(Configuration::class);

        /** @var LoggerInterface $logger */
        $logger = $this->getService(LoggerInterface::class);

        /** @var PaymentOptionPresenter $paymentOptionPresenter */
        $paymentOptionPresenter = $this->getService(PaymentOptionPresenter::class);

        /** @var IsCartAmountValidForKlarnaPayments $isCartAmountValidForKlarnaPayments */
        $isCartAmountValidForKlarnaPayments = $this->getService(IsCartAmountValidForKlarnaPayments::class);
        $isKlarnaEnabled = $configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_ENABLE_BOX);

        try {
            if (!$isKlarnaEnabled) {
                return [];
            }

            if (!$isCartAmountValidForKlarnaPayments->verify((int) $params['cart']->id)) {
                return [];
            }

            return $paymentOptionPresenter->present((int) $params['cart']->id);
        } catch (Throwable $exception) {
            $logger->error('Failed to present payment option.', [
                'context' => [],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            return [];
        }
    }

    public function hookDisplayAdminOrder(array $params): string
    {
        if (VersionUtility::isPsVersionGreaterOrEqualTo('1.7.7.0')) {
            return '';
        }

        $orderId = $params['id_order'];
        $order = new Order((int) $orderId);

        if ($order->module !== $this->name) {
            return '';
        }

        /** @var LoggerInterface $logger */
        $logger = $this->getService(LoggerInterface::class);

        /** @var Configuration $configuration */
        $configuration = $this->getService(Configuration::class);

        $this->context->currency = new Currency($order->id_currency);

        $orderStatusProcessing = $configuration->getByEnvironment(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_PROCESSING);

        $warnings = null;

        if ((int) $order->getCurrentState() === (int) $orderStatusProcessing) {
            $warnings = [
                'message' => $this->l('Order created with errors. For more information please check the Klarna module logs.'),
            ];
        }

        if ($configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_ALLOW_ORDER_STATUS_CHANGE)) {
            try {
                /** @var UpdateOrderStatusAction $updateOrderStatusAction */
                $updateOrderStatusAction = $this->getService(UpdateOrderStatusAction::class);

                $updateOrderStatusAction->run($orderId);
            } catch (\Throwable $exception) {
                $logger->error('Failed to change order status', [
                    'context' => [
                        'id_internal' => $orderId,
                    ],
                    'exceptions' => ExceptionUtility::getExceptions($exception),
                ]);
            }
        }

        /** @var OrderManagementTemplateParameterBuilder $orderManagementTemplateParameterBuilder */
        $orderManagementTemplateParameterBuilder = $this->getService(OrderManagementTemplateParameterBuilder::class);

        $this->context->smarty->assign([
            'isLegacyHook' => true,
            'klarnaPaymentOrder' => $orderManagementTemplateParameterBuilder
                ->setOrder($order)
                ->buildParams(),
            'warnings' => $warnings,
            'customStyles' => 'margin: 10px',
        ]);

        return $this->context->smarty->fetch(
            $this->getLocalPath() . 'views/templates/admin/hook/order_management.tpl'
        );
    }

    public function hookDisplayAdminOrderSide(array $params): string
    {
        if (VersionUtility::isPsVersionLessThan('1.7.7.0')) {
            return '';
        }

        $orderId = $params['id_order'];
        $order = new Order((int) $orderId);

        if ($order->module !== $this->name) {
            return '';
        }

        /** @var LoggerInterface $logger */
        $logger = $this->getService(LoggerInterface::class);

        /** @var Configuration $configuration */
        $configuration = $this->getService(Configuration::class);

        $this->context->currency = new Currency($order->id_currency);

        $orderStatusProcessing = $configuration->getByEnvironment(OrderStatusConfig::KLARNA_PAYMENT_ORDER_STATE_PROCESSING);

        $warnings = null;

        if ((int) $order->getCurrentState() === (int) $orderStatusProcessing) {
            $warnings = [
                'message' => $this->l('Order created with errors. For more information please check the Klarna module logs.'),
            ];
        }

        if ($configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_ALLOW_ORDER_STATUS_CHANGE)) {
            try {
                /** @var UpdateOrderStatusAction $updateOrderStatusAction */
                $updateOrderStatusAction = $this->getService(UpdateOrderStatusAction::class);

                $updateOrderStatusAction->run($orderId);
            } catch (\Throwable $exception) {
                $logger->error('Failed to change order status', [
                    'context' => [
                        'id_internal' => $orderId,
                    ],
                    'exceptions' => ExceptionUtility::getExceptions($exception),
                ]);
            }
        }

        /** @var OrderManagementTemplateParameterBuilder $orderManagementTemplateParameterBuilder */
        $orderManagementTemplateParameterBuilder = $this->getService(OrderManagementTemplateParameterBuilder::class);

        $this->context->smarty->assign([
            'isLegacyHook' => false,
            'klarnaPaymentOrder' => $orderManagementTemplateParameterBuilder
                ->setOrder($order)
                ->buildParams(),
            'warnings' => $warnings,
            'customStyles' => 'margin: 10px',
        ]);

        return $this->context->smarty->fetch(
            $this->getLocalPath() . 'views/templates/admin/hook/order_management.tpl'
        );
    }

    public function hookActionFrontControllerSetMedia(): void
    {
        /** @var Configuration $configuration */
        $configuration = $this->getService(Configuration::class);

        /** @var LoggerInterface $logger */
        $logger = $this->getService(LoggerInterface::class);

        /** @var ApplicationContextProvider $applicationContextProvider */
        $applicationContextProvider = $this->getService(ApplicationContextProvider::class);

        if (!$applicationContextProvider->get()->isValid()) {
            return;
        }

        /** @var PaymentFormAssetLoader $paymentFormAssetsLoader */
        $paymentFormAssetsLoader = $this->getService(PaymentFormAssetLoader::class);

        /** @var IsCartAmountValidForKlarnaPayments $isCartAmountValidForKlarnaPayments */
        $isCartAmountValidForKlarnaPayments = $this->getService(IsCartAmountValidForKlarnaPayments::class);
        $isKlarnaEnabled = $configuration->getByEnvironmentAsBoolean(Config::KLARNA_PAYMENT_ENABLE_BOX);

        try {
            if (!$isKlarnaEnabled) {
                return;
            }

            if (!$isCartAmountValidForKlarnaPayments->verify((int) $this->context->cart->id)) {
                return;
            }

            $paymentFormAssetsLoader->register($this->context->controller);
        } catch (Throwable $exception) {
            $logger->error('Failed to present payment option assets.', [
                'context' => [],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            return;
        }
    }

    /**
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     * @throws Exception
     */
    public function hookActionAdminControllerSetMedia(): void
    {
        $logger = $this->getService(LoggerInterface::class);

        try {
            /** @var Tools $tools */
            $tools = $this->getService(Tools::class);

            /** @var PrestaShopCookie $cookie */
            $cookie = $this->getService(PrestaShopCookie::class);

            /** @var LoggerInterface $logger */

            /** @var PruneOldRecordsAction $pruneOldRecordsAction */
            $pruneOldRecordsAction = $this->getService(PruneOldRecordsAction::class);

            /** @var KlarnaPaymentOrderRepositoryInterface $klarnaPaymentOrderRepository */
            $klarnaPaymentOrderRepository = $this->getService(KlarnaPaymentOrderRepositoryInterface::class);

            /** @var RetrieveOrderAction $retrieveOrderAction */
            $retrieveOrderAction = $this->getService(RetrieveOrderAction::class);

            $currentController = $tools->getValue('controller');
        } catch (\Exception $exception) {
            $logger->error('Klarna cannot load services.', [
                'context' => [],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            return;
        }

        try {
            $pruneOldRecordsAction->run(PruneOldRecordsData::create(Config::DAYS_TO_KEEP_LOGS));
        } catch (CouldNotPruneOldRecords $exception) {
            $logger->error('Failed to prune old records.', [
                'context' => [],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            // NOTE: continuing on as it does not affect the functionality.
        }

        if ('AdminOrders' === $currentController) {
            $orderId = $tools->getValue('id_order');
            $internalOrder = new Order($orderId);

            if (!Validate::isLoadedObject($internalOrder)) {
                return;
            }

            /** @var KlarnaPaymentOrder|null $klarnaPaymentOrder */
            $klarnaPaymentOrder = $klarnaPaymentOrderRepository->findOneBy([
                'id_internal' => $internalOrder->id,
                'id_shop' => $this->context->shop->id,
            ]);

            if (!$klarnaPaymentOrder) {
                return;
            }

            try {
                $this->context->currency = new Currency($internalOrder->id_currency);
                $externalOrder = $retrieveOrderAction->run(
                    RetrieveOrderRequestData::create($klarnaPaymentOrder->id_external, $this->context->currency->iso_code)
                );
            } catch (KlarnaPaymentException $exception) {
                $logger->error('Failed to retrieve external order', [
                    'context' => [
                        'id_external' => $klarnaPaymentOrder->id_external,
                    ],
                    'exceptions' => ExceptionUtility::getExceptions($exception),
                ]);

                return;
            }

            if ($internalOrder->module !== $this->name) {
                return;
            }

            /** @var OrderManagementAssetLoader $orderManagementAssetLoader */
            $orderManagementAssetLoader = $this->getService(OrderManagementAssetLoader::class);

            $orderManagementAssetLoader->register($this->context->controller, $externalOrder->getOrder()->getPurchaseCurrency());

            if (!empty($cookie->get(Config::KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_ERRORS))) {
                $errors = json_decode($cookie->get(Config::KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_ERRORS), true);

                if (isset($errors[$orderId])) {
                    $this->addFlash((string) $errors[$orderId], 'error');
                    unset($errors[$orderId]);
                    $cookie->set(Config::KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_ERRORS, json_encode($errors));
                }
            }

            if ($cookie->get(Config::KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_CANCELED)) {
                $this->addFlash($this->l('Order canceled'), 'success');
                $cookie->set(Config::KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_CANCELED, false);
            }

            if ($cookie->get(Config::KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_CAPTURED)) {
                $this->addFlash($this->l('Order captured'), 'success');
                $cookie->set(Config::KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_CAPTURED, false);
            }

            if ($cookie->get(Config::KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_REFUNDED)) {
                $this->addFlash($this->l('Order refunded'), 'success');
                $cookie->set(Config::KLARNA_PAYMENT_ORDER_MANAGEMENT_COOKIE_REFUNDED, false);
            }
        }
    }

    public function hookActionOrderHistoryAddAfter(array $params): void
    {
        /** @var OrderHistory $orderHistory */
        $orderHistory = $params['order_history'];

        if (!$orderHistory instanceof OrderHistory) {
            return;
        }

        $id_order = (int) $orderHistory->id_order;

        $internalOrder = new Order($id_order);

        if (!Validate::isLoadedObject($internalOrder)) {
            return;
        }

        /** @var LoggerInterface $logger */
        $logger = $this->getService(LoggerInterface::class);

        /** @var ApplicationContextProvider $applicationContextProvider */
        $applicationContextProvider = $this->getService(ApplicationContextProvider::class);

        /** @var KlarnaPaymentOrderRepositoryInterface $klarnaPaymentOrderRepository */
        $klarnaPaymentOrderRepository = $this->getService(KlarnaPaymentOrderRepositoryInterface::class);

        /** @var RetrieveOrderAction $retrieveOrderAction */
        $retrieveOrderAction = $this->getService(RetrieveOrderAction::class);

        /** @var AutoCaptureAction $autoCaptureAction */
        $autoCaptureAction = $this->getService(AutoCaptureAction::class);

        $currency = new Currency($internalOrder->id_currency);
        $currencyIso = $currency->iso_code;

        if (!$applicationContextProvider->get($currencyIso)->isValid()) {
            return;
        }

        /** @var KlarnaPaymentOrder|null $klarnaPaymentOrder */
        $klarnaPaymentOrder = $klarnaPaymentOrderRepository->findOneBy([
            'id_internal' => $internalOrder->id,
            'id_shop' => $internalOrder->id_shop,
        ]);

        if (!$klarnaPaymentOrder) {
            return;
        }

        try {
            $externalOrder = $retrieveOrderAction->run(
                RetrieveOrderRequestData::create($klarnaPaymentOrder->id_external, $currencyIso)
            );
        } catch (KlarnaPaymentException $exception) {
            $logger->error('Failed to retrieve external order', [
                'context' => [
                    'id_external' => $klarnaPaymentOrder->id_external,
                ],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            return;
        }

        try {
            $autoCaptureAction->run($externalOrder->getOrder(), $internalOrder);
        } catch (CouldNotAutoCapture $exception) {
            $logger->error('Failed to auto capture.', [
                'context' => [
                    'external_order_id' => $klarnaPaymentOrder->id_external,
                    'internal_order_id' => $internalOrder->id,
                ],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);
        }
    }

    public function hookDisplayProductPriceBlock(array $params): string
    {
        /** @var CanRenderWidgets $canRenderWidgets */
        $canRenderWidgets = $this->getService(CanRenderWidgets::class);

        if (!$canRenderWidgets->verify()) {
            return '';
        }

        if (!isset($params['type']) || $params['type'] !== 'after_price') {
            return '';
        }

        /** @var KlarnaExpressCheckoutPresenter $klarnaExpressCheckoutPresenter */
        $klarnaExpressCheckoutPresenter = $this->getService(KlarnaExpressCheckoutPresenter::class);

        $product = (array) json_decode(json_encode($params['product']), true);

        $klarnaExpressCheckoutContent = $klarnaExpressCheckoutPresenter->present($product);

        /** @var Context $context */
        $context = $this->getService(Context::class);

        /** @var OnsiteMessagingPresenter $onsiteMessagingPresenter */
        $onsiteMessagingPresenter = $this->getService(OnsiteMessagingPresenter::class);

        if (isset($params['product']['price_amount'], $params['product']['minimal_quantity'])) {
            $onsiteMessagingPresenter->setPurchaseAmount(
                (float) NumberUtility::multiplyBy(
                    (float) $params['product']['price_amount'],
                    (int) $params['product']['minimal_quantity'],
                    $context->getComputingPrecision()
                )
            );
        }

        return $onsiteMessagingPresenter->present(
            OnsiteMessagingPlacement::PRODUCT_PAGE['theme'],
            OnsiteMessagingPlacement::PRODUCT_PAGE['data_key']
        ) . $klarnaExpressCheckoutContent;
    }

    public function hookDisplayShoppingCart(): string
    {
        /** @var CanRenderWidgets $canRenderWidgets */
        $canRenderWidgets = $this->getService(CanRenderWidgets::class);

        if (!$canRenderWidgets->verify()) {
            return '';
        }

        /** @var OnsiteMessagingPresenter $onsiteMessagingPresenter */
        $onsiteMessagingPresenter = $this->getService(OnsiteMessagingPresenter::class);

        $onsiteMessagingPresenter->setPurchaseAmount((float) $this->context->cart->getOrderTotal());

        return $onsiteMessagingPresenter->present(
            OnsiteMessagingPlacement::CART_PAGE['theme'],
            OnsiteMessagingPlacement::CART_PAGE['data_key']
        );
    }

    public function hookDisplayTop(): string
    {
        /** @var SignInWithKlarnaNotificationPresenter $signInWithKlarnaNotificationPresenter */
        $signInWithKlarnaNotificationPresenter = $this->getService(SignInWithKlarnaNotificationPresenter::class);

        /** @var LoggerInterface $logger */
        $logger = $this->getService(LoggerInterface::class);

        /** @var Context $context */
        $context = $this->getService(Context::class);

        try {
            if (!$context->isCustomerLoggedIn()) {
                return $signInWithKlarnaNotificationPresenter->present();
            }
        } catch (\Throwable $exception) {
            $logger->error('Failed to present sign in with Klarna notification', [
                'context' => [],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            // NOTE: continuing on as it does not affect SIWK login functionality.
        }

        return '';
    }

    public function hookDisplayExpressCheckout(): string
    {
        /** @var CanRenderWidgets $canRenderWidgets */
        $canRenderWidgets = $this->getService(CanRenderWidgets::class);

        if (!$canRenderWidgets->verify()) {
            return '';
        }

        /** @var KlarnaExpressCheckoutPresenter $klarnaExpressCheckoutPresenter */
        $klarnaExpressCheckoutPresenter = $this->getService(KlarnaExpressCheckoutPresenter::class);

        /** @var SignInWithKlarnaPresenter $signInWithKlarnaPresenter */
        $signInWithKlarnaPresenter = $this->getService(SignInWithKlarnaPresenter::class);

        /** @var Context $context */
        $context = $this->getService(Context::class);

        if (!$context->isCustomerLoggedIn()) {
            return $signInWithKlarnaPresenter->present() . $klarnaExpressCheckoutPresenter->present();
        }

        return $klarnaExpressCheckoutPresenter->present();
    }

    public function hookDisplayBanner(): string
    {
        /** @var CanRenderWidgets $canRenderWidgets */
        $canRenderWidgets = $this->getService(CanRenderWidgets::class);

        if (!$canRenderWidgets->verify()) {
            return '';
        }

        /** @var OnsiteMessagingPresenter $onsiteMessagingPresenter */
        $onsiteMessagingPresenter = $this->getService(OnsiteMessagingPresenter::class);

        return $onsiteMessagingPresenter->present(
            OnsiteMessagingPlacement::TOP_OF_PAGE['theme'],
            OnsiteMessagingPlacement::TOP_OF_PAGE['data_key']
        );
    }

    public function hookDisplayFooter(): string
    {
        /** @var CanRenderWidgets $canRenderWidgets */
        $canRenderWidgets = $this->getService(CanRenderWidgets::class);

        if (!$canRenderWidgets->verify()) {
            return '';
        }

        /** @var OnsiteMessagingPresenter $onsiteMessagingPresenter */
        $onsiteMessagingPresenter = $this->getService(OnsiteMessagingPresenter::class);

        return $onsiteMessagingPresenter->present(
            OnsiteMessagingPlacement::FOOTER['theme'],
            OnsiteMessagingPlacement::FOOTER['data_key']
        );
    }

    public function hookDisplayCustomerLoginFormAfter(): string
    {
        /** @var SignInWithKlarnaPresenter $signInWithKlarnaPresenter */
        $signInWithKlarnaPresenter = $this->getService(SignInWithKlarnaPresenter::class);

        /** @var Context $context */
        $context = $this->getService(Context::class);

        /** @var CanRenderWidgets $canRenderWidgets */
        $canRenderWidgets = $this->getService(CanRenderWidgets::class);

        if (!$canRenderWidgets->verify()) {
            return '';
        }

        if (!$context->isCustomerLoggedIn()) {
            return $signInWithKlarnaPresenter->present();
        }

        return '';
    }

    public function hookDisplayCustomerAccountFormTop(): string
    {
        /** @var SignInWithKlarnaPresenter $signInWithKlarnaPresenter */
        $signInWithKlarnaPresenter = $this->getService(SignInWithKlarnaPresenter::class);

        /** @var CanRenderWidgets $canRenderWidgets */
        $canRenderWidgets = $this->getService(CanRenderWidgets::class);

        if (!$canRenderWidgets->verify()) {
            return '';
        }

        /** @var Context $context */
        $context = $this->getService(Context::class);

        if (!$context->isCustomerLoggedIn()) {
            return $signInWithKlarnaPresenter->present();
        }

        return '';
    }

    public function hookModuleRoutes($params)
    {
        return [
            'module-klarnapayment-siwkcallback' => [
                'controller' => 'siwkcallback',
                'rule' => Config::SIGN_IN_WITH_KLARNA_CALLBACK_URL_PATH,
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => 'klarnapayment',
                ],
            ],
            'module-klarnapayment-osm-infopage' => [
                'controller' => 'osmInfoPage',
                'rule' => \Configuration::get(Config::ON_SITE_MESSAGING_INFOPAGE_CONFIG),
                'keywords' => [],
                'params' => [
                    'fc' => 'module',
                    'module' => 'klarnapayment',
                ],
            ],
        ];
    }

    /**
     * @param string $msg
     * @param string $type
     *
     * @return string|true
     *
     * @throws Exception
     */
    public function addFlash(string $msg, string $type)
    {
        /** @var ApplicationContextProvider $applicationContextProvider */
        $applicationContextProvider = $this->getService(ApplicationContextProvider::class);

        if (!$applicationContextProvider->get()->isValid()) {
            return '';
        }

        if (VersionUtility::isPsVersionGreaterThan('1.7.7.0') && VersionUtility::isPsVersionLessThan('9.0.0')) {
            return $this->get('session')->getFlashBag()->add($type, $msg);
        }

        switch ($type) {
            case 'success':
                return $this->context->controller->confirmations[] = $msg;
            case 'error':
                return $this->context->controller->errors[] = $msg;
            case 'warning':
                return $this->context->controller->warnings[] = $msg;
        }

        return true;
    }

    private function autoload()
    {
        include_once "{$this->getLocalPath()}vendor/autoload.php";
    }

    /**
     * Override method to add "IGNORE" in the SQL Request to prevent duplicate entry and for getting All Carriers installed
     * Add checkbox carrier restrictions for a new module.
     *
     * @see PaymentModuleCore
     *
     * @param array $shopsList List of Shop identifier
     *
     * @return bool
     */
    public function addCheckboxCarrierRestrictionsForModule(array $shopsList = []): bool
    {
        if (false === version_compare(_PS_VERSION_, '1.7.0.0', '>=')) {
            return true;
        }

        $shopsList = empty($shopsList) ? Shop::getShops(true, null, true) : $shopsList;
        $carriersList = Carrier::getCarriers((int) \Context::getContext()->language->id, false, false, false, null, Carrier::ALL_CARRIERS);
        $allCarriers = array_column($carriersList, 'id_reference');
        $dataToInsert = [];

        foreach ($shopsList as $idShop) {
            foreach ($allCarriers as $idCarrier) {
                $dataToInsert[] = [
                    'id_reference' => (int) $idCarrier,
                    'id_shop' => (int) $idShop,
                    'id_module' => (int) $this->id,
                ];
            }
        }

        return \Db::getInstance()->insert(
            'module_carrier',
            $dataToInsert,
            false,
            true,
            Db::INSERT_IGNORE
        );
    }
}
