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

namespace KlarnaPayment\Module\Api\Apis;

use KlarnaPayment\Module\Api\ApiClient;
use KlarnaPayment\Module\Api\Exception\ApiException;
use KlarnaPayment\Module\Api\Helper\ApiHelper;
use KlarnaPayment\Module\Api\Http\ApiResponse;
use KlarnaPayment\Module\Api\Http\HttpContext;
use KlarnaPayment\Module\Api\Http\HttpMethod;
use KlarnaPayment\Module\Api\Http\HttpRequest;
use KlarnaPayment\Module\Api\Http\HttpResponse;
use KlarnaPayment\Module\Api\Requests\CancelOrderRequest;
use KlarnaPayment\Module\Api\Requests\CreateCaptureRequest;
use KlarnaPayment\Module\Api\Requests\CreateOrderRequest;
use KlarnaPayment\Module\Api\Requests\CreateRefundRequest;
use KlarnaPayment\Module\Api\Requests\RetrieveOrderRequest;
use KlarnaPayment\Module\Api\Requests\UpdateMerchantReferencesRequest;
use KlarnaPayment\Module\Api\Responses\CreateOrderResponse;
use KlarnaPayment\Module\Api\Responses\RetrieveOrderResponse;
use KlarnaPayment\Vendor\Unirest\Request;

if (!defined('_PS_VERSION_')) {
    exit;
}

