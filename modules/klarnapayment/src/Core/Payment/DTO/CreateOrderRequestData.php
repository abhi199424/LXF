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

namespace KlarnaPayment\Module\Core\Payment\DTO;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CreateOrderRequestData
{
    /** @var \Cart */
    private $cart;
    /** @var string */
    private $authorizationToken;

    private function __construct(\Cart $cart, string $authorizationToken)
    {
        $this->cart = $cart;
        $this->authorizationToken = $authorizationToken;
    }

    public function getCart(): \Cart
    {
        return $this->cart;
    }

    public function getAuthorizationToken(): string
    {
        return $this->authorizationToken;
    }

    public static function create(\Cart $cart, string $authorizationToken): self
    {
        return new self(
            $cart,
            $authorizationToken
        );
    }
}
