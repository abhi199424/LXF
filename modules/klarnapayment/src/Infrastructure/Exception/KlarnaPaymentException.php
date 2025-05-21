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

namespace KlarnaPayment\Module\Infrastructure\Exception;

use KlarnaPayment\Module\Infrastructure\Utility\ExceptionUtility;

if (!defined('_PS_VERSION_')) {
    exit;
}

class KlarnaPaymentException extends \Exception
{
    private $context;

    final public function __construct(
        string $internalMessage,
        int $code,
        \Throwable $previous = null,
        array $context = []
    ) {
        parent::__construct($internalMessage, $code, $previous);
        $this->context = $context;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getExceptions(): array
    {
        $exceptions = [];
        $exception = $this;
        while ($exception) {
            $exceptions[] = ExceptionUtility::toArray($exception);

            $exception = $exception->getPrevious();
        }

        return $exceptions;
    }

    public static function unknownError(\Throwable $exception): self
    {
        return new static(
            'An unknown error error occurred. Please check system logs or contact Klarna Official support.',
            ExceptionCode::UNKNOWN_ERROR,
            $exception
        );
    }

    public static function failedToGetSuccessfulApiResponse(\Exception $exception): self
    {
        return new static(
            'Failed to get successful api response.',
            ExceptionCode::API_FAILED_TO_GET_SUCCESSFUL_RESPONSE,
            $exception
        );
    }

    public static function failedToCreateApiRequest(\Throwable $exception): self
    {
        return new static(
            'Failed to create api request.',
            ExceptionCode::API_FAILED_TO_CREATE_REQUEST,
            $exception
        );
    }

    public static function merchantIsNotLoggedIn(): self
    {
        return new static(
            'Merchant is not logged in.',
            ExceptionCode::CONFIGURATION_MERCHANT_IS_NOT_LOGGED_IN
        );
    }
}
