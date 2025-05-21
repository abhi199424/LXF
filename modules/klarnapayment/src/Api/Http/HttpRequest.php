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

namespace KlarnaPayment\Module\Api\Http;

if (!defined('_PS_VERSION_')) {
    exit;
}

class HttpRequest implements \JsonSerializable
{
    private $httpMethod;
    private $headers;
    private $queryUrl;
    private $rawBody;
    private $parameters;

    public function __construct(
        string $httpMethod,
        array $headers,
        string $queryUrl,
        string $rawBody = '',
        array $parameters = []
    ) {
        $this->httpMethod = $httpMethod;
        $this->headers = $headers;
        $this->queryUrl = $queryUrl;
        $this->rawBody = $rawBody;
        $this->parameters = $parameters;
    }

    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function setHeaders(array $headers): void
    {
        $this->headers = $headers;
    }

    public function getQueryUrl(): string
    {
        return $this->queryUrl;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }

    public function getRawBody(): string
    {
        return $this->rawBody;
    }

    public function setRawBody(string $rawBody): void
    {
        $this->rawBody = $rawBody;
    }

    /**
     * Encode this object to JSON
     *
     * @return array
     */
    public function jsonSerialize(): array
    {
        $rawBody = $this->rawBody;

        // NOTE: need to check if json is valid because we have one request that is urlencoded.
        if ($this->rawBody) {
            json_decode($this->rawBody);

            if (json_last_error() === 0) {
                // NOTE: need to decode it to not add new slashes from json_encode.
                $rawBody = json_decode($this->rawBody);
            }
        }

        $json = [];
        $json['http_method'] = $this->httpMethod;
        $json['headers'] = $this->headers;
        $json['query_url'] = $this->queryUrl;
        $json['raw_body'] = $rawBody; // NOTE: need to decode it to not add new slashes from json_encode.
        $json['parameters'] = $this->parameters;

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
