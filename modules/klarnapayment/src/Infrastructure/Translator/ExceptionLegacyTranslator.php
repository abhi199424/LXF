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

namespace KlarnaPayment\Module\Infrastructure\Translator;

use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Bootstrap\Exception\CouldNotInstallModule;
use KlarnaPayment\Module\Infrastructure\Exception\CouldNotUninstallModule;
use KlarnaPayment\Module\Infrastructure\Exception\ExceptionCode;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ExceptionLegacyTranslator implements ExceptionTranslatorInterface
{
    const FILE_NAME = 'ExceptionLegacyTranslator';

    /** @var \KlarnaPayment */
    private $module;

    public function __construct(ModuleFactory $moduleFactory)
    {
        $this->module = $moduleFactory->getModule();
    }

    public function translate(KlarnaPaymentException $exception): string
    {
        $customMessages = $this->getCustomMessages();
        $genericMessages = $this->getGenericMessages();

        $exceptionType = get_class($exception);
        $exceptionCode = $exception->getCode();

        if (isset($customMessages[$exceptionType], $customMessages[$exceptionType][$exceptionCode])) {
            return $this->getFormattedMessage($customMessages[$exceptionType][$exceptionCode], $exception->getContext());
        }

        if (isset($genericMessages[$exceptionCode])) {
            return $this->getFormattedMessage($genericMessages[$exceptionCode], $exception->getContext());
        }

        return $this->getFormattedMessage($exception->getMessage(), $exception->getContext());
    }

    private function getCustomMessages(): array
    {
        return [
            /* BOOTSTRAP start */
            CouldNotInstallModule::class => [
                ExceptionCode::INFRASTRUCTURE_FAILED_TO_INSTALL_DATABASE_TABLE => $this->module->l('Failed to install database table (%s).', self::FILE_NAME),
                ExceptionCode::INFRASTRUCTURE_FAILED_TO_INSTALL_MODULE_TAB => $this->module->l('Failed to install module tab (%s)', self::FILE_NAME),
            ],
            CouldNotUninstallModule::class => [
                ExceptionCode::INFRASTRUCTURE_FAILED_TO_UNINSTALL_DATABASE_TABLE => $this->module->l('Failed to uninstall database table (%s)', self::FILE_NAME),
                ExceptionCode::INFRASTRUCTURE_FAILED_TO_UNINSTALL_MODULE_TAB => $this->module->l('Failed to uninstall module tab (%s)', self::FILE_NAME),
            ],
            /* BOOTSTRAP end */
        ];
    }

    private function getGenericMessages(): array
    {
        return [
            ExceptionCode::UNKNOWN_ERROR => $this->module->l('An unknown error error occurred. Please check system logs or contact Klarna payment support.', self::FILE_NAME),
            ExceptionCode::API_FAILED_TO_GET_SUCCESSFUL_RESPONSE => $this->module->l('Failed to get successful api response.', self::FILE_NAME),
            ExceptionCode::API_FAILED_TO_CREATE_REQUEST => $this->module->l('Failed to create api request.', self::FILE_NAME),
            ExceptionCode::CONFIGURATION_MERCHANT_IS_NOT_LOGGED_IN => $this->module->l('Merchant is not logged in.', self::FILE_NAME),
            ExceptionCode::CONFIGURATION_UNSUPPORTED_CURRENCY => $this->module->l('Unsupported currency (%s). Supported currencies: [%2$s]', self::FILE_NAME),
        ];
    }

    private function getFormattedMessage(string $message, array $context): string
    {
        if (strpos($message, '%') !== false) {
            return vsprintf($message, $context);
        }

        return $message;
    }
}
