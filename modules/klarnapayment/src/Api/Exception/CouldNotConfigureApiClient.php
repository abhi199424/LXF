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

class CouldNotConfigureApiClient extends KlarnaPaymentApiException
{
    public function __construct($message = '', $code = 0)
    {
        parent::__construct($message, $code, null);
    }

    public static function missingApiKey(): self
    {
        return new self('Api key is missing for api client.', ExceptionCode::MISSING_API_KEY);
    }

    public static function missingPublishableKey(): self
    {
        return new self('Publishable key is missing for api client.', ExceptionCode::MISSING_PUBLISHABLE_KEY);
    }

    public static function missingMerchantPublicId(): self
    {
        return new self('Merchant public id is missing for api client.', ExceptionCode::MISSING_MERCHANT_PUBLIC_ID);
    }

    public static function missingDivisionPublicId(): self
    {
        return new self('Division public id is missing for api client.', ExceptionCode::MISSING_DIVISION_PUBLIC_ID);
    }

    public static function unsupportedApiEnvironment(string $environment): self
    {
        return new self(sprintf('Api environment (%s) is not supported.', $environment), ExceptionCode::UNSUPPORTED_API_ENVIRONMENT);
    }

    public static function unsupportedApiEndpoint(string $endpoint): self
    {
        return new self(sprintf('Api endpoint (%s) is not supported.', $endpoint), ExceptionCode::UNSUPPORTED_API_ENDPOINT);
    }
}
