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

class MerchantUrl implements \JsonSerializable
{
    /** @var ?string */
    private $confirmation;
    /** @var ?string */
    private $notification;
    /** @var ?string */
    private $push;
    /** @var ?string */
    private $authorization;

    public function getConfirmation(): ?string
    {
        return $this->confirmation;
    }

    public function getNotification(): ?string
    {
        return $this->notification;
    }

    public function getPush(): ?string
    {
        return $this->push;
    }

    public function getAuthorization(): ?string
    {
        return $this->authorization;
    }

    /**
     * @maps confirmation
     */
    public function setConfirmation(?string $confirmation): void
    {
        $this->confirmation = $confirmation;
    }

    /**
     * @maps notification
     */
    public function setNotification(?string $notification): void
    {
        $this->notification = $notification;
    }

    /**
     * @maps push
     */
    public function setPush(?string $push): void
    {
        $this->push = $push;
    }

    /**
     * @maps authorization
     */
    public function setAuthorization(?string $authorization): void
    {
        $this->authorization = $authorization;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['confirmation'] = $this->getConfirmation();
        $json['notification'] = $this->getNotification();
        $json['push'] = $this->getPush();
        $json['authorization'] = $this->getAuthorization();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
