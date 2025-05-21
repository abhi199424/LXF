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

class RefreshTokenRequest implements \JsonSerializable, RequestInterface
{
    /** @var ?string */
    private $refreshToken;
    /** @var ?string */
    private $clientId;
    /** @var ?string */
    private $grantType;

    public function getRefreshToken(): ?string
    {
        return $this->refreshToken;
    }

    public function getClientId(): ?string
    {
        return $this->clientId;
    }

    public function getGrantType(): ?string
    {
        return $this->grantType;
    }

    /**
     * @maps refresh_token
     */
    public function setRefreshToken(?string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    /**
     * @maps client_id
     */
    public function setClientId(?string $clientId): void
    {
        $this->clientId = $clientId;
    }

    /**
     * @maps grant_type
     */
    public function setGrantType(?string $grantType): void
    {
        $this->grantType = $grantType;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['client_id'] = $this->getClientId();
        $json['refresh_token'] = $this->getRefreshToken();
        $json['grant_type'] = $this->getGrantType();

        return array_filter($json, function ($val) {
            return $val !== null;
        });
    }
}
