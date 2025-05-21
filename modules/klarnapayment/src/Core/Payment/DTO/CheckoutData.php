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

use KlarnaPayment\Module\Infrastructure\Request\Request;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CheckoutData
{
    /** @var int */
    private $cartId;
    /** @var string */
    private $authorizationToken;

    private function __construct(int $cartId, string $authorizationToken)
    {
        $this->cartId = $cartId;
        $this->authorizationToken = $authorizationToken;
    }

    public function getCartId(): int
    {
        return $this->cartId;
    }

    public function getAuthorizationToken(): string
    {
        return $this->authorizationToken;
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            (int) $request->getArgument('cart_id'),
            (string) $request->getArgument('authorization_token')
        );
    }
}
