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

if (!defined('_PS_VERSION_')) {
    exit;
}

/** @see https://docs.klarna.com/sign-in-with-klarna/integrate-sign-in-with-klarna/sign-in-with-klarna-tokens/decoding-id-token/ */
class RefreshTokenResponse implements \JsonSerializable, ResponseInterface
{
    /** @var ?string */
    private $accessToken;
    /** @var ?string */
    private $refreshToken;

    /**
     * @maps access_token
     */
    public function setAccessToken(?string $accessToken): void
    {
        $this->accessToken = $accessToken;
    }

    /**
     * @maps refresh_token
     */
    public function setRefreshToken(?string $refreshToken): void
    {
        $this->refreshToken = $refreshToken;
    }

    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public function getAccessToken(): ?string
    {
        return $this->accessToken;
    }

    public function jsonSerialize(): array
    {
        $json = [];
        $json['refresh_token'] = $this->getRefreshToken();
        $json['access_token'] = $this->getAccessToken();

        return array_filter($json, static function ($val) {
            return !empty($val);
        });
    }
}
