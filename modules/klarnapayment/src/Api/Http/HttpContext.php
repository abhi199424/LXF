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

class HttpContext
{
    private $request;

    private $response;

    public function __construct(HttpRequest $request, HttpResponse $response)
    {
        $this->request = $request;
        $this->response = $response;
    }

    public function getRequest(): HttpRequest
    {
        return $this->request;
    }

    public function getResponse(): HttpResponse
    {
        return $this->response;
    }
}
