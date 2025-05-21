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

namespace KlarnaPayment\Module\Api\Exception;

use KlarnaPayment\Module\Api\ExceptionCode;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CouldNotHandleApiRequest extends ApiException
{
    public static function jsonExtensionNotAvailable()
    {
        return new self('JSON Extension not available', ExceptionCode::JSON_EXTENSION_NOT_AVAILABLE);
    }
}
