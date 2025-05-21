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

namespace KlarnaPayment\Module\Core\Merchant\Exception;

use KlarnaPayment\Module\Infrastructure\Exception\ExceptionCode;
use KlarnaPayment\Module\Infrastructure\Exception\KlarnaPaymentException;

if (!defined('_PS_VERSION_')) {
    exit;
}

class CouldNotLogin extends KlarnaPaymentException
{
    public static function genericError(array $apiRegions): self
    {
        return new self(
            'Could not login',
            ExceptionCode::CONFIGURATION_MERCHANT_LOGIN_FAILED,
            null,
            ['apiRegions' => $apiRegions]
        );
    }

    public function getRegionsNames(): array
    {
        $regionsNames = [];

        foreach ($this->getContext()['apiRegions'] ?? [] as $region) {
            $regionsNames[] = $region['region'];
        }

        return $regionsNames;
    }
}
