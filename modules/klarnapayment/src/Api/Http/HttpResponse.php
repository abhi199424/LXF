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

class HttpResponse implements \JsonSerializable
{
    private $statusCode;
    private $headers;
    private $rawBody;

    public function __construct(int $statusCode, array $headers, string $rawBody)
    {
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->rawBody = $rawBody;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getRawBody(): string
    {
        return $this->rawBody;
    }

    public function setRawBody(string $rawBody)
    {
        $this->rawBody = $rawBody;
    }

    /**
     * Encode this object to JSON
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
        $json['status_code'] = $this->statusCode;
        $json['headers'] = $this->headers;
        $json['raw_body'] = $rawBody;

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
