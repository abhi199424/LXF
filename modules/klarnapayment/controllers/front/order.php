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
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;
use KlarnaPayment\Module\Infrastructure\Validation\SecurityTokenValidation;
use KlarnaPayment\Module\Presentation\Controller\Front\GetOrderDetailsController;
use Rakit\Validation\Validator;

if (!defined('_PS_VERSION_')) {
    exit;
}

class KlarnaPaymentOrderModuleFrontController extends AbstractFrontController
{
    const FILE_NAME = 'order';

    public function displayAjaxGetOrderDetails(): string
    {
        $this->checkApplicationCredentials();

        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        /** @var SecurityTokenValidation $securityTokenValidation */
        $securityTokenValidation = $this->module->getService(SecurityTokenValidation::class);

        $logger->debug(sprintf('%s - Controller called', self::FILE_NAME));

        $request = Request::createFromGlobals();

        $validation = (new Validator())->make($request->all(), [
            'cart_id' => 'required|integer',
            'security_token' => 'required',
        ]);

        $validation->validate();

        if ($validation->fails()) {
            $this->ajaxResponse(JsonResponse::error($validation->errors()->toArray(), JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
        }

        /** @var Configuration $configuration */
        $configuration = $this->module->getService(Configuration::class);

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

            $this->ajaxResponse(JsonResponse::error([
                $this->module->l('Failed to validate security token', self::FILE_NAME),
                JsonResponse::HTTP_BAD_REQUEST,
            ]));
        }

        $lockResult = $this->applyLock(sprintf(
            '%s-%s',
            $request->get('cart_id'),
            $request->get('security_token')
        ));

        if (!$lockResult->isSuccessful()) {
            $this->ajaxResponse(JsonResponse::error(
                $lockResult->getContent(),
                $lockResult->getStatusCode()
            ));
        }

        /** @var GetOrderDetailsController $getOrderDetailsController */
        $getOrderDetailsController = $this->module->getService(GetOrderDetailsController::class);

        try {
            $response = $getOrderDetailsController->execute($request);
        } catch (\Throwable $exception) {
            $this->releaseLock();

            $this->ajaxResponse(JsonResponse::error(
                $this->module->l('Unknown error occurred', self::FILE_NAME),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            ));
            $logger->error('Failed to validate security token', [
                'context' => [],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);
        }

        $this->releaseLock();

        $logger->debug(sprintf('%s - Controller action ended', self::FILE_NAME));

        $this->ajaxResponse($response);
    }

    public function displayAjaxHppExternalWebsite(): void
    {
        $this->checkApplicationCredentials();

        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        $logger->debug(sprintf('%s - Controller called', self::FILE_NAME));

        $request = Request::createFromGlobals();

        $validation = (new Validator())->make($request->all(), [
            'cart_id' => 'required|integer',
            'security_token' => 'required',
            'redirection_url' => 'required',
        ]);

        $validation->validate();

        if ($validation->fails()) {
            $this->ajaxResponse(JsonResponse::error($validation->errors()->toArray(), JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
        }

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

            $this->ajaxResponse(JsonResponse::error([
                $this->module->l('Failed to validate security token', self::FILE_NAME),
                JsonResponse::HTTP_BAD_REQUEST,
            ]));
        }

        $lockResult = $this->applyLock(sprintf(
            '%s-%s',
            $request->get('cart_id'),
            $request->get('security_token')
        ));

        if (!$lockResult->isSuccessful()) {
            $this->ajaxResponse(JsonResponse::error(
                $lockResult->getContent(),
                $lockResult->getStatusCode()
            ));
        }

        $this->releaseLock();

        $logger->debug(sprintf('%s - Controller action ended', self::FILE_NAME));

        /** @var \KlarnaPayment\Module\Infrastructure\Adapter\Tools $tools */
        $tools = $this->module->getService(\KlarnaPayment\Module\Infrastructure\Adapter\Tools::class);

        $tools->redirect($request->get('redirection_url'));
    }
}
