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

namespace KlarnaPayment\Module\Api\Responses;

use KlarnaPayment\Module\Api\Models\PaymentMethodCategory;

if (!defined('_PS_VERSION_')) {
    exit;
}

/**
 * @see https://docs.klarna.com/api/payments/#operation/createCreditSession
 */
class CreatePaymentSessionResponse implements \JsonSerializable, ResponseInterface
{
    /** @var ?string */
    private $clientToken;
    /** @var ?PaymentMethodCategory[] */
    private $paymentMethodCategories;
    /** @var ?string */
    private $sessionId;

    public function getClientToken(): ?string
    {
        return $this->clientToken;
    }

    /**
     * @return ?PaymentMethodCategory[]
     */
    public function getPaymentMethodCategories(): ?array
    {
        return $this->paymentMethodCategories;
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    /**
     * @maps client_token
     */
    public function setClientToken(string $clientToken): void
    {
        $this->clientToken = $clientToken;
    }

    /**
     * @param \KlarnaPayment\Module\Api\Models\PaymentMethodCategory[] $paymentMethodCategories
     *
     * @maps payment_method_categories
     */
    public function setPaymentMethodCategories(array $paymentMethodCategories): void
    {
        $this->paymentMethodCategories = $paymentMethodCategories;
    }

    /**
     * @maps session_id
     */
    public function setSessionId(string $sessionId): void
    {
        $this->sessionId = $sessionId;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['client_token'] = $this->getClientToken();
        $json['payment_method_categories'] = $this->getPaymentMethodCategories();
        $json['session_id'] = $this->getSessionId();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
