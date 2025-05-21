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

class HppSessionMerchantUrl implements \JsonSerializable
{
    /** @var ?string */
    private $back;
    /** @var ?string */
    private $cancel;
    /** @var ?string */
    private $error;
    /** @var ?string */
    private $failure;
    /** @var ?string */
    private $statusUpdate;
    /** @var ?string */
    private $success;

    /**
     * @return string|null
     */
    public function getBack(): ?string
    {
        return $this->back;
    }

    /**
     * @return string|null
     */
    public function getCancel(): ?string
    {
        return $this->cancel;
    }

    /**
     * @return string|null
     */
    public function getError(): ?string
    {
        return $this->error;
    }

    /**
     * @return string|null
     */
    public function getFailure(): ?string
    {
        return $this->failure;
    }

    /**
     * @return string|null
     */
    public function getStatusUpdate(): ?string
    {
        return $this->statusUpdate;
    }

    /**
     * @return string|null
     */
    public function getSuccess(): ?string
    {
        return $this->success;
    }

    /**
     * @maps back
     */
    public function setBack(?string $back): void
    {
        $this->back = $back;
    }

    /**
     * @maps cancel
     */
    public function setCancel(?string $cancel): void
    {
        $this->cancel = $cancel;
    }

    /**
     * @maps error
     */
    public function setError(?string $error): void
    {
        $this->error = $error;
    }

    /**
     * @maps failure
     */
    public function setFailure(?string $failure): void
    {
        $this->failure = $failure;
    }

    /**
     * @maps status_update
     */
    public function setStatusUpdate(?string $statusUpdate): void
    {
        $this->statusUpdate = $statusUpdate;
    }

    /**
     * @maps success
     */
    public function setSuccess(?string $success): void
    {
        $this->success = $success;
    }

    public function jsonSerialize(): array
    {
        $json = [
            'back' => $this->getBack(),
            'cancel' => $this->getCancel(),
            'error' => $this->getError(),
            'failure' => $this->getFailure(),
            'status_update' => $this->getStatusUpdate(),
            'success' => $this->getSuccess(),
        ];

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
