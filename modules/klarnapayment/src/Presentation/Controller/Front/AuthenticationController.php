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

namespace KlarnaPayment\Module\Presentation\Controller\Front;

use KlarnaPayment\Module\Core\Account\DTO\AuthenticationProcessorData;
use KlarnaPayment\Module\Core\Account\Processor\AuthenticationProcessor;
use KlarnaPayment\Module\Infrastructure\Adapter\ModuleFactory;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Request\Request;
use KlarnaPayment\Module\Infrastructure\Response\JsonResponse;
use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AuthenticationController
{
    private const FILE_NAME = 'AuthenticationController';

    /** @var AuthenticationProcessor */
    private $authenticationProcessor;
    /** @var LoggerInterface */
    private $logger;
    /** @var \Module */
    private $module;

    public function __construct(
        AuthenticationProcessor $authenticationProcessor,
        LoggerInterface $logger,
        ModuleFactory $moduleFactory
    ) {
        $this->authenticationProcessor = $authenticationProcessor;
        $this->logger = $logger;
        $this->module = $moduleFactory->getModule();
    }

    public function execute(Request $request): JsonResponse
    {
        try {
            $this->authenticationProcessor->run(AuthenticationProcessorData::create(
                (string) $request->get('id_token'),
                (string) $request->get('refresh_token')
            ));
        } catch (\Throwable $exception) {
            $this->logger->error('Failed to process authentication', [
                'context' => [],
                'exceptions' => ExceptionUtility::getExceptions($exception),
            ]);

            return JsonResponse::error(
                $this->module->l('Failed to process authentication', self::FILE_NAME),
                JsonResponse::HTTP_UNAUTHORIZED
            );
        }

        return JsonResponse::success([
            'request_details' => $request,
        ], JsonResponse::HTTP_OK);
    }
}
