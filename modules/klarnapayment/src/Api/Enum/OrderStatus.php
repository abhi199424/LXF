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

namespace KlarnaPayment\Module\Api\Enum;

if (!defined('_PS_VERSION_')) {
    exit;
}

class OrderStatus
{
    // TODO: what is pending and why authorized is not mapped ? not handling expired and closed statuses ...

    public const AUTHORIZED = 'authorized';
    public const CANCELLED = 'cancelled';
    /** NOTE: Klarna does not have "COMPLETED" order state */
    public const CAPTURED = 'captured';
    public const ACCEPTED = 'accepted';
    public const CLOSED = 'closed';
    public const PARTIALLY_CAPTURED = 'part_captured';

    // NOT ACTUAL VALUES JUST USING THEM AS ENUM
    public const FULLY_REFUNDED = 'fully_refunded';
    public const EXPIRED = 'expired';
    public const PROCESSING = 'processing';
}
