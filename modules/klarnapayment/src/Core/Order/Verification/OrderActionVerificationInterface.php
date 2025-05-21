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

namespace KlarnaPayment\Module\Core\Order\Verification;

use KlarnaPayment\Module\Api\Models\Order;

if (!defined('_PS_VERSION_')) {
    exit;
}

interface OrderActionVerificationInterface
{
    public function verify(Order $order): bool;
}
