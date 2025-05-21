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

namespace KlarnaPayment\Module\Core\Account\Exception;

use KlarnaPayment\Module\Infrastructure\Exception\ExceptionCode;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CouldNotProcessExpressCheckout extends KlarnaPaymentException
{
    public static function isNotActive(): self
    {
        return new self('KEC flow is not active', ExceptionCode::KEC_FLOW_NOT_AVAILABLE);
    }

    public static function addressMismatch(): self
    {
        return new self('Addresses does not match', ExceptionCode::KEC_FLOW_NOT_AVAILABLE);
    }

    public static function flowFinished(): self
    {
        return new self('KEC flow has been finished', ExceptionCode::KEC_FLOW_NOT_AVAILABLE);
    }
}
