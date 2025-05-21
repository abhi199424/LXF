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

if (!defined('_PS_VERSION_')) {
    exit;
}

class UpdateMerchantReferencesRequestData
{
    /** @var string */
    private $orderId;
    /** @var string|null */
    private $merchantReference1;
    /** @var string|null */
    private $merchantReference2;

    private function __construct(
        string $orderId,
        ?string $merchantReference1 = null,
        ?string $merchantReference2 = null
    ) {
        $this->orderId = $orderId;
        $this->merchantReference1 = $merchantReference1;
        $this->merchantReference2 = $merchantReference2;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    public function getMerchantReference1(): ?string
    {
        return $this->merchantReference1;
    }

    public function getMerchantReference2(): ?string
    {
        return $this->merchantReference2;
    }

    public static function create(
        string $orderId,
        ?string $merchantReference1 = null,
        ?string $merchantReference2 = null
    ): self {
        return new self(
            $orderId,
            $merchantReference1,
            $merchantReference2
        );
    }
}
