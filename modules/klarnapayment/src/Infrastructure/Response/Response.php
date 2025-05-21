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

namespace KlarnaPayment\Module\Infrastructure\Response;

use Symfony\Component\HttpFoundation\Response as BaseResponse;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Response extends BaseResponse
{
    /**
     * @param mixed $data
     */
    public function __construct($data = null, int $status = 200, array $headers = [])
    {
        parent::__construct($data, $status, $headers);
    }

    public static function respond(string $message, int $status = 200): self
    {
        return new self($message, $status);
    }
}
