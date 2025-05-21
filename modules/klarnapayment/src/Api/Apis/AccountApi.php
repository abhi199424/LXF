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
use KlarnaPayment\Module\Api\Requests\RefreshTokenRequest;
use KlarnaPayment\Module\Api\Responses\RefreshTokenResponse;
use KlarnaPayment\Module\Api\Responses\RetrieveJWKSResponse;
use KlarnaPayment\Module\Core\Config\Config;
use KlarnaPayment\Vendor\Unirest\Request;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AccountApi extends BaseApi
{
    private $apiClient;

    public function __construct(ApiClient $apiClient)
    {
        $this->apiClient = $apiClient;
    }

    /**
     * @throws \Throwable
     */
    public function retrieveJWKS(): ApiResponse
    {
        $queryParameters = '/eu/lp/idp/.well-known/jwks.json';

        $queryUrl = ApiHelper::cleanUrl(Config::KLARNA_PAYMENT_LOGIN_URL[$this->apiClient->getEnvironment()] . $queryParameters);

        $headers = [
            'User-Agent' => $this->apiClient->getUserAgent(),
            'Authorization' => 'Basic ' . $this->apiClient->getApiKey(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
        ];

        $headers = ApiHelper::mergeHeaders($headers, $this->apiClient->getAdditionalHeaders());

        $request = new Request();
        $request->timeout($this->apiClient->getTimeout());

        $httpRequest = new HttpRequest(HttpMethod::GET, $headers, $queryUrl);

        // and invoke the API call request to fetch the response
        try {
            $response = $request->get($queryUrl, $headers);
        } catch (\KlarnaPayment\Vendor\Unirest\Exception $exception) {
            throw new ApiException(sprintf('Api exception. Exception message: (%s). Exception code: (%s)', $exception->getMessage(), (int) $exception->getCode()));
        }

        $httpResponse = new HttpResponse($response->code, $response->headers, $response->raw_body);
        $httpContext = new HttpContext($httpRequest, $httpResponse);

        if (!$this->apiClient->isValidResponse($httpResponse)) {
            return ApiResponse::createFromContext(null, $httpContext);
        }

        $mapper = $this->getJsonMapper();

        $deserializedResponse = $mapper->mapClass($response->body, RetrieveJWKSResponse::class);

        return ApiResponse::createFromContext($deserializedResponse, $httpContext);
    }

    /**
     * @throws \Throwable
     */
    public function refreshToken(RefreshTokenRequest $body): ApiResponse
    {
        $queryParameters = '/eu/lp/idp/oauth2/token';

        $queryUrl = ApiHelper::cleanUrl(Config::KLARNA_PAYMENT_LOGIN_URL[$this->apiClient->getEnvironment()] . $queryParameters);

        $headers = [
            'User-Agent' => $this->apiClient->getUserAgent(),
            'Accept' => 'application/json',
            'Content-Type' => 'application/x-www-form-urlencoded',
        ];

        $body = \KlarnaPayment\Vendor\Unirest\Request\Body::form(json_decode(json_encode($body), true));

        $headers = ApiHelper::mergeHeaders($headers, $this->apiClient->getAdditionalHeaders());

        $request = new Request();
        $request->timeout($this->apiClient->getTimeout());

        $httpRequest = new HttpRequest(HttpMethod::POST, $headers, $queryUrl, $body);

        // and invoke the API call request to fetch the response
        try {
            $response = $request->post($queryUrl, $headers, $body);
        } catch (\KlarnaPayment\Vendor\Unirest\Exception $exception) {
            throw new ApiException(sprintf('Api exception. Exception message: (%s). Exception code: (%s)', $exception->getMessage(), (int) $exception->getCode()));
        }

        $httpResponse = new HttpResponse($response->code, $response->headers, $response->raw_body);
        $httpContext = new HttpContext($httpRequest, $httpResponse);

        if (!$this->apiClient->isValidResponse($httpResponse)) {
            return ApiResponse::createFromContext(null, $httpContext);
        }

        $mapper = $this->getJsonMapper();

        $deserializedResponse = $mapper->mapClass($response->body, RefreshTokenResponse::class);

        return ApiResponse::createFromContext($deserializedResponse, $httpContext);
    }
}
