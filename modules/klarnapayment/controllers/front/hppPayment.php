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
use KlarnaPayment\Module\Core\Payment\Action\ValidateHppSessionAction;
use KlarnaPayment\Module\Core\Payment\Enum\ActionStatus;
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Controller\AbstractFrontController;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Notification\Notifications\ErrorNotification;
use KlarnaPayment\Module\Infrastructure\Request\Request;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;
use KlarnaPayment\Module\Infrastructure\Validation\SecurityTokenValidation;
use KlarnaPayment\Module\Presentation\Controller\Front\CheckoutController;
use Rakit\Validation\Validator;

if (!defined('_PS_VERSION_')) {
    exit;
}

class KlarnaPaymentHppPaymentModuleFrontController extends AbstractFrontController
{
    protected const FILE_NAME = 'hppPayment';

    private const ORDER_PAGE_REDIRECT_DESTINATION = 'order';

    /** @var KlarnaPayment */
    public $module;

    public function initContent(): void
    {
        parent::initContent();

        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        $logger->debug(sprintf('%s - Controller called', self::FILE_NAME));

        $request = Request::createFromGlobals();

        $validation = (new Validator())->make($request->all(), [
            'cart_id' => 'required',
            'security_token' => 'required',
            'hppId' => 'required',
            'action' => 'required|in:' . implode(',', [ActionStatus::CANCEL, ActionStatus::ERROR, ActionStatus::SUCCESS, ActionStatus::UPDATE]),
            'authorization_token' => 'required_if:action,' . implode(',', [ActionStatus::SUCCESS, ActionStatus::UPDATE]),
        ]);

        $validation->validate();

        if ($validation->fails()) {
            $this->addNotification(ErrorNotification::create(
                $this->module->l('Validation failed for checkout. Contact support.', self::FILE_NAME)
            ));

            $this->redirectWithNotifications($this->getControllerLink(self::ORDER_PAGE_REDIRECT_DESTINATION));
        }

        $cart = new \Cart($request->get('cart_id'));

        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        /** @var SecurityTokenValidation $securityTokenValidation */
        $securityTokenValidation = $this->module->getService(SecurityTokenValidation::class);

        try {
            $securityTokenValidation->isTokenValid(
                $request->get('security_token'),
                (int) $request->get('cart_id'),
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

        if ($cart->orderExists()) {
            $logger->debug(sprintf('%s - order already exists, redirecting to success url', self::FILE_NAME));
            $this->redirectWithNotifications($this->getSuccessUrl($cart, $request->get('security_token')));
        }

        switch ($request->get('action')) {
            case ActionStatus::CANCEL: // NOTE: Customer decides to go back to the merchant shop
                $this->redirectWithNotifications($this->getControllerLink(self::ORDER_PAGE_REDIRECT_DESTINATION));

                break;
            case ActionStatus::ERROR: // NOTE: Something wrong with the checkout flow
                $this->addNotification(ErrorNotification::create(
                    $this->module->l('Error occurred during checkout. Please try again or contact support.', self::FILE_NAME)
                ));

                $this->redirectWithNotifications($this->getControllerLink(self::ORDER_PAGE_REDIRECT_DESTINATION));

                break;
        }

        $lockResult = $this->applyLock(sprintf(
            '%s-%s',
            $request->get('cart_id'),
            $request->get('security_token')
        ));

        if (!$lockResult->isSuccessful()) {
            $this->redirectWithNotifications($this->getControllerLink(self::ORDER_PAGE_REDIRECT_DESTINATION));
        }

        /** @var CheckoutController $checkoutController */
        $checkoutController = $this->module->getService(CheckoutController::class);

        /** @var ValidateHppSessionAction $validateHppSessionAction * */
        $validateHppSessionAction = $this->module->getService(ValidateHppSessionAction::class);

        try {
            $validateHppSessionAction->run($request->get('authorization_token'), $request->get('hppId'));
            $result = $checkoutController->execute($request);
        } catch (\Throwable $exception) {
            $this->releaseLock();

            $this->addNotification(ErrorNotification::create($this->module->l('Unknown error occurred', self::FILE_NAME)));
            $logger->error('Unknown error occurred', [
                'context' => [],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);
            $this->redirectWithNotifications($this->getControllerLink(self::ORDER_PAGE_REDIRECT_DESTINATION));

            return;
        }

        $this->releaseLock();

        $logger->debug(sprintf('%s - Controller action ended', self::FILE_NAME));

        if (!$result->isSuccessful()) {
            $this->addNotification(ErrorNotification::create($result->getContent()));

            $this->redirectWithNotifications($this->getControllerLink(self::ORDER_PAGE_REDIRECT_DESTINATION));
        }

        $this->redirectWithNotifications($this->getSuccessUrl($cart, $request->get('security_token')));
    }

    private function getSuccessUrl(Cart $cart, string $securityToken): string
    {
        return $this->context->link->getModuleLink(
            $this->module->name,
            'success',
            [
                'cartId' => $cart->id,
                'securityToken' => $securityToken,
                'orderId' => Order::getIdByCartId($cart->id),
                'moduleId' => $this->module->id,
            ],
            true
        );
    }
}
