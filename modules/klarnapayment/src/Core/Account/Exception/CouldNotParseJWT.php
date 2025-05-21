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

namespace KlarnaPayment\Module\Core\Account\Exception;

use KlarnaPayment\Module\Infrastructure\Exception\ExceptionCode;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CouldNotParseJWT extends KlarnaPaymentException
{
    public static function invalidAccessTokenKid(string $actualKid): self
    {
        return new self(
            'Token expired',
            ExceptionCode::ACCOUNT_INVALID_KID,
            null,
            [
                'actual_kid' => $actualKid,
            ]
        );
    }

    public static function tokenExpired(string $expectedToken, string $actualToken): self
    {
        return new self(
            'Token expired',
            ExceptionCode::ACCOUNT_TOKEN_EXPIRED,
            null,
            [
                'expected_token' => $expectedToken,
                'actual_token' => $actualToken,
            ]
        );
    }

    public static function invalidIssuer(string $expectedIssuer, string $actualIssuer): self
    {
        return new self(
            'Invalid issuer',
            ExceptionCode::ACCOUNT_INVALID_ISSUER,
            null,
            [
                'expected_issuer' => $expectedIssuer,
                'actual_issuer' => $actualIssuer,
            ]
        );
    }

    public static function invalidScope(string $expectedScope, string $actualScope): self
    {
        return new self(
            'Invalid scope',
            ExceptionCode::ACCOUNT_INVALID_SCOPE,
            null,
            [
                'expected_scope' => $expectedScope,
                'actual_scope' => $actualScope,
            ]
        );
    }
}
