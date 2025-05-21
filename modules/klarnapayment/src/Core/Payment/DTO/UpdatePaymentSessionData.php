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

class UpdatePaymentSessionData
{
    /** @var string */
    private $sessionId;
    /** @var \Cart */
    private $cart;
    /** @var bool */
    private $addCustomerDetails;

    private function __construct(
        $sessionId,
        \Cart $cart,
        bool $addCustomerDetails
    ) {
        $this->sessionId = $sessionId;
        $this->cart = $cart;
        $this->addCustomerDetails = $addCustomerDetails;
    }

    public function getSessionId(): string
    {
        return $this->sessionId;
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
        string $sessionId,
        \Cart $cart,
        bool $addCustomerDetails = false
    ): self {
        return new self(
            $sessionId,
            $cart,
            $addCustomerDetails
        );
    }
}
