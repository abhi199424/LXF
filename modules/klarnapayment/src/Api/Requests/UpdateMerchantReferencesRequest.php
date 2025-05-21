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

namespace KlarnaPayment\Module\Api\Requests;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @see https://docs.klarna.com/api/ordermanagement/#operation/updateMerchantReferences
 */
class UpdateMerchantReferencesRequest implements \JsonSerializable, RequestInterface
{
    /** @var string */
    private $orderId;

    /** @var ?string */
    private $merchantReference1;

    /** @var ?string */
    private $merchantReference2;

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

    /**
     * @maps order_id
     */
    public function setOrderId(string $orderId): void
    {
        $this->orderId = $orderId;
    }

    /**
     * @maps merchant_reference1
     */
    public function setMerchantReference1(?string $merchantReference1): void
    {
        $this->merchantReference1 = $merchantReference1;
    }

    /**
     * @maps merchant_reference2
     */
    public function setMerchantReference2(?string $merchantReference2): void
    {
        $this->merchantReference2 = $merchantReference2;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['order_id'] = $this->getOrderId();
        $json['merchant_reference1'] = $this->getMerchantReference1();
        $json['merchant_reference2'] = $this->getMerchantReference2();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
