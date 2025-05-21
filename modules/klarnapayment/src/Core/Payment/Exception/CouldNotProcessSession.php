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

namespace KlarnaPayment\Module\Core\Payment\Exception;

use KlarnaPayment\Module\Infrastructure\Exception\ExceptionCode;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CouldNotProcessSession extends KlarnaPaymentException
{
    public static function failedToFindCart(int $cartId): self
    {
        return new static(
            sprintf('Failed to find cart by ID [%s]', $cartId),
            ExceptionCode::PAYMENT_FAILED_TO_FIND_CART,
            null,
            [
                'cart_id' => $cartId,
            ]
        );
    }
}
