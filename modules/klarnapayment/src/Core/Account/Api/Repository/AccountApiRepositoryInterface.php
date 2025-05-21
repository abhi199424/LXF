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

namespace KlarnaPayment\Module\Core\Account\Api\Repository;

use KlarnaPayment\Module\Api\Exception\ApiException;
use KlarnaPayment\Module\Api\Requests\RefreshTokenRequest;
use KlarnaPayment\Module\Api\Responses\RefreshTokenResponse;
use KlarnaPayment\Module\Api\Responses\RetrieveJWKSResponse;

if (!defined('_PS_VERSION_')) {
    exit;
}

interface AccountApiRepositoryInterface
{
    /**
     * @throws ApiException
     */
    public function retrieveJWKS(): ?RetrieveJWKSResponse;

    public function refreshToken(RefreshTokenRequest $request): ?RefreshTokenResponse;
}
