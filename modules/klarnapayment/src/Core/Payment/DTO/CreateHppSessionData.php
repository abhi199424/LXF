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

use KlarnaPayment\Module\Api\Models\Session;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CreateHppSessionData
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var \Cart
     */
    private $cart;

    private function __construct(Session $session, \Cart $cart)
    {
        $this->session = $session;
        $this->cart = $cart;
    }

    public function getSession(): Session
    {
        return $this->session;
    }

    public function getCart(): \Cart
    {
        return $this->cart;
    }

    public static function create(Session $session, \Cart $cart): self
    {
        return new self(
            $session,
            $cart
        );
    }
}
