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
use KlarnaPayment\Module\Infrastructure\Adapter\Configuration;
use KlarnaPayment\Module\Infrastructure\Controller\AbstractFrontController;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Request\Request;
use KlarnaPayment\Module\Infrastructure\Response\JsonResponse;
use KlarnaPayment\Module\Infrastructure\Response\Response;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;
use KlarnaPayment\Module\Infrastructure\Validation\SecurityTokenValidation;
use KlarnaPayment\Module\Presentation\Controller\Front\CheckoutController;
use Rakit\Validation\Validator;

if (!defined('_PS_VERSION_')) {
    exit;
}

class KlarnaPaymentAuthorizationModuleFrontController extends AbstractFrontController
{
    const FILE_NAME = 'authorization';

    /**
     * NOTE: As it is the only way to catch some orders (e.g. pay now in some EU regions where if the customer closes their browser before flow returns to Klarna, the KP auth callback is the only way the order would get created)
     * Cannot really test it manually.
     */
    public function postProcess(): void
    {
        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        $logger->debug(sprintf('%s - Controller called', self::FILE_NAME));

        $request = Request::createFromGlobals();

        // NOTE: authorization_token is added from Klarna side.
        $validation = (new Validator())->make($request->all(), [
            'authorization_token' => 'required',
            'security_token' => 'required',
            'cart_id' => 'required|integer',
        ]);

        $validation->validate();

        if ($validation->fails()) {
            http_response_code(Response::HTTP_UNPROCESSABLE_ENTITY);
            exit;
        }

        $cart = new Cart((int) $request->getArgument('cart_id'));

        if (!$cart->id) {
            http_response_code(Response::HTTP_UNPROCESSABLE_ENTITY);
            exit;
        }

        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

        /** @var SecurityTokenValidation $securityTokenValidation */
        $securityTokenValidation = $this->module->getService(SecurityTokenValidation::class);

        try {
            $securityTokenValidation->isTokenValid(
                $request->getArgument('security_token'),
                (int) $request->getArgument('cart_id'),
                (string) $configuration->get(Config::KLARNA_PAYMENT_SECRET_KEY)
            );
        } catch (\Throwable $exception) {
            $logger->error('Failed to validate security token', [
                'context' => [],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            http_response_code(Response::HTTP_UNAUTHORIZED);
            exit;
        }

        if ($cart->orderExists()) {
            http_response_code(Response::HTTP_CONFLICT);
            exit;
        }

        $lockResult = $this->applyLock(sprintf(
            '%s-%s',
            $request->getArgument('authorization_token'),
            $request->getArgument('security_token')
        ));

        if (!$lockResult->isSuccessful()) {
            $this->ajaxResponse(JsonResponse::error(
                $lockResult->getContent(),
                $lockResult->getStatusCode()
            ));
        }

        /** @var CheckoutController $checkoutController */
        $checkoutController = $this->module->getService(CheckoutController::class);

        $result = $checkoutController->execute($request);

        $this->releaseLock();

        $logger->debug(sprintf('%s - Controller action ended', self::FILE_NAME));

        if (!$result->isSuccessful()) {
            if ($result->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
                $this->ajaxResponse(JsonResponse::error(
                    $this->module->l('Unauthorized', self::FILE_NAME),
                    JsonResponse::HTTP_UNAUTHORIZED)
                );
            }

            http_response_code(Response::HTTP_INTERNAL_SERVER_ERROR);
            exit;
        }

        http_response_code(Response::HTTP_NO_CONTENT);
        exit;
    }
}
