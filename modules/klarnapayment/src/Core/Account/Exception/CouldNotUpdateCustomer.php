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

class CouldNotUpdateCustomer extends KlarnaPaymentException
{
    public static function customerNotFound(int $id): self
    {
        return new static(
            sprintf('Could not find customer with ID: [%s]', $id),
            ExceptionCode::ACCOUNT_COULD_NOT_FIND_CUSTOMER,
            null,
            [
                'id' => $id,
            ]
        );
    }
}
