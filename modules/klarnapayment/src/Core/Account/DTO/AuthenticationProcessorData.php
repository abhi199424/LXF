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

class AuthenticationProcessorData
{
    /** @var string */
    private $tokenId;
    /** @var string */
    private $refreshToken;

    public function __construct(
        string $tokenId,
        string $refreshToken
    ) {
        $this->tokenId = $tokenId;
        $this->refreshToken = $refreshToken;
    }

    /**
     * @return string
     */
    public function getTokenId(): string
    {
        return $this->tokenId;
    }

    /**
     * @return string
     */
    public function getRefreshToken(): string
    {
        return $this->refreshToken;
    }

    public static function create(
        string $tokenId,
        string $refreshToken
    ): self {
        return new self(
            $tokenId,
            $refreshToken
        );
    }
}
