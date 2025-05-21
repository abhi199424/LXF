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

namespace KlarnaPayment\Module\Core\Order\DTO;

use KlarnaPayment\Module\Infrastructure\Request\Request;

if (!defined('_PS_VERSION_')) {
    exit;
}

class GetOrderDetailsData
{
    /** @var int */
    private $cartId;

    private function __construct(int $cartId)
    {
        $this->cartId = $cartId;
    }

    public function getCartId(): int
    {
        return $this->cartId;
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            $request->get('cart_id')
        );
    }
}
