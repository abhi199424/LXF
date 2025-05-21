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

use KlarnaPayment\Module\Api\Helper\ApiHelper;
use KlarnaPayment\Module\Api\Models\Error;
use KlarnaPayment\Module\Api\Responses\ResponseInterface;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ApiResponse
{
    /** @var HttpRequest */
    private $request;

    /** @var HttpResponse */
    private $response;

    /** @var int */
    private $statusCode;

    /** @var array */
    private $headers;

    /** @var ResponseInterface|null */
    private $result;

    /** @var null */
    private $error;

    public function __construct(
        HttpRequest $request,
        HttpResponse $response,
        int $statusCode,
        array $headers,
        ?ResponseInterface $result,
        ?Error $error
    ) {
        $this->request = $request;
        $this->response = $response;
        $this->statusCode = $statusCode;
        $this->headers = $headers;
        $this->result = $result;
        $this->error = $error;
    }

    public static function createFromContext(?ResponseInterface $deserializedResponse, HttpContext $context): self
    {
        $statusCode = $context->getResponse()->getStatusCode();
        $headers = $context->getResponse()->getHeaders();
        $error = null;

        if ($statusCode >= 400) {
            $decodedBody = ApiHelper::jsonDecode($context->getResponse()->getRawBody(), true, JSON_OBJECT_AS_ARRAY);

            $error = new Error();
            $error->setErrorCode($decodedBody['error_code'] ?? '');
            $error->setErrorMessage($decodedBody['error_message'] ?? '');
            $error->setErrorMessages($decodedBody['error_messages'] ?? []);
            $error->setCorrelationId($decodedBody['correlation_id'] ?? '');
        }

        return new self(
            $context->getRequest(),
            $context->getResponse(),
            $statusCode,
            $headers,
            $deserializedResponse,
            $error
        );
    }

    public function getRequest(): HttpRequest
    {
        return $this->request;
    }

    public function getResponse(): HttpResponse
    {
        return $this->response;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getResult(): ?ResponseInterface
    {
        return $this->result;
    }

    public function getError(): ?Error
    {
        return $this->error;
    }

    public function isSuccess(): bool
    {
        return isset($this->statusCode) && $this->statusCode >= 200 && $this->statusCode < 300;
    }

    public function isError(): bool
    {
        return !$this->isSuccess();
    }
}
