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

namespace KlarnaPayment\Module\Infrastructure\Controller;

use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Notification\Enum\NotificationType;
use KlarnaPayment\Module\Infrastructure\Notification\Handler\NotificationHandlerInterface;
use KlarnaPayment\Module\Infrastructure\Provider\ApplicationContextProvider;
use KlarnaPayment\Module\Infrastructure\Response\JsonResponse;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;
use KlarnaPayment\Module\Infrastructure\Verification\AreRequiredHooksAttached;
use KlarnaPayment\Module\Infrastructure\Verification\CanConfigureShop;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AbstractAdminController extends \ModuleAdminController
{
    const FILE_NAME = 'AbstractAdminController';
    const USER_GUIDE_URL = 'https://docs.klarna.com/platform-solutions/prestashop/';

    /** @var \KlarnaPayment */
    public $module;

    public function initContent(): void
    {
        /** @var NotificationHandlerInterface $notificationHandler */
        $notificationHandler = $this->module->getService(NotificationHandlerInterface::class);

        /** @var CanConfigureShop $canConfigureShop */
        $canConfigureShop = $this->module->getService(CanConfigureShop::class);

        if (!$canConfigureShop->verify()) {
            $this->warnings[] = $this->module->l('Please select a single shop context to use this module', self::FILE_NAME);

            return;
        }

        $notifications = $notificationHandler->getNotifications(static::FILE_NAME);

        foreach ($notifications as $notification) {
            if ($notification->getType() === NotificationType::SUCCESS) {
                $this->confirmations[] = $notification->getMessage();
            }

            if ($notification->getType() === NotificationType::ERROR) {
                $this->errors[] = $notification->getMessage();
            }

            if ($notification->getType() === NotificationType::WARNING) {
                $this->warnings[] = $notification->getMessage();
            }

            $notificationHandler->removeNotification(static::FILE_NAME, $notification);
        }

        $this->isValidContext();

        parent::initContent();
    }

    protected function ajaxResponse($value = null, $controller = null, $method = null): void
    {
        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        if ($value instanceof JsonResponse) {
            if ($value->getStatusCode() === JsonResponse::HTTP_INTERNAL_SERVER_ERROR) {
                $logger->error('Failed to return valid response', [
                    'context' => [
                        'response' => $value->getContent(),
                    ],
                ]);
            }

            http_response_code($value->getStatusCode());

            $value = $value->getContent();
        }

        try {
            if (method_exists(\ControllerCore::class, 'ajaxRender')) {
                $this->ajaxRender($value, $controller, $method);

                exit;
            }

            $this->ajaxDie($value, $controller, $method);
        } catch (\Exception $exception) {
            $logger->error('Could not return ajax response', [
                'context' => [
                    'response' => json_encode($value ?: []),
                    'exceptions' => ExceptionUtility::getExceptions($exception),
                ],
            ]);
        }

        exit;
    }

    public function isValidContext(): void
    {
        /** @var ApplicationContextProvider $applicationContextProvider */
        $applicationContextProvider = $this->module->getService(ApplicationContextProvider::class);

        /** @var AreRequiredHooksAttached $areRequiredHooksAttached */
        $areRequiredHooksAttached = $this->module->getService(AreRequiredHooksAttached::class);

        if (!$areRequiredHooksAttached->verify()) {
            $this->errors[] = $this->module->l('Required hooks are not attached, reset module and contact support if that does not help.', self::FILE_NAME);

            return;
        }
    }

    public function ensureHasPermissions(array $permissions, bool $ajax = false): bool
    {
        foreach ($permissions as $permission) {
            if (!$this->access($permission)) {
                if ($ajax) {
                    $this->ajaxResponse(json_encode([
                        'error' => true,
                        'message' => $this->module->l('Unauthorized.', self::FILE_NAME),
                    ]), JsonResponse::HTTP_UNAUTHORIZED);
                } else {
                    $this->errors[] = $this->module->l(
                        'You do not have permission to view this.',
                        self::FILE_NAME
                    );
                }

                return false;
            }
        }

        return true;
    }

    public function initPageHeaderToolbar()
    {
        parent::initPageHeaderToolbar();

        $this->page_header_toolbar_btn['user_guide'] = [
            'href' => self::USER_GUIDE_URL,
            'desc' => $this->module->l('User guide', self::FILE_NAME),
            'icon' => 'process-icon-newCombination toolbar-new',
            'target' => '_blank',
        ];
    }
}
