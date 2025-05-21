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

namespace KlarnaPayment\Module\Api;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ExceptionCode
{
    const JSON_EXTENSION_NOT_AVAILABLE = 1001;
    const FAILED_TO_CREATE_REQUEST = 1002;
    const FAILED_TO_GET_SUCCESSFUL_RESPONSE = 1003;
    const MISSING_API_KEY = 1004;
    const MISSING_PUBLISHABLE_KEY = 1005;
    const UNSUPPORTED_API_ENVIRONMENT = 1006;
    const MISSING_MERCHANT_PUBLIC_ID = 1007;
    const MISSING_DIVISION_PUBLIC_ID = 1008;
    const FAILED_TO_PERFORM_ACTION = 1009;
    const UNSUPPORTED_API_ENDPOINT = 1010;
}
