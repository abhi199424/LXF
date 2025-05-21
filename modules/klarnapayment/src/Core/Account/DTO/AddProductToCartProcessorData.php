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

namespace KlarnaPayment\Module\Core\Account\DTO;

if (!defined('_PS_VERSION_')) {
    exit;
}

class AddProductToCartProcessorData
{
    /** @var int */
    private $quantity;
    /** @var int */
    private $productId;
    /** @var int */
    private $productAttributeId;
    /** @var int */
    private $customizationId;

    private function __construct(
        int $quantity,
        int $productId,
        int $productAttributeId,
        int $customizationId
    ) {
        $this->quantity = $quantity;
        $this->productId = $productId;
        $this->productAttributeId = $productAttributeId;
        $this->customizationId = $customizationId;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return int
     */
    public function getProductId(): int
    {
        return $this->productId;
    }

    /**
     * @return int
     */
    public function getProductAttributeId(): int
    {
        return $this->productAttributeId;
    }

    /**
     * @return int
     */
    public function getCustomizationId(): int
    {
        return $this->customizationId;
    }

    public static function create(
        int $quantity,
        int $productId,
        int $productAttributeId,
        int $customizationId
    ): self {
        return new self(
            $quantity,
            $productId,
            $productAttributeId,
            $customizationId
        );
    }
}
