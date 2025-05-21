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

class CouldNotProcessProductAddToCart extends KlarnaPaymentException
{
    public static function failedToCreateCartInstance(\Throwable $exception): self
    {
        return new static(
            'Failed to create cart instance.',
            ExceptionCode::ACCOUNT_FAILED_TO_CREATE_CART_INSTANCE,
            $exception
        );
    }

    public static function failedToAddProductToCart(
        int $cartId,
        int $quantity,
        int $productId,
        int $productAttributeId,
        int $customizationId
    ): self {
        return new static(
            'Failed to add product to cart.',
            ExceptionCode::ACCOUNT_FAILED_TO_ADD_PRODUCT_TO_CART,
            null,
            [
                'cart_id' => $cartId,
                'quantity' => $quantity,
                'product_id' => $productId,
                'product_attribute_id' => $productAttributeId,
                'customization_id' => $customizationId,
            ]
        );
    }

    public static function failedToUpdateContext(\Throwable $exception): self
    {
        return new static(
            'Failed to update context.',
            ExceptionCode::ACCOUNT_FAILED_TO_UPDATE_CONTEXT,
            $exception
        );
    }
}
