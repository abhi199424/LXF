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

namespace KlarnaPayment\Module\Infrastructure\Validation;

use KlarnaPayment\Module\Infrastructure\Utility\SecurityTokenUtility;
use KlarnaPayment\Module\Infrastructure\Validation\Exception\CouldNotValidateSecurityToken;

if (!defined('_PS_VERSION_')) {
    exit;
}

class SecurityTokenValidation
{
    /**
     * @throws CouldNotValidateSecurityToken
     */
    public function isTokenValid(string $securityToken, int $id, string $secretKey): void
    {
        if ($securityToken !== SecurityTokenUtility::generateTokenFromCart($id, $secretKey)) {
            throw CouldNotValidateSecurityToken::couldNotValidateSecurityToken();
        }
    }
}
