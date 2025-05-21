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

class CouldNotProcessCheckout extends KlarnaPaymentException
{
    public static function failedToFindCart(int $cartId): self
    {
        return new static(
            sprintf('Failed to find cart by ID %s', $cartId),
            ExceptionCode::PAYMENT_FAILED_TO_FIND_CART,
            null,
            [
                'cart_id' => $cartId,
            ]
        );
    }

    public static function failedToLockCart(\Throwable $exception, int $cartId): self
    {
        return new static(
            sprintf('Failed to lock Cart. Cart ID %s', $cartId),
            ExceptionCode::PAYMENT_FAILED_TO_LOCK_CART,
            $exception,
            [
                'cart_id' => $cartId,
            ]
        );
    }

    public static function failedToCreateExternalOrder(\Throwable $exception, int $cartId): self
    {
        return new static(
            sprintf('Failed to create external order. Cart ID %s', $cartId),
            ExceptionCode::PAYMENT_FAILED_TO_CREATE_EXTERNAL_ORDER,
            $exception,
            [
                'cart_id' => $cartId,
            ]
        );
    }

    public static function failedToRetrieveExternalOrder(\Throwable $exception, string $orderId): self
    {
        return new static(
            sprintf('Failed to retrieve external order. External order ID %s', $orderId),
            ExceptionCode::PAYMENT_FAILED_TO_RETRIEVE_EXTERNAL_ORDER,
            $exception,
            [
                'order_id' => $orderId,
            ]
        );
    }

    public static function failedToValidateOrder(\Throwable $exception, int $cartId): self
    {
        return new static(
            sprintf('Failed to validate order. Cart ID %s', $cartId),
            ExceptionCode::PAYMENT_FAILED_TO_VALIDATE_ORDER,
            $exception,
            [
                'cart_id' => $cartId,
            ]
        );
    }

    public static function failedToUpdateMerchantReferences(\Throwable $exception, string $externalOrderId, int $internalOrderId): self
    {
        return new static(
            sprintf(
                'Failed to update merchant references. External order ID %s, internal order ID %s',
                $externalOrderId,
                $internalOrderId
            ),
            ExceptionCode::PAYMENT_FAILED_TO_UPDATE_MERCHANT_REFERENCES,
            $exception,
            [
                'external_order_id' => $externalOrderId,
                'internal_order_id' => $internalOrderId,
            ]
        );
    }

    public static function failedToAddOrderMapping(\Throwable $exception, string $externalOrderId, int $internalOrderId): self
    {
        return new static(
            sprintf(
                'Failed to add order mapping. External order ID %s, internal order ID %s',
                $externalOrderId,
                $internalOrderId
            ),
            ExceptionCode::PAYMENT_FAILED_TO_ADD_ORDER_MAPPING,
            $exception,
            [
                'external_order_id' => $externalOrderId,
                'internal_order_id' => $internalOrderId,
            ]
        );
    }

    public static function failedToAutoCapture(\Throwable $exception, string $externalOrderId, int $internalOrderId): self
    {
        return new static(
            sprintf('Failed to auto capture order. External order ID (%s), internal order ID (%s)',
                $externalOrderId,
                $internalOrderId
            ),
            ExceptionCode::PAYMENT_FAILED_TO_AUTO_CAPTURE,
            $exception,
            [
                'external_order_id' => $externalOrderId,
                'internal_order_id' => $internalOrderId,
            ]
        );
    }

    public static function failedToUnlockCart(\Throwable $exception, int $cartId): self
    {
        return new static(
            sprintf('Failed to unlock cart (%s).', $cartId),
            ExceptionCode::PAYMENT_FAILED_TO_UNLOCK_CART,
            $exception,
            [
                'cart_id' => $cartId,
            ]
        );
    }

    public static function failedToChangeOrderStatus(\Throwable $exception, int $orderId): self
    {
        return new static(
            sprintf('Failed to change order status (%s).', $orderId),
            ExceptionCode::ORDER_FAILED_TO_CHANGE_ORDER_STATUS,
            $exception,
            [
                'order_id' => $orderId,
            ]
        );
    }
}
