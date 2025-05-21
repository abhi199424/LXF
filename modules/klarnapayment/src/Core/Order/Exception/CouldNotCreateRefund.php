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

namespace KlarnaPayment\Module\Core\Order\Exception;

use KlarnaPayment\Module\Infrastructure\Exception\ExceptionCode;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CouldNotCreateRefund extends OrderProcessException
{
    public static function invalidRefundRequest(int $refundedAmount): self
    {
        return new static(
            sprintf('Failed to refund amount. Given amount (%s) is invalid', $refundedAmount),
            ExceptionCode::ORDER_REFUND_AMOUNT_IS_INVALID
        );
    }
}
