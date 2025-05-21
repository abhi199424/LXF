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

use KlarnaPayment\Module\Infrastructure\Controller\AbstractFrontController;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Request\Request;
use KlarnaPayment\Module\Infrastructure\Response\JsonResponse;
use KlarnaPayment\Module\Presentation\Controller\Front\AuthenticationController;
use Rakit\Validation\Validator;

if (!defined('_PS_VERSION_')) {
    exit;
}

class KlarnaPaymentAuthenticationModuleFrontController extends AbstractFrontController
{
    const FILE_NAME = 'authentication';

    public function initContent()
    {
        parent::initContent();

        /** @var LoggerInterface $logger */
        $logger = $this->module->getService(LoggerInterface::class);

        $logger->debug(sprintf('%s - Controller called', self::FILE_NAME));

        $request = Request::createFromGlobals();

        if ($request->get('siwkError')) {
            $this->errors[] = $this->module->l('An error occurred please try again.', self::FILE_NAME);

            $logger->error('Sign In With Klarna SDK returned error response', [
                'error' => $request->get('siwkErrorMessage'),
            ]);

            return $this->redirectWithNotifications($this->context->link->getPageLink('index', true));
        }

        $validation = (new Validator())->make($request->all(), [
            'id_token' => 'required',
            'refresh_token' => 'required',
        ]);

        $validation->validate();

        if ($validation->fails()) {
            $this->ajaxResponse(JsonResponse::error($validation->errors()->toArray(), JsonResponse::HTTP_UNPROCESSABLE_ENTITY));
        }

        /** @var AuthenticationController $authenticationController */
        $authenticationController = $this->module->getService(AuthenticationController::class);

        try {
            $response = $authenticationController->execute($request);
        } catch (\Throwable $exception) {
            $this->errors[] = $this->module->l('An error occurred while redirecting', self::FILE_NAME);
            $this->ajaxResponse(JsonResponse::error(
                $this->module->l('Unknown error occurred', self::FILE_NAME),
                JsonResponse::HTTP_INTERNAL_SERVER_ERROR
            ));
        }

        $logger->debug(sprintf('%s - Controller action ended', self::FILE_NAME));

        if ((bool) $request->get('siwkFallbackRedirect')) {
            $this->success[] = $this->module->l('You have been successfully authenticated', self::FILE_NAME);

            return $this->redirectWithNotifications($this->context->link->getPageLink('index', true));
        }

        $this->ajaxResponse($response);
    }
}
