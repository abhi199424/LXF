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

class CreatePaymentSessionData
{
    /** @var \Cart */
    private $cart;
    /** @var bool */
    private $addCustomerDetails;

    private function __construct(
        \Cart $cart,
        bool $addCustomerDetails
    ) {
        $this->cart = $cart;
        $this->addCustomerDetails = $addCustomerDetails;
    }

    public function getCart(): \Cart
    {
        return $this->cart;
    }

    public function isAddCustomerDetails(): bool
    {
        return $this->addCustomerDetails;
    }

    public static function create(
        \Cart $cart,
        bool $addCustomerDetails = false
    ): self {
        return new self(
            $cart,
            $addCustomerDetails
        );
    }
}