class OrderApi extends BaseApi
{
    private const WRAP_SINGULAR_KEY = 'order';

    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @throws ApiException
     */
    public function retrieveOrder(RetrieveOrderRequest $body): ApiResponse
    {
        $queryParameters = '/ordermanagement/v1/orders/{order_id}';

        $queryParameters = ApiHelper::appendUrlWithTemplateParameters($queryParameters, [
            'order_id' => $body->getOrderId(),
        ]);

        $queryUrl = ApiHelper::cleanUrl($this->apiClient->getBaseUrl() . $queryParameters);

        $headers = [
            'User-Agent' => $this->apiClient->getUserAgent(),
            'Authorization' => 'Basic ' . $this->apiClient->getApiKey(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $headers = ApiHelper::mergeHeaders($headers, $this->apiClient->getAdditionalHeaders());

        $encodedBody = ApiHelper::jsonEncode($body);

        $request = new Request();
        $request->timeout($this->apiClient->getTimeout());

        $httpRequest = new HttpRequest(HttpMethod::GET, $headers, $queryUrl, $encodedBody);

        // and invoke the API call request to fetch the response
        try {
            $response = $request->get($queryUrl, $headers, $encodedBody);
        } catch (\KlarnaPayment\Vendor\Unirest\Exception $exception) {
            throw new ApiException(sprintf('Api exception. Exception message: (%s). Exception code: (%s)', $exception->getMessage(), (int) $exception->getCode()));
        }

        $httpResponse = new HttpResponse($response->code, $response->headers, $response->raw_body);
        $httpContext = new HttpContext($httpRequest, $httpResponse);

        if (!$this->apiClient->isValidResponse($httpResponse)) {
            return ApiResponse::createFromContext(null, $httpContext);
        }

        if (!isset($response->body->{self::WRAP_SINGULAR_KEY})) {
            $body = new \stdClass();
            $body->{self::WRAP_SINGULAR_KEY} = $response->body;

            $response->body = $body;
        }

        $mapper = $this->getJsonMapper();

        $deserializedResponse = $mapper->mapClass($response->body, RetrieveOrderResponse::class);

        return ApiResponse::createFromContext($deserializedResponse, $httpContext);
    }

    /**
     * @throws ApiException
     */
    public function createOrder(CreateOrderRequest $body): ApiResponse
    {
        $queryParameters = '/payments/v1/authorizations/{authorization_token}/order';

        $queryParameters = ApiHelper::appendUrlWithTemplateParameters($queryParameters, [
            'authorization_token' => $body->getAuthorizationToken(),
        ]);

        $queryUrl = ApiHelper::cleanUrl($this->apiClient->getBaseUrl() . $queryParameters);

        $headers = [
            'User-Agent' => $this->apiClient->getUserAgent(),
            'Authorization' => 'Basic ' . $this->apiClient->getApiKey(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $headers = ApiHelper::mergeHeaders($headers, $this->apiClient->getAdditionalHeaders());

        $encodedBody = ApiHelper::jsonEncode($body);

        $request = new Request();
        $request->timeout($this->apiClient->getTimeout());

        $httpRequest = new HttpRequest(HttpMethod::POST, $headers, $queryUrl, $encodedBody);

        // and invoke the API call request to fetch the response
        try {
            $response = $request->post($queryUrl, $headers, $encodedBody);
        } catch (\KlarnaPayment\Vendor\Unirest\Exception $exception) {
            throw new ApiException(sprintf('Api exception. Exception message: (%s). Exception code: (%s)', $exception->getMessage(), (int) $exception->getCode()));
        }

        $httpResponse = new HttpResponse($response->code, $response->headers, $response->raw_body);
        $httpContext = new HttpContext($httpRequest, $httpResponse);

        if (!$this->apiClient->isValidResponse($httpResponse)) {
            return ApiResponse::createFromContext(null, $httpContext);
        }

        $mapper = $this->getJsonMapper();

        $deserializedResponse = $mapper->mapClass($response->body, CreateOrderResponse::class);

        return ApiResponse::createFromContext($deserializedResponse, $httpContext);
    }

    /**
     * @throws ApiException
     */
    public function createCapture(CreateCaptureRequest $body): ApiResponse
    {
        $queryParameters = '/ordermanagement/v1/orders/{order_id}/captures';

        $queryParameters = ApiHelper::appendUrlWithTemplateParameters($queryParameters, [
            'order_id' => $body->getOrderId(),
        ]);

        $queryUrl = ApiHelper::cleanUrl($this->apiClient->getBaseUrl() . $queryParameters);

        $headers = [
            'User-Agent' => $this->apiClient->getUserAgent(),
            'Authorization' => 'Basic ' . $this->apiClient->getApiKey(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $headers = ApiHelper::mergeHeaders($headers, $this->apiClient->getAdditionalHeaders());

        $encodedBody = ApiHelper::jsonEncode($body);

        $request = new Request();
        $request->timeout($this->apiClient->getTimeout());

        $httpRequest = new HttpRequest(HttpMethod::POST, $headers, $queryUrl, $encodedBody);

        // and invoke the API call request to fetch the response
        try {
            $response = $request->post($queryUrl, $headers, $encodedBody);
        } catch (\KlarnaPayment\Vendor\Unirest\Exception $exception) {
            throw new ApiException(sprintf('Api exception. Exception message: (%s). Exception code: (%s)', $exception->getMessage(), (int) $exception->getCode()));
        }

        $httpResponse = new HttpResponse($response->code, $response->headers, $response->raw_body);
        $httpContext = new HttpContext($httpRequest, $httpResponse);

        return ApiResponse::createFromContext(null, $httpContext);
    }

    /**
     * @throws ApiException
     */
    public function createRefund(CreateRefundRequest $body): ApiResponse
    {
        $queryParameters = '/ordermanagement/v1/orders/{order_id}/refunds';

        $queryParameters = ApiHelper::appendUrlWithTemplateParameters($queryParameters, [
            'order_id' => $body->getOrderId(),
        ]);

        $queryUrl = ApiHelper::cleanUrl($this->apiClient->getBaseUrl() . $queryParameters);

        $headers = [
            'User-Agent' => $this->apiClient->getUserAgent(),
            'Authorization' => 'Basic ' . $this->apiClient->getApiKey(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $headers = ApiHelper::mergeHeaders($headers, $this->apiClient->getAdditionalHeaders());

        $encodedBody = ApiHelper::jsonEncode($body);

        $request = new Request();
        $request->timeout($this->apiClient->getTimeout());

        $httpRequest = new HttpRequest(HttpMethod::POST, $headers, $queryUrl, $encodedBody);

        // and invoke the API call request to fetch the response
        try {
            $response = $request->post($queryUrl, $headers, $encodedBody);
        } catch (\KlarnaPayment\Vendor\Unirest\Exception $exception) {
            throw new ApiException(sprintf('Api exception. Exception message: (%s). Exception code: (%s)', $exception->getMessage(), (int) $exception->getCode()));
        }

        $httpResponse = new HttpResponse($response->code, $response->headers, $response->raw_body);
        $httpContext = new HttpContext($httpRequest, $httpResponse);

        return ApiResponse::createFromContext(null, $httpContext);
    }

    /**
     * @throws ApiException
     */
    public function cancelOrder(CancelOrderRequest $body): ApiResponse
    {
        $queryParameters = '/ordermanagement/v1/orders/{order_id}/cancel';

        $queryParameters = ApiHelper::appendUrlWithTemplateParameters($queryParameters, [
            'order_id' => $body->getOrderId(),
        ]);

        $queryUrl = ApiHelper::cleanUrl($this->apiClient->getBaseUrl() . $queryParameters);

        $headers = [
            'User-Agent' => $this->apiClient->getUserAgent(),
            'Authorization' => 'Basic ' . $this->apiClient->getApiKey(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $headers = ApiHelper::mergeHeaders($headers, $this->apiClient->getAdditionalHeaders());

        $encodedBody = ApiHelper::jsonEncode($body);

        $request = new Request();
        $request->timeout($this->apiClient->getTimeout());

        $httpRequest = new HttpRequest(HttpMethod::POST, $headers, $queryUrl, $encodedBody);

        // and invoke the API call request to fetch the response
        try {
            $response = $request->post($queryUrl, $headers, $encodedBody);
        } catch (\KlarnaPayment\Vendor\Unirest\Exception $exception) {
            throw new ApiException(sprintf('Api exception. Exception message: (%s). Exception code: (%s)', $exception->getMessage(), (int) $exception->getCode()));
        }

        $httpResponse = new HttpResponse($response->code, $response->headers, $response->raw_body);
        $httpContext = new HttpContext($httpRequest, $httpResponse);

        return ApiResponse::createFromContext(null, $httpContext);
    }

    /**
     * @throws ApiException
     */
    public function updateMerchantReferences(UpdateMerchantReferencesRequest $body): ApiResponse
    {
        $queryParameters = '/ordermanagement/v1/orders/{order_id}/merchant-references';

        $queryParameters = ApiHelper::appendUrlWithTemplateParameters($queryParameters, [
            'order_id' => $body->getOrderId(),
        ]);

        $queryUrl = ApiHelper::cleanUrl($this->apiClient->getBaseUrl() . $queryParameters);

        $headers = [
            'User-Agent' => $this->apiClient->getUserAgent(),
            'Authorization' => 'Basic ' . $this->apiClient->getApiKey(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $headers = ApiHelper::mergeHeaders($headers, $this->apiClient->getAdditionalHeaders());

        $encodedBody = ApiHelper::jsonEncode($body);

        $request = new Request();
        $request->timeout($this->apiClient->getTimeout());

        $httpRequest = new HttpRequest(HttpMethod::PATCH, $headers, $queryUrl, $encodedBody);

        // and invoke the API call request to fetch the response
        try {
            $response = $request->patch($queryUrl, $headers, $encodedBody);
        } catch (\KlarnaPayment\Vendor\Unirest\Exception $exception) {
            throw new ApiException(sprintf('Api exception. Exception message: (%s). Exception code: (%s)', $exception->getMessage(), (int) $exception->getCode()));
        }

        $httpResponse = new HttpResponse($response->code, $response->headers, $response->raw_body);
        $httpContext = new HttpContext($httpRequest, $httpResponse);

        return ApiResponse::createFromContext(null, $httpContext);
    }
}
