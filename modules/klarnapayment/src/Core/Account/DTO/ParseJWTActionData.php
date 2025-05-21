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

use KlarnaPayment\Module\Api\Models\JWK;

if (!defined('_PS_VERSION_')) {
    exit;
}

class ParseJWTActionData
{
    /** @var string */
    private $token;
    /** @var JWK[] */
    private $jwks;

    public function __construct(
        string $token,
        array $jwks
    ) {
        $this->token = $token;
        $this->jwks = $jwks;
    }

    /**
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @return JWK[]
     */
    public function getJwks(): array
    {
        return $this->jwks;
    }

    public static function create(
        string $token,
        array $jwks
    ): self {
        return new self(
            $token,
            $jwks
        );
    }
}
