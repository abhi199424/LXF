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

if (!defined('_PS_VERSION_')) {
    exit;
}

class CouldNotHandleLocking extends KlarnaPaymentException
{
    public static function lockExists(): self
    {
        return new self(
            'Lock exists',
            ExceptionCode::INFRASTRUCTURE_LOCK_EXISTS
        );
    }

    public static function lockOnAcquireIsMissing(): self
    {
        return new self(
            'Lock on acquire is missing',
            ExceptionCode::INFRASTRUCTURE_LOCK_ON_ACQUIRE_IS_MISSING
        );
    }

    public static function lockOnReleaseIsMissing(): self
    {
        return new self(
            'Lock on release is missing',
            ExceptionCode::INFRASTRUCTURE_LOCK_ON_RELEASE_IS_MISSING
        );
    }
}
