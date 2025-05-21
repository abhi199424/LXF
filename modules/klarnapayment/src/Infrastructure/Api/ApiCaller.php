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

namespace KlarnaPayment\Module\Infrastructure\Api;

use KlarnaPayment\Module\Api\Exception\ApiException;
use KlarnaPayment\Module\Api\Http\ApiResponse;
use KlarnaPayment\Module\Api\Responses\ResponseInterface;
use KlarnaPayment\Module\Infrastructure\Logger\LoggerInterface;
use KlarnaPayment\Module\Infrastructure\Obfuscator\Obfuscator;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ApiCaller
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    const KEYS_TO_OBFUSCATE = ['Authorization'];

    /**
     * Returns full ApiResponse.
     *
     * @param callable $function
     *
     * @return ApiResponse
     *
     * @throws ApiException
     */
    public function call(callable $function): ApiResponse
    {
        /** @var ApiResponse $apiResponse */
        $apiResponse = $function();

        $maskedApiResponse = $this->getObfuscatedApiResponse($apiResponse);

        $this->logger->debug(
            sprintf(
                '%s request to api url: `%s`. Response status code: %s.',
                \Tools::strtoupper($maskedApiResponse->getRequest()->getHttpMethod()),
                $maskedApiResponse->getRequest()->getQueryUrl(),
                $maskedApiResponse->getResponse()->getStatusCode()
            ),
            [
                'request' => $maskedApiResponse->getRequest(),
                'response' => $maskedApiResponse->getResponse(),
                'correlation-id' => $maskedApiResponse->getResponse()->getHeaders()['klarna-correlation-id'] ?? '',
            ]
        );

        if ($maskedApiResponse->getError() !== null) {
            if (!empty($maskedApiResponse->getError()->getErrorMessages())) {
                $message = implode(',', $maskedApiResponse->getError()->getErrorMessages());
            } else {
                $message = $maskedApiResponse->getError()->getErrorMessage();
            }

            throw new ApiException(sprintf('Api exception. Message: (%s). Correlation id: %s', $message, $maskedApiResponse->getResponse()->getHeaders()['klarna-correlation-id'] ?? ''));
        }

        return $apiResponse;
    }

    private function getObfuscatedApiResponse(ApiResponse $apiResponse)
    {
        $apiResponse->getRequest()->setHeaders(
            Obfuscator::recursiveArray($apiResponse->getRequest()->getHeaders(), self::KEYS_TO_OBFUSCATE)
        );

        $requestBody = $apiResponse->getRequest()->getRawBody();
        $deserializedRequestBody = json_decode($requestBody, true);

        /*
         * NOTE: we have one request that has URL encoded body.
         * So to verify this body we check if it's not empty first
         * and then try to json_decode as it would turn null. Other
         * json type bodies would just turn to array.
         */
        if (!empty($requestBody) && $deserializedRequestBody === null) {
            $apiResponse->getRequest()->setRawBody(
                Obfuscator::url($requestBody, self::KEYS_TO_OBFUSCATE)
            );
        }

        $deserializedResponseBody = (array) json_decode($apiResponse->getResponse()->getRawBody(), true);

        if ($deserializedResponseBody !== null) {
            $apiResponse->getResponse()->setRawBody(json_encode(
                Obfuscator::recursiveArray($deserializedResponseBody, self::KEYS_TO_OBFUSCATE)
            ));
        }

        return $apiResponse;
    }

    /**
     * Returns only response from ApiResponse.
     *
     * @throws ApiException
     */
    public function getResult(callable $function): ?ResponseInterface
    {
        $apiResponse = $this->call($function);

        return $apiResponse->getResult();
    }
}
