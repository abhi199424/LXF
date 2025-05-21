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

use Symfony\Component\HttpFoundation\JsonResponse as BaseJsonResponse;

if (!defined('_PS_VERSION_')) {
    exit;
}

class JsonResponse extends BaseJsonResponse
{
    /**
     * @param mixed $data
     */
    public function __construct($data = null, int $status = 200, array $headers = [])
    {
        parent::__construct($data, $status, $headers);
    }

    public static function success(array $data, int $status = 200): self
    {
        return new self([
            'success' => true,
            'errors' => [],
            'data' => $data,
        ], $status);
    }

    /**
     * @param string|array $error
     * @param int $status
     *
     * @return static
     */
    public static function error($error, int $status = 400): self
    {
        if ($status === JsonResponse::HTTP_UNPROCESSABLE_ENTITY) {
            // NOTE: removing rule name. ['required' => 'message'] becomes [0 => 'message']
            foreach ($error as $key => $messages) {
                $error[$key] = array_values($messages);
            }
        }

        if (!is_array($error)) {
            $error = [$error];
        }

        return new self([
            'success' => false,
            'errors' => $error,
            'data' => [],
        ], $status);
    }
}
