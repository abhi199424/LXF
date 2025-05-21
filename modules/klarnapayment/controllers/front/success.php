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

declare(strict_types=1);

use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Adapter\Tools;
use KlarnaPayment\Module\Infrastructure\Controller\AbstractFrontController;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Notification\Notifications\ErrorNotification;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;
use KlarnaPayment\Module\Infrastructure\Validation\SecurityTokenValidation;

if (!defined('_PS_VERSION_')) {
    exit;
}

class KlarnaPaymentSuccessModuleFrontController extends AbstractFrontController
{
    const FILE_NAME = 'success';

    const ORDER_PAGE_REDIRECT_DESTINATION = 'order';

    const ORDER_CONFIRMATION_PAGE_REDIRECT_DESTINATION = 'order-confirmation';

    public function postProcess(): void
    {
        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        /** @var Tools $tools */
        $tools = $this->module->getService(Tools::class);

        $cartId = $tools->getValue('cartId');
        $moduleId = $tools->getValue('moduleId');
        $orderId = $tools->getValue('orderId');
        $securityToken = $tools->getValue('securityToken');

        $logger->debug(sprintf('%s - Controller called', self::FILE_NAME));

        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        /** @var SecurityTokenValidation $securityTokenValidation */
        $securityTokenValidation = $this->module->getService(SecurityTokenValidation::class);

        try {
            $securityTokenValidation->isTokenValid(
                $securityToken,
                (int) $cartId,
                (string) $configuration->get(Config::KLARNA_PAYMENT_SECRET_KEY)
            );
        } catch (\Throwable $exception) {
            $logger->error('Failed to validate security token', [
                'context' => [],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            $this->addNotification(ErrorNotification::create(
                $this->module->l('Validation failed for security token. Contact support.', self::FILE_NAME)
            ));

            $this->redirectWithNotifications($this->getControllerLink(self::ORDER_PAGE_REDIRECT_DESTINATION));
        }

        $orderLink = $this->context->link->getPageLink(
            self::ORDER_CONFIRMATION_PAGE_REDIRECT_DESTINATION,
            true,
            null,
            [
                'id_cart' => $cartId,
                'id_module' => $moduleId,
                'id_order' => $orderId,
                'key' => (new Cart($cartId))->secure_key,
            ]
        );

        $logger->debug(sprintf('%s - Controller action ended', self::FILE_NAME));

        $tools->redirect($orderLink);
    }
}
