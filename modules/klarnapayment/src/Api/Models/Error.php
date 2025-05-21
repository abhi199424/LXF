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

namespace KlarnaPayment\Module\Api\Models;

if (!defined('_PS_VERSION_')) {
    exit;
}

class Error implements \JsonSerializable
{
    /** @var ?string */
    private $errorCode;
    /** @var ?string[] */
    private $errorMessages;
    /** @var ?string */
    private $errorMessage;
    /** @var ?string */
    private $correlationId;

    public function getErrorCode(): ?string
    {
        return $this->errorCode;
    }

    public function getErrorMessages(): ?array
    {
        return $this->errorMessages;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function getCorrelationId(): ?string
    {
        return $this->correlationId;
    }

    /**
     * @maps error_code
     */
    public function setErrorCode(?string $errorCode): void
    {
        $this->errorCode = $errorCode;
    }

    /**
     * @maps error_messages
     */
    public function setErrorMessages(?array $errorMessages): void
    {
        $this->errorMessages = $errorMessages;
    }

    /**
     * @maps error_messages
     */
    public function setErrorMessage(?string $errorMessage): void
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @maps correlation_id
     */
    public function setCorrelationId(?string $correlationId): void
    {
        $this->correlationId = $correlationId;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['error_code'] = $this->getErrorCode();
        $json['error_message'] = $this->getErrorMessage();
        $json['error_messages'] = $this->getErrorMessages();
        $json['correlation_id'] = $this->getCorrelationId();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
